<?php

namespace app\store\controller\apps\bargain;

use app\store\controller\Controller;
use app\store\model\bargain\Task as TaskModel;
use app\store\model\bargain\TaskHelp as TaskHelpModel;

/**
 * 砍价任务管理
 * Class Task
 * @package app\store\controller\apps\bargain
 */
class Task extends Controller
{
    /**
     * 砍价任务列表
     * @param string $search
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index($search = '')
    {
        $model = new TaskModel;
        $list = $model->getList($search);
        return $this->fetch('index', compact('list'));
    }

    /**
     * 砍价榜
     * @param $task_id
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function help($task_id)
    {
        $model = new TaskHelpModel;
        $list = $model->getList($task_id);
        return $this->fetch('help', compact('list'));
    }

    /**
     * 删除砍价任务
     * @param $task_id
     * @return array
     * @throws \think\exception\DbException
     */
    public function delete($task_id)
    {
        // 砍价活动详情
        $model = TaskModel::detail($task_id);
        if (!$model->setDelete()) {
            return $this->renderError($model->getError() ?: '删除失败');
        }
        return $this->renderSuccess('删除成功');
    }

}