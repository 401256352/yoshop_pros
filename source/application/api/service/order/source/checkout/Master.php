<?php

namespace app\api\service\order\source\checkout;

/**
 * 订单结算台-普通商品扩展类
 * Class Checkout
 * @package app\api\service\master
 */
class Master extends Basics
{
    /**
     * 验证商品列表
     * @return bool
     */
    public function validateGoodsList()
    {
        foreach ($this->goodsList as $goods) {
            // 判断商品是否下架
            if ($goods['goods_status']['value'] != 10) {
                $this->error = "很抱歉，商品 [{$goods['goods_name']}] 已下架";
                return false;
            }
            // 判断商品库存
            if ($goods['total_num'] > $goods['goods_sku']['stock_num']) {
                $this->error = "很抱歉，商品 [{$goods['goods_name']}] 库存不足";
                return false;
            }
        }
        return true;
    }

}