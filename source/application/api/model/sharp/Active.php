<?php

namespace app\api\model\sharp;

use app\common\model\sharp\Active as ActiveModel;

/**
 * 整点秒杀-活动会场模型
 * Class Active
 * @package app\api\model\sharp
 */
class Active extends ActiveModel
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

    /**
     * 获取当天的活动
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getNowActive()
    {
        $todayTime = strtotime(date('Y-m-d'));
        return $this->getActiveByDate($todayTime, '=');
    }

    /**
     * 获取当天的活动
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getNextActive()
    {
        $todayTime = strtotime(date('Y-m-d'));
        return $this->getActiveByDate($todayTime, '>');
    }

    /**
     * 根据日期获取活动
     * @param $date
     * @param string $op
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function getActiveByDate($date, $op = '=')
    {
        return $this->where('active_date', $op, $date)
            ->where('status', '=', 1)
            ->where('is_delete', '=', 0)
            ->find();
    }

}