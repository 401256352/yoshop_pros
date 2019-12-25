<?php

namespace app\store\controller\apps\sharp;

use app\store\controller\Controller;
use app\store\model\sharp\Setting as SettingModel;

/**
 * 整点秒杀设置
 * Class Setting
 * @package app\store\controller\apps\sharp
 */
class Setting extends Controller
{
    /**
     * 拼团设置
     * @return array|mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {
        if (!$this->request->isAjax()) {
            $values = SettingModel::getItem('basic');
            return $this->fetch('index', compact('values'));
        }
        $model = new SettingModel;
        if ($model->edit('basic', $this->postData('basic'))) {
            return $this->renderSuccess('更新成功');
        }
        return $this->renderError($model->getError() ?: '更新失败');
    }

}