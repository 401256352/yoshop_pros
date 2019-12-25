<?php

namespace app\api\service\order;

use think\Hook;
use app\api\service\Basics;
use app\api\model\User as UserModel;
use app\api\model\Order as OrderModel;
use app\api\model\WxappPrepayId as WxappPrepayIdModel;
use app\api\model\user\BalanceLog as BalanceLogModel;
use app\common\service\goods\source\Factory as StockFactory;
use app\common\enum\OrderType as OrderTypeEnum;
use app\common\enum\order\PayType as PayTypeEnum;
use app\common\enum\user\balanceLog\Scene as SceneEnum;

/**
 * 订单支付成功服务类
 * Class PaySuccess
 * @package app\api\service\order
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
            Hook::listen('order_pay_success', $this->model, OrderTypeEnum::MASTER);
        }
        return $status;
    }

    /**
     * 更新付款状态
     * @param $payType
     * @param array $payData
     * @return bool
     */
    private function updatePayStatus($payType, $payData = [])
    {
        // 验证余额支付时用户余额是否满足
        if ($payType == PayTypeEnum::BALANCE) {
            if ($this->user['balance'] < $this->model['pay_price']) {
                $this->error = '用户余额不足，无法使用余额支付';
                return false;
            }
        }
        // 事务处理
        $this->model->transaction(function () use ($payType, $payData) {
            // 更新订单状态
            $this->updateOrderInfo($payType, $payData);
            // 累积用户总消费金额
            $this->user->setIncPayMoney($this->model['pay_price']);
            // 记录订单支付信息
            $this->updatePayInfo($payType);
        });
        return true;
    }

    /**
     * 更新订单记录
     * @param $payType
     * @param $payData
     * @return false|int
     * @throws \Exception
     */
    private function updateOrderInfo($payType, $payData)
    {
        // 更新商品库存、销量
        StockFactory::getFactory($this->model['order_source'])->updateStockSales($this->model['goods']);
        // 整理订单信息
        $order = [
            'pay_type' => $payType,
            'pay_status' => 20,
            'pay_time' => time()
        ];
        if ($payType == PayTypeEnum::WECHAT) {
            $order['transaction_id'] = $payData['transaction_id'];
        }
        // 更新订单状态
        return $this->model->save($order);
    }

    /**
     * 记录订单支付信息
     * @param $payType
     * @throws \think\Exception
     */
    private function updatePayInfo($payType)
    {
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
            WxappPrepayIdModel::updatePayStatus($this->model['order_id'], OrderTypeEnum::MASTER);
        }
    }

}