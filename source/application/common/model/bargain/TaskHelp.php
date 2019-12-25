<?php

namespace app\common\model\bargain;

use app\common\model\BaseModel;

/**
 * 砍价任务助力记录模型
 * Class TaskHelp
 * @package app\common\model\bargain
 */
class TaskHelp extends BaseModel
{
    protected $name = 'bargain_task_help';
    protected $updateTime = false;

    /**
     * 关联用户表
     * @return \think\model\relation\BelongsTo
     */
    public function user()
    {
        $module = self::getCalledModule() ?: 'common';
        return $this->BelongsTo("app\\{$module}\\model\\User")
            ->field(['user_id', 'nickName', 'avatarUrl']);
    }

}