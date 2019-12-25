<?php

namespace app\api\controller\sharp;

use app\api\controller\Controller;
use app\api\service\sharp\Active as ActiveService;
use app\common\service\qrcode\sharp\Goods as GoodsPoster;

/**
 * 整点秒杀-商品管理
 * Class Goods
 * @package app\api\controller\sharp
 */
class Goods extends Controller
{
    /**
     * 秒杀活动商品列表
     * @param $active_time_id
     * @return array
     */
    public function lists($active_time_id)
    {
        // 获取秒杀活动会场首页数据
        $service = new ActiveService;
        $list = $service->getGoodsListByActiveTimeId($active_time_id);
        return $this->renderSuccess(compact('list'));
    }

    /**
     * 获取活动商品详情
     * @param $active_time_id
     * @param $sharp_goods_id
     * @return array
     * @throws \think\exception\DbException
     */
    public function detail($active_time_id, $sharp_goods_id)
    {
        // 获取秒杀活动商品详情
        $service = new ActiveService;
        $data = $service->getyActiveGoodsDetail($active_time_id, $sharp_goods_id);
        if ($data === false) {
            return $this->renderError($service->getError());
        }
        return $this->renderSuccess($data);
    }

    /**
     * 生成商品海报
     * @param $active_time_id
     * @param $sharp_goods_id
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function poster($active_time_id, $sharp_goods_id)
    {
        // 获取秒杀活动商品详情
        $service = new ActiveService;
        $data = $service->getyActiveGoodsDetail($active_time_id, $sharp_goods_id);
        if ($data === false) {
            return $this->renderError($service->getError());
        }
        // 生成商品海报图
        $Qrcode = new GoodsPoster($data['active'], $data['goods'], $this->getUser(false));
        return $this->renderSuccess([
            'qrcode' => $Qrcode->getImage(),
        ]);
    }

}