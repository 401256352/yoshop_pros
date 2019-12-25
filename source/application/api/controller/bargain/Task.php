<?php

namespace app\api\controller\bargain;

use app\api\controller\Controller;
use app\api\model\bargain\Task as TaskModel;
use app\api\model\bargain\Setting as SettingModel;
use app\common\library\Lock;

class Task extends Controller
{
    /**
     * 我的砍价列表
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function lists()
    {
        $model = new TaskModel;
        $myList = $model->getMyList($this->getUser()['user_id']);
        return $this->renderSuccess(compact('myList'));
    }

    /**
     * 创建砍价任务
     * @param $active_id
     * @param $goods_sku_id
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function partake($active_id, $goods_sku_id)
    {
        // 用户信息
        $user = $this->getUser();
        // 创建砍价任务
        $model = new TaskModel;
        if (!$model->partake($user['user_id'], $active_id, $goods_sku_id)) {
            return $this->renderError($model->getError() ?: '砍价任务创建失败');
        }
        return $this->renderSuccess([
            'task_id' => $model['task_id']
        ]);
    }

    /**
     * 获取砍价任务详情
     * @param $task_id
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function detail($task_id)
    {
        $model = new TaskModel;
        $detail = $model->getTaskDetail($task_id, $this->getUser(false));
        if ($detail === false) {
            return $this->renderError($model->getError());
        }
        // 砍价规则
        $setting = SettingModel::getBasic();
        return $this->renderSuccess(array_merge($detail, ['setting' => $setting]));
    }

    /**
     * 帮砍一刀
     * @param $task_id
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function help_cut($task_id)
    {
        // 加阻塞锁, 防止并发
        Lock::lockUp("bargain_help_cut_{$task_id}");
        // 砍价任务详情
        $model = TaskModel::detail($task_id);
        // 砍一刀的金额
        $cut_money = $model->getCutMoney();
        // 帮砍一刀事件
        $status = $model->helpCut($this->getUser());
        // 解除并发锁
        Lock::unLock("bargain_help_cut_{$task_id}");
        if ($status == true) {
            return $this->renderSuccess(compact('cut_money'), '砍价成功');
        }
        return $this->renderError($model->getError() ?: '砍价失败');
    }

}