<?php

namespace app\common\enum\sharp;

use app\common\enum\EnumBasics;

/**
 * 整点秒杀-活动会场状态
 * Class ActiveStatus
 * @package app\common\enum\sharp
 */
class ActiveStatus extends EnumBasics
{
    // 活动状态：已开始
    const ACTIVE_STATE_BEGIN = 10;

    // 活动状态：即将开始
    const ACTIVE_STATE_SOON = 20;

    // 活动状态：预告
    const ACTIVE_STATE_NOTICE = 30;

    /**
     * 获取枚举数据
     * @return array
     */
    public static function data()
    {
        return [
            self::ACTIVE_STATE_BEGIN => [
                'name' => '已开始',
                'value' => self::ACTIVE_STATE_BEGIN,
            ],
            self::ACTIVE_STATE_SOON => [
                'name' => '即将开始',
                'value' => self::ACTIVE_STATE_SOON,
            ],
            self::ACTIVE_STATE_NOTICE => [
                'name' => '预告',
                'value' => self::ACTIVE_STATE_SOON,
            ],
        ];
    }

}