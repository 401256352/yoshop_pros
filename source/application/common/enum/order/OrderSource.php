<?php

namespace app\common\enum\order;

use app\common\enum\EnumBasics;

/**
 * 订单来源
 * Class OrderSource
 * @package app\common\enum\order
 */
class OrderSource extends EnumBasics
{
    // 普通订单
    const MASTER = 10;

    // 砍价订单
    const BARGAIN = 20;

    // 秒杀订单
    const SHARP = 30;

    /**
     * 获取枚举数据
     * @return array
     */
    public static function data()
    {
        return [
            self::MASTER => [
                'name' => '普通订单',
                'value' => self::MASTER,
            ],
            self::BARGAIN => [
                'name' => '砍价订单',
                'value' => self::BARGAIN,
            ],
            self::SHARP => [
                'name' => '秒杀订单',
                'value' => self::SHARP,
            ],
        ];
    }

}