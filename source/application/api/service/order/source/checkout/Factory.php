<?php

namespace app\api\service\order\source\checkout;

use app\api\service\Basics;
use app\common\enum\order\OrderSource as OrderSourceEnum;

/**
 * 订单结算台扩展工厂类
 * Class Factory
 * @package app\common\service\stock
 */
class Factory extends Basics
{
    // 订单来源的结算台服务类
    private static $class = [
        OrderSourceEnum::MASTER => 'Master',
        OrderSourceEnum::BARGAIN => 'Bargain',
        OrderSourceEnum::SHARP => 'Sharp',
    ];

    /**
     * 根据订单来源获取商品库存类
     * @param $user
     * @param $goodsList
     * @param int $orderSource
     * @return mixed
     */
    public static function getFactory($user, $goodsList, $orderSource = OrderSourceEnum::MASTER)
    {
        $className = __NAMESPACE__ . '\\' . static::$class[$orderSource];
        return new $className($user, $goodsList);
    }

}