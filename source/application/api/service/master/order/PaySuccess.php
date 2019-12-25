<?php

namespace app\api\service\master\order;

use app\api\service\Basics;
use app\common\library\helper;
use app\api\model\dealer\Apply as DealerApplyModel;

/**
 * 普通订单支付成功后的回调
 * Class PaySuccess
 * @package app\api\service\master\order
 */
class PaySuccess extends Basics
{
    /**
     * 回调方法
     * @param $order
     * @return bool
     * @throws \think\exception\DbException
     */
    public function onPaySuccess($order)
    {
        // 购买指定商品成为分销商
        $this->becomeDealerUser($order);
        return true;
    }

    /**
     * 购买指定商品成为分销商
     * @param $order
     * @return bool
     * @throws \think\exception\DbException
     */
    private function becomeDealerUser($order)
    {
        // 整理商品id集
        $goodsIds = helper::getArrayColumn($order['goods'], 'goods_id');
        $model = new DealerApplyModel;
        return $model->becomeDealerUser($order['user_id'], $goodsIds, $order['wxapp_id']);
    }

}