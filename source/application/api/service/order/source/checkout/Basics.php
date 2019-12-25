<?php

namespace app\api\service\order\source\checkout;

use app\api\model\User as UserModel;

/**
 * 订单结算台扩展基类
 * Class Basics
 * @package app\api\service\order\source\checkout
 */
abstract class Basics extends \app\api\service\Basics
{
    /* @var UserModel $user 当前用户信息 */
    protected $user;

    // 订单结算商品列表
    protected $goodsList = [];

    /**
     * 构造方法
     * Checkout constructor.
     * @param UserModel $user
     * @param array $goodsList
     */
    public function __construct($user, $goodsList)
    {
        $this->user = $user;
        $this->goodsList = $goodsList;
    }

    /**
     * 验证商品列表
     * @return mixed
     */
    abstract public function validateGoodsList();

}