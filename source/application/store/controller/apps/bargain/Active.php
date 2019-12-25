<?php

namespace app\store\controller\apps\bargain;

use app\store\controller\Controller;
use app\store\model\Goods as GoodsModel;
use app\store\model\bargain\Active as ActiveModel;

/**
 * 砍价活动管理
 * Class Active
 * @package app\store\controller\apps\bargain
 */
class Active extends Controller
{
    /**
     * 砍价活动列表
     * @param string $search
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index($search = '')
    {
        $model = new ActiveModel;
        $list = $model->getList($search);
        return $this->fetch('index', compact('list'));
    }

    /**
     * 新增砍价活动
     * @return array|bool|mixed
     */
    public function add()
    {
        if (!$this->request->isAjax()) {
            return $this->fetch('add');
        }
        $model = new ActiveModel;
        // 新增记录
        if ($model->add($this->postData('active'))) {
            return $this->renderSuccess('添加成功', url('apps.bargain.active/index'));
        }
        return $this->renderError($model->getError() ?: '添加失败');
    }

    /**
     * 更新砍价活动
     * @param $active_id
     * @return array|mixed
     * @throws \think\exception\DbException
     */
    public function edit($active_id)
    {
        // 砍价活动详情
        $model = ActiveModel::detail($active_id);
        if (!$this->request->isAjax()) {
            // 获取商品详情
            $goods = GoodsModel::detail($model['goods_id']);
            return $this->fetch('edit', compact('model', 'goods'));
        }
        // 更新记录
        if ($model->edit($this->postData('active'))) {
            return $this->renderSuccess('更新成功', url('apps.bargain.active/index'));
        }
        return $this->renderError($model->getError() ?: '更新失败');
    }

    /**
     * 删除砍价活动
     * @param $active_id
     * @return array
     * @throws \think\exception\DbException
     */
    public function delete($active_id)
    {
        // 砍价活动详情
        $model = ActiveModel::detail($active_id);
        if (!$model->setDelete()) {
            return $this->renderError($model->getError() ?: '删除失败');
        }
        return $this->renderSuccess('删除成功');
    }

}