<?php

namespace app\common\model\sharp;

use app\common\model\BaseModel;

/**
 * 整点秒杀-活动会场场次模型
 * Class ActiveTime
 * @package app\common\model\sharp
 */
class ActiveTime extends BaseModel
{
    protected $name = 'sharp_active_time';

    /**
     * 关联活动会场表
     * @return \think\model\relation\BelongsTo
     */
    public function active()
    {
        return $this->belongsTo('Active', 'active_id');
    }

    /**
     * 当前场次下秒杀商品的数量
     * @return \think\model\relation\HasMany
     */
    public function goods()
    {
        return $this->hasMany('ActiveGoods', 'active_time_id');
    }

    /**
     * 获取器：活动场次时间
     * @param $value
     * @return string
     */
    public function getActiveTimeAttr($value)
    {
        return \pad_left($value) . ':00';
    }

    /**
     * 活动会场场次详情
     * @param $activeTimeId
     * @param array $with
     * @return static|null
     * @throws \think\exception\DbException
     */
    public static function detail($activeTimeId, $with = [])
    {
        return static::get($activeTimeId, $with);
    }

}