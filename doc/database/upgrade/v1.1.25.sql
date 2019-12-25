
START TRANSACTION;

# 修改字段：用户表 - 用户总支付的金额 
ALTER TABLE `yoshop_user`
CHANGE COLUMN `money` `pay_money`  decimal(10,2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '用户总支付的金额' AFTER `balance`;

# 新增字段：用户表 - 实际消费的金额 
ALTER TABLE `yoshop_user`
ADD COLUMN `expend_money`  decimal(10,2) UNSIGNED NOT NULL DEFAULT 0 COMMENT '实际消费的金额(不含退款)' AFTER `pay_money`;

# 新增字段：用户表 - 会员等级id
ALTER TABLE `yoshop_user`
ADD COLUMN `grade_id`  int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '会员等级id' AFTER `expend_money`;



# 新增字段：商品表 - 是否开启会员折扣
ALTER TABLE `yoshop_goods`
ADD COLUMN `is_enable_grade`  tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否开启会员折扣(1开启 0关闭)' AFTER `delivery_id`;

# 新增字段：商品表 - 会员折扣设置
ALTER TABLE `yoshop_goods`
ADD COLUMN `is_alone_grade`  tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '会员折扣设置(0默认等级折扣 1单独设置折扣)' AFTER `is_enable_grade`;

# 新增字段：商品表 - 单独设置折扣的配置
ALTER TABLE `yoshop_goods`
ADD COLUMN `alone_grade_equity`  text NULL COMMENT '单独设置折扣的配置' AFTER `is_alone_grade`;


# 新增字段：商品表 - 是否开启会员折扣
ALTER TABLE `yoshop_sharing_goods`
ADD COLUMN `is_enable_grade`  tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否开启会员折扣(1开启 0关闭)' AFTER `delivery_id`;

# 新增字段：商品表 - 会员折扣设置
ALTER TABLE `yoshop_sharing_goods`
ADD COLUMN `is_alone_grade`  tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '会员折扣设置(0默认等级折扣 1单独设置折扣)' AFTER `is_enable_grade`;

# 新增字段：商品表 - 单独设置折扣的配置
ALTER TABLE `yoshop_sharing_goods`
ADD COLUMN `alone_grade_equity`  text NULL COMMENT '单独设置折扣的配置' AFTER `is_alone_grade`;


# 新增字段：订单表 - 标识：累积用户实际消费金额
ALTER TABLE `yoshop_order`
ADD COLUMN `is_user_expend`  tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '标识：累积用户实际消费金额' AFTER `user_id`;

# 修改字段：订单表 - 优惠券抵扣金额
ALTER TABLE `yoshop_order`
CHANGE COLUMN `coupon_price` `coupon_money`  decimal(10,2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '优惠券抵扣金额' AFTER `coupon_id`;


# 新增字段：拼团订单表 - 标识：累积用户实际消费金额
ALTER TABLE `yoshop_sharing_order`
ADD COLUMN `is_user_expend`  tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '标识：累积用户实际消费金额' AFTER `user_id`;

# 修改字段：拼团订单表 - 优惠券抵扣金额
ALTER TABLE `yoshop_sharing_order`
CHANGE COLUMN `coupon_price` `coupon_money`  decimal(10,2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '优惠券抵扣金额' AFTER `coupon_id`;






# 新增字段：订单商品记录表 - 会员等级折扣金额 + 优惠券折扣金额

ALTER TABLE `yoshop_order`
MODIFY COLUMN `total_price`  decimal(10,2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '商品总金额(不含优惠折扣)' AFTER `order_no`,
ADD COLUMN `order_price`  decimal(10,2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '订单金额(含优惠折扣)' AFTER `total_price`;

ALTER TABLE `yoshop_order_goods`
ADD COLUMN `grade_total_money`  decimal(10,2) UNSIGNED NOT NULL DEFAULT 0 COMMENT '会员等级折扣金额' AFTER `goods_weight`,
ADD COLUMN `coupon_money`  decimal(10,2) UNSIGNED NOT NULL DEFAULT 0 COMMENT '优惠券折扣金额' AFTER `grade_total_money`;

# 新增字段：订单商品记录表 - 是否存在会员等级折扣
ALTER TABLE `yoshop_order_goods`
ADD COLUMN `is_user_grade`  tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否存在会员等级折扣' AFTER `goods_weight`;

# 新增字段：订单商品记录表 - 会员折扣比例(0-10)
ALTER TABLE `yoshop_order_goods`
MODIFY COLUMN `grade_total_money`  decimal(10,2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '会员等级折扣金额(总)' AFTER `is_user_grade`,
ADD COLUMN `grade_ratio`  tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '会员折扣比例(0-10)' AFTER `is_user_grade`;

ALTER TABLE `yoshop_order_goods`
MODIFY COLUMN `goods_price`  decimal(10,2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '商品价格(单价)' AFTER `goods_no`,
MODIFY COLUMN `grade_total_money`  decimal(10,2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '会员折扣总金额' AFTER `grade_ratio`,
ADD COLUMN `grade_goods_price`  decimal(10,2) UNSIGNED NOT NULL DEFAULT 0 COMMENT '会员折扣的商品单价' AFTER `grade_ratio`;

ALTER TABLE `yoshop_order_goods`
MODIFY COLUMN `grade_total_money`  decimal(10,2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '会员折扣的总额差' AFTER `grade_goods_price`;



# 新增字段：订单商品记录表 - 会员等级折扣金额 + 优惠券折扣金额

ALTER TABLE `yoshop_sharing_order`
MODIFY COLUMN `total_price`  decimal(10,2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '商品总金额(不含优惠折扣)' AFTER `order_no`,
ADD COLUMN `order_price`  decimal(10,2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '订单金额(含优惠折扣)' AFTER `total_price`;

ALTER TABLE `yoshop_sharing_order_goods`
ADD COLUMN `grade_total_money`  decimal(10,2) UNSIGNED NOT NULL DEFAULT 0 COMMENT '会员等级折扣金额' AFTER `goods_weight`,
ADD COLUMN `coupon_money`  decimal(10,2) UNSIGNED NOT NULL DEFAULT 0 COMMENT '优惠券折扣金额' AFTER `grade_total_money`;

# 新增字段：订单商品记录表 - 是否存在会员等级折扣
ALTER TABLE `yoshop_sharing_order_goods`
ADD COLUMN `is_user_grade`  tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否存在会员等级折扣' AFTER `goods_weight`;

# 新增字段：订单商品记录表 - 会员折扣比例(0-10)
ALTER TABLE `yoshop_sharing_order_goods`
MODIFY COLUMN `grade_total_money`  decimal(10,2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '会员等级折扣金额(总)' AFTER `is_user_grade`,
ADD COLUMN `grade_ratio`  tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '会员折扣比例(0-10)' AFTER `is_user_grade`;

ALTER TABLE `yoshop_sharing_order_goods`
MODIFY COLUMN `goods_price`  decimal(10,2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '商品价格(单价)' AFTER `goods_no`,
MODIFY COLUMN `grade_total_money`  decimal(10,2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '会员折扣总金额' AFTER `grade_ratio`,
ADD COLUMN `grade_goods_price`  decimal(10,2) UNSIGNED NOT NULL DEFAULT 0 COMMENT '会员折扣的商品单价' AFTER `grade_ratio`;

ALTER TABLE `yoshop_sharing_order_goods`
MODIFY COLUMN `grade_total_money`  decimal(10,2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '会员折扣的总额差' AFTER `grade_goods_price`;





# 新增表：用户会员等级表
CREATE TABLE `yoshop_user_grade` (
  `grade_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '等级ID',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '等级名称',
  `weight` int(11) unsigned NOT NULL DEFAULT '1' COMMENT '等级权重(1-9999)',
  `upgrade` text NOT NULL COMMENT '升级条件',
  `equity` text NOT NULL COMMENT '等级权益(折扣率0-100)',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1启用 0禁用)',
  `is_delete` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`grade_id`),
  KEY `wxapp_id` (`wxapp_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='用户会员等级表';


# 新增表：用户会员等级变更记录表
CREATE TABLE `yoshop_user_grade_log` (
  `log_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `old_grade_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '变更前的等级id',
  `new_grade_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '变更后的等级id',
  `change_type` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '变更类型(10后台管理员设置 20自动升级)',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='用户会员等级变更记录表';



UPDATE `yoshop_store_access` SET `sort`='125' WHERE (`access_id`='10387');

INSERT INTO `yoshop_store_access` VALUES ('10411', '修改会员等级', 'user/grade', '10049', '115', '1558317213', '1558317226');
INSERT INTO `yoshop_store_access` VALUES ('10412', '会员等级管理', 'user.grade', '10049', '120', '1558317440', '1558317440');
INSERT INTO `yoshop_store_access` VALUES ('10413', '会员等级列表', 'user.grade/index', '10412', '100', '1558317464', '1558317464');
INSERT INTO `yoshop_store_access` VALUES ('10414', '新增等级', 'user.grade/add', '10412', '105', '1558317464', '1558317464');
INSERT INTO `yoshop_store_access` VALUES ('10415', '编辑等级', 'user.grade/edit', '10412', '110', '1558317464', '1558317464');
INSERT INTO `yoshop_store_access` VALUES ('10416', '删除等级', 'user.grade/delete', '10412', '115', '1558317464', '1558317464');



COMMIT;