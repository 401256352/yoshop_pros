<?php

namespace app\task\service;

use app\common\service\Basics;
use app\task\model\Order as OrderModel;
use app\task\model\UserCoupon as UserCouponModel;
use app\common\library\helper;
use app\common\service\goods\source\Factory as FactoryStock;

class Order extends Basics
{
    /* @var \app\task\model\Order $model */
    private $model;

    // 自动关闭的订单id集
    private $closeOrderIds = [];

    /**
     * 构造方法
     * Order constructor.
     */
    public function __construct()
    {
        $this->model = new OrderModel;
    }

    /**
     * 未支付订单自动关闭
     * @param int $deadlineTime
     * @param array $where
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \Exception
     */
    public function close($deadlineTime, $where = [])
    {
        // 条件
        $filter = array_merge($where, [
            'pay_status' => 10,
            'order_status' => 10,
            'create_time' => ['<=', $deadlineTime]
        ]);
        // 查询截止时间未支付的订单
        $list = $this->model->getList($filter, ['goods', 'user']);
        $this->closeOrderIds = helper::getArrayColumn($list, 'order_id');
        // 取消订单事件
        if (!empty($this->closeOrderIds)) {
            foreach ($list as &$order) {
                // 回退商品库存
                FactoryStock::getFactory($order['order_source'])->backGoodsStock($order['goods'], false);
                // 回退用户优惠券
                $order['coupon_id'] > 0 && UserCouponModel::setIsUse($order['coupon_id'], false);
                // 回退用户积分
                $describe = "订单取消：{$order['order_no']}";
                $order['points_num'] > 0 && $order->user->setIncPoints($order['points_num'], $describe);
            }
            // 批量更新订单状态为已取消
            return $this->model->onBatchUpdate($this->closeOrderIds, ['order_status' => 20]);
        }
        return true;
    }

    /**
     * 获取自动关闭的订单id集
     * @return array
     */
    public function getCloseOrderIds()
    {
        return $this->closeOrderIds;
    }

}