
#商品记录表：新增商品卖点字段
ALTER TABLE `yoshop_goods`
ADD COLUMN `selling_point`  varchar(500) NOT NULL DEFAULT '' COMMENT '商品卖点' AFTER `category_id`;


# 小程序prepay_id记录表：新增订单类型
ALTER TABLE `yoshop_wxapp_prepay_id`
ADD COLUMN `order_type` tinyint(3) UNSIGNED NOT NULL DEFAULT 10 COMMENT '订单类型(10商城订单 20拼团订单)' AFTER `order_id`;


# 分销商订单记录表：新增订单类型
ALTER TABLE `yoshop_dealer_order`
ADD COLUMN `order_type` TINYINT (3) UNSIGNED NOT NULL DEFAULT '10' COMMENT '订单类型(10商城订单 20拼团订单)' AFTER `order_id`;


# 新增：拼团拼单记录表
CREATE TABLE `yoshop_sharing_active` (
  `active_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '拼单id',
  `goods_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '拼团商品id',
  `people` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '成团人数',
  `actual_people` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '当前已拼人数',
  `creator_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '团长用户id',
  `end_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '拼单结束时间',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '拼单状态(0未拼单 10拼单中 20拼单成功 30拼单失败)',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`active_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='拼团拼单记录表';


# 新增：拼团拼单成员记录表
CREATE TABLE `yoshop_sharing_active_users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `active_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '拼单id',
  `order_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '拼团订单id',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `is_creator` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否为创建者',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='拼团拼单成员记录表';


# 新增：拼团商品分类表
CREATE TABLE `yoshop_sharing_category` (
  `category_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '商品分类id',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '分类名称',
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '上级分类id',
  `image_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分类图片id',
  `sort` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '排序方式(数字越小越靠前)',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='拼团商品分类表';


# 新增：拼团商品评价表
CREATE TABLE `yoshop_sharing_comment` (
  `comment_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '评价id',
  `order_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '拼团订单id',
  `goods_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '拼团商品id',
  `order_goods_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单商品id',
  `score` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '评分(10好评 20中评 30差评)',
  `content` text NOT NULL COMMENT '评价内容',
  `is_picture` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否为图片评价',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '评价排序',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态(0隐藏 1显示)',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '软删除',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`comment_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='拼团商品评价表';


# 新增：拼团评价图片记录表
CREATE TABLE `yoshop_sharing_comment_image` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `comment_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '评价id',
  `image_id` int(11) NOT NULL DEFAULT '0' COMMENT '图片id(关联文件记录表)',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='拼团评价图片记录表';


# 新增：拼团商品记录表
CREATE TABLE `yoshop_sharing_goods` (
  `goods_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '拼团商品id',
  `goods_name` varchar(255) NOT NULL DEFAULT '' COMMENT '商品名称',
  `category_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商品分类id',
  `selling_point` varchar(500) NOT NULL DEFAULT '' COMMENT '商品卖点',
  `people` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '成团人数',
  `group_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '成团有效时间(单位:小时)',
  `is_alone` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否允许单买(0不允许 1允许)',
  `spec_type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '商品规格(10单规格 20多规格)',
  `deduct_stock_type` tinyint(3) unsigned NOT NULL DEFAULT '20' COMMENT '库存计算方式(10下单减库存 20付款减库存)',
  `content` longtext NOT NULL COMMENT '商品详情',
  `sales_initial` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '初始销量',
  `sales_actual` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '实际销量',
  `goods_sort` int(11) unsigned NOT NULL DEFAULT '100' COMMENT '商品排序(数字越小越靠前)',
  `delivery_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '配送模板id',
  `goods_status` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '商品状态(10上架 20下架)',
  `is_delete` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`goods_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='拼团商品记录表';


# 新增：商品图片记录表
CREATE TABLE `yoshop_sharing_goods_image` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `goods_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商品id',
  `image_id` int(11) NOT NULL COMMENT '图片id(关联文件记录表)',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='商品图片记录表';


# 新增：拼团商品规格表
CREATE TABLE `yoshop_sharing_goods_sku` (
  `goods_sku_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '商品规格id',
  `goods_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商品id',
  `spec_sku_id` varchar(255) NOT NULL DEFAULT '0' COMMENT '商品sku记录索引(由规格id组成)',
  `image_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '规格图片id',
  `goods_no` varchar(100) NOT NULL DEFAULT '' COMMENT '商品编码',
  `sharing_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '拼团价格',
  `goods_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '商品价格(单买价)',
  `line_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '商品划线价',
  `stock_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '当前库存数量',
  `goods_sales` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商品销量',
  `goods_weight` double unsigned NOT NULL DEFAULT '0' COMMENT '商品重量(Kg)',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`goods_sku_id`),
  UNIQUE KEY `sku_idx` (`goods_id`,`spec_sku_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='拼团商品规格表';


# 新增：拼团商品与规格值关系记录表
CREATE TABLE `yoshop_sharing_goods_spec_rel` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `goods_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商品id',
  `spec_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '规格组id',
  `spec_value_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '规格值id',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='拼团商品与规格值关系记录表';


# 新增：拼团订单记录表
CREATE TABLE `yoshop_sharing_order` (
  `order_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '订单id',
  `order_no` varchar(20) NOT NULL DEFAULT '' COMMENT '订单号',
  `order_type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '订单类型(10单独购买 20拼团)',
  `active_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '拼单id',
  `total_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '订单金额(不含运费)',
  `coupon_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '优惠券id',
  `coupon_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '优惠券抵扣金额',
  `pay_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '实际付款金额(包含运费、优惠)',
  `update_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '后台修改的订单金额（差价）',
  `buyer_remark` varchar(255) NOT NULL DEFAULT '' COMMENT '买家留言',
  `pay_status` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '付款状态(10未付款 20已付款)',
  `pay_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '付款时间',
  `express_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '运费金额',
  `express_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '物流公司id',
  `express_company` varchar(50) NOT NULL DEFAULT '' COMMENT '物流公司',
  `express_no` varchar(50) NOT NULL DEFAULT '' COMMENT '物流单号',
  `delivery_status` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '发货状态(10未发货 20已发货)',
  `delivery_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '发货时间',
  `receipt_status` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '收货状态(10未收货 20已收货)',
  `receipt_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '收货时间',
  `order_status` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '订单状态(10进行中 20已取消 21待取消 30已完成)',
  `is_refund` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '拼团未成功退款(0未退款 1已退款)',
  `transaction_id` varchar(30) NOT NULL DEFAULT '' COMMENT '微信支付交易号',
  `is_comment` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否已评价(0否 1是)',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`order_id`),
  UNIQUE KEY `order_no` (`order_no`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='拼团订单记录表';


# 新增：拼团订单收货地址记录表
CREATE TABLE `yoshop_sharing_order_address` (
  `order_address_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '地址id',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '收货人姓名',
  `phone` varchar(20) NOT NULL DEFAULT '' COMMENT '联系电话',
  `province_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '所在省份id',
  `city_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '所在城市id',
  `region_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '所在区id',
  `detail` varchar(255) NOT NULL DEFAULT '' COMMENT '详细地址',
  `order_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '拼团订单id',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`order_address_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='拼团订单收货地址记录表';


# 新增：拼团订单商品记录表
CREATE TABLE `yoshop_sharing_order_goods` (
  `order_goods_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `goods_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '拼团商品id',
  `goods_name` varchar(255) NOT NULL DEFAULT '' COMMENT '商品名称',
  `image_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商品封面图id',
  `selling_point` varchar(500) NOT NULL DEFAULT '' COMMENT '商品卖点',
  `people` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '成团人数',
  `group_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '成团有效时间(单位:小时)',
  `is_alone` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否允许单买(0不允许 1允许)',
  `deduct_stock_type` tinyint(3) unsigned NOT NULL DEFAULT '20' COMMENT '库存计算方式(10下单减库存 20付款减库存)',
  `spec_type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '规格类型(10单规格 20多规格)',
  `spec_sku_id` varchar(255) NOT NULL DEFAULT '' COMMENT '商品sku标识',
  `goods_sku_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商品规格id',
  `goods_attr` varchar(500) NOT NULL DEFAULT '' COMMENT '商品规格信息',
  `content` longtext NOT NULL COMMENT '商品详情',
  `goods_no` varchar(100) NOT NULL DEFAULT '' COMMENT '商品编码',
  `goods_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '商品价格',
  `line_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '商品划线价',
  `goods_weight` double unsigned NOT NULL DEFAULT '0' COMMENT '商品重量(Kg)',
  `total_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '购买数量',
  `total_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '商品总价(数量×单价)',
  `total_pay_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '实际付款价(包含优惠、折扣)',
  `is_comment` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否已评价(0否 1是)',
  `order_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '拼团订单id',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`order_goods_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='拼团订单商品记录表';


# 新增：拼团售后单记录表
CREATE TABLE `yoshop_sharing_order_refund` (
  `order_refund_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '售后单id',
  `order_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '拼团订单id',
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
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='拼团售后单记录表';


# 新增：拼团售后单退货地址记录表
CREATE TABLE `yoshop_sharing_order_refund_address` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `order_refund_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '售后单id',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '收货人姓名',
  `phone` varchar(20) NOT NULL DEFAULT '' COMMENT '联系电话',
  `detail` varchar(255) NOT NULL DEFAULT '' COMMENT '详细地址',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='拼团售后单退货地址记录表';


# 新增：拼团售后单图片记录表
CREATE TABLE `yoshop_sharing_order_refund_image` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `order_refund_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '售后单id',
  `image_id` int(11) NOT NULL DEFAULT '0' COMMENT '图片id(关联文件记录表)',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='拼团售后单图片记录表';


# 新增：拼团设置表
CREATE TABLE `yoshop_sharing_setting` (
  `key` varchar(30) NOT NULL DEFAULT '' COMMENT '设置项标示',
  `describe` varchar(255) NOT NULL DEFAULT '' COMMENT '设置项描述',
  `values` mediumtext NOT NULL COMMENT '设置内容(json格式)',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  UNIQUE KEY `unique_key` (`key`,`wxapp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='拼团设置表';



# 拼团管理
INSERT INTO `yoshop_store_access` VALUES ('10300', '拼团管理', 'apps.sharing', '10074', '100', '1544601161', '1544601161');


# 商品分类
INSERT INTO `yoshop_store_access` VALUES ('10301', '商品分类', 'apps.sharing.category', '10300', '100', '1544601378', '1544601378');

INSERT INTO `yoshop_store_access` VALUES ('10302', '分类列表', 'apps.sharing.category/index', '10301', '100', '1544601378', '1544601378');
INSERT INTO `yoshop_store_access` VALUES ('10303', '添加分类', 'apps.sharing.category/add', '10301', '100', '1544601378', '1544601378');
INSERT INTO `yoshop_store_access` VALUES ('10304', '编辑分类', 'apps.sharing.category/edit', '10301', '100', '1544601378', '1544601378');
INSERT INTO `yoshop_store_access` VALUES ('10305', '删除分类', 'apps.sharing.category/delete', '10301', '100', '1544601378', '1544601378');


# 拼团商品管理
INSERT INTO `yoshop_store_access` VALUES ('10306', '商品管理', 'apps.sharing.goods', '10300', '100', '1544601378', '1544601378');

INSERT INTO `yoshop_store_access` VALUES ('10307', '商品列表', 'apps.sharing.goods/index', '10306', '100', '1544601378', '1544601378');
INSERT INTO `yoshop_store_access` VALUES ('10308', '添加商品', 'apps.sharing.goods/add', '10306', '100', '1544601378', '1544601378');
INSERT INTO `yoshop_store_access` VALUES ('10309', '编辑商品', 'apps.sharing.goods/edit', '10306', '100', '1544601378', '1544601378');
INSERT INTO `yoshop_store_access` VALUES ('10310', '复制商品', 'apps.sharing.goods/copy', '10306', '100', '1544601378', '1544601378');
INSERT INTO `yoshop_store_access` VALUES ('10311', '删除商品', 'apps.sharing.goods/delete', '10306', '100', '1544601378', '1544601378');
INSERT INTO `yoshop_store_access` VALUES ('10312', '商品上下架', 'apps.sharing.goods/state', '10306', '100', '1544601378', '1544601378');


# 拼单管理
INSERT INTO `yoshop_store_access` VALUES ('10313', '拼单管理', 'apps.sharing.active', '10300', '100', '1544601378', '1544601378');

INSERT INTO `yoshop_store_access` VALUES ('10314', '拼单列表', 'apps.sharing.active/index', '10313', '100', '1544601378', '1544601378');
INSERT INTO `yoshop_store_access` VALUES ('10315', '拼单成员列表', 'apps.sharing.active/users', '10313', '100', '1544601378', '1544601378');


# 拼团订单
INSERT INTO `yoshop_store_access` VALUES ('10316', '订单管理', 'apps.sharing.order', '10300', '100', '1544601378', '1544601378');

INSERT INTO `yoshop_store_access` VALUES ('10317', '订单列表', 'apps.sharing.order/index', '10316', '100', '1544601378', '1544601378');
INSERT INTO `yoshop_store_access` VALUES ('10318', '订单详情', '', '10316', '100', '1544601378', '1544601378');
INSERT INTO `yoshop_store_access` VALUES ('10319', '详情信息', 'apps.sharing.order/detail', '10318', '100', '1544601378', '1544601378');
INSERT INTO `yoshop_store_access` VALUES ('10320', '确认发货', 'apps.sharing.order/delivery', '10318', '100', '1544601378', '1544601378');
INSERT INTO `yoshop_store_access` VALUES ('10321', '修改订单价格', 'apps.sharing.order/updateprice', '10318', '100', '1544601378', '1544601378');
INSERT INTO `yoshop_store_access` VALUES ('10322', '审核用户取消订单', 'apps.sharing.order/confirmcancel', '10318', '100', '1544601378', '1544601378');
INSERT INTO `yoshop_store_access` VALUES ('10323', '拼团失败手动退款', 'apps.sharing.order/refund', '10318', '100', '1544601378', '1544601378');
INSERT INTO `yoshop_store_access` VALUES ('10324', '订单导出', 'apps.sharing.order.operate/export', '10316', '100', '1544601378', '1544601378');
INSERT INTO `yoshop_store_access` VALUES ('10325', '批量发货', 'apps.sharing.order.operate/batchdelivery', '10316', '100', '1544601378', '1544601378');


# 售后管理
INSERT INTO `yoshop_store_access` VALUES ('10326', '售后管理', 'apps.sharing.order.refund', '10300', '100', '1544601378', '1544601378');

INSERT INTO `yoshop_store_access` VALUES ('10327', '售后列表', 'apps.sharing.order.refund/index', '10326', '100', '1544601378', '1544601378');
INSERT INTO `yoshop_store_access` VALUES ('10328', '售后详情', 'apps.sharing.order.refund/detail', '10326', '100', '1544601378', '1544601378');
INSERT INTO `yoshop_store_access` VALUES ('10329', '审核售后单', 'apps.sharing.order.refund/audit', '10326', '100', '1544601378', '1544601378');
INSERT INTO `yoshop_store_access` VALUES ('10330', '确认收货并退款', 'apps.sharing.order.refund/receipt', '10326', '100', '1544601378', '1544601378');


# 商品评价
INSERT INTO `yoshop_store_access` VALUES ('10331', '商品评价', 'apps.sharing.comment', '10300', '100', '1544601378', '1544601378');

INSERT INTO `yoshop_store_access` VALUES ('10332', '评价列表', 'apps.sharing.comment/index', '10331', '100', '1544601378', '1544601378');
INSERT INTO `yoshop_store_access` VALUES ('10333', '评价详情', 'apps.sharing.comment/detail', '10331', '100', '1544601378', '1544601378');
INSERT INTO `yoshop_store_access` VALUES ('10334', '删除评价', 'apps.sharing.comment/delete', '10331', '100', '1544601378', '1544601378');


# 拼团设置
INSERT INTO `yoshop_store_access` VALUES ('10335', '拼团设置', 'apps.sharing.setting/index', '10300', '100', '1544601378', '1544601378');


