<?php

namespace app\task\model\sharing;

use app\common\model\sharing\Order as OrderModel;
use app\common\service\order\Refund as RefundService;

/**
 * 拼团订单模型
 * Class Order
 * @package app\common\model\sharing
 */
class Order extends OrderModel
{
    /**
     * 获取订单列表
     * @param array $filter
     * @param array $with
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getList($filter = [], $with = [])
    {
        return $this->with($with)
            ->where($filter)
            ->where('is_delete', '=', 0)
            ->select();
    }

    /**
     * 获取拼团失败的订单
     * @param int $limit
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getFailedOrderList($limit = 100)
    {
        return $this->alias('order')
            ->join('sharing_active active', 'order.active_id = active.active_id', 'INNER')
            ->where('order_type', '=', 20)
            ->where('pay_status', '=', 20)
            ->where('order_status', '=', 10)
            ->where('active.status', '=', 30)
            ->where('is_refund', '=', 0)
            ->where('order.is_delete', '=', 0)
            ->limit($limit)
            ->select();
    }

    /**
     * 更新拼团失败的订单并退款
     * @param $orderList
     * @return bool
     */
    public function updateFailedStatus($orderList)
    {
        // 批量更新订单状态
        foreach ($orderList as $order) {
            /* @var static $order */
            try {
                // 执行退款操作
                (new RefundService)->execute($order);
                // 更新订单状态
                $order->save([
                    'is_refund' => 1,
                    'order_status' => '20'
                ]);
            } catch (\Exception $e) {
                $this->error = '订单ID：' . $order['order_id'] . ' 退款失败，错误信息：' . $e->getMessage();
                return false;
            }
        }
        return true;
    }

}
