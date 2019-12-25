<?php

namespace app\task\model\sharing;

use app\common\model\sharing\Active as ActiveModel;

/**
 * 拼团拼单模型
 * Class Active
 * @package app\task\model\sharing
 */
class Active extends ActiveModel
{
    /**
     * 获取已过期的拼单列表
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getEndedList()
    {
        return $this->with(['goods', 'users' => ['user', 'sharingOrder']])
            ->where('end_time', '<=', time())
            ->where('status', '=', 10)
            ->select();
    }

    /**
     * 设置拼单失败状态
     * @param $activeIds
     * @return false|int
     */
    public function updateEndedStatus($activeIds)
    {
        if (empty($activeIds)) {
            return false;
        }
        return $this->save(['status' => 30], ['active_id' => ['in', $activeIds]]);
    }

}
