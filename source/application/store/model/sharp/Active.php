<?php

namespace app\store\model\sharp;

use app\common\library\helper;
use app\common\model\sharp\Active as ActiveModel;
use app\store\model\sharp\ActiveTime as ActiveTimeModel;

/**
 * 整点秒杀-活动会场模型
 * Class Active
 * @package app\store\model\sharp
 */
class Active extends ActiveModel
{
    /**
     * 获取器：活动日期
     * @param $value
     * @return false|string
     */
    public function getActiveDateAttr($value)
    {
        return date('Y-m-d', $value);
    }

    /**
     * 获取活动会场列表
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function getList()
    {
        $list = $this->with(['active_time'])
            ->where('is_delete', '=', 0)
            ->order(['active_date' => 'desc'])
            ->paginate(15, false, [
                'query' => \request()->request()
            ]);
        return $this->getActiveTimeCount($list);
    }

    private function getActiveTimeCount($list)
    {
        foreach ($list as &$item) {
            $activeTimeArr = helper::getArrayColumn($item['active_time'], 'active_time');
            $item['active_time_count'] = count($activeTimeArr);
        }
        return $list;
    }

    /**
     * 新增记录
     * @param $data
     * @return bool
     * @throws \Exception
     */
    public function add($data)
    {
        // 表单验证
        if (!$this->onValidate($data)) return false;
        // 新增活动
        $data['wxapp_id'] = static::$wxapp_id;
        $data['active_date'] = strtotime($data['active_date']);
        !isset($data['sharp_goods']) && $data['sharp_goods'] = [];
        return $this->transaction(function () use ($data) {
            // 新增活动
            $this->allowField(true)->save($data);
            // 新增活动场次
            (new ActiveTimeModel)->onBatchAdd(
                $this['active_id'],
                $data['active_times'],
                $data['sharp_goods']
            );
            return true;
        });
    }

    /**
     * 表单验证
     * @param $data
     * @return bool
     */
    private function onValidate($data)
    {
        // 活动日期是否已存在
        if ($this->isExistByActiveDate($data['active_date'])) {
            $this->error = '该活动日期已存在';
            return false;
        }
//        // 验证是否选择商品
//        if (!isset($data['sharp_goods']) || empty($data['sharp_goods'])) {
//            $this->error = '您还没有选择秒杀商品';
//            return false;
//        }
        return true;
    }

    /**
     * 活动日期是否已存在
     * @param $date
     * @return bool
     */
    private function isExistByActiveDate($date)
    {
        return !!(new static)->where('active_date', '=', strtotime($date))
            ->where('is_delete', '=', 0)
            ->value('active_id');
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
     * 软删除
     * @return false|int
     */
    public function setDelete()
    {
        // 同步删除场次和商品关联
        (new ActiveTimeModel)->onDeleteByActiveId($this['active_id']);
        // 将该活动设置为已删除
        return $this->allowField(true)->save(['is_delete' => 1]);
    }

}