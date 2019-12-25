<?php

namespace app\api\model\sharp;

use app\common\library\helper;
use app\common\model\sharp\ActiveGoods as ActiveGoodsModel;
use app\api\model\Goods as GoodsModel;

/**
 * 整点秒杀-活动会场与商品关联模型
 * Class ActiveGoods
 * @package app\api\model\sharp
 */
class ActiveGoods extends ActiveGoodsModel
{
    /**
     * 隐藏字段
     * @var array
     */
    protected $hidden = [
        'wxapp_id',
        'create_time',
        'update_time',
    ];

    /**
     * 获取指定商品的活动详情
     * @param $activeTimeId
     * @param $sharpGoodsId
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public static function getGoodsActive($activeTimeId, $sharpGoodsId)
    {
        return (new static)->with(['active', 'activeTime'])
            ->where('active_time_id', '=', $activeTimeId)
            ->where('sharp_goods_id', '=', $sharpGoodsId)
            ->find();
    }

    /**
     * 获取活动商品详情
     * @param $active
     * @param $sharpGoodsId
     * @param $isCheckStatus
     * @return GoodsModel|bool|\think\model\Collection
     * @throws \think\exception\DbException
     */
    public function getGoodsActiveDetail($active, $sharpGoodsId, $isCheckStatus = true)
    {
        // 获取商品详情
        $goods = $this->getGoodsDetail($sharpGoodsId);
        if (empty($goods)) return false;
        if ($isCheckStatus == true && ($goods['is_delete'] || !$goods['status'])) {
            $this->error = '很抱歉，秒杀商品不存在或已下架';
            return false;
        }
        // 活动商品的销量
        $goods['sales_actual'] = $active['sales_actual'];
        // 商品销售进度
        $goods['progress'] = $this->getProgress($active['sales_actual'], $goods['seckill_stock']);
        /* @var $goods \think\model\Collection */
        return $goods;
    }

    /**
     * 获取商品详情
     * @param $sharpGoodsId
     * @return GoodsModel|bool
     * @throws \think\exception\DbException
     */
    private function getGoodsDetail($sharpGoodsId)
    {
        // 获取秒杀商品详情
        $model = $this->getGoodsModel();
        $sharpGoods = $model::detail($sharpGoodsId, ['sku']);
        if (empty($sharpGoods)) {
            $this->error = '秒杀商品信息不存在';
            return false;
        }
        // 获取主商品详情
        $goods = GoodsModel::detail($sharpGoods['goods_id']);
        if (empty($goods)) return false;
        // 整理商品信息
        $goods['sharp_goods_id'] = $sharpGoods['sharp_goods_id'];
        $goods['deduct_stock_type'] = $sharpGoods['deduct_stock_type'];
        $goods['limit_num'] = $sharpGoods['limit_num'];
        $goods['seckill_stock'] = $sharpGoods['seckill_stock'];
        $goods['total_sales'] = $sharpGoods['total_sales'];
        $goods['status'] = $sharpGoods['status'];
        $goods['is_delete'] = $sharpGoods['is_delete'];
        // 商品sku信息
        $goods['sku'] = $this->getSharpSku($sharpGoods['sku'], $goods['sku']);
        return $goods->hidden(['category']);
    }

    /**
     * 获取秒杀商品的sku信息
     * @param $sharpSku
     * @param $goodsSku
     * @return array
     */
    protected function getSharpSku($sharpSku, $goodsSku)
    {
        $sharpSku = helper::arrayColumn2Key($sharpSku, 'spec_sku_id');
        foreach ($goodsSku as &$item) {
            $sharpSkuItem = clone $sharpSku[$item['spec_sku_id']];
            $item['original_price'] = $item['goods_price'];
            $item['seckill_price'] = $sharpSkuItem['seckill_price'];
            $item['seckill_stock'] = $sharpSkuItem['seckill_stock'];
        }
        return $goodsSku;
    }

}