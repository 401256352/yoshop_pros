<?php

namespace app\api\controller\bargain;

use app\api\controller\Controller;
use app\api\model\Goods as GoodsModel;
use app\api\model\bargain\Active as ActiveModel;
use app\api\model\bargain\Setting as SettingModel;
use app\common\service\qrcode\bargain\Goods as GoodsPoster;

/**
 * 砍价活动管理
 * Class Active
 * @package app\api\controller\bargain
 */
class Active extends Controller
{
    /**
     * 砍价活动会场列表
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function lists()
    {
        // 获取砍价活动会场列表
        $model = new ActiveModel;
        $activeList = $model->getHallList();
        return $this->renderSuccess(compact('activeList'));
    }

    /**
     * 砍价活动详情
     * @param $active_id
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function detail($active_id)
    {
        // 获取砍价活动详情
        $model = new ActiveModel;
        $active = $model->getDetail($active_id);
        if ($active === false) {
            return $this->renderError($model->getError());
        }
        // 标记当前用户是否正在参与
        $task_id = $model->getWhetherPartake($active_id, $this->getUser(false));
        $is_partake = $task_id > 0;
        // 获取商品详情
        $goods = GoodsModel::detail($active['goods_id']);
        // 砍价规则
        $setting = SettingModel::getBasic();
        return $this->renderSuccess(compact('active', 'goods', 'setting', 'is_partake', 'task_id'));
    }

    /**
     * 生成商品海报
     * @param $active_id
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     * @throws \Exception
     */
    public function poster($active_id)
    {
        // 获取砍价活动详情
        $model = new ActiveModel;
        $active = $model->getDetail($active_id);
        if ($active === false) {
            return $this->renderError($model->getError());
        }
        // 获取商品详情
        $goods = GoodsModel::detail($active['goods_id']);
        // 生成商品海报图
        $Qrcode = new GoodsPoster($active, $goods, $this->getUser(false));
        return $this->renderSuccess([
            'qrcode' => $Qrcode->getImage(),
        ]);
    }

}