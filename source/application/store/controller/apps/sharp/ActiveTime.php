<?php

namespace app\store\controller\apps\sharp;

use app\store\controller\Controller;
use app\store\model\sharp\Active as ActiveModel;
use app\store\model\sharp\ActiveTime as ActiveTimeModel;
use app\store\model\sharp\ActiveGoods as ActiveGoodsModel;

/**
 * 秒杀活动会场-场次管理
 * Class ActiveTime
 * @package app\store\controller\apps\sharp
 */
class ActiveTime extends Controller
{
    /**
     * 活动会场-场次列表
     * @param $active_id
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index($active_id)
    {
        $model = new ActiveTimeModel;
        $list = $model->getList($active_id);
        return $this->fetch('index', compact('list'));
    }

    /**
     * 新增活动会场
     * @param $active_id
     * @return array|bool|mixed
     * @throws \think\exception\DbException
     */

    /**
     * 新增活动会场
     * @param $active_id
     * @return array|bool|mixed
     * @throws \think\exception\DbException
     */
    public function add($active_id)
    {
        // 活动详情
        $active = ActiveModel::detail($active_id);
        // 已存在的场次
        $model = new ActiveTimeModel;
        $existTimes = $model->getActiveTimeData($active_id);
        if (!$this->request->isAjax()) {
            return $this->fetch('add', compact('active', 'existTimes'));
        }
        // 新增记录
        if ($model->add($active_id, $this->postData('active'))) {
            $url = url('apps.sharp.active_time/index', ['active_id' => $active_id]);
            return $this->renderSuccess('添加成功', $url);
        }
        return $this->renderError($model->getError() ?: '添加失败');
    }

    /**
     * 编辑活动会场
     * @param $id
     * @return array|bool|mixed
     * @throws \think\exception\DbException
     * @throws \Exception
     */
    public function edit($id)
    {
        // 场次详情
        $model = ActiveTimeModel::detail($id, ['active']);
        // 当前场次关联的商品
        $goodsList = $model->getGoodsListByActiveTimeId($id);
        if (!$this->request->isAjax()) {
            return $this->fetch('edit', compact('model', 'goodsList'));
        }
        // 新增记录
        if ($model->edit($this->postData('active'))) {
            $url = url('apps.sharp.active_time/index', ['active_id' => $model['active_id']]);
            return $this->renderSuccess('更新成功', $url);
        }
        return $this->renderError($model->getError() ?: '更新失败');
    }

    /**
     * 修改场次状态
     * @param $id
     * @param $state
     * @return array|bool
     * @throws \think\exception\DbException
     */
    public function state($id, $state)
    {
        // 场次详情
        $model = ActiveTimeModel::detail($id);
        if (!$model->setStatus($state)) {
            return $this->renderError('操作失败');
        }
        return $this->renderSuccess('操作成功');
    }

    /**
     * 删除活动场次
     * @param $id
     * @return array|bool
     * @throws \think\Exception
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function delete($id)
    {
        // 场次详情
        $model = ActiveTimeModel::detail($id);
        if (!$model->onDelete()) {
            return $this->renderError($model->getError() ?: '删除失败');
        }
        return $this->renderSuccess('删除成功');
    }

}