<?php

namespace app\api\service\order\source;

use app\api\model\sharp\Goods as SharpGoodsModel;
use app\common\library\helper;

/**
 * 订单来源-秒杀订单扩展类
 * Class Sharp
 * @package app\api\service\order\source
 */
class Sharp extends Basics
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
            // 秒杀商品信息
            $sharpGoods = SharpGoodsModel::detail($goods['goods_source_id'], ['sku']);
            // 判断商品是否下架
            if (empty($sharpGoods) || $sharpGoods['is_delete'] || !$sharpGoods['status']) {
                $this->error = "很抱歉，商品 [{$goods['goods_name']}] 不存在或已下架";
                return false;
            }
            // 获取秒杀商品的sku信息
            $goodsSku = $this->getOrderGoodsSku($sharpGoods, $goods['spec_sku_id']);
            if (empty($goodsSku)) {
                $this->error = "很抱歉，商品 [{$goods['goods_name']}] sku已不存在，请重新下单";
                return false;
            }
            // 付款减库存
            if ($goods['deduct_stock_type'] == 20 && $goods['total_num'] > $goodsSku['seckill_stock']) {
                $this->error = "很抱歉，商品 [{$goods['goods_name']}] 库存不足";
                return false;
            }
        }
        return true;
    }

    /**
     * 获取指定的商品sku信息
     * @param $sharpGoods
     * @param $specSkuId
     * @return bool
     */
    private function getOrderGoodsSku($sharpGoods, $specSkuId)
    {
        return helper::getArrayItemByColumn($sharpGoods['sku'], 'spec_sku_id', $specSkuId);
    }

}