<?php

namespace app\common\model;

/**
 * 商品SKU模型
 * Class GoodsSku
 * @package app\common\model
 */
class GoodsSku extends BaseModel
{
    protected $name = 'goods_sku';

    /**
     * 规格图片
     * @return \think\model\relation\HasOne
     */
    public function image()
    {
        return $this->hasOne('uploadFile', 'file_id', 'image_id');
    }

    /**
     * 获取sku信息详情
     * @param $goodsId
     * @param $specSkuId
     * @return GoodsSku|null
     * @throws \think\exception\DbException
     */
    public static function detail($goodsId, $specSkuId)
    {
        return static::get(['goods_id' => $goodsId, 'spec_sku_id' => $specSkuId]);
    }

}
