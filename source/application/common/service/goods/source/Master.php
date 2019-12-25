<?php

namespace app\common\service\goods\source;

use app\common\model\Goods as GoodsModel;
use app\common\model\GoodsSku as GoodsSkuModel;
use app\common\enum\goods\DeductStockType as DeductStockTypeEnum;

/**
 * 商品来源-普通商品扩展类
 * Class Master
 * @package app\common\service\stock
 */
class Master extends Basics
{
    /**
     * 更新商品库存 (针对下单减库存的商品)
     * @param $goodsList
     * @return bool
     */
    public function updateGoodsStock($goodsList)
    {
        $data = [];
        foreach ($goodsList as $goods) {
            // 下单减库存
            if ($goods['deduct_stock_type'] == 10) {
                $data[] = [
                    'data' => ['stock_num' => ['dec', $goods['total_num']]],
                    'where' => [
                        'goods_id' => $goods['goods_id'],
                        'spec_sku_id' => $goods['spec_sku_id'],
                    ],
                ];
            }
        }
        return !empty($data) && $this->updateGoodsSku($data);
    }

    /**
     * 更新商品库存销量（订单付款后）
     * @param $goodsList
     * @return bool
     * @throws \Exception
     */
    public function updateStockSales($goodsList)
    {
        $goodsData = [];
        $goodsSkuData = [];
        foreach ($goodsList as $goods) {
            // 记录商品的销量
            $goodsData[] = [
                'goods_id' => $goods['goods_id'],
                'sales_actual' => ['inc', $goods['total_num']]
            ];
            // 付款减库存
            if ($goods['deduct_stock_type'] == 20) {
                $goodsSkuData[] = [
                    'data' => ['stock_num' => ['dec', $goods['total_num']]],
                    'where' => [
                        'goods_id' => $goods['goods_id'],
                        'spec_sku_id' => $goods['spec_sku_id'],
                    ],
                ];
            }
        }
        // 更新商品销量
        !empty($goodsData) && $this->updateGoods($goodsData);
        // 更新商品sku库存
        !empty($goodsSkuData) && $this->updateGoodsSku($goodsSkuData);
        return true;
    }

    /**
     * 回退商品库存
     * @param $goodsList
     * @param $isPayOrder
     * @return array|false
     * @throws \Exception
     */
    public function backGoodsStock($goodsList, $isPayOrder = false)
    {
        $goodsSkuData = [];
        foreach ($goodsList as $goods) {
            $item = [
                'where' => [
                    'goods_id' => $goods['goods_id'],
                    'spec_sku_id' => $goods['spec_sku_id'],
                ],
                'data' => ['stock_num' => ['inc', $goods['total_num']]],
            ];
            if ($isPayOrder == true) {
                // 付款订单全部库存
                $goodsSkuData[] = $item;
            } else {
                // 未付款订单，判断必须为下单减库存时才回退
                $goods['deduct_stock_type'] == DeductStockTypeEnum::CREATE && $goodsSkuData[] = $item;
            }
        }
        // 更新商品sku库存
        return !empty($goodsSkuData) && $this->updateGoodsSku($goodsSkuData);
    }

    /**
     * 更新商品信息
     * @param $data
     * @return array|false
     * @throws \Exception
     */
    private function updateGoods($data)
    {
        return (new GoodsModel)->allowField(true)->isUpdate()->saveAll($data);
    }

    /**
     * 更新商品sku信息
     * @param $data
     * @return \think\Collection
     */
    private function updateGoodsSku($data)
    {
        return (new GoodsSkuModel)->updateAll($data);
    }
}