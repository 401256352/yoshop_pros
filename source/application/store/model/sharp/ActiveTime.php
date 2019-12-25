<?php

namespace app\store\model\sharp;

use app\common\model\sharp\ActiveTime as ActiveTimeModel;
use app\store\model\sharp\ActiveGoods as ActiveGoodsModel;

/**
 * 整点秒杀-活动会场场次模型
 * Class ActiveTime
 * @package app\store\model\sharp
 */
class ActiveTime extends ActiveTimeModel
{
    /**
     * 获取活动会场场次列表
     * @param $activeId
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function getList($activeId)
    {
        $list = $this->with(['active'])
            ->withCount(['goods'])
            ->where('active_id', '=', $activeId)
            ->order(['active_time' => 'asc'])
            ->paginate(15, false, [
                'query' => \request()->request()
            ]);
        return $list;
    }

    /**
     * 修改商品状态
     * @param $state
     * @return false|int
     */
    public function setStatus($state)
    {
        return $this->allowField(true)->save(['status' => (int)$state]) !== false;
    }

    /**
     * 获取指定会场的所有场次时间
     * @param $activeId
     * @return array
     */
    public function getActiveTimeData($activeId)
    {
        return $this->where('active_id', '=', $activeId)->column('active_time');
    }

    /**
     * 根据活动场次ID获取商品列表 (格式化后用于编辑页)
     * @param $activeTimeId
     * @return array
     */
    public function getGoodsListByActiveTimeId($activeTimeId)
    {
        $data = [];
        foreach (ActiveGoodsModel::getGoodsListByActiveTimeId($activeTimeId) as $item) {
            $data[] = [
                'goods_id' => $item['sharp_goods_id'],
                'goods_name' => $item['goods']['goods_name'],
                'goods_image' => $item['goods']['goods_image'],
            ];
        }
        return $data;
    }

    /**
     * 新增记录
     * @param $activeId
     * @param $data
     * @return bool|mixed
     */
    public function add($activeId, $data)
    {
        // 表单验证
        if (!$this->onValidate($data)) return false;
        // 事务处理
        return $this->transaction(function () use ($activeId, $data) {
            // 新增活动场次
            $this->onBatchAdd(
                $activeId,
                $data['active_times'],
                $data['sharp_goods'],
                $data['status']
            );
            return true;
        });
    }

    /**
     * 更新记录
     * @param $data
     * @return bool|mixed
     */
    public function edit($data)
    {
        // 验证是否选择商品
        if (!$this->onValidateSharpGoods($data)) {
            return false;
        }
        // 事务处理
        return $this->transaction(function () use ($data) {
            // 更新活动场次
            $this->allowField(true)->save($data);
            // 更新场次的商品关联记录
            $this->onUpdateActiveGoodsRec($data['sharp_goods']);
            return true;
        });
    }

    /**
     * 更新当前场次的商品关联记录
     * @param $sharpGoodsIds
     * @return array|false
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    private function onUpdateActiveGoodsRec($sharpGoodsIds)
    {
        $saveData = [];
        foreach ($sharpGoodsIds as $goodsId) {
            $saveData[] = [
                'active_id' => $this['active_id'],
                'active_time_id' => $this['active_time_id'],
                'sharp_goods_id' => $goodsId,
                'wxapp_id' => static::$wxapp_id,
            ];
        }
        $this->goods()->delete();
        return (new ActiveGoodsModel)->isUpdate(false)->saveAll($saveData);
    }

    /**
     * 表单验证
     * @param $data
     * @return bool
     */
    private function onValidate($data)
    {
        // 验证是否选择活动场次
        if (!isset($data['active_times']) || empty($data['active_times'])) {
            $this->error = '您还没有选择活动场次';
            return false;
        }
        // 验证是否选择商品
        if (!$this->onValidateSharpGoods($data)) {
            return false;
        }
        return true;
    }

    /**
     * 验证是否选择商品
     * @param $data
     * @return bool
     */
    private function onValidateSharpGoods($data)
    {
        // 验证是否选择商品
        if (!isset($data['sharp_goods']) || empty($data['sharp_goods'])) {
            $this->error = '您还没有选择秒杀商品';
            return false;
        }
        return true;
    }

    /**
     * 批量新增活动场次
     * @param $activeId
     * @param array $times
     * @param array $sharpGoodsIds
     * @param int $status
     * @return bool
     * @throws \Exception
     */
    public function onBatchAdd($activeId, $times, $sharpGoodsIds, $status = 1)
    {
        $saveData = [];
        foreach ($times as $time) {
            $saveData[] = [
                'active_id' => $activeId,
                'active_time' => (int)$time,
                'status' => (int)$status,
                'wxapp_id' => static::$wxapp_id,
            ];
        }
        // 批量更新
        $activeTimes = $this->isUpdate(false)->saveAll($saveData);
        // 新增活动场次与商品关联关系记录
        if (!empty($sharpGoodsIds)) {
            $this->onBatchAddActiveGoodsRec($activeTimes, $sharpGoodsIds);
        }
        return true;
    }

    /**
     * 新增活动场次与商品关联记录
     * @param $activeTimes
     * @param $sharpGoodsIds
     * @return array|false
     * @throws \Exception
     */
    private function onBatchAddActiveGoodsRec($activeTimes, $sharpGoodsIds)
    {
        $saveData = [];
        foreach ($activeTimes as $item) {
            foreach ($sharpGoodsIds as $goodsId) {
                $saveData[] = [
                    'active_id' => $item['active_id'],
                    'active_time_id' => $item['active_time_id'],
                    'sharp_goods_id' => $goodsId,
                    'wxapp_id' => static::$wxapp_id,
                ];
            }
        }
        return (new ActiveGoodsModel)->isUpdate(false)->saveAll($saveData);
    }

    /**
     * 根据活动ID删除全部场次和商品关系
     * @param $activeId
     * @return bool
     */
    public function onDeleteByActiveId($activeId)
    {
        $this->where('active_id', '=', $activeId)->delete();
        (new ActiveGoodsModel)->where('active_id', '=', $activeId)->delete();
        return true;
    }

    /**
     * 删除当前场次
     * @return bool
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function onDelete()
    {
        $this->delete();
        $this->goods()->delete();
        return true;
    }

}