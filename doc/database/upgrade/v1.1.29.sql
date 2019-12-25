
ALTER TABLE `yoshop_user`
ADD COLUMN `points`  int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户可用积分' AFTER `balance`;



CREATE TABLE `yoshop_user_points_log` (
  `log_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `value` int(11) NOT NULL DEFAULT '0.00' COMMENT '变动数量',
  `describe` varchar(500) NOT NULL DEFAULT '' COMMENT '描述/说明',
  `remark` varchar(500) NOT NULL DEFAULT '' COMMENT '管理员备注',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序商城id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='用户积分变动明细表';



ALTER TABLE `yoshop_order`
ADD COLUMN `points_money`  decimal(10,2) UNSIGNED NOT NULL DEFAULT 0 COMMENT '积分抵扣金额' AFTER `coupon_money`;

ALTER TABLE `yoshop_order`
ADD COLUMN `points_num`  int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '积分抵扣数量' AFTER `points_money`;

ALTER TABLE `yoshop_order`
ADD COLUMN `points_bonus`  int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '赠送的积分数量' AFTER `order_status`;

ALTER TABLE `yoshop_order`
CHANGE COLUMN `is_user_expend` `is_settled`  tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '订单是否已结算(0未结算 1已结算)' AFTER `points_bonus`;



ALTER TABLE `yoshop_sharing_order`
ADD COLUMN `points_money`  decimal(10,2) UNSIGNED NOT NULL DEFAULT 0 COMMENT '积分抵扣金额' AFTER `coupon_money`;

ALTER TABLE `yoshop_sharing_order`
ADD COLUMN `points_num`  int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '积分抵扣数量' AFTER `points_money`;

ALTER TABLE `yoshop_sharing_order`
ADD COLUMN `points_bonus`  int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '赠送的积分数量' AFTER `order_status`;

ALTER TABLE `yoshop_sharing_order`
CHANGE COLUMN `is_user_expend` `is_settled`  tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '订单是否已结算(0未结算 1已结算)' AFTER `points_bonus`;



ALTER TABLE `yoshop_goods`
ADD COLUMN `is_points_gift`  tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否开启积分赠送(1开启 0关闭)' AFTER `delivery_id`;

ALTER TABLE `yoshop_goods`
ADD COLUMN `is_points_discount`  tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否允许使用积分抵扣(1允许 0不允许)' AFTER `is_points_gift`;

ALTER TABLE `yoshop_order_goods`
ADD COLUMN `points_money`  decimal(10,2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '积分金额' AFTER `coupon_money`,
ADD COLUMN `points_num`  int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '积分抵扣数量' AFTER `points_money`,
ADD COLUMN `points_bonus`  int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '赠送的积分数量' AFTER `points_num`;



ALTER TABLE `yoshop_sharing_goods`
ADD COLUMN `is_points_gift`  tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否开启积分赠送(1开启 0关闭)' AFTER `delivery_id`;

ALTER TABLE `yoshop_sharing_goods`
ADD COLUMN `is_points_discount`  tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否允许使用积分抵扣(1允许 0不允许)' AFTER `is_points_gift`;

ALTER TABLE `yoshop_sharing_order_goods`
ADD COLUMN `points_money`  decimal(10,2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '积分金额' AFTER `coupon_money`,
ADD COLUMN `points_num`  int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '积分抵扣数量' AFTER `points_money`,
ADD COLUMN `points_bonus`  int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '赠送的积分数量' AFTER `points_num`;


UPDATE `yoshop_store_access` SET `sort`='125' WHERE (`access_id`='10370');


INSERT INTO `yoshop_store_access` VALUES ('10443', '活跃用户', 'market.push/user', '10441', '105', '1561080384', '1561080384');
INSERT INTO `yoshop_store_access` VALUES ('10442', '发送消息', 'market.push/send', '10441', '100', '1561080384', '1561080384');
INSERT INTO `yoshop_store_access` VALUES ('10441', '消息推送', 'market.push', '10052', '120', '1561080292', '1561080292');
INSERT INTO `yoshop_store_access` VALUES ('10440', '积分明细', 'market.points/log', '10438', '105', '1561080384', '1561080384');
INSERT INTO `yoshop_store_access` VALUES ('10439', '积分设置', 'market.points/setting', '10438', '100', '1561080384', '1561080384');
INSERT INTO `yoshop_store_access` VALUES ('10438', '积分管理', 'market.points', '10052', '115', '1561080292', '1561080292');


