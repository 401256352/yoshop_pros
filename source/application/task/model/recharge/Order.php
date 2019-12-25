<?php

namespace app\task\model\recharge;

use app\common\model\recharge\Order as OrderModel;

use app\task\model\User as UserModel;
use app\task\model\user\BalanceLog as BalanceLogModel;
use app\task\model\WxappPrepayId as WxappPrepayIdModel;

use app\common\enum\OrderType as OrderTypeEnum;
use app\common\enum\order\PayType as PayTypeEnum;
use app\common\enum\user\balanceLog\Scene as SceneEnum;
use app\common\enum\recharge\order\PayStatus as PayStatusEnum;

/**
 * 用户充值订单模型
 * Class Order
 * @package app\task\model\recharge
 */
class Order extends OrderModel
{

}