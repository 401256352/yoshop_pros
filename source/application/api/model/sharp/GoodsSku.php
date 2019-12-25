<?php

namespace app\api\model\sharp;

use app\common\model\sharp\GoodsSku as GoodsSkuModel;

/**
 * 整点秒杀-秒杀商品sku模型
 * Class Goods
 * @package app\api\model\sharp
 */
class GoodsSku extends GoodsSkuModel
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

}