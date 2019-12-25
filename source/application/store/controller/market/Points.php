<?php

namespace app\store\controller\market;

use app\store\controller\Controller;
use app\store\model\Setting as SettingModel;
use app\store\model\user\PointsLog as PointsLogModel;

/**
 * 积分管理
 * Class Points
 * @package app\store\controller\market
 */
class Points extends Controller
{
    /**
     * 积分设置
     * @return array|bool|mixed
     * @throws \think\exception\DbException
     */
    public function setting()
    {
        if (!$this->request->isAjax()) {
            $values = SettingModel::getItem('points');
            return $this->fetch('setting', compact('values'));
        }
        $model = new SettingModel;
        if ($model->edit('points', $this->postData('points'))) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失败');
    }

    /**
     * 积分明细
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function log()
    {
        // 积分明细列表
        $model = new PointsLogModel;
        $list = $model->getList($this->request->param());
        return $this->fetch('log', compact('list'));
    }

}