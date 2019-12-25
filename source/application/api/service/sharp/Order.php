<?php

namespace app\api\service\sharp;

use app\api\service\Basics;
use app\api\model\Order as OrderModel;
use app\common\enum\order\Status as OrderStatusEnum;
use app\common\enum\order\OrderSource as OrderSourceEnum;

/**
 * 整点秒杀订单服务类
 * Class Order
 * @package app\api\service\sharp
 */
class Order extends Basics
{
    /**
     * 获取某商品的购买件数
     * @param $userId
     * @param $goodsId
     * @return float|int
     */
    public static function getAlreadyBuyNum($userId, $goodsId)
    {
        $model = new OrderModel;
        $totalNum = $model
            ->setBaseQuery('order', [
                ['order_goods', 'order_id'],
            ])
            ->where('order_goods.user_id', '=', $userId)
            ->where('order_goods.goods_id', '=', $goodsId)
            ->where('order.order_source', '=', OrderSourceEnum::SHARP)
            ->where('order.order_status', '<>', OrderStatusEnum::CANCELLED)
            ->where('order.is_delete', '=', 0)
            ->sum('order_goods.total_num');
        return $totalNum;
    }
}