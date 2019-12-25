<?php

namespace app\common\model\sharp;

use app\common\model\BaseModel;

/**
 * 整点秒杀-秒杀商品sku模型
 * Class Goods
 * @package app\common\model\sharp
 */
class GoodsSku extends BaseModel
{
    protected $name = 'sharp_goods_sku';

    /**
     * 关联商品表
     * @return \think\model\relation\BelongsTo
     */
    public function goods()
    {
        $module = self::getCalledModule() ?: 'common';
        return $this->belongsTo("app\\{$module}\\model\\sharp\\Goods");
    }

}