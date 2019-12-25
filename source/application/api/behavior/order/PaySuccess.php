<?php

namespace app\api\behavior\order;

use app\common\service\Message as MessageService;
use app\common\service\order\Printer as PrinterService;
use app\common\service\wechat\wow\Order as WowOrder;
use app\common\enum\OrderType as OrderTypeEnum;
use app\common\enum\OrderStatus as OrderStatusEnum;
use app\common\enum\order\OrderSource as OrderSourceEnum;

/**
 * 订单支付成功后扩展类
 * Class PaySuccess
 * @package app\api\behavior\order
 */
class PaySuccess
{
    // 订单信息
    private $order;

    // 订单类型
    private $orderType;

    private $wxappId;

    /**
     * 订单来源回调业务映射类
     * @var array
     */
    protected $sourceCallbackClass = [
        OrderSourceEnum::MASTER => 'app\api\service\master\order\PaySuccess',
        OrderSourceEnum::BARGAIN => 'app\api\service\bargain\order\PaySuccess',
        OrderSourceEnum::SHARP => 'app\api\service\sharp\order\PaySuccess',
    ];

    /**
     * 执行入口
     * @param $order
     * @param int $orderType
     * @return bool
     * @throws \app\common\exception\BaseException
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function run($order, $orderType = OrderTypeEnum::MASTER)
    {
        // 设置当前类的属性
        $this->setAttribute($order, $orderType);
        // 订单公共业务
        $this->onCommonEvent();
        // 普通订单业务
        if ($orderType == OrderTypeEnum::MASTER) {
            $this->onMasterEvent();
        }
        // 订单来源回调业务
        $this->onSourceCallback();
        return true;
    }

    /**
     * 设置当前类的属性
     * @param $order
     * @param int $orderType
     */
    private function setAttribute($order, $orderType = OrderTypeEnum::MASTER)
    {
        $this->order = $order;
        $this->wxappId = $this->order['wxapp_id'];
        $this->orderType = $orderType;
    }

    /**
     * 订单公共业务
     * @throws \app\common\exception\BaseException
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    private function onCommonEvent()
    {
        // 发送消息通知
        (new MessageService)->payment($this->order, $this->orderType);
        // 小票打印
        (new PrinterService)->printTicket($this->order, OrderStatusEnum::ORDER_PAYMENT);
    }

    /**
     * 普通订单业务
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    private function onMasterEvent()
    {
        // 同步好物圈
        (new WowOrder($this->wxappId))->import([$this->order], true);
    }

    /**
     * 订单来源回调业务
     * @return bool
     */
    private function onSourceCallback()
    {
        if (!isset($this->order['order_source'])) {
            return false;
        }
        if (!isset($this->sourceCallbackClass[$this->order['order_source']])) {
            return false;
        }
        $class = $this->sourceCallbackClass[$this->order['order_source']];
        return !is_null($class) ? (new $class)->onPaySuccess($this->order) : false;
    }

}