<?php

namespace app\store\model\sharp;

use app\common\model\sharp\ActiveGoods as ActiveGoodsModel;
use app\store\model\sharp\Goods as GoodsModel;

/**
 * 整点秒杀-活动会场与商品关联模型
 * Class ActiveGoods
 * @package app\store\model\sharp
 */
class ActiveGoods extends ActiveGoodsModel
{
    /**
     * 获取秒杀商品模型
     * @return Goods
     */
    protected function getGoodsModel()
    {
        return new GoodsModel;
    }

    /**
     * 同步删除活动会场与商品关联记录
     * @param $sharpGoodsId
     */
    public function onDeleteSharpGoods($sharpGoodsId)
    {
        $this->where('sharp_goods_id', '=', $sharpGoodsId)->delete();
    }

}