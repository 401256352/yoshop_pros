<?php

namespace app\common\model\sharp;

use app\common\model\BaseModel;

/**
 * 整点秒杀-活动会场模型
 * Class Active
 * @package app\common\model\sharp
 */
class Active extends BaseModel
{
    protected $name = 'sharp_active';

    /**
     * 关联活动场次表
     * @return \think\model\relation\HasMany
     */
    public function activeTime()
    {
        return $this->hasMany('ActiveTime', 'active_id')
            ->order(['active_time' => 'asc']);
    }

    /**
     * 活动会场详情
     * @param $activeId
     * @param array $with
     * @return static|null
     * @throws \think\exception\DbException
     */
    public static function detail($activeId, $with = [])
    {
        return static::get($activeId, $with);
    }

}