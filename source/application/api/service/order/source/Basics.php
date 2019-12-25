<?php

namespace app\api\service\order\source;

use app\common\enum\order\Status as OrderStatusEnum;
use app\common\enum\order\PayStatus as OrderPayStatusEnum;

abstract class Basics extends \app\api\service\Basics
{
    /**
     * 判断订单是否允许付款
     * @param $order
     * @return mixed
     */
    abstract public function checkOrderStatusOnPay($order);

    /**
     * 判断商品状态、库存 (未付款订单)
     * @param $goodsList
     * @return mixed
     */
    abstract protected function checkGoodsStatusOnPay($goodsList);

    /**
     * 判断订单状态(公共)
     * @param $order
     * @return bool
     */
    protected function checkOrderStatusOnPayCommon($order)
    {

        // 判断订单状态
        if (
            $order['order_status']['value'] != OrderStatusEnum::NORMAL
            || $order['pay_status']['value'] != OrderPayStatusEnum::PENDING
        ) {
            $this->error = '很抱歉，当前订单不合法，无法支付';
            return false;
        }
        return true;
    }

}