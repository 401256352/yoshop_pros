<?php

namespace app\api\service\sharing\order;

use think\Hook;
use app\api\service\Basics;
use app\api\model\User as UserModel;
use app\api\model\sharing\Order as OrderModel;
use app\api\model\sharing\Goods as GoodsModel;
use app\api\model\sharing\Active as ActiveModel;
use app\api\model\user\BalanceLog as BalanceLogModel;
use app\api\model\WxappPrepayId as WxappPrepayIdModel;

use app\common\enum\user\balanceLog\Scene as SceneEnum;
use app\common\enum\order\PayType as PayTypeEnum;
use app\common\enum\OrderType as OrderTypeEnum;

/**
 * 订单支付成功服务类
 * Class PaySuccess
 * @package app\api\service\sharing\order
 */
class PaySuccess extends Basics
{
    // 订单模型
    public $model;

    // 当前用户信息
    private $user;

    /**
     * 构造函数
     * PaySuccess constructor.
     * @param $orderNo
     * @throws \think\exception\DbException
     */
    public function __construct($orderNo)
    {
        // 实例化订单模型
        $this->model = OrderModel::getPayDetail($orderNo);
        if (!empty($this->model)) {
            $this->wxappId = $this->model['wxapp_id'];
        }
        // 获取用户信息
        $this->user = UserModel::detail($this->model['user_id']);
    }

    /**
     * 获取订单详情
     * @return OrderModel|null
     */
    public function getOrderInfo()
    {
        return $this->model;
    }

    /**
     * 订单支付成功业务处理
     * @param $payType
     * @param array $payData
     * @return bool
     */
    public function onPaySuccess($payType, $payData = [])
    {
        if (empty($this->model)) {
            $this->error = '未找到该订单信息';
            return false;
        }
        // 更新付款状态
        $status = $this->updatePayStatus($payType, $payData);
        // 订单支付成功行为
        if ($status == true) {
            Hook::listen('order_pay_success', $this->model, OrderTypeEnum::SHARING);
        }
        return $status;
    }

    /**
     * 更新付款状态
     * @param $payType
     * @param $payData
     * @return bool
     */
    private function updatePayStatus($payType, $payData)
    {
        // 验证余额支付时用户余额是否满足
        if ($payType == PayTypeEnum::BALANCE) {
            if ($this->user['balance'] < $this->model['pay_price']) {
                $this->error = '用户余额不足，无法使用余额支付';
                return false;
            }
        }
        $this->model->transaction(function () use ($payType, $payData) {
            // 更新商品库存、销量
            (new GoodsModel)->updateStockSales($this->model['goods']);
            // 更新拼单记录
            $this->saveSharingActive($this->model['goods'][0]);
            // 整理订单信息
            $order = ['pay_type' => $payType, 'pay_status' => 20, 'pay_time' => time()];
            if ($payType == PayTypeEnum::WECHAT) {
                $order['transaction_id'] = $payData['transaction_id'];
            }
            // 更新订单状态
            $this->model->save($order);
            // 累积用户总消费金额
            $this->user->setIncPayMoney($this->model['pay_price']);
            // 余额支付
            if ($payType == PayTypeEnum::BALANCE) {
                // 更新用户余额
                $this->user->setDec('balance', $this->model['pay_price']);
                BalanceLogModel::add(SceneEnum::CONSUME, [
                    'user_id' => $this->user['user_id'],
                    'money' => -$this->model['pay_price'],
                ], ['order_no' => $this->model['order_no']]);
            }
            // 微信支付
            if ($payType == PayTypeEnum::WECHAT) {
                // 更新prepay_id记录
                WxappPrepayIdModel::updatePayStatus($this->model['order_id'], OrderTypeEnum::SHARING);
            }
        });
        return true;
    }

    /**
     * 更新拼单记录
     * @param $goods
     * @return bool
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    private function saveSharingActive($goods)
    {
        // 新增/更新拼单记录
        if ($this->model['order_type']['value'] != 20) {
            return false;
        }
        // 拼单模型
        $ActiveModel = new ActiveModel;
        // 参与他人的拼单, 更新拼单记录
        if ($this->model['active_id'] > 0) {
            $ActiveModel = $ActiveModel::detail($this->model['active_id']);
            return $ActiveModel->onUpdate($this->model['user_id'], $this->model['order_id']);
        }
        // 自己发起的拼单, 新增拼单记录
        $ActiveModel->onCreate($this->model['user_id'], $this->model['order_id'], $goods);
        // 记录拼单id
        $this->model['active_id'] = $ActiveModel['active_id'];
        return true;
    }

}