<?php

namespace app\common\model\bargain;

use think\Hook;
use app\common\model\BaseModel;
use app\common\library\helper;

/**
 * 砍价任务模型
 * Class Task
 * @package app\common\model\bargain
 */
class Task extends BaseModel
{
    protected $name = 'bargain_task';
    protected $alias = 'task';

    protected $type = [
        'is_floor' => 'integer',
        'is_buy' => 'integer',
        'status' => 'integer',
        'is_delete' => 'integer',
    ];

    /**
     * 追加的字段
     * @var array $append
     */
    protected $append = [
        'is_end',   // 是否已结束
        'surplus_money',    // 剩余砍价金额
        'bargain_rate', // 砍价进度百分比(0-100)
    ];

    /**
     * 订单模型初始化
     */
    public static function init()
    {
        parent::init();
        // 监听行为管理
        $static = new static;
        Hook::listen('bargain_task', $static);
    }

    /**
     * 关联用户表
     * @return \think\model\relation\BelongsTo
     */
    public function user()
    {
        $module = self::getCalledModule() ?: 'common';
        return $this->BelongsTo("app\\{$module}\\model\\User");
    }

    /**
     * 获取器：任务结束时间
     * @param $value
     * @return false|string
     */
    public function getEndTimeAttr($value)
    {
        return \format_time($value);
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
     * 获取器：剩余砍价金额
     * @param $value
     * @param $data
     * @return false|string
     */
    public function getSurplusMoneyAttr($value, $data)
    {
        $maxCutMoney = helper::bcsub($data['goods_price'], $data['floor_price']);
        return $value ?: helper::bcsub($maxCutMoney, $data['cut_money']);
    }

    /**
     * 获取器：砍价进度百分比
     * @param $value
     * @param $data
     * @return false|string
     */
    public function getBargainRateAttr($value, $data)
    {
        $maxCutMoney = helper::bcsub($data['goods_price'], $data['floor_price']);
        $rate = helper::bcdiv($data['cut_money'], $maxCutMoney) * 100;
        return $value ?: $rate;
    }

    /**
     * 获取器：砍价金额区间
     * @param $value
     * @return mixed
     */
    public function getSectionAttr($value)
    {
        return json_decode($value, true);
    }

    /**
     * 修改器：砍价金额区间
     * @param $value
     * @return string
     */
    public function setSectionAttr($value)
    {
        return json_encode($value);
    }

    /**
     * 砍价任务详情
     * @param $taskId
     * @param array $with
     * @return static|null
     * @throws \think\exception\DbException
     */
    public static function detail($taskId, $with = [])
    {
        $model = static::get($taskId, $with);
        // 标识砍价任务过期
        if (!empty($model) && $model['status'] == 1 && $model->getData('end_time') <= time()) {
            $model->save(['status' => 0]);
        }
        return $model;
    }

}