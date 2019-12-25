<?php

namespace app\api\controller\sharp;

use app\api\controller\Controller;
use app\api\service\sharp\Active as ActiveService;

/**
 * 整点秒杀-秒杀首页
 * Class index
 * @package app\api\controller\sharp
 */
class Index extends Controller
{
    /**
     * 秒杀活动首页
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        // 获取秒杀活动会场首页数据
        $service = new ActiveService;
        $data = $service->getHallIndex();
        if (empty($data['tabbar'])) {
            return $this->renderError('很抱歉，暂无秒杀活动');
        }
        return $this->renderSuccess($data);
    }
}