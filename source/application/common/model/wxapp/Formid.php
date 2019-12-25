<?php

namespace app\common\model\wxapp;

use app\common\model\BaseModel;

/**
 * form_id 模型
 * Class Apply
 * @package app\common\model\dealer
 */
class Formid extends BaseModel
{
    protected $name = 'wxapp_formid';

    /**
     * 关联用户表
     * @return \think\model\relation\BelongsTo
     */
    public function user()
    {
        $module = self::getCalledModule() ?: 'common';
        return $this->belongsTo("app\\{$module}\\model\\User");
    }

    /**
     * 获取一个可用的formid
     * @param $userId
     * @return array|false|\PDOStatement|string|\think\Model|static
     */
    public static function getAvailable($userId)
    {
        return (new static)->where([
            'user_id' => $userId,
            'is_used' => 0,
            'expiry_time' => ['>=', time()]
        ])->order(['create_time' => 'asc'])->find();
    }

    /**
     * 标记为已使用
     * @param $id
     * @return Formid
     */
    public static function setIsUsed($id)
    {
        return static::update(['is_used' => 1], compact('id'));
    }

}