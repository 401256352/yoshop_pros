<?php

namespace app\api\service\order\source;

use app\api\model\GoodsSku as GoodsSkuModel;

/**
 * 订单来源-砍价订单扩展类
 * Class Bargain
 * @package app\api\service\order\source
 */
class Bargain extends Basics
{
    /**
     * 判断订单是否允许付款
     * @param $order
     * @return bool
     * @throws \think\exception\DbException
     */
    public function checkOrderStatusOnPay($order)
    {
        // 判断订单状态
        if (!$this->checkOrderStatusOnPayCommon($order)) {
            return false;
        }
        // 判断商品状态、库存
        if (!$this->checkGoodsStatusOnPay($order['goods'])) {
            return false;
        }
        return true;
    }

    /**
     * 判断商品状态、库存 (未付款订单)
     * @param $goodsList
     * @return bool
     * @throws \think\exception\DbException
     */
    protected function checkGoodsStatusOnPay($goodsList)
    {
        foreach ($goodsList as $goods) {
            // 获取商品的sku信息
            $goodsSku = $this->getOrderGoodsSku($goods['goods_id'], $goods['spec_sku_id']);
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
     * 获取指定的商品sku信息
     * @param $goodsId
     * @param $specSkuId
     * @return \app\common\model\GoodsSku|null
     * @throws \think\exception\DbException
     */
    private function getOrderGoodsSku($goodsId, $specSkuId)
    {
        return GoodsSkuModel::detail($goodsId, $specSkuId);
    }

}