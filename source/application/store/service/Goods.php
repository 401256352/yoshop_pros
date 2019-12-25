<?php

namespace app\store\service;

use app\common\service\Goods as GoodsService;

use app\common\library\helper;
use app\store\model\Goods as GoodsModel;
use app\store\model\Category as CategoryModel;
use app\store\model\Delivery as DeliveryModel;
use app\store\model\user\Grade as GradeModel;
use app\store\service\goods\Apply as GoodsApplyService;
use app\store\model\store\Shop as ShopModel;
/**
 * 商品服务类
 * Class Goods
 * @package app\store\service
 */
class Goods extends GoodsService
{
    /**
     * 商品管理公共数据
     * @param GoodsModel|null $model
     * @return array
     */
    public static function getEditData($model = null)
    {
        // 商品分类
        $catgory = CategoryModel::getCacheTree();
        // 配送模板
        $delivery = DeliveryModel::getAll();
        // 会员等级列表
        $gradeList = GradeModel::getUsableList();
        // 商品sku数据
        $specData = helper::jsonEncode(static::getSpecData($model));
        
        // 门店列表
        $shopList = (new ShopModel)->getList();
        // 商品规格是否锁定
        $isSpecLocked = static::checkSpecLocked($model);
        return compact('catgory', 'delivery', 'gradeList', 'specData', 'isSpecLocked','shopList');
    }

    /**
     * 验证商品是否允许删除
     * @param $goodsId
     * @return bool
     */
    public static function checkIsAllowDelete($goodsId)
    {
        return GoodsApplyService::checkIsAllowDelete($goodsId);
    }

    /**
     * 商品规格是否允许编辑
     * @param null $model
     * @return bool
     */
    private static function checkSpecLocked($model = null)
    {
        if ($model == null) return false;
        return GoodsApplyService::checkSpecLocked($model['goods_id']);
    }

}