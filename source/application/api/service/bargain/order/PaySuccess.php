<?php

namespace app\api\service\bargain\order;

use app\api\model\bargain\Task as TaskModel;
use app\api\model\bargain\Active as ActiveModel;

use app\api\service\Basics;

/**
 * 砍价订单支付成功后的回调
 * Class PaySuccess
 * @package app\api\service\bargain\order
 */
class PaySuccess extends Basics
{
    /**
     * 回调方法
     * @param $order
     * @return bool
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function onPaySuccess($order)
    {
        // 砍价任务详情
        $task = TaskModel::detail($order['order_source_id']);
        if (empty($task)) {
            $this->error = '未找到砍价任务信息';
            return false;
        }
        // 标记为已购买
        $task->setIsBuy();
        // 砍价活动详情
        $active = ActiveModel::detail($task['active_id']);
        if (empty($active)) {
            $this->error = '未找到砍价活动信息';
            return false;
        }
        // 累计活动销量
        $active->setIncSales();
        return true;
    }

}