<?php

namespace app\task\behavior;

use think\Cache;
use app\task\model\Setting;
use app\task\model\Order as OrderModel;
use app\task\service\Order as OrderService;
use app\common\enum\OrderType as OrderTypeEnum;
use app\common\service\order\Complete as OrderCompleteService;
use app\common\library\helper;

/**
 * 订单行为管理
 * Class Order
 * @package app\task\behavior
 */
class Order
{
    /* @var \app\task\model\Order $model */
    private $model;

    /* @var \app\task\service\Order $service */
    private $service;

    // 小程序id
    private $wxappId;

    /**
     * 执行函数
     * @param $model
     * @return bool
     */
    public function run($model)
    {
        if (!$model instanceof OrderModel)
            return new OrderModel and false;
        if (!$model::$wxapp_id) return false;
        // 绑定订单模型
        $this->model = $model;
        $this->wxappId = $model::$wxapp_id;
        // 普通订单行为管理
        $this->master();
        return true;
    }

    /**
     * 普通订单行为管理
     * @return bool
     */
    private function master()
    {
        $key = "__task_space__order__{$this->wxappId}";
        if (Cache::has($key)) return true;
        // 获取商城交易设置
        $this->service = new OrderService;
        $config = Setting::getItem('trade');
        $this->model->transaction(function () use ($config) {
            // 未支付订单自动关闭
            $this->close($config['order']['close_days']);
            // 已发货订单自动确认收货
            $this->receive($config['order']['receive_days']);
            // 已完成订单结算
            $this->settled($config['order']['refund_days']);
        });
        Cache::set($key, time(), 3600);
        return true;
    }

    /**
     * 未支付订单自动关闭
     * @param $closeDays
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \Exception
     */
    private function close($closeDays)
    {
        // 取消n天以前的的未付款订单
        if ($closeDays < 1) return false;
        // 截止时间
        $deadlineTime = time() - ((int)$closeDays * 86400);
        // 执行自动关闭
        $this->service->close($deadlineTime);
        // 记录日志
        $this->dologs('close', [
            'close_days' => (int)$closeDays,
            'deadline_time' => $deadlineTime,
            'orderIds' => json_encode($this->service->getCloseOrderIds()),
        ]);
        return true;
    }

    /**
     * 已发货订单自动确认收货
     * @param $receiveDays
     * @return bool|false|int
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    private function receive($receiveDays)
    {
        // 截止时间
        if ($receiveDays < 1) return false;
        $deadlineTime = time() - ((int)$receiveDays * 86400);
        // 条件
        $filter = [
            'pay_status' => 20,
            'delivery_status' => 20,
            'receipt_status' => 10,
            'delivery_time' => ['<=', $deadlineTime]
        ];
        // 订单id集
        $orderIds = $this->model->where($filter)->column('order_id');
        if (!empty($orderIds)) {
            // 更新订单收货状态
            $this->model->onBatchUpdate($orderIds, [
                'receipt_status' => 20,
                'receipt_time' => time(),
                'order_status' => 30
            ]);
            // 批量处理已完成的订单
            $this->onReceiveCompleted($orderIds);
        }
        // 记录日志
        $this->dologs('receive', [
            'receive_days' => (int)$receiveDays,
            'deadline_time' => $deadlineTime,
            'orderIds' => json_encode($orderIds),
        ]);
        return true;
    }

    /**
     * 已完成订单结算
     * @param $refundDays
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \Exception
     */
    private function settled($refundDays)
    {
        // 获取已完成的订单（未累积用户实际消费金额）
        // 条件1：订单状态：已完成
        // 条件2：超出售后期限
        // 条件3：is_settled 为 0
        // 截止时间
        $deadlineTime = time() - ((int)$refundDays * 86400);
        // 查询条件
        $filter = [
            'order_status' => 30,
            'receipt_time' => ['<=', $deadlineTime],     // 此处使用<=，用于兼容自动确认收货后
            'is_settled' => 0
        ];
        // 查询订单列表
        $orderList = $this->model->getList($filter, [
            'goods' => ['refund'],  // 用于计算售后退款金额
        ]);
        // 订单id集
        $orderIds = helper::getArrayColumn($orderList, 'order_id');
        // 订单结算服务
        $OrderCompleteService = new OrderCompleteService(OrderTypeEnum::MASTER);
        !empty($orderIds) && $OrderCompleteService->settled($orderList);
        // 记录日志
        $this->dologs('settled', [
            'refund_days' => (int)$refundDays,
            'deadline_time' => $deadlineTime,
            'orderIds' => json_encode($orderIds),
        ]);
    }

    /**
     * 批量处理已完成的订单
     * @param $orderIds
     * @return bool
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function onReceiveCompleted($orderIds)
    {
        // 获取已完成的订单列表
        $list = $this->model->getList(['order_id' => ['in', $orderIds]], [
            'goods' => ['refund'],  // 用于发放分销佣金
            'user', 'address', 'goods', 'express',  // 用于同步微信好物圈
        ]);
        if ($list->isEmpty()) return false;
        // 执行订单完成后的操作
        $OrderCompleteService = new OrderCompleteService(OrderTypeEnum::MASTER);
        $OrderCompleteService->complete($list, $this->wxappId);
        return true;
    }

    /**
     * 记录日志
     * @param $method
     * @param array $params
     * @return bool|int
     */
    private function dologs($method, $params = [])
    {
        $value = 'behavior Order --' . $method;
        foreach ($params as $key => $val)
            $value .= ' --' . $key . ' ' . $val;
        return log_write($value);
    }

}
