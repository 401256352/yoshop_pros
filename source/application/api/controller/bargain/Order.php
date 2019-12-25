<?php

namespace app\api\controller\bargain;

use app\api\controller\Controller;
use app\api\model\bargain\Task as TaskModel;
use app\api\model\bargain\Setting as SettingModel;
use app\api\service\order\Checkout as CheckoutModel;
use app\common\enum\order\OrderSource as OrderSourceEnum;

class Order extends Controller
{
    /* @var \app\api\model\User $user */
    private $user;

    /**
     * 构造方法
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function _initialize()
    {
        parent::_initialize();
        // 用户信息
        $this->user = $this->getUser();
    }

    /**
     * 砍价订单结算
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \Exception
     */
    public function checkout()
    {
        // 实例化结算台服务
        $Checkout = new CheckoutModel;
        // 订单结算api参数
        $params = $Checkout->setParam($this->getParam([
            'task_id' => 0
        ]));
        // 获取砍价任务详情
        $task = TaskModel::detail($params['task_id']);
        // 获取砍价商品信息
        $goodsList = $task->getTaskGoods($params['task_id']);
        if ($goodsList === false) {
            return $this->renderError($task->getError());
        }
        // 设置订单来源
        $Checkout->setOrderSource([
            'source' => OrderSourceEnum::BARGAIN,
            'source_id' => $params['task_id'],
        ]);
        // 砍价商品不参与 等级折扣和优惠券折扣
        $Checkout->setCheckoutRule([
            'is_user_grade' => false,
            'is_coupon' => false,
            'is_use_points' => false,
            'is_dealer' => SettingModel::getIsDealer(),
        ]);
        // 获取订单结算信息
        $orderInfo = $Checkout->onCheckout($this->user, $goodsList);
        if ($this->request->isGet()) {
            return $this->renderSuccess($orderInfo);
        }
        // submit：订单结算提交
        if ($Checkout->hasError()) {
            return $this->renderError($Checkout->getError());
        }
        // 创建订单
        if (!$Checkout->createOrder($orderInfo)) {
            return $this->renderError($Checkout->getError() ?: '订单创建失败');
        }
        // 订单创建后将砍价任务结束
        $task->setTaskEnd();
        // 构建微信支付请求
        $payment = $Checkout->onOrderPayment();
        // 支付状态提醒
        $message = ['success' => '支付成功', 'error' => '订单未支付'];
        return $this->renderSuccess([
            'order_id' => $Checkout->model['order_id'],   // 订单id
            'pay_type' => $params['pay_type'],  // 支付方式
            'payment' => $payment               // 微信支付参数
        ], $message);
    }

    /**
     * 订单结算提交的参数
     * @param array $define
     * @return array
     */
    private function getParam($define = [])
    {
        return array_merge($define, $this->request->param());
    }

}