<?php

namespace app\api\model\bargain;

use app\common\model\bargain\TaskHelp as TaskHelpModel;

/**
 * 砍价任务助力记录模型
 * Class TaskHelp
 * @package app\api\model\bargain
 */
class TaskHelp extends TaskHelpModel
{
    /**
     * 隐藏的字段
     * @var array
     */
    protected $hidden = [
        'wxapp_id',
        'create_time',
    ];

    /**
     * 获取助力列表记录
     * @param $taskId
     * @return false|\PDOStatement|string|\think\Collection
     */
    public static function getListByTaskId($taskId)
    {
        // 获取列表数据
        $list = (new static)->with(['user'])
            ->where('task_id', '=', $taskId)
            ->order(['create_time' => 'desc'])
            ->select();
        // 隐藏会员昵称
        foreach ($list as &$item) {
            $item['user']['nickName'] = \substr_cut($item['user']['nickName']);
        }
        return $list;
    }

    /**
     * 新增记录
     * @param $task
     * @param $userId
     * @param $cutMoney
     * @param $isCreater
     * @return false|int
     */
    public function add($task, $userId, $cutMoney, $isCreater = false)
    {
        return $this->save([
            'task_id' => $task['task_id'],
            'active_id' => $task['active_id'],
            'user_id' => $userId,
            'cut_money' => $cutMoney,
            'is_creater' => $isCreater,
            'wxapp_id' => static::$wxapp_id,
        ]);
    }

    /**
     * 根据砍价活动id获取正在砍价的助力信息列表
     * @param $activeId
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getHelpListByActiveId($activeId)
    {
        return $this->with('user')
            ->alias('help')
            ->field('help.*')
            ->join('bargain_task task', 'task.task_id = help.task_id')
            ->where('help.active_id', '=', $activeId)
            // is_creater 只统计发起人
//            ->where('help.is_creater', '=', 1)
            ->where('task.status', '=', 1)
            ->where('task.is_delete', '=', 0)
            ->group('help.user_id')
            ->limit(5)
            ->select();
    }

    /**
     * 根据砍价活动id获取正在砍价的助力人数
     * @param $activeId
     * @return int|string
     * @throws \think\Exception
     */
    public function getHelpCountByActiveId($activeId)
    {
        return $this->alias('help')
            ->field('help.*')
            ->join('bargain_task task', 'task.task_id = help.task_id')
            ->where('help.active_id', '=', $activeId)
            // is_creater 只统计发起人
//            ->where('help.is_creater', '=', 1)
            ->where('task.status', '=', 1)
            ->where('task.is_delete', '=', 0)
            ->group('help.user_id')
            ->count();
    }

    /**
     * 根据砍价活动id集获取正在砍价的助力信息
     * @param $activeIds
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUnderwayByActiveIds_x($activeIds)
    {
        return $this->with('user')
            ->alias('help')
            ->field('help.*')
            ->join('bargain_task task', 'task.task_id = help.task_id')
            ->where('help.active_id', 'in', $activeIds)
            ->where('task.status', '=', 1)
            ->where('task.is_delete', '=', 0)
            ->group('help.user_id')
            ->limit(5)
            ->select();
    }

}