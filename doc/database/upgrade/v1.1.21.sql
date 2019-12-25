
ALTER TABLE `yoshop_user` ADD COLUMN `balance`  decimal(10,2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '用户可用余额' AFTER `address_id`;
ALTER TABLE `yoshop_order` ADD COLUMN `pay_type`  tinyint(3) UNSIGNED NOT NULL DEFAULT 20 COMMENT '支付方式(10余额支付 20微信支付)' AFTER `buyer_remark`;
ALTER TABLE `yoshop_sharing_order` ADD COLUMN `pay_type`  tinyint(3) UNSIGNED NOT NULL DEFAULT 20 COMMENT '支付方式(10余额支付 20微信支付)' AFTER `buyer_remark`;


CREATE TABLE `yoshop_recharge_order` (
  `order_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '订单id',
  `order_no` varchar(20) NOT NULL DEFAULT '' COMMENT '订单号',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `recharge_type` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '充值方式(10自定义金额 20套餐充值)',
  `plan_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '充值套餐id',
  `pay_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '用户支付金额',
  `gift_money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '赠送金额',
  `actual_money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '实际到账金额',
  `pay_status` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '支付状态(10待支付 20已支付)',
  `pay_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '付款时间',
  `transaction_id` varchar(30) NOT NULL DEFAULT '' COMMENT '微信支付交易号',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序商城id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='用户充值订单表';


CREATE TABLE `yoshop_recharge_order_plan` (
  `order_plan_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `order_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单id',
  `plan_id` int(11) unsigned NOT NULL COMMENT '主键id',
  `plan_name` varchar(255) NOT NULL DEFAULT '' COMMENT '方案名称',
  `money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '充值金额',
  `gift_money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '赠送金额',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序商城id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`order_plan_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='用户充值订单套餐快照表';


CREATE TABLE `yoshop_recharge_plan` (
  `plan_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `plan_name` varchar(255) NOT NULL DEFAULT '' COMMENT '套餐名称',
  `money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '充值金额',
  `gift_money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '赠送金额',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序(数字越小越靠前)',
  `is_delete` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序商城id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`plan_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='余额充值套餐表';


CREATE TABLE `yoshop_user_balance_log` (
  `log_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `scene` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '余额变动场景(10用户充值 20用户消费 30管理员操作 40订单退款)',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '变动金额',
  `describe` varchar(500) NOT NULL DEFAULT '' COMMENT '描述/说明',
  `remark` varchar(500) NOT NULL DEFAULT '' COMMENT '管理员备注',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序商城id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='用户余额变动明细表';



UPDATE `yoshop_store_access` SET `sort`='120' WHERE (`access_id` = '10370');

INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10387', '余额记录', 'user.balance', '10049', '105', '1554685953', '1554685965');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10388', '充值记录', 'user.recharge/order', '10387', '100', '1554686010', '1554686010');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10389', '余额明细', 'user.balance/log', '10387', '105', '1554686031', '1554686031');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10390', '用户充值', 'market.recharge', '10052', '110', '1554686283', '1554686339');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10391', '充值套餐', 'market.recharge.plan', '10390', '100', '1554686316', '1554686316');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10392', '套餐列表', 'market.recharge.plan/index', '10391', '100', '1554686316', '1554686316');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10393', '添加套餐', 'market.recharge.plan/add', '10391', '105', '1554686316', '1554686316');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10394', '编辑套餐', 'market.recharge.plan/edit', '10391', '110', '1554686316', '1554686316');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10395', '删除套餐', 'market.recharge.plan/delete', '10391', '115', '1554686316', '1554686316');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10396', '充值设置', 'market.recharge/setting', '10390', '105', '1554686647', '1554686647');
