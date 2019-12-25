<?php

namespace app\store\model\user;

use app\common\model\user\PointsLog as PointsLogModel;

/**
 * 用户余额变动明细模型
 * Class PointsLog
 * @package app\store\model\user
 */
class PointsLog extends PointsLogModel
{
    /**
     * 获取积分明细列表
     * @param array $query
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function getList($query = [])
    {
        // 设置查询条件
        !empty($query) && $this->setQueryWhere($query);
        // 获取列表数据
        return $this->with(['user'])
            ->alias('log')
            ->field('log.*')
            ->join('user', 'user.user_id = log.user_id')
            ->order(['log.create_time' => 'desc'])
            ->paginate(15, false, [
                'query' => \request()->request()
            ]);
    }

    /**
     * 设置查询条件
     * @param $query
     */
    private function setQueryWhere($query)
    {
        // 设置默认的检索数据
        $params = $this->setQueryDefaultValue($query, [
            'user_id' => 0,
            'search' => '',
            'start_time' => '',
            'end_time' => '',
        ]);
        // 用户ID
        $params['user_id'] > 0 && $this->where('log.user_id', '=', $params['user_id']);
        // 用户昵称
        !empty($params['search']) && $this->where('user.nickName', 'like', "%{$params['search']}%");
        // 起始时间
        !empty($params['start_time']) && $this->where('log.create_time', '>=', strtotime($params['start_time']));
        // 截止时间
        !empty($params['end_time']) && $this->where('log.create_time', '<', strtotime($params['end_time']) + 86400);
    }

}