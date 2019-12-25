<?php

namespace app\store\controller\apps\sharp;

use app\store\controller\Controller;
use app\store\model\Goods as GoodsModel;
use app\store\service\Goods as GoodsService;
use app\store\model\sharp\Goods as SharpGoodsModel;

/**
 * 秒杀商品管理
 * Class Goods
 * @package app\store\controller\apps\sharp
 */
class Goods extends Controller
{
    /**
     * 秒杀商品列表
     * @param string $search
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index($search = '')
    {
        $model = new SharpGoodsModel;
        $list = $model->getList($search);
        return $this->fetch('index', compact('list'));
    }

    /**
     * 添加秒杀商品
     * @param int $step
     * @param null $goods_id
     * @return array|bool|mixed
     * @throws \Exception
     */
    public function add($step = 1, $goods_id = null)
    {
        if ($step == 2) {
            return $this->step2($goods_id);
        }
        return $this->step1();
    }

    /**
     * 添加秒杀商品：步骤1
     * @return mixed
     */
    private function step1()
    {
        return $this->fetch('step1');
    }

    /**
     * 添加秒杀商品：步骤2
     * @param $goodsId
     * @return array|bool|mixed
     * @throws \Exception
     */
    private function step2($goodsId)
    {
        $model = new SharpGoodsModel;
        // 验证商品ID能否被添加
        if (!$model->validateGoodsId($goodsId)) {
            $this->renderError($model->getError());
        }
        // 商品信息
        $goods = GoodsModel::detail($goodsId);
        $specData = GoodsService::getSpecData($goods);
        // 填写商品信息页面
        if (!$this->request->isAjax()) {
            return $this->fetch('step2', compact('goods', 'specData'));
        }
        // 表单提交
        if ($model->add($goods, $this->postData('goods'))) {
            return $this->renderSuccess('添加成功', url('apps.sharp.goods/index'));
        }
        return $this->renderError($model->getError() ?: '添加失败');
    }

    /**
     * 编辑秒杀商品
     * @param $sharp_goods_id
     * @return array|bool|mixed
     * @throws \think\exception\DbException
     * @throws \Exception
     */
    public function edit($sharp_goods_id)
    {
        // 砍价活动详情
        $model = SharpGoodsModel::detail($sharp_goods_id, ['sku']);
        // 商品信息
        $goods = GoodsModel::detail($model['goods_id']);
        // 商品多规格信息
        $specData = $model->getSpecData($goods);
        if (!$this->request->isAjax()) {
            return $this->fetch('edit', compact('model', 'goods', 'specData'));
        }
        // 更新记录
        if ($model->edit($goods, $this->postData('goods'))) {
            return $this->renderSuccess('更新成功', url('apps.sharp.goods/index'));
        }
        return $this->renderError($model->getError() ?: '更新失败');
    }

    /**
     * 删除秒杀商品
     * @param $sharp_goods_id
     * @return array
     * @throws \think\exception\DbException
     */
    public function delete($sharp_goods_id)
    {
        // 秒杀商品详情
        $model = SharpGoodsModel::detail($sharp_goods_id);
        if (!$model->setDelete()) {
            return $this->renderError($model->getError() ?: '删除失败');
        }
        return $this->renderSuccess('删除成功');
    }

}