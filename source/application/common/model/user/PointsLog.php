<?php

namespace app\common\model\user;

use app\common\model\BaseModel;

/**
 * 用户积分变动明细模型
 * Class PointsLog
 * @package app\common\model\user
 */
class PointsLog extends BaseModel
{
    protected $name = 'user_points_log';
    protected $updateTime = false;

    /**
     * 关联会员记录表
     * @return \think\model\relation\BelongsTo
     */
    public function user()
    {
        $module = self::getCalledModule() ?: 'common';
        return $this->belongsTo("app\\{$module}\\model\\User");
    }

    /**
     * 新增记录
     * @param $data
     */
    public static function add($data)
    {
        $static = new static;
        $static->save(array_merge(['wxapp_id' => $static::$wxapp_id], $data));
    }

    /**
     * 新增记录 (批量)
     * @param $saveData
     * @return array|false
     * @throws \Exception
     */
    public function onBatchAdd($saveData)
    {
        return $this->isUpdate(false)->saveAll($saveData);
    }

}