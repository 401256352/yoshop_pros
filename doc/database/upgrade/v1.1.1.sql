
# 订单记录表：买家留言
ALTER TABLE `yoshop_order`
ADD COLUMN `buyer_remark`  varchar(255) NOT NULL DEFAULT '' COMMENT '买家留言' AFTER `update_price`;


# 小程序prepay_id记录表
CREATE TABLE `yoshop_wxapp_prepay_id` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `order_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单id',
  `prepay_id` varchar(50) NOT NULL DEFAULT '' COMMENT '微信支付prepay_id',
  `can_use_times` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '可使用次数',
  `used_times` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '已使用次数',
  `pay_status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '支付状态(1已支付)',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `expiry_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '过期时间',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='小程序prepay_id记录';
