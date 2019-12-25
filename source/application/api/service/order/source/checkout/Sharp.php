<?php

namespace app\api\service\order\source\checkout;

use app\api\service\sharp\Order as SharpOrderService;

/**
 * 订单结算台-秒杀商品扩展类
 * Class Sharp
 * @package app\api\service\order\source\checkout
 */
class Sharp extends Basics
{
    /**
     * 验证商品列表
     * @return bool
     */
    public function validateGoodsList()
    {
        // 验证商品是否下架
        if (!$this->validateGoodsStatus()) {
            return false;
        }
        // 验证商品限购
        if (!$this->validateLimitNum()) {
            return false;
        }
        // 判断商品库存
        if (!$this->validateGoodsSeckillStock()) {
            return false;
        }
        return true;
    }

    /**
     * 判断商品是否下架
     * @return bool
     */
    private function validateGoodsStatus()
    {
        foreach ($this->goodsList as $goods) {
            if ($goods['is_delete'] || !$goods['status']) {
                $this->error = "很抱歉，商品 [{$goods['goods_name']}] 已下架";
                return false;
            }
        }
        return true;
    }

    /**
     * 判断商品是否下架
     * @return bool
     */
    private function validateGoodsSeckillStock()
    {
        foreach ($this->goodsList as $goods) {
            if ($goods['total_num'] > $goods['goods_sku']['seckill_stock']) {
                $this->error = "很抱歉，商品 [{$goods['goods_name']}] 库存不足";
                return false;
            }
        }
        return true;
    }

    /**
     * 验证商品限购
     * @return bool
     */
    public function validateLimitNum()
    {
        foreach ($this->goodsList as $goods) {
            // 不限购
            if ($goods['limit_num'] <= 0) return true;
            // 获取用户已下单的件数（未取消 订单来源）
            $alreadyBuyNum = SharpOrderService::getAlreadyBuyNum($this->user['user_id'], $goods['goods_id']);
            // 情况1: 已购买0件, 实际想购买5件
            if ($alreadyBuyNum == 0 && $goods['total_num'] > $goods['limit_num']) {
                $this->error = "很抱歉，该商品限购{$goods['limit_num']}件，请修改购买数量";
                return false;
            }
            // 情况2: 已购买3件, 实际想购买1件
            if ($alreadyBuyNum >= $goods['limit_num']) {
                $this->error = "很抱歉，该商品限购{$goods['limit_num']}件，您当前已下单{$alreadyBuyNum}件，无法购买";
                return false;
            }
            // 情况3: 已购买2件, 实际想购买2件
            if (($alreadyBuyNum + $goods['total_num']) > $goods['limit_num']) {
                $diffNum = ($alreadyBuyNum + $goods['total_num']) - $goods['limit_num'];
                $this->error = "很抱歉，该商品限购{$goods['limit_num']}件，您最多能再购买{$diffNum}件";
                return false;
            }
        }
        return true;
    }

}