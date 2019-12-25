<?php

namespace app\api\model\sharing;

use app\api\model\User as UserModel;
use app\api\model\Setting as SettingModel;
use app\api\model\UserCoupon as UserCouponModel;
use app\api\model\dealer\Order as DealerOrderModel;
use app\api\model\sharing\Goods as GoodsModel;
use app\api\model\sharing\GoodsSku as GoodsSkuModel;
use app\common\model\sharing\Order as OrderModel;
use app\api\model\sharing\OrderGoods as OrderGoodsModel;
use app\api\service\User as UserService;
use app\api\service\sharing\order\PaySuccess;
use app\api\service\Payment as PaymentService;
use app\common\enum\OrderType as OrderTypeEnum;
use app\common\enum\order\Status as OrderStatusEnum;
use app\common\enum\DeliveryType as DeliveryTypeEnum;
use app\common\enum\order\PayType as PayTypeEnum;
use app\common\enum\order\PayStatus as PayStatusEnum;
use app\common\service\order\Complete as OrderCompleteService;
use app\common\library\helper;
use app\common\exception\BaseException;

/**
 * 拼团订单模型
 * Class Order
 * @package app\api\model
 */
class Order extends OrderModel
{
    /**
     * 隐藏字段
     * @var array
     */
    protected $hidden = [
        'wxapp_id',
        'update_time'
    ];

    /**
     * 订单结算api参数
     * @var array
     */
    private $checkoutParam = [
        'active_id' => 0,   // 参与的拼单id
        'delivery' => null, // 配送方式
        'shop_id' => 0,     // 自提门店id
        'linkman' => '',    // 自提联系人
        'phone' => '',    // 自提联系电话
        'coupon_id' => 0,    // 优惠券id
        'remark' => '',    // 买家留言
        'pay_type' => PayTypeEnum::WECHAT,  // 支付方式
    ];

    /**
     * 待支付订单详情
     * @param $orderNo
     * @return null|static
     * @throws \think\exception\DbException
     */
    public static function getPayDetail($orderNo)
    {
        return self::get(['order_no' => $orderNo, 'pay_status' => 10, 'is_delete' => 0], ['goods', 'user']);
    }

    /**
     * 获取订单商品列表
     * @param $params
     * @return array
     */
    public function getOrderGoodsListByNow($params)
    {
        // 商品详情
        /* @var GoodsModel $goods */
        $goods = GoodsModel::detail($params['goods_id']);
        // 商品sku信息
        $goods['goods_sku'] = GoodsModel::getGoodsSku($goods, $params['goods_sku_id']);
        // 商品列表
        $goodsList = [$goods->hidden(['category', 'content', 'image', 'sku'])];
        foreach ($goodsList as &$item) {
            // 商品单价(根据order_type判断单买还是拼单)
            // order_type：下单类型 10 => 单独购买，20 => 拼团
            $item['goods_price'] = $params['order_type'] == 10 ? $item['goods_sku']['goods_price']
                : $item['goods_sku']['sharing_price'];
            // 商品购买数量
            $item['total_num'] = $params['goods_num'];
            $item['spec_sku_id'] = $item['goods_sku']['spec_sku_id'];
            // 商品购买总金额
            $item['total_price'] = helper::bcmul($item['goods_price'], $params['goods_num']);
        }
        return $goodsList;
    }

    /**
     * 订单支付事件
     * @param int $payType
     * @return bool
     * @throws \think\exception\DbException
     */
    public function onPay($payType = PayTypeEnum::WECHAT)
    {
        // 判断商品状态、库存
        if (!$this->checkGoodsStatusFromOrder($this['goods'])) {
            return false;
        }
        // 余额支付
        if ($payType == PayTypeEnum::BALANCE) {
            return $this->onPaymentByBalance($this['order_no']);
        }
        return true;
    }

    /**
     * 构建支付请求的参数
     * @param $user
     * @param $order
     * @param $payType
     * @return array
     * @throws BaseException
     * @throws \think\exception\DbException
     */
    public function onOrderPayment($user, $order, $payType)
    {
        if ($payType == PayTypeEnum::WECHAT) {
            return $this->onPaymentByWechat($user, $order);
        }
        return [];
    }

    /**
     * 构建微信支付请求
     * @param $user
     * @param $order
     * @return array
     * @throws BaseException
     * @throws \think\exception\DbException
     */
    private function onPaymentByWechat($user, $order)
    {
        return PaymentService::wechat(
            $user,
            $order['order_id'],
            $order['order_no'],
            $order['pay_price'],
            OrderTypeEnum::SHARING
        );
    }

    /**
     * 余额支付标记订单已支付
     * @param $orderNo
     * @return bool
     * @throws \think\exception\DbException
     */
    public function onPaymentByBalance($orderNo)
    {
        // 获取订单详情
        $PaySuccess = new PaySuccess($orderNo);
        // 发起余额支付
        $status = $PaySuccess->onPaySuccess(PayTypeEnum::BALANCE);
        if (!$status) {
            $this->error = $PaySuccess->getError();
        }
        return $status;
    }

    /**
     * 验证拼单是否允许加入
     * @param $active_id
     * @return bool
     * @throws BaseException
     * @throws \think\exception\DbException
     */
    public function checkActiveIsAllowJoin($active_id)
    {
        // 拼单详情
        $detail = Active::detail($active_id);
        if (!$detail) {
            throw new BaseException('很抱歉，拼单不存在');
        }
        // 验证当前拼单是否允许加入新成员
        return $detail->checkAllowJoin();
    }

    /**
     * 保存上门自提联系人
     * @param $linkman
     * @param $phone
     * @return false|\think\Model
     */
    public function saveOrderExtract($linkman, $phone)
    {
        // 记忆上门自提联系人(缓存)，用于下次自动填写
        UserService::setLastExtract($this['user_id'], trim($linkman), trim($phone));
        // 保存上门自提联系人(数据库)
        return $this->extract()->save([
            'linkman' => trim($linkman),
            'phone' => trim($phone),
            'user_id' => $this['user_id'],
            'wxapp_id' => self::$wxapp_id,
        ]);
    }

    /**
     * 用户拼团订单列表
     * @param $user_id
     * @param string $type
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function getList($user_id, $type = 'all')
    {
        // 筛选条件
        $filter = [];
        // 订单数据类型
        switch ($type) {
            case 'all':
                // 全部
                break;
            case 'payment';
                // 待支付
                $filter['pay_status'] = PayStatusEnum::PENDING;
                break;
            case 'sharing';
                // 拼团中
                $filter['active.status'] = 10;
                break;
            case 'delivery';
                // 待发货
                $this->where('IF ( (`order`.`order_type` = 20), (`active`.`status` = 20), TRUE)');
                $filter['pay_status'] = 20;
                $filter['delivery_status'] = 10;
                break;
            case 'received';
                // 待收货
                $filter['pay_status'] = 20;
                $filter['delivery_status'] = 20;
                $filter['receipt_status'] = 10;
                break;
            case 'comment';
                $filter['order_status'] = 30;
                $filter['is_comment'] = 0;
                break;
        }
        return $this->with(['goods.image', 'active'])
            ->alias('order')
            ->field('order.*, active.status as active_status')
            ->join('sharing_active active', 'order.active_id = active.active_id', 'LEFT')
            ->where('user_id', '=', $user_id)
            ->where($filter)
            ->where('order.is_delete', '=', 0)
            ->order(['create_time' => 'desc'])
            ->paginate(15, false, [
                'query' => \request()->request()
            ]);
    }

    /**
     * 取消订单
     * @param UserModel $user
     * @return bool|false|int
     */
    public function cancel($user)
    {
        if ($this['delivery_status']['value'] == 20) {
            $this->error = '已发货订单不可取消';
            return false;
        }
        if ($this['order_type']['value'] == 20) {
            $this->error = '拼团订单不允许取消';
            return false;
        }
        // 订单取消事件
        $this->transaction(function () use ($user) {
            // 订单是否已支付
            $isPay = $this['pay_status']['value'] == PayStatusEnum::SUCCESS;
            // 未付款的订单
            if ($isPay == false) {
                // 回退商品库存
                (new OrderGoodsModel)->backGoodsStock($this['goods'], $isPay);
                // 回退用户优惠券
                $this['coupon_id'] > 0 && UserCouponModel::setIsUse($this['coupon_id'], false);
                // 回退用户积分
                $describe = "订单取消：{$this['order_no']}";
                $this['points_num'] > 0 && $user->setIncPoints($this['points_num'], $describe);
            }
            // 更新订单状态
            return $this->save(['order_status' => $isPay ? OrderStatusEnum::APPLY_CANCEL : OrderStatusEnum::CANCELLED]);
        });
        return true;
    }

    /**
     * 确认收货
     * @return bool|mixed
     */
    public function receipt()
    {
        // 验证订单是否合法
        // 条件1: 订单必须已发货
        // 条件2: 订单必须未收货
        if ($this['delivery_status']['value'] != 20 || $this['receipt_status']['value'] != 10) {
            $this->error = '该订单不合法';
            return false;
        }
        return $this->transaction(function () {
            // 更新订单状态
            $status = $this->save([
                'receipt_status' => 20,
                'receipt_time' => time(),
                'order_status' => 30
            ]);
            // 执行订单完成后的操作
            $OrderCompleteService = new OrderCompleteService(OrderTypeEnum::SHARING);
            $OrderCompleteService->complete([$this], static::$wxapp_id);
            return $status;
        });
    }

    /**
     * 获取订单总数
     * @param $user_id
     * @param string $type
     * @return int|string
     * @throws \think\Exception
     */
    public function getCount($user_id, $type = 'all')
    {
        // 筛选条件
        $filter = [];
        // 订单数据类型
        switch ($type) {
            case 'all':
                break;
            case 'payment';
                $filter['pay_status'] = PayStatusEnum::PENDING;
                break;
            case 'received';
                $filter['pay_status'] = PayStatusEnum::SUCCESS;
                $filter['delivery_status'] = 20;
                $filter['receipt_status'] = 10;
                break;
            case 'comment';
                $filter['order_status'] = 30;
                $filter['is_comment'] = 0;
                break;
        }
        return $this->where('user_id', '=', $user_id)
            ->where('order_status', '<>', 20)
            ->where($filter)
            ->where('is_delete', '=', 0)
            ->count();
    }

    /**
     * 订单详情
     * @param $order_id
     * @param $user_id
     * @return array|false|\PDOStatement|string|\think\Model|static
     * @throws BaseException
     */
    public static function getUserOrderDetail($order_id, $user_id)
    {
        $order = (new static)->with(['goods' => ['image', 'refund'], 'address', 'express', 'extract_shop'])
            ->alias('order')
            ->field('order.*, active.status as active_status')
            ->join('sharing_active active', 'order.active_id = active.active_id', 'LEFT')
            ->where([
                'order_id' => $order_id,
                'user_id' => $user_id,
            ])->find();
        if (!$order) {
            throw new BaseException(['msg' => '订单不存在']);
        }
        return $order;
    }

    /**
     * 判断商品库存不足 (未付款订单)
     * @param $goodsList
     * @return bool
     * @throws \think\exception\DbException
     */
    private function checkGoodsStatusFromOrder($goodsList)
    {
        foreach ($goodsList as $goods) {
            // 判断商品是否下架
            if (
                empty($goods['goods'])
                || $goods['goods']['goods_status']['value'] != 10
            ) {
                $this->error = "很抱歉，商品 [{$goods['goods_name']}] 已下架";
                return false;
            }
            // 获取商品的sku信息
            $goodsSku = GoodsSkuModel::detail($goods['goods_id'], $goods['spec_sku_id']);
            // sku已不存在
            if (empty($goodsSku)) {
                $this->error = "很抱歉，商品 [{$goods['goods_name']}] sku已不存在，请重新下单";
                return false;
            }
            // 付款减库存
            if ($goods['deduct_stock_type'] == 20 && $goods['total_num'] > $goodsSku['stock_num']) {
                $this->error = "很抱歉，商品 [{$goods['goods_name']}] 库存不足";
                return false;
            }
        }
        return true;
    }

    /**
     * 当前订单是否允许申请售后
     * @return bool
     */
    public function isAllowRefund()
    {
        // 允许申请售后期限
        $refund_days = SettingModel::getItem('trade')['order']['refund_days'];
        if ($refund_days == 0) {
            return false;
        }
        if (time() > $this['receipt_time'] + ((int)$refund_days * 86400)) {
            return false;
        }
        if ($this['receipt_status']['value'] != 20) {
            return false;
        }
        return true;
    }

    /**
     * 判断当前订单是否允许核销
     * @param static $order
     * @return bool
     */
    public function checkExtractOrder(&$order)
    {
        if (
            $order['pay_status']['value'] == PayStatusEnum::SUCCESS
            && $order['delivery_type']['value'] == DeliveryTypeEnum::EXTRACT
            && $order['delivery_status']['value'] == 10
            // 拼团订单验证拼单状态
            && ($order['order_type']['value'] == 20 ? $order['active']['status']['value'] == 20 : true)
        ) {
            return true;
        }
        $this->setError('该订单不能被核销');
        return false;
    }

    /**
     * 设置错误信息
     * @param $error
     */
    private function setError($error)
    {
        empty($this->error) && $this->error = $error;
    }

    /**
     * 是否存在错误
     * @return bool
     */
    public function hasError()
    {
        return !empty($this->error);
    }

}
