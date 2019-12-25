

# 微信小程序记录表：新增证书文件字段
ALTER TABLE `yoshop_wxapp`
ADD COLUMN `cert_pem`  longtext COMMENT '证书文件cert' AFTER `apikey`,
ADD COLUMN `key_pem`  longtext COMMENT '证书文件key' AFTER `cert_pem`;


# 订单记录表：订单状态 新增21待取消状态
ALTER TABLE `yoshop_order`
MODIFY COLUMN `order_status`  tinyint(3) UNSIGNED NOT NULL DEFAULT 10 COMMENT '订单状态(10进行中 20取消 21待取消 30已完成)' AFTER `receipt_time`;


# 退货地址记录表
CREATE TABLE `yoshop_return_address` (
  `address_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '退货地址id',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '收货人姓名',
  `phone` varchar(20) NOT NULL DEFAULT '' COMMENT '联系电话',
  `detail` varchar(255) NOT NULL DEFAULT '' COMMENT '详细地址',
  `sort` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '排序 (数字越小越靠前)',
  `is_delete` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`address_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='退货地址记录表';


# 售后单记录表
CREATE TABLE `yoshop_order_refund` (
  `order_refund_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '售后单id',
  `order_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单id',
  `order_goods_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单商品id',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '售后类型(10退货退款 20换货)',
  `apply_desc` varchar(1000) NOT NULL DEFAULT '' COMMENT '用户申请原因(说明)',
  `is_agree` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '商家审核状态(0待审核 10已同意 20已拒绝)',
  `refuse_desc` varchar(1000) NOT NULL DEFAULT '' COMMENT '商家拒绝原因(说明)',
  `refund_money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '实际退款金额',
  `is_user_send` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '用户是否发货(0未发货 1已发货)',
  `send_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户发货时间',
  `express_id` varchar(32) NOT NULL DEFAULT '' COMMENT '用户发货物流公司id',
  `express_no` varchar(32) NOT NULL DEFAULT '' COMMENT '用户发货物流单号',
  `is_receipt` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '商家收货状态(0未收货 1已收货)',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '售后单状态(0进行中 10已拒绝 20已完成 30已取消)',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`order_refund_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='售后单记录表';


# 售后单退货地址记录表
CREATE TABLE `yoshop_order_refund_address` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `order_refund_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '售后单id',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '收货人姓名',
  `phone` varchar(20) NOT NULL DEFAULT '' COMMENT '联系电话',
  `detail` varchar(255) NOT NULL DEFAULT '' COMMENT '详细地址',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='售后单退货地址记录表';


# 售后单图片记录表
CREATE TABLE `yoshop_order_refund_image` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `order_refund_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '售后单id',
  `image_id` int(11) NOT NULL DEFAULT '0' COMMENT '图片id(关联文件记录表)',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='售后单图片记录表';


# 新增权限记录
INSERT INTO `yoshop_store_access` (`name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('审核用户取消申请', 'order.operate/confirmcancel', '10043', '100', '1542163260', '1542163260');
INSERT INTO `yoshop_store_access` (`name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('售后管理', 'order.refund', '10035', '100', '1542161684', '1542161684');
INSERT INTO `yoshop_store_access` (`name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('售后列表', 'order.refund/index', '10108', '100', '1542161714', '1542161714');
INSERT INTO `yoshop_store_access` (`name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('售后详情', 'order.refund/detail', '10108', '100', '1542161736', '1542161736');
INSERT INTO `yoshop_store_access` (`name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('审核售后单', 'order.refund/audit', '10108', '100', '1542162196', '1542162196');
INSERT INTO `yoshop_store_access` (`name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('确认收货并退款', 'order.refund/receipt', '10108', '100', '1542162231', '1542162231');
