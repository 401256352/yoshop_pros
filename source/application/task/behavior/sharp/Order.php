<?php

namespace app\task\behavior\sharp;

use think\Cache;
use app\task\model\Order as OrderModel;
use app\task\model\sharp\Setting as SettingModel;
use app\task\service\Order as OrderService;
use app\common\enum\order\OrderSource as OrderSourceEnum;

/**
 * 订单行为管理
 * Class Order
 * @package app\task\behavior
 */
class Order
{
    /* @var \app\task\model\Order $model */
    private $model;

    // 小程序id
    private $wxappId;

    /**
     * 执行函数
     * @param $model
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function run($model)
    {
        if (!$model instanceof OrderModel)
            return new OrderModel and false;
        if (!$model::$wxapp_id) return false;
        // 绑定订单模型
        $this->model = $model;
        $this->wxappId = $model::$wxapp_id;
        // 秒杀订单行为管理
        $this->sharp();
        return true;
    }

    /**
     * 秒杀订单行为管理
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function sharp()
    {
        // 未支付订单自动关闭
        $this->close();
    }

    /**
     * 未支付订单自动关闭
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \Exception
     */
    private function close()
    {
        $key = "__task_space__sharp_order__{$this->wxappId}";
        if (Cache::has($key)) return true;
        // 取消n分钟以前的的未付款订单
        $minute = SettingModel::getItem('basic')['order']['order_close'];
        if ($minute < 1) return false;
        // 截止时间
        $deadlineTime = time() - ((int)$minute * 60);
        // 执行自动关闭
        $service = new OrderService;
        $service->close($deadlineTime, ['order_source' => OrderSourceEnum::SHARP]);
        // 记录日志
        $this->dologs('close', [
            'close_minute' => (int)$minute,
            'deadline_time' => $deadlineTime,
            'orderIds' => json_encode($service->getCloseOrderIds()),
        ]);
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
        $value = 'behavior sharp Order --' . $method;
        foreach ($params as $key => $val)
            $value .= ' --' . $key . ' ' . $val;
        return log_write($value);
    }

}
