<?php

namespace app\store\service\goods;

use app\common\service\Goods as GoodsService;
use app\store\model\sharp\Goods as SharpGoodsModel;
use app\store\model\bargain\Active as BargainGoodsModel;

class Apply extends GoodsService
{
    /**
     * 验证商品规格属性是否锁定
     * @param $goodsId
     * @return bool
     */
    public static function checkSpecLocked($goodsId)
    {
        $service = new static;
        return $service->checkSharpGoods($goodsId);
    }

    /**
     * 验证商品是否允许删除
     * @param $goodsId
     * @return bool
     */
    public static function checkIsAllowDelete($goodsId)
    {
        $service = new static;
        if ($service->checkSharpGoods($goodsId)) return false;
        if ($service->checkBargainGoods($goodsId)) return false;
        return true;
    }

    /**
     * 验证商品是否参与了秒杀商品
     * @param $goodsId
     * @return bool
     */
    private function checkSharpGoods($goodsId)
    {
        return SharpGoodsModel::isExistGoodsId($goodsId);
    }

    /**
     * 验证商品是否参与了砍价商品
     * @param $goodsId
     * @return bool
     */
    private function checkBargainGoods($goodsId)
    {
        return BargainGoodsModel::isExistGoodsId($goodsId);
    }

}