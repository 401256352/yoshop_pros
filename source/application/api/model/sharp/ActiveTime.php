<?php

namespace app\api\model\sharp;

use app\common\model\sharp\ActiveTime as ActiveTimeModel;

/**
 * 整点秒杀-活动会场场次模型
 * Class ActiveTime
 * @package app\api\model\sharp
 */
class ActiveTime extends ActiveTimeModel
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

    /**
     * 获取当前进行中的活动场次
     * @param $activeId
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getNowActiveTime($activeId)
    {
        // 当前的时间点
        $nowTime = date('H');
        return $this->where('active_id', '=', $activeId)
            ->where('active_time', '=', $nowTime)
            ->where('status', '=', 1)
            ->find();
    }

    /**
     * 获取下一场活动场次
     * @param $activeId
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getNextActiveTimes($activeId)
    {
        // 当前的时间点
        $nowTime = date('H');
        return $this->where('active_id', '=', $activeId)
            ->where('active_time', '>', $nowTime)
            ->where('status', '=', 1)
            ->order(['active_time' => 'asc'])
            ->select();
    }

    /**
     * 获取指定日期最近的活动场次
     * @param $activeId
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getRecentActiveTime($activeId)
    {
        return $this->where('active_id', '=', $activeId)
            ->where('status', '=', 1)
            ->order(['active_time' => 'asc'])
            ->find();
    }

}