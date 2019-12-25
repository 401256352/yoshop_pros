<?php

namespace app\api\model\user;

use app\common\model\user\PointsLog as PointsLogModel;

/**
 * 用户余额变动明细模型
 * Class PointsLog
 * @package app\api\model\user
 */
class PointsLog extends PointsLogModel
{
    /**
     * 获取日志明细列表
     * @param $userId
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function getList($userId)
    {
        // 获取列表数据
        return $this->where('user_id', '=', $userId)
            ->order(['create_time' => 'desc'])
            ->paginate(15, false, [
                'query' => \request()->request()
            ]);
    }

}