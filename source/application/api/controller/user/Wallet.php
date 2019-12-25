<?php

namespace app\api\controller\user;

use app\api\controller\Controller;
use app\api\model\Setting as SettingModel;

class Wallet extends Controller
{
    /* @var \app\api\model\User $user */
    private $user;

    /**
     * 构造方法
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->user = $this->getUser();   // 用户信息
    }

    /**
     * 我的钱包信息
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        // 当前用户信息
        $user = $this->getUser();
        // 获取充值设置
        $setting = SettingModel::getItem('recharge');
        return $this->renderSuccess([
            'userInfo' => $user,
            'setting' => [
                'is_entrance' => (bool)$setting['is_entrance']
            ]
        ]);
    }

}