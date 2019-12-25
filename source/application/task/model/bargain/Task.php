<?php

namespace app\task\model\bargain;

use app\common\model\bargain\Task as TaskModel;


/**
 * 砍价任务模型
 * Class Task
 * @package app\api\model\bargain
 */
class Task extends TaskModel
{
    /**
     * 获取已过期但未结束的砍价任务
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getEndList()
    {
        return $this->where('end_time', '<=', time())
            ->where('status', '=', 1)
            ->where('is_delete', '=', 0)
            ->select();
    }

    /**
     * 将砍价任务标记为已结束(批量)
     * @param $taskIds
     * @return false|int
     */
    public function setIsEnd($taskIds)
    {
        return $this->isUpdate(true)->save([
            'status' => 0
        ], ['task_id' => ['in', $taskIds]]);
    }

}