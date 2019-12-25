<?php

namespace app\task\behavior\bargain;

use think\Cache;
use app\task\model\bargain\Task as TaskModel;
use app\common\library\helper;

/**
 * 砍价任务行为管理
 * Class Task
 * @package app\task\behavior\bargain
 */
class Task
{
    /* @var TaskModel $model */
    private $model;

    /**
     * 执行函数
     * @param $model
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function run($model)
    {
        if (!$model instanceof TaskModel) {
            return new TaskModel and false;
        }
        $this->model = $model;
        if (!$model::$wxapp_id) {
            return false;
        }
        if (!Cache::has('__task_space__bargain_task__')) {
            // 将已过期的砍价任务标记为已结束
            $this->onSetIsEnd();
            Cache::set('__task_space__bargain_task__', time(), 10);
        }
        return true;
    }

    /**
     * 将已过期的砍价任务标记为已结束
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function onSetIsEnd()
    {
        // 获取已过期但未结束的砍价任务
        $list = $this->model->getEndList();
        $taskIds = helper::getArrayColumn($list, 'task_id');
        // 将砍价任务标记为已结束(批量)
        !empty($taskIds) && $this->model->setIsEnd($taskIds);
        // 记录日志
        $this->dologs('close', [
            'orderIds' => json_encode($taskIds),
        ]);
        return true;
    }

    /**
     * 记录日志
     * @param $method
     * @param array $params
     * @return bool|int
     */
    private function dologs($method, $params = [])
    {
        $value = 'behavior bargain Task --' . $method;
        foreach ($params as $key => $val)
            $value .= ' --' . $key . ' ' . $val;
        return log_write($value);
    }

}