<?php

namespace app\store\model\bargain;

use app\common\model\bargain\TaskHelp as TaskHelpModel;

/**
 * 砍价任务助力记录模型
 * Class TaskHelp
 * @package app\store\model\bargain
 */
class TaskHelp extends TaskHelpModel
{
    /**
     * 获取列表数据
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function getList($task_id)
    {
        // 砍价任务助力记录
        $list = $this->with(['user'])
            ->where('task_id', '=', $task_id)
            ->order(['create_time' => 'desc'])
            ->paginate(15, false, [
                'query' => \request()->request()
            ]);
        return $list;
    }

}