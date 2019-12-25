
# 用户记录表：新增用户总消费金额
ALTER TABLE `yoshop_user`
ADD COLUMN `money`  decimal(10,2) unsigned NOT NULL DEFAULT 0 COMMENT '用户总消费金额' AFTER `address_id`;


# 订单商品记录表：新增实际付款价
ALTER TABLE `yoshop_order_goods`
MODIFY COLUMN `total_price`  decimal(10,2) unsigned NOT NULL DEFAULT 0.00 COMMENT '商品总价(数量×单价)' AFTER `total_num`,
ADD COLUMN `total_pay_price`  decimal(10,2) unsigned NOT NULL DEFAULT 0.00 COMMENT '实际付款价(折扣和优惠后)' AFTER `total_price`;


# 分销商申请记录表
CREATE TABLE `yoshop_dealer_apply` (
  `apply_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `real_name` varchar(30) NOT NULL DEFAULT '' COMMENT '姓名',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号',
  `referee_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '推荐人用户id',
  `apply_type` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '申请方式(10需后台审核 20无需审核)',
  `apply_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '申请时间',
  `apply_status` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '审核状态 (10待审核 20审核通过 30驳回)',
  `audit_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '审核时间',
  `reject_reason` varchar(500) NOT NULL DEFAULT '' COMMENT '驳回原因',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`apply_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='分销商申请记录表';


# 分销商资金明细表
CREATE TABLE `yoshop_dealer_capital` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分销商用户id',
  `flow_type` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '资金流动类型 (10佣金收入 20提现支出)',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '金额',
  `describe` varchar(500) NOT NULL DEFAULT '' COMMENT '描述',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='分销商资金明细表';


# 销商订单记录表
CREATE TABLE `yoshop_dealer_order` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id (买家)',
  `order_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单id',
  `order_no` varchar(20) NOT NULL DEFAULT '' COMMENT '订单号(废弃,勿用)',
  `order_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '订单总金额(不含运费)',
  `first_user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分销商用户id(一级)',
  `second_user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分销商用户id(二级)',
  `third_user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分销商用户id(三级)',
  `first_money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '分销佣金(一级)',
  `second_money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '分销佣金(二级)',
  `third_money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '分销佣金(三级)',
  `is_settled` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否已结算佣金 (0未结算 1已结算)',
  `settle_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '结算时间',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='分销商订单记录表';


# 分销商推荐关系表
CREATE TABLE `yoshop_dealer_referee` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `dealer_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分销商用户id',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id(被推荐人)',
  `level` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '推荐关系层级(1,2,3)',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `dealer_id` (`dealer_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='分销商推荐关系表';


# 分销商设置表
CREATE TABLE `yoshop_dealer_setting` (
  `key` varchar(30) NOT NULL DEFAULT '' COMMENT '设置项标示',
  `describe` varchar(255) NOT NULL DEFAULT '' COMMENT '设置项描述',
  `values` mediumtext NOT NULL COMMENT '设置内容(json格式)',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  UNIQUE KEY `unique_key` (`key`,`wxapp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='分销商设置表';


# 分销商用户记录表
CREATE TABLE `yoshop_dealer_user` (
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分销商用户id',
  `real_name` varchar(30) NOT NULL DEFAULT '' COMMENT '姓名',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号',
  `money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '当前可提现佣金',
  `freeze_money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '已冻结佣金',
  `total_money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '累积提现佣金',
  `referee_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '推荐人用户id',
  `first_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '成员数量(一级)',
  `second_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '成员数量(二级)',
  `third_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '成员数量(三级)',
  `is_delete` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='分销商用户记录表';


# 分销商提现明细表
CREATE TABLE `yoshop_dealer_withdraw` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分销商用户id',
  `money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '提现金额',
  `pay_type` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '打款方式 (10微信 20支付宝 30银行卡)',
  `alipay_name` varchar(30) NOT NULL DEFAULT '' COMMENT '支付宝姓名',
  `alipay_account` varchar(30) NOT NULL DEFAULT '' COMMENT '支付宝账号',
  `bank_name` varchar(30) NOT NULL DEFAULT '' COMMENT '开户行名称',
  `bank_account` varchar(30) NOT NULL DEFAULT '' COMMENT '银行开户名',
  `bank_card` varchar(30) NOT NULL DEFAULT '' COMMENT '银行卡号',
  `apply_status` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '申请状态 (10待审核 20审核通过 30驳回 40已打款)',
  `audit_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '审核时间',
  `reject_reason` varchar(500) NOT NULL DEFAULT '' COMMENT '驳回原因',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='分销商提现明细表';


# 小程序form_id记录表
CREATE TABLE `yoshop_wxapp_formid` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `form_id` varchar(50) NOT NULL DEFAULT '' COMMENT '小程序form_id',
  `expiry_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '过期时间',
  `is_used` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否已使用',
  `used_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '使用时间',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='小程序form_id记录表';