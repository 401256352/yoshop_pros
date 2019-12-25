<?php

namespace app\api\service\sharp\order;

use app\api\service\Basics;
use app\api\model\sharp\ActiveGoods as ActiveGoodsModel;

/**
 * 砍价订单支付成功后的回调
 * Class PaySuccess
 * @package app\api\service\sharp\order
 */
class PaySuccess extends Basics
{
    /**
     * 回调方法
     * @param $order
     * @return bool
     */
    public function onPaySuccess($order)
    {
        // 更新活动会场的商品实际销量
        $activeTimeId = $order['order_source_id'];
        return $this->updateActiveGoodsAales($activeTimeId, $order['goods']);
    }

    /**
     * 更新活动会场的商品实际销量
     * @param $activeTimeId
     * @param $goodsList
     * @return bool
     */
    private function updateActiveGoodsAales($activeTimeId, $goodsList)
    {
        $data = [];
        foreach ($goodsList as $goods) {
            $data[] = [
                'data' => ['sales_actual' => ['inc', $goods['total_num']]],
                'where' => [
                    'active_time_id' => $activeTimeId,
                    'sharp_goods_id' => $goods['goods_source_id'],
                ],
            ];
        }
        return !empty($data) && (new ActiveGoodsModel)->updateAll($data);
    }
}