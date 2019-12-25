<?php

namespace app\common\enum\sharp;

use app\common\enum\EnumBasics;

/**
 * 整点秒杀-活动商品状态
 * Class GoodsStatus
 * @package app\common\enum\sharp
 */
class GoodsStatus extends EnumBasics
{
    // 活动状态：已开始
    const STATE_BEGIN = 10;

    // 活动状态：未开始
    const STATE_SOON = 20;

    // 活动状态：已结束
    const STATE_END = 30;

    /**
     * 获取枚举数据
     * @return array
     */
    public static function data()
    {
        return [
            self::STATE_BEGIN => [
                'name' => '已开始',
                'value' => self::STATE_BEGIN,
            ],
            self::STATE_SOON => [
                'name' => '未开始',
                'value' => self::STATE_SOON,
            ],
            self::STATE_END => [
                'name' => '已结束',
                'value' => self::STATE_END,
            ],
        ];
    }

}