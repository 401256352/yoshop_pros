

# 分销商订单记录表：新增 "订单是否失效" 字段
ALTER TABLE `yoshop_dealer_order`
ADD COLUMN `is_invalid`  tinyint(3) NOT NULL DEFAULT 0 COMMENT '订单是否失效 (0未失效 1已失效)' AFTER `third_money`;


# 门店记录表：新增 "排序" 字段
ALTER TABLE `yoshop_store_shop`
ADD COLUMN `sort`  tinyint(3) NOT NULL DEFAULT 0 COMMENT '门店排序(数字越小越靠前)' AFTER `summary`;

