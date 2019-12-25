<?php

namespace app\common\model\sharing;

use app\common\model\BaseModel;
use app\common\model\sharing\GoodsSku as GoodsSkuModel;
use app\common\enum\goods\DeductStockType as DeductStockTypeEnum;

/**
 * 拼团订单商品模型
 * Class OrderGoods
 * @package app\common\model\sharing
 */
class OrderGoods extends BaseModel
{
    protected $name = 'sharing_order_goods';
    protected $updateTime = false;

    /**
     * 关联拼团商品表
     * @return \think\model\relation\BelongsTo
     */
    public function goods()
    {
        return $this->belongsTo('Goods');
    }

    /**
     * 订单拼团商品图
     * @return \think\model\relation\BelongsTo
     */
    public function image()
    {
        $module = self::getCalledModule() ?: 'common';
        return $this->belongsTo("app\\{$module}\\model\\UploadFile", 'image_id', 'file_id');
    }

    /**
     * 关联拼团商品sku表
     * @return \think\model\relation\BelongsTo
     */
    public function sku()
    {
        return $this->belongsTo('GoodsSku', 'spec_sku_id', 'spec_sku_id');
    }

    /**
     * 关联拼团订单主表
     * @return \think\model\relation\BelongsTo
     */
    public function orderM()
    {
        return $this->belongsTo('Order');
    }

    /**
     * 关联拼团售后单记录表
     * @return \think\model\relation\HasOne
     */
    public function refund()
    {
        return $this->hasOne('OrderRefund', 'order_goods_id');
    }

    /**
     * 拼团订单商品详情
     * @param $where
     * @return OrderGoods|null
     * @throws \think\exception\DbException
     */
    public static function detail($where)
    {
        return static::get($where, ['image', 'refund']);
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
        return !empty($goodsSkuData) && (new GoodsSkuModel)->updateAll($goodsSkuData);
    }

}
