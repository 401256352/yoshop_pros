<?php

namespace app\api\controller\sharing;

use app\api\controller\Controller;
use app\api\model\sharing\Goods as GoodsModel;
use app\common\service\qrcode\Goods as GoodsPoster;
use app\api\model\sharing\Active as ActiveModel;

/**
 * 商品控制器
 * Class Goods
 * @package app\api\controller
 */
class Goods extends Controller
{
    /**
     * 商品列表
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function lists()
    {
        // 整理请求的参数
        $param = array_merge($this->request->param(), [
            'status' => 10
        ]);
        // 获取列表数据
        $model = new GoodsModel;
        $list = $model->getList($param, $this->getUser(false));
        return $this->renderSuccess(compact('list'));
    }

    /**
     * 获取商品详情
     * @param $goods_id
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function detail($goods_id)
    {
        // 商品详情
        $model = new GoodsModel;
        $goods = $model->getDetails($goods_id, $this->getUser(false));
        if ($goods === false) {
            return $this->renderError($model->getError() ?: '商品信息不存在');
        }
        // 多规格商品sku信息, todo: 已废弃 v1.1.25
        $specData = $goods['spec_type'] == 20 ? $model->getManySpecData($goods['spec_rel'], $goods['sku']) : null;
        // 当前进行中的拼单
        $activeList = ActiveModel::getActivityListByGoods($goods_id, 2);
        return $this->renderSuccess([
            // 商品详情
            'detail' => $goods,
            // 当前进行中的拼单
            'activeList' => $activeList,
            // 多规格商品sku信息
            'specData' => $specData,
        ]);
    }

    /**
     * 生成商品海报
     * @param $goods_id
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     * @throws \Exception
     */
    public function poster($goods_id)
    {
        // 商品详情
        $detail = GoodsModel::detail($goods_id);
        // 生成推广二维码
        $Qrcode = new GoodsPoster($detail, $this->getUser(false), 20);
        return $this->renderSuccess([
            'qrcode' => $Qrcode->getImage(),
        ]);
    }

}
