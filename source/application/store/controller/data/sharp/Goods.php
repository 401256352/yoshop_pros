<?php

namespace app\store\controller\data\sharp;

use app\store\controller\Controller;
use app\store\model\sharp\Goods as GoodsModel;

/**
 * 秒杀商品数据控制器
 * Class Goods
 * @package app\store\controller\data\sharp
 */
class Goods extends Controller
{
    /**
     * 构造方法
     * @throws \app\common\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->view->engine->layout(false);
    }

    /**
     * 商品列表
     * @param string $search
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function lists($search = '')
    {
        $model = new GoodsModel;
        $list = $model->getList($search);
        return $this->fetch('list', compact('list'));
    }

}
