<?php

namespace app\api\service\order;

use app\api\model\Order as OrderModel;

use app\api\model\User as UserModel;
use app\api\model\Goods as GoodsModel;
use app\api\model\Setting as SettingModel;
use app\api\model\store\Shop as ShopModel;
use app\api\model\UserCoupon as UserCouponModel;
use app\api\model\dealer\Order as DealerOrderModel;

use app\api\service\User as UserService;
use app\api\service\Payment as PaymentService;
use app\api\service\coupon\GoodsDeduct as GoodsDeductService;
use app\api\service\points\GoodsDeduct as PointsDeductService;
use app\api\service\order\source\checkout\Factory as CheckoutFactory;

use app\common\library\helper;
use app\common\enum\OrderType as OrderTypeEnum;
use app\common\enum\DeliveryType as DeliveryTypeEnum;
use app\common\enum\order\PayType as PayTypeEnum;
use app\common\service\goods\source\Factory as StockFactory;
use app\common\enum\order\OrderSource as OrderSourceEnum;
use app\common\service\delivery\Express as ExpressService;
use app\common\exception\BaseException;

/**
 * 订单结算台服务类
 * Class Checkout
 * @package app\api\service\order
 */
class Checkout
{
    /* $model OrderModel 订单模型 */
    public $model;

    // 当前小程序id
    private $wxapp_id;

    /* @var UserModel $user 当前用户信息 */
    private $user;

    // 订单结算商品列表
    private $goodsList = [];

    // 错误信息
    protected $error;

    /**
     * 订单结算api参数
     * @var array
     */
    private $param = [
        'delivery' => null, // 配送方式
        'shop_id' => 0,     // 自提门店id
        'linkman' => '',    // 自提联系人
        'phone' => '',    // 自提联系电话
        'coupon_id' => 0,    // 优惠券id
        'is_use_points' => 0,    // 是否使用积分抵扣
        'remark' => '',    // 买家留言
        'pay_type' => PayTypeEnum::WECHAT,  // 支付方式
    ];

    /**
     * 订单结算的规则
     * @var array
     */
    private $checkoutRule = [
        'is_user_grade' => true,    // 会员等级折扣
        'is_coupon' => true,        // 优惠券抵扣
        'is_use_points' => true,        // 是否使用积分抵扣
        'is_dealer' => true,        // 是否开启分销
    ];

    /**
     * 订单来源
     * @var array
     */
    private $orderSource = [
        'source' => OrderSourceEnum::MASTER,
        'source_id' => 0,
    ];

    /**
     * 订单结算数据
     * @var array
     */
    private $orderData = [];

    /**
     * 构造函数
     * Checkout constructor.
     */
    public function __construct()
    {
        $this->model = new OrderModel;
        $this->wxapp_id = OrderModel::$wxapp_id;
    }

    /**
     * 设置结算台请求的参数
     * @param $param
     * @return array
     */
    public function setParam($param)
    {
        $this->param = array_merge($this->param, $param);
        return $this->getParam();
    }

    /**
     * 获取结算台请求的参数
     * @return array
     */
    public function getParam()
    {
        return $this->param;
    }

    /**
     * 订单结算的规则
     * @param $data
     * @return $this
     */
    public function setCheckoutRule($data)
    {
        $this->checkoutRule = array_merge($this->checkoutRule, $data);
        return $this;
    }

    /**
     * 设置订单来源(普通订单、砍价订单、秒杀订单)
     * @param $data
     * @return $this
     */
    public function setOrderSource($data)
    {
        $this->orderSource = array_merge($this->orderSource, $data);
        return $this;
    }

    /**
     * 订单确认-结算台
     * @param $user
     * @param $goodsList
     * @return array
     * @throws BaseException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function onCheckout($user, $goodsList)
    {
        $this->user = $user;
        $this->goodsList = $goodsList;
        // 订单确认-立即购买
        return $this->checkout();
    }

    /**
     * 订单结算台
     * @return array
     * @throws BaseException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function checkout()
    {
        // 整理订单数据
        $this->orderData = $this->getOrderData();
        // 验证商品状态, 是否允许购买
        $this->validateGoodsList();
        // 订单商品总数量
        $orderTotalNum = helper::getArrayColumnSum($this->goodsList, 'total_num');
        // 设置订单商品会员折扣价
        $this->setOrderGoodsGradeMoney();
        // 设置订单商品总金额(不含优惠折扣)
        $this->setOrderTotalPrice();
        // 计算可用积分抵扣
        $this->setOrderPoints();
        // 当前用户可用的优惠券列表
        $couponList = $this->getUserCouponList($this->orderData['order_total_price']);
        // 计算优惠券抵扣
        $this->setOrderCouponMoney($couponList, $this->param['coupon_id']);
        // 计算订单商品的实际付款金额
        $this->setOrderGoodsPayPrice();
        // 设置默认配送方式
        !$this->param['delivery'] && $this->param['delivery'] = current(SettingModel::getItem('store')['delivery_type']);
        // 处理配送方式
        if ($this->param['delivery'] == DeliveryTypeEnum::EXPRESS) {
            $this->setOrderExpress();
        } elseif ($this->param['delivery'] == DeliveryTypeEnum::EXTRACT) {
            $this->param['shop_id'] > 0 && $this->orderData['extract_shop'] = ShopModel::detail($this->param['shop_id']);
        }
        // 计算订单最终金额
        $this->setOrderPayPrice();
        // 计算订单积分赠送数量
        $this->setOrderPointsBonus();
        // 返回订单数据
        return array_merge([
            'goods_list' => array_values($this->goodsList),   // 商品信息
            'order_total_num' => $orderTotalNum,        // 商品总数量
            'coupon_list' => array_values($couponList), // 优惠券列表
            'has_error' => $this->hasError(),
            'error_msg' => $this->getError(),
        ], $this->orderData);
    }

    /**
     * 计算订单可用积分抵扣
     * @return bool
     */
    private function setOrderPoints()
    {
        // 设置默认的商品积分抵扣信息
        $this->setDefaultGoodsPoints();
        // 积分设置
        $setting = SettingModel::getItem('points');
        // 条件：后台开启下单使用积分抵扣
        if (!$setting['is_shopping_discount'] || !$this->checkoutRule['is_use_points']) {
            return false;
        }
        // 条件：订单金额满足[?]元
        if (helper::bccomp($setting['discount']['full_order_price'], $this->orderData['order_total_price']) === 1) {
            return false;
        }
        // 计算订单商品最多可抵扣的积分数量
        $this->setOrderGoodsMaxPointsNum();
        // 订单最多可抵扣的积分总数量
        $maxPointsNumCount = helper::getArrayColumnSum($this->goodsList, 'max_points_num');
        // 实际可抵扣的积分数量
        $actualPointsNum = min($maxPointsNumCount, $this->user['points']);
        if ($actualPointsNum < 1) {
            return false;
        }
        // 计算订单商品实际抵扣的积分数量和金额
        $GoodsDeduct = new PointsDeductService($this->goodsList);
        $GoodsDeduct->setGoodsPoints($maxPointsNumCount, $actualPointsNum);
        // 积分抵扣总金额
        $orderPointsMoney = helper::getArrayColumnSum($this->goodsList, 'points_money');
        $this->orderData['points_money'] = helper::number2($orderPointsMoney);
        // 积分抵扣总数量
        $this->orderData['points_num'] = $actualPointsNum;
        // 允许积分抵扣
        $this->orderData['is_allow_points'] = true;
        return true;
    }

    /**
     * 计算订单商品最多可抵扣的积分数量
     * @return bool
     */
    private function setOrderGoodsMaxPointsNum()
    {
        // 积分设置
        $setting = SettingModel::getItem('points');
        foreach ($this->goodsList as &$goods) {
            // 商品不允许积分抵扣
            if (!$goods['is_points_discount']) continue;
            // 积分抵扣比例
            $deductionRatio = helper::bcdiv($setting['discount']['max_money_ratio'], 100);
            // 最多可抵扣的金额
            $maxPointsMoney = helper::bcmul($goods['total_price'], $deductionRatio);
            // 最多可抵扣的积分数量
            $goods['max_points_num'] = helper::bcdiv($maxPointsMoney, $setting['discount']['discount_ratio'], 0);
        }
        return true;
    }

    /**
     * 设置默认的商品积分抵扣信息
     * @return bool
     */
    private function setDefaultGoodsPoints()
    {
        foreach ($this->goodsList as &$goods) {
            // 最多可抵扣的积分数量
            $goods['max_points_num'] = 0;
            // 实际抵扣的积分数量
            $goods['points_num'] = 0;
            // 实际抵扣的金额
            $goods['points_money'] = 0.00;
        }
        return true;
    }

    /**
     * 整理订单数据(结算台初始化)
     * @return array
     */
    private function getOrderData()
    {
        // 系统支持的配送方式 (后台设置)
        $deliveryType = SettingModel::getItem('store')['delivery_type'];
        // 积分设置
        $pointsSetting = SettingModel::getItem('points');
        return [
            // 配送类型
            'delivery' => $this->param['delivery'] > 0 ? $this->param['delivery'] : $deliveryType[0],
            // 默认地址
            'address' => $this->user['address_default'],
            // 是否存在收货地址
            'exist_address' => $this->user['address_id'] > 0,
            // 配送费用
            'express_price' => 0.00,
            // 当前用户收货城市是否存在配送规则中
            'intra_region' => true,
            // 自提门店信息
            'extract_shop' => [],
            // 是否允许使用积分抵扣
            'is_allow_points' => false,
            // 是否使用积分抵扣
            'is_use_points' => $this->param['is_use_points'],
            // 积分抵扣金额
            'points_money' => 0.00,
            // 赠送的积分数量
            'points_bonus' => 0,
            // 支付方式
            'pay_type' => $this->param['pay_type'],
            // 系统设置
            'setting' => [
                'delivery' => $deliveryType,     // 支持的配送方式
                'points_name' => $pointsSetting['points_name'],      // 积分名称
                'points_describe' => $pointsSetting['describe'],     // 积分说明
            ],
            // 记忆的自提联系方式
            'last_extract' => UserService::getLastExtract($this->user['user_id']),
            // todo: delete - 兼容处理
            'deliverySetting' => $deliveryType,
        ];
    }

    /**
     * 当前用户可用的优惠券列表
     * @param $orderTotalPrice
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function getUserCouponList($orderTotalPrice)
    {
        // 是否开启优惠券折扣
        if (!$this->checkoutRule['is_coupon']) {
            return [];
        }
        return UserCouponModel::getUserCouponList($this->user['user_id'], $orderTotalPrice);
    }

    /**
     * 验证订单商品的状态
     * @return bool
     */
    private function validateGoodsList()
    {
        $Checkout = CheckoutFactory::getFactory(
            $this->user,
            $this->goodsList,
            $this->orderSource['source']
        );
        $status = $Checkout->validateGoodsList();
        $status == false && $this->setError($Checkout->getError());
        return $status;
    }

    /**
     * 设置订单的商品总金额(不含优惠折扣)
     */
    private function setOrderTotalPrice()
    {
        // 订单商品的总金额(不含优惠券折扣)
        $this->orderData['order_total_price'] = helper::number2(helper::getArrayColumnSum($this->goodsList, 'total_price'));
    }

    /**
     * 设置订单的实际支付金额(含配送费)
     */
    private function setOrderPayPrice()
    {
        // 订单金额(含优惠折扣)
        $this->orderData['order_price'] = helper::number2(helper::getArrayColumnSum($this->goodsList, 'total_pay_price'));
        // 订单实付款金额(订单金额 + 运费)
        $this->orderData['order_pay_price'] = helper::number2(helper::bcadd($this->orderData['order_price'], $this->orderData['express_price']));
    }

    /**
     * 计算订单积分赠送数量
     * @return bool
     */
    private function setOrderPointsBonus()
    {
        // 初始化商品积分赠送数量
        foreach ($this->goodsList as &$goods) {
            $goods['points_bonus'] = 0;
        }
        // 积分设置
        $setting = SettingModel::getItem('points');
        // 条件：后台开启开启购物送积分
        if (!$setting['is_shopping_gift']) {
            return false;
        }
        // 设置商品积分赠送数量
        foreach ($this->goodsList as &$goods) {
            // 积分赠送比例
            $ratio = $setting['gift_ratio'] / 100;
            // 计算抵扣积分数量
            $goods['points_bonus'] = !$goods['is_points_gift'] ? 0 : helper::bcmul($goods['total_pay_price'], $ratio, 0);
        }
        //  订单积分赠送数量
        $this->orderData['points_bonus'] = helper::getArrayColumnSum($this->goodsList, 'points_bonus');
        return true;
    }

    /**
     * 计算订单商品的实际付款金额
     * @return bool
     */
    private function setOrderGoodsPayPrice()
    {
        // 商品总价 - 优惠抵扣
        foreach ($this->goodsList as &$goods) {
            // 减去优惠券抵扣金额
            $value = helper::bcsub($goods['total_price'], $goods['coupon_money']);
            // 减去积分抵扣金额
            if ($this->orderData['is_allow_points'] && $this->orderData['is_use_points']) {
                $value = helper::bcsub($value, $goods['points_money']);
            }
            $goods['total_pay_price'] = helper::number2($value);
        }
        return true;
    }

    /**
     * 设置订单商品会员折扣价
     * @return bool
     */
    private function setOrderGoodsGradeMoney()
    {
        // 设置默认数据
        helper::setDataAttribute($this->goodsList, [
            // 标记参与会员折扣
            'is_user_grade' => false,
            // 会员等级抵扣的金额
            'grade_ratio' => 0,
            // 会员折扣的商品单价
            'grade_goods_price' => 0.00,
            // 会员折扣的总额差
            'grade_total_money' => 0.00,
        ], true);

        // 是否开启会员等级折扣
        if (!$this->checkoutRule['is_user_grade']) {
            return false;
        }
        // 会员等级状态
        if (!(
            $this->user['grade_id'] > 0 && !empty($this->user['grade'])
            && !$this->user['grade']['is_delete'] && $this->user['grade']['status']
        )) {
            return false;
        }
        // 计算抵扣金额
        foreach ($this->goodsList as &$goods) {
            // 判断商品是否参与会员折扣
            if (!$goods['is_enable_grade']) {
                continue;
            }
            // 商品单独设置了会员折扣
            if ($goods['is_alone_grade'] && isset($goods['alone_grade_equity'][$this->user['grade_id']])) {
                // 折扣比例
                $discountRatio = helper::bcdiv($goods['alone_grade_equity'][$this->user['grade_id']], 10);
            } else {
                // 折扣比例
                $discountRatio = helper::bcdiv($this->user['grade']['equity']['discount'], 10);
            }
            if ($discountRatio > 0) {
                // 会员折扣后的商品总金额
                $gradeTotalPrice = max(0.01, helper::bcmul($goods['total_price'], $discountRatio));
                helper::setDataAttribute($goods, [
                    'is_user_grade' => true,
                    'grade_ratio' => $discountRatio,
                    'grade_goods_price' => helper::number2(helper::bcmul($goods['goods_price'], $discountRatio), true),
                    'grade_total_money' => helper::number2(helper::bcsub($goods['total_price'], $gradeTotalPrice)),
                    'total_price' => $gradeTotalPrice,
                ], false);
            }
        }
        return true;
    }

    /**
     * 设置订单优惠券抵扣信息
     * @param array $couponList 当前用户可用的优惠券列表
     * @param int $couponId 当前选择的优惠券id
     * @return bool
     * @throws BaseException
     */
    private function setOrderCouponMoney($couponList, $couponId)
    {
        // 设置默认数据：订单信息
        helper::setDataAttribute($this->orderData, [
            'coupon_id' => 0,       // 用户优惠券id
            'coupon_money' => 0,    // 优惠券抵扣金额
        ], false);
        // 设置默认数据：订单商品列表
        helper::setDataAttribute($this->goodsList, [
            'coupon_money' => 0,    // 优惠券抵扣金额
        ], true);
        // 是否开启优惠券折扣
        if (!$this->checkoutRule['is_coupon']) {
            return false;
        }
        // 如果没有可用的优惠券，直接返回
        if ($couponId <= 0 || empty($couponList)) {
            return true;
        }
        // 获取优惠券信息
        $couponInfo = helper::getArrayItemByColumn($couponList, 'user_coupon_id', $couponId);
        if ($couponInfo == false) {
            throw new BaseException(['msg' => '未找到优惠券信息']);
        }
        // 计算订单商品优惠券抵扣金额
        $goodsListTemp = helper::getArrayColumns($this->goodsList, ['total_price']);
        $CouponMoney = new GoodsDeductService;
        $completed = $CouponMoney->setGoodsCouponMoney($goodsListTemp, $couponInfo['reduced_price']);
        // 分配订单商品优惠券抵扣金额
        foreach ($this->goodsList as $key => &$goods) {
            $goods['coupon_money'] = $completed[$key]['coupon_money'] / 100;
        }
        // 记录订单优惠券信息
        $this->orderData['coupon_id'] = $couponId;
        $this->orderData['coupon_money'] = helper::number2($CouponMoney->getActualReducedMoney() / 100);
        return true;
    }

    /**
     * 订单配送-快递配送
     * @return bool
     */
    private function setOrderExpress()
    {
        // 设置默认数据：配送费用
        helper::setDataAttribute($this->goodsList, [
            'express_price' => 0,
        ], true);
        // 当前用户收货城市id
        $cityId = $this->user['address_default'] ? $this->user['address_default']['city_id'] : null;
        // 初始化配送服务类
        $ExpressService = new ExpressService(
            $this->wxapp_id,
            $cityId,
            $this->goodsList,
            OrderTypeEnum::MASTER
        );
        // 获取不支持当前城市配送的商品
        $notInRuleGoods = $ExpressService->getNotInRuleGoods();
        // 验证商品是否在配送范围
        $intraRegion = $this->orderData['intra_region'] = $notInRuleGoods === false;
        if ($intraRegion == false) {
            $notInRuleGoodsName = $notInRuleGoods['goods_name'];
            $this->setError("很抱歉，您的收货地址不在商品 [{$notInRuleGoodsName}] 的配送范围内");
        } else {
            // 计算配送金额
            $ExpressService->setExpressPrice();
        }
        // 订单总运费金额
        $this->orderData['express_price'] = helper::number2($ExpressService->getTotalFreight());
        return true;
    }

    /**
     * 创建新订单
     * @param array $order 订单信息
     * @return bool
     * @throws \Exception
     */
    public function createOrder($order)
    {
        // 表单验证
        if (!$this->validateOrderForm($order, $this->param['linkman'], $this->param['phone'])) {
            return false;
        }
        // 创建新的订单
        $status = $this->model->transaction(function () use ($order) {
            // 创建订单事件
            return $this->createOrderEvent($order);
        });
        // 余额支付标记订单已支付
        if ($status && $order['pay_type'] == PayTypeEnum::BALANCE) {
            return $this->model->onPaymentByBalance($this->model['order_no']);
        }
        return $status;
    }

    /**
     * 创建订单事件
     * @param $order
     * @return bool
     * @throws BaseException
     * @throws \think\exception\DbException
     * @throws \Exception
     */
    private function createOrderEvent($order)
    {
        // 新增订单记录
        $status = $this->add($order, $this->param['remark']);
        if ($order['delivery'] == DeliveryTypeEnum::EXPRESS) {
            // 记录收货地址
            $this->saveOrderAddress($order['address']);
        } elseif ($order['delivery'] == DeliveryTypeEnum::EXTRACT) {
            // 记录自提信息
            $this->saveOrderExtract($this->param['linkman'], $this->param['phone']);
        }
        // 保存订单商品信息
        $this->saveOrderGoods($order);
        // 更新商品库存 (针对下单减库存的商品)
        $this->updateGoodsStockNum($order);
        // 设置优惠券使用状态
        UserCouponModel::setIsUse($this->param['coupon_id']);
        // 积分抵扣情况下扣除用户积分
        if ($order['is_allow_points'] && $order['is_use_points'] && $order['points_num'] > 0) {
            $describe = "用户消费：{$this->model['order_no']}";
            $this->user->setIncPoints(-$order['points_num'], $describe);
        }
        // 获取订单详情
        $detail = OrderModel::getUserOrderDetail($this->model['order_id'], $this->user['user_id']);
        // 记录分销商订单
        $this->checkoutRule['is_dealer'] && DealerOrderModel::createOrder($detail);
        return $status;
    }

    /**
     * 构建支付请求的参数
     * @return array
     * @throws BaseException
     * @throws \think\exception\DbException
     */
    public function onOrderPayment()
    {
        return PaymentService::orderPayment($this->user, $this->model, $this->param['pay_type']);
    }

    /**
     * 表单验证 (订单提交)
     * @param array $order 订单信息
     * @param string $linkman 联系人
     * @param string $phone 联系电话
     * @return bool
     */
    private function validateOrderForm(&$order, $linkman, $phone)
    {
        if ($order['delivery'] == DeliveryTypeEnum::EXPRESS) {
            if (empty($order['address'])) {
                $this->error = '请先选择收货地址';
                return false;
            }
        }
        if ($order['delivery'] == DeliveryTypeEnum::EXTRACT) {
            if (empty($order['extract_shop'])) {
                $this->error = '请先选择自提门店';
                return false;
            }
            if (empty($linkman) || empty($phone)) {
                $this->error = '请填写联系人和电话';
                return false;
            }
        }
        // 余额支付时判断用户余额是否足够
        if ($order['pay_type'] == PayTypeEnum::BALANCE) {
            if ($this->user['balance'] < $order['order_pay_price']) {
                $this->error = '用户余额不足，无法使用余额支付';
                return false;
            }
        }
        return true;
    }

    /**
     * 当前订单是否存在和使用积分抵扣
     * @param $order
     * @return bool
     */
    private function isExistPointsDeduction($order)
    {
        return $order['is_allow_points'] && $order['is_use_points'];
    }

    /**
     * 新增订单记录
     * @param $order
     * @param string $remark
     * @return false|int
     */
    private function add($order, $remark = '')
    {
        // 当前订单是否存在和使用积分抵扣
        $isExistPointsDeduction = $this->isExistPointsDeduction($order);
        // 订单数据
        $data = [
            'user_id' => $this->user['user_id'],
            'order_no' => $this->model->orderNo(),
            'total_price' => $order['order_total_price'],
            'order_price' => $order['order_price'],
            'coupon_id' => $order['coupon_id'],
            'coupon_money' => $order['coupon_money'],
            'points_money' => $isExistPointsDeduction ? $order['points_money'] : 0.00,
            'points_num' => $isExistPointsDeduction ? $order['points_num'] : 0,
            'pay_price' => $order['order_pay_price'],
            'delivery_type' => $order['delivery'],
            'pay_type' => $order['pay_type'],
            'buyer_remark' => trim($remark),
            'order_source' => $this->orderSource['source'],
            'order_source_id' => $this->orderSource['source_id'],
            'points_bonus' => $order['points_bonus'],
            'wxapp_id' => $this->wxapp_id,
        ];
        if ($order['delivery'] == DeliveryTypeEnum::EXPRESS) {
            $data['express_price'] = $order['express_price'];
        } elseif ($order['delivery'] == DeliveryTypeEnum::EXTRACT) {
            $data['extract_shop_id'] = $order['extract_shop']['shop_id'];
        }
        // 保存订单记录
        return $this->model->save($data);
    }

    /**
     * 保存订单商品信息
     * @param $order
     * @return int
     */
    private function saveOrderGoods($order)
    {
        // 当前订单是否存在和使用积分抵扣
        $isExistPointsDeduction = $this->isExistPointsDeduction($order);
        // 订单商品列表
        $goodsList = [];
        foreach ($order['goods_list'] as $goods) {
            /* @var GoodsModel $goods */
            $item = [
                'user_id' => $this->user['user_id'],
                'wxapp_id' => $this->wxapp_id,
                'goods_id' => $goods['goods_id'],
                'goods_name' => $goods['goods_name'],
                'image_id' => $goods['image'][0]['image_id'],
                'deduct_stock_type' => $goods['deduct_stock_type'],
                'spec_type' => $goods['spec_type'],
                'spec_sku_id' => $goods['goods_sku']['spec_sku_id'],
                'goods_sku_id' => $goods['goods_sku']['goods_sku_id'],
                'goods_attr' => $goods['goods_sku']['goods_attr'],
                'content' => $goods['content'],
                'goods_no' => $goods['goods_sku']['goods_no'],
                'goods_price' => $goods['goods_sku']['goods_price'],
                'line_price' => $goods['goods_sku']['line_price'],
                'goods_weight' => $goods['goods_sku']['goods_weight'],
                'is_user_grade' => (int)$goods['is_user_grade'],
                'grade_ratio' => $goods['grade_ratio'],
                'grade_goods_price' => $goods['grade_goods_price'],
                'grade_total_money' => $goods['grade_total_money'],
                'coupon_money' => $goods['coupon_money'],
                'points_money' => $isExistPointsDeduction ? $goods['points_money'] : 0.00,
                'points_num' => $isExistPointsDeduction ? $goods['points_num'] : 0,
                'points_bonus' => $goods['points_bonus'],
                'total_num' => $goods['total_num'],
                'total_price' => $goods['total_price'],
                'total_pay_price' => $goods['total_pay_price'],
                'is_ind_dealer' => $goods['is_ind_dealer'],
                'dealer_money_type' => $goods['dealer_money_type'],
                'first_money' => $goods['first_money'],
                'second_money' => $goods['second_money'],
                'third_money' => $goods['third_money'],
            ];
            // 记录订单商品来源id
            $item['goods_source_id'] = isset($goods['goods_source_id']) ? $goods['goods_source_id'] : 0;
            $goodsList[] = $item;
        }
        return $this->model->goods()->saveAll($goodsList);
    }

    /**
     * 更新商品库存 (针对下单减库存的商品)
     * @param $order
     * @return mixed
     */
    private function updateGoodsStockNum($order)
    {
        return StockFactory::getFactory($this->model['order_source'])->updateGoodsStock($order['goods_list']);
    }

    /**
     * 记录收货地址
     * @param $address
     * @return false|\think\Model
     */
    private function saveOrderAddress($address)
    {
        if ($address['region_id'] == 0 && !empty($address['district'])) {
            $address['detail'] = $address['district'] . ' ' . $address['detail'];
        }
        return $this->model->address()->save([
            'user_id' => $this->user['user_id'],
            'wxapp_id' => $this->wxapp_id,
            'name' => $address['name'],
            'phone' => $address['phone'],
            'province_id' => $address['province_id'],
            'city_id' => $address['city_id'],
            'region_id' => $address['region_id'],
            'detail' => $address['detail'],
        ]);
    }

    /**
     * 保存上门自提联系人
     * @param $linkman
     * @param $phone
     * @return false|\think\Model
     */
    private function saveOrderExtract($linkman, $phone)
    {
        // 记忆上门自提联系人(缓存)，用于下次自动填写
        UserService::setLastExtract($this->model['user_id'], trim($linkman), trim($phone));
        // 保存上门自提联系人(数据库)
        return $this->model->extract()->save([
            'linkman' => trim($linkman),
            'phone' => trim($phone),
            'user_id' => $this->model['user_id'],
            'wxapp_id' => $this->wxapp_id,
        ]);
    }

    /**
     * 设置错误信息
     * @param $error
     */
    protected function setError($error)
    {
        empty($this->error) && $this->error = $error;
    }

    /**
     * 获取错误信息
     * @return mixed
     */
    public function getError()
    {
        return $this->error ?: '';
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