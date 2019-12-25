<?php

namespace app\api\model\sharp;

use app\common\model\sharp\Setting as SettingModel;

/**
 * 整点秒杀设置模型
 * Class Setting
 * @package app\api\model\sharp
 */
class Setting extends SettingModel
{
    /**
     * 获取是否开启分销
     * @return array
     */
    public static function getIsDealer()
    {
        return static::getItem('basic')['is_dealer'];
    }

}