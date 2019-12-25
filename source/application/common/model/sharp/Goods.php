<?php

namespace app\common\model\sharp;

use app\common\library\helper;
use app\common\model\BaseModel;
use app\common\service\Goods as GoodsService;

/**
 * 整点秒杀-商品模型
 * Class Goods
 * @package app\common\model\sharp
 */
class Goods extends BaseModel
{
    protected $name = 'sharp_goods';
    protected $alias = 'sharp_goods';

    /**
     * 关联商品表
     * @return \think\model\relation\BelongsTo
     */
    public function goods()
    {
        $module = self::getCalledModule() ?: 'common';
        return $this->belongsTo("app\\{$module}\\model\\Goods");
    }

    /**
     * 关联商品规格表
     * @return \think\model\relation\HasMany
     */
    public function sku()
    {
        return $this->hasMany('GoodsSku', 'sharp_goods_id')
            ->order(['seckill_price' => 'asc', 'goods_sku_id' => 'asc']);
    }

    /**
     * 根据商品id集获取商品列表
     * @param array $goodsIds
     * @param array $param
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getListByIds($goodsIds, $param = [])
    {
        // 默认条件
        $param = array_merge([
            'status' => null,
            'limit' => 15,
        ], $param);
        // 筛选条件
        !is_null($param['status']) && $this->where('status', '=', (int)$param['status']);
        // 获取商品列表数据
        $list = $this->with(['sku'])
            ->where('sharp_goods_id', 'in', $goodsIds)
            ->where('is_delete', '=', 0)
            ->order(['sort' => 'asc'])
            ->paginate($param['limit'], false, [
                'query' => \request()->request()
            ]);
        // 设置商品数据
        return $this->setGoodsListData($list, true);
    }

    /**
     * 秒杀商品详情
     * @param $sharpGoodsId
     * @param array $with
     * @return static|null
     * @throws \think\exception\DbException
     */
    public static function detail($sharpGoodsId, $with = [])
    {
        // 整理商品数据并返回
        $model = static::get($sharpGoodsId, $with);
        return $model->setGoodsListData($model, false);
    }

    /**
     * 商品多规格信息
     * @param $goods
     * @return array|null
     */
    public function getSpecData($goods)
    {
        $specData = GoodsService::getSpecData($goods);
        if ($goods['spec_type'] == 10) {
            return $specData;
        }
        $skuData = helper::arrayColumn2Key($this['sku'], 'spec_sku_id');
        foreach ($specData['spec_list'] as &$item) {
            if (isset($skuData[$item['spec_sku_id']])) {
                $item['form']['seckill_price'] = $skuData[$item['spec_sku_id']]['seckill_price'];
                $item['form']['original_price'] = $item['form']['goods_price'];
                $item['form']['seckill_stock'] = $skuData[$item['spec_sku_id']]['seckill_stock'];
            }
        }
        return $specData;
    }

    /**
     * 设置商品展示的数据
     * @param $data
     * @param bool $isMultiple
     * @param callable|null $callback
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function setGoodsListData($data, $isMultiple = true, callable $callback = null)
    {
        // 设置原商品数据
        $data = GoodsService::setGoodsData($data, $isMultiple);
        if (!$isMultiple) $dataSource = [&$data]; else $dataSource = &$data;
        // 整理商品数据
        foreach ($dataSource as &$item) {
            // 商品名称
            $item['goods_name'] = $item['goods']['goods_name'];
            // 商品图片
            $item['goods_image'] = $item['goods']['goods_image'];
            // 秒杀商品sku信息
            $item['goods_sku'] = $this->getDefaultSharpSku($item['sku'], $item['goods']['sku']);
            // 回调函数
            is_callable($callback) && call_user_func($callback, $item);
        }
        return $data;
    }

    /**
     * 整理秒杀商品的默认sku信息 (用于商品列表)
     * @param $sharpSku
     * @param $goodsSku
     * @return mixed
     */
    private function getDefaultSharpSku($sharpSku, $goodsSku)
    {
        $sharpGoodsSku = $sharpSku[0];
        $goodsSkuItem = helper::getArrayItemByColumn($goodsSku, 'spec_sku_id', $sharpGoodsSku['spec_sku_id']);
        $sharpGoodsSku['original_price'] = $goodsSkuItem['goods_price'];
        $sharpGoodsSku['image_id'] = $goodsSkuItem['image_id'];
        $sharpGoodsSku['goods_no'] = $goodsSkuItem['goods_no'];
        $sharpGoodsSku['line_price'] = $goodsSkuItem['line_price'];
        $sharpGoodsSku['goods_weight'] = $goodsSkuItem['goods_weight'];
        return $sharpGoodsSku;
    }

}