<?php

namespace app\api\service\bargain;

class Amount
{
    /**
     * 砍价金额
     *
     * @var float
     */
    protected $amount;

    /**
     * 砍价人数
     *
     * @var int
     */
    protected $num;

    /**
     * 砍价的最小金额
     *
     * @var float
     */
    protected $coupon_min;

    /**
     * 砍价分配结果
     *
     * @var array
     */
    protected $items = [];

    /**
     * 初始化
     * @param float $amount 砍价金额（单位：元）最多保留2位小数
     * @param int $num 砍价个数
     * @param float $coupon_min 每个至少领取的砍价金额
     */
    public function __construct($amount, $num = 1, $coupon_min = 0.01)
    {
        $this->amount = $amount;
        $this->num = $num;
        $this->coupon_min = $coupon_min;
    }

    /**
     * 处理返回
     * @return array
     * @throws \Exception
     */
    public function handle()
    {
        // A. 验证
        if ($this->amount < $validAmount = $this->coupon_min * $this->num) {
            throw new \Exception('砍价总金额必须≥' . $validAmount . '元');
        }
        // B. 分配砍价
        $this->apportion();
        return [
            'items' => $this->items,
        ];
    }

    /**
     * 分配砍价
     */
    protected function apportion()
    {
        $num = $this->num;  // 剩余可分配的砍价个数
        $amount = $this->amount;  //剩余可领取的砍价金额
        while ($num >= 1) {
            // 剩余一个的时候，直接取剩余砍价
            if ($num == 1) {
                $coupon_amount = $this->decimal_number($amount);
            } else {
                $avg_amount = $this->decimal_number($amount / $num);  // 剩余的砍价的平均金额
                $coupon_amount = $this->decimal_number(
                    $this->calcCouponAmount($avg_amount, $amount, $num)
                );
            }
            $this->items[] = $coupon_amount; // 追加分配
            $amount -= $coupon_amount;
            --$num;
        }
        shuffle($this->items);  // 随机打乱
    }

    /**
     * 计算分配的砍价金额
     * @param float $avg_amount 每次计算的平均金额
     * @param float $amount 剩余可领取金额
     * @param int $num 剩余可领取的砍价个数
     *
     * @return float
     */
    protected function calcCouponAmount($avg_amount, $amount, $num)
    {
        // 如果平均金额小于等于最低金额，则直接返回最低金额
        if ($avg_amount <= $this->coupon_min) {
            return $this->coupon_min;
        }
        // 浮动计算
        $coupon_amount = $this->decimal_number($avg_amount * (1 + $this->apportionRandRatio()));
        // 如果低于最低金额或超过可领取的最大金额，则重新获取
        if ($coupon_amount < $this->coupon_min
            || $coupon_amount > $this->calcCouponAmountMax($amount, $num)
        ) {
            return $this->calcCouponAmount($avg_amount, $amount, $num);
        }
        return $coupon_amount;
    }

    /**
     * 计算分配的砍价金额-可领取的最大金额
     * @param $amount
     * @param $num
     * @return float|int
     */
    protected function calcCouponAmountMax($amount, $num)
    {
        return $this->coupon_min + $amount - $num * $this->coupon_min;
    }

    /**
     * 砍价金额浮动比例
     */
    protected function apportionRandRatio()
    {
        // 60%机率获取剩余平均值的大幅度砍价（可能正数、可能负数）
        if (rand(1, 100) <= 60) {
            return rand(-70, 70) / 100; // 上下幅度70%
        }
        return rand(-30, 30) / 100; // 其他情况，上下浮动30%；
    }

    /**
     * 格式化金额，保留2位
     * @param float $amount
     * @return float
     */
    protected function decimal_number($amount)
    {
        return sprintf('%01.2f', round($amount, 2));
    }
}