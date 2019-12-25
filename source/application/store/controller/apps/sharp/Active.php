<?php

namespace app\store\controller\apps\sharp;

use app\store\controller\Controller;
use app\store\model\sharp\Active as ActiveModel;

/**
 * 秒杀活动会场管理
 * Class Active
 * @package app\store\controller\apps\sharp
 */
class Active extends Controller
{
    /**
     * 活动会场列表
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $model = new ActiveModel;
        $list = $model->getList();
        return $this->fetch('index', compact('list'));
    }

    /**
     * 新增活动会场
     * @return array|bool|mixed
     * @throws \Exception
     */
    public function add()
    {
        if (!$this->request->isAjax()) {
            return $this->fetch('add');
        }
        $model = new ActiveModel;
        // 新增记录
        if ($model->add($this->postData('active'))) {
            return $this->renderSuccess('添加成功', url('apps.sharp.active/index'));
        }
        return $this->renderError($model->getError() ?: '添加失败');
    }

    /**
     * 修改活动状态
     * @param $active_id
     * @param $state
     * @return array|bool
     * @throws \think\exception\DbException
     */
    public function state($active_id, $state)
    {
        // 活动详情
        $model = ActiveModel::detail($active_id);
        if (!$model->setStatus($state)) {
            return $this->renderError('操作失败');
        }
        return $this->renderSuccess('操作成功');
    }

    /**
     * 删除活动会场
     * @param $active_id
     * @return array
     * @throws \think\exception\DbException
     */
    public function delete($active_id)
    {
        // 活动会场详情
        $model = ActiveModel::detail($active_id);
        if (!$model->setDelete()) {
            return $this->renderError($model->getError() ?: '删除失败');
        }
        return $this->renderSuccess('删除成功');
    }

}