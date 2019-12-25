<?php

namespace app\store\model\sharp;

use app\common\model\sharp\Goods as GoodsModel;
use app\store\model\sharp\GoodsSku as GoodsSkuModel;
use app\store\model\sharp\ActiveGoods as ActiveGoodsModel;
use app\store\service\Goods as GoodsService;

/**
 * 整点秒杀-商品模型
 * Class Goods
 * @package app\store\model\sharp
 */
class Goods extends GoodsModel
{
    /**
     * 获取列表数据
     * @param string $search
     * @return mixed|\think\Paginator
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getList($search = '')
    {
        $this->setBaseQuery($this->alias, [
            ['goods', 'goods_id'],
        ]);
        // 检索查询条件
        if (!empty($search)) {
            $this->where('goods.goods_name', 'like', "%{$search}%");
        }
        // 获取活动列表
        $list = $this->where("{$this->alias}.is_delete", '=', 0)
            ->order(["{$this->alias}.sort" => 'asc', "{$this->alias}.create_time" => 'desc'])
            ->paginate(15, false, [
                'query' => \request()->request()
            ]);
        // 设置商品数据
        return $this->setGoodsListData($list, true);
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
        // 获取商品列表数据
        $list = parent::getListByIds($goodsIds, $param);
        // 整理列表数据并返回
        return $this->setGoodsListData($list, true);
    }

    /**
     * 添加商品
     * @param $goods
     * @param $data
     * @return bool
     * @throws \Exception
     */
    public function add($goods, $data)
    {
        // 添加商品
        $this->allowField(true)->save(array_merge($data, [
            'goods_id' => $goods['goods_id'],
            'seckill_stock' => $this->getSeckillStock($goods, $data),
            'wxapp_id' => self::$wxapp_id,
        ]));
        // 商品规格
        $this->addGoodsSpec($goods, $data);
        return true;
    }

    /**
     * 编辑商品
     * @param $goods
     * @param $data
     * @return bool
     * @throws \Exception
     */
    public function edit($goods, $data)
    {
        // 更新商品
        $this->allowField(true)->save(array_merge($data, [
            'seckill_stock' => $this->getSeckillStock($goods, $data),
        ]));
        // 商品规格
        $this->addGoodsSpec($goods, $data, true);
        return true;
    }

    /**
     * 获取总库存数量
     * @param $goods
     * @param $data
     * @return int
     */
    private function getSeckillStock($goods, $data)
    {
        if ($goods['spec_type'] == '10') {
            return $data['sku']['seckill_stock'];
        }
        $seckillStock = 0;
        foreach ($data['spec_many']['spec_list'] as $item) {
            $seckillStock += $item['form']['seckill_stock'];
        }
        return $seckillStock;
    }

    /**
     * 验证商品ID能否被添加
     * @param $goodsId
     * @return bool
     */
    public function validateGoodsId($goodsId)
    {
        if ($goodsId <= 0) {
            $this->error = '很抱歉，您还没有选择商品';
            return false;
        }
        // 验证是否存在秒杀商品
        if ($this->isExistGoodsId($goodsId)) {
            $this->error = '很抱歉，该商品已存在，无需重复添加';
            return false;
        }
        return true;
    }

    /**
     * 添加商品规格
     * @param $goods
     * @param $data
     * @param bool $isUpdate
     * @return array|false|\think\Model
     * @throws \Exception
     */
    private function addGoodsSpec($goods, $data, $isUpdate = false)
    {
        // 更新模式: 先删除所有规格
        $model = new GoodsSkuModel;
        $isUpdate && $model->removeAll($this['sharp_goods_id']);
        // 添加sku (单规格)
        if ($goods['spec_type'] == '10') {
            return $this->sku()->save(array_merge($data['sku'], [
                'wxapp_id' => self::$wxapp_id,
            ]));
        }
        // 添加sku (多规格)
        return $model->addSkuList($this['sharp_goods_id'], $data['spec_many']['spec_list']);
    }

    /**
     * 商品ID是否存在
     * @param $goodsId
     * @return bool
     */
    public static function isExistGoodsId($goodsId)
    {
        return !!(new static)->where('goods_id', '=', $goodsId)
            ->where('is_delete', '=', 0)
            ->value('sharp_goods_id');
    }

    /**
     * 软删除
     * @return false|int
     */
    public function setDelete()
    {
        // 同步删除活动会场与商品关联记录
        $model = new ActiveGoodsModel;
        $model->onDeleteSharpGoods($this['sharp_goods_id']);
        return $this->allowField(true)->save(['is_delete' => 1]);
    }

}