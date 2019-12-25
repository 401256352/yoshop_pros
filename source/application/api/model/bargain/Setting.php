<?php

namespace app\api\model\bargain;

use app\common\model\bargain\Setting as SettingModel;

/**
 * 砍价活动设置模型
 * Class Setting
 * @package app\api\model\bargain
 */
class Setting extends SettingModel
{
    /**
     * 获取砍价基本配置
     * @return array
     */
    public static function getBasic()
    {
        return [
            // 砍价规则
            'bargain_rules' => static::getItem('basic')['bargain_rules'],
        ];
    }

    /**
     * 获取是否开启分销
     * @return array
     */
    public static function getIsDealer()
    {
        return static::getItem('basic')['is_dealer'];
    }

}