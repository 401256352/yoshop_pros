<?php

namespace app\api\model\sharp;

use app\common\model\sharp\Goods as GoodsModel;

/**
 * 整点秒杀-商品模型
 * Class Goods
 * @package app\api\model\sharp
 */
class Goods extends GoodsModel
{
    /**
     * 隐藏字段
     * @var array
     */
    protected $hidden = [
        'is_delete',
        'wxapp_id',
        'create_time',
        'update_time',
    ];

}