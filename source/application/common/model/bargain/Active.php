<?php

namespace app\common\model\bargain;

use app\common\model\BaseModel;

/**
 * 砍价活动模型
 * Class Active
 * @package app\common\model\bargain
 */
class Active extends BaseModel
{
    protected $name = 'bargain_active';
    protected $alias = 'active';

    protected $type = [
        'is_self_cut' => 'integer',
        'is_floor_buy' => 'integer',
        'status' => 'integer',
    ];

    /**
     * 追加的字段
     * @var array $append
     */
    protected $append = [
        'is_start',   // 活动已开启
        'is_end',   // 活动已结束
        'active_sales', // 活动销量
    ];

    /**
     * 获取器：活动开始时间
     * @param $value
     * @return false|string
     */
    public function getStartTimeAttr($value)
    {
        return \format_time($value);
    }

    /**
     * 获取器：活动结束时间
     * @param $value
     * @return false|string
     */
    public function getEndTimeAttr($value)
    {
        return \format_time($value);
    }

    /**
     * 获取器：活动是否已开启
     * @param $value
     * @param $data
     * @return false|string
     */
    public function getIsStartAttr($value, $data)
    {
        return $value ?: $data['start_time'] <= time();
    }

    /**
     * 获取器：活动是否已结束
     * @param $value
     * @param $data
     * @return false|string
     */
    public function getIsEndAttr($value, $data)
    {
        return $value ?: $data['end_time'] <= time();
    }

    /**
     * 获取器：显示销量
     * @param $value
     * @param $data
     * @return false|string
     */
    public function getActiveSalesAttr($value, $data)
    {
        return $value ?: $data['actual_sales'] + $data['initial_sales'];
    }

    /**
     * 砍价活动详情
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