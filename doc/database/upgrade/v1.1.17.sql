

ALTER TABLE `yoshop_order` ADD COLUMN `delivery_type`  tinyint(3) UNSIGNED NOT NULL DEFAULT 10 COMMENT '配送方式(10快递配送 20上门自提)' AFTER `pay_time`;
ALTER TABLE `yoshop_order` ADD COLUMN `extract_shop_id`  int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '自提门店id' AFTER `delivery_type`;
ALTER TABLE `yoshop_order` ADD COLUMN `extract_clerk_id`  int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '核销店员id' AFTER `extract_shop_id`;

ALTER TABLE `yoshop_sharing_order` ADD COLUMN `delivery_type`  tinyint(3) UNSIGNED NOT NULL DEFAULT 10 COMMENT '配送方式(10快递配送 20上门自提)' AFTER `pay_time`;
ALTER TABLE `yoshop_sharing_order` ADD COLUMN `extract_shop_id`  int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '自提门店id' AFTER `delivery_type`;
ALTER TABLE `yoshop_sharing_order` ADD COLUMN `extract_clerk_id`  int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '核销店员id' AFTER `extract_shop_id`;


# 商家门店记录表
CREATE TABLE `yoshop_store_shop` (
  `shop_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '门店id',
  `shop_name` varchar(255) NOT NULL DEFAULT '' COMMENT '门店名称',
  `logo_image_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '门店logo图片id',
  `linkman` varchar(20) NOT NULL DEFAULT '' COMMENT '联系人',
  `phone` varchar(20) NOT NULL DEFAULT '' COMMENT '联系电话',
  `shop_hours` varchar(255) NOT NULL DEFAULT '' COMMENT '营业时间',
  `province_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '所在省份id',
  `city_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '所在城市id',
  `region_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '所在辖区id',
  `address` varchar(100) NOT NULL DEFAULT '' COMMENT '详细地址',
  `longitude` varchar(50) NOT NULL DEFAULT '' COMMENT '门店坐标经度',
  `latitude` varchar(50) NOT NULL DEFAULT '' COMMENT '门店坐标纬度',
  `geohash` varchar(50) NOT NULL DEFAULT '' COMMENT 'geohash',
  `summary` varchar(1000) NOT NULL DEFAULT '0' COMMENT '门店简介',
  `is_check` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否支持自提核销(0否 1支持)',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '门店状态(0禁用 1启用)',
  `is_delete` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`shop_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='商家门店记录表';


# 商家门店店员表
CREATE TABLE `yoshop_store_shop_clerk` (
  `clerk_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '店员id',
  `shop_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '所属门店id',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `real_name` varchar(30) NOT NULL DEFAULT '' COMMENT '店员姓名',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态(0禁用 1启用)',
  `is_delete` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`clerk_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='商家门店店员表';


#商家门店核销订单记录表
CREATE TABLE `yoshop_store_shop_order` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `order_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单id',
  `order_type` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '订单类型(10商城订单 20拼团订单)',
  `shop_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '门店id',
  `clerk_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '核销员id',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='商家门店核销订单记录表';



# 新增权限url：门店管理
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10356', '门店管理', 'shop', '0', '100', '1551504862', '1551504862');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10357', '门店管理', 'shop', '10356', '105', '1551505016', '1551505016');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10358', '门店列表', 'shop/index', '10357', '100', '1551505032', '1551505048');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10359', '添加门店', 'shop/add', '10357', '100', '1551505032', '1551505048');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10360', '编辑门店', 'shop/edit', '10357', '100', '1551505032', '1551505048');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10361', '删除门店', 'shop/delete', '10357', '100', '1551505032', '1551505048');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10362', '店员管理', 'shop.clerk', '10356', '110', '1551505016', '1551505016');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10363', '店员列表', 'shop.clerk/index', '10362', '100', '1551505032', '1551505048');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10364', '添加店员', 'shop.clerk/add', '10362', '100', '1551505032', '1551505048');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10365', '编辑店员', 'shop.clerk/edit', '10362', '100', '1551505032', '1551505048');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10366', '删除店员', 'shop.clerk/delete', '10362', '100', '1551505032', '1551505048');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10367', '订单核销记录', 'shop.order/index', '10356', '115', '1551505016', '1551505016');

# 新增权限url：订单核销
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10368', '门店自提核销', 'order.operate/extract', '10043', '100', '1551505016', '1551505016');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10369', '门店自提核销', 'apps.sharing.order.operate/extract', '10318', '100', '1551505016', '1551505016');

# 新增权限url：满额包邮
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10370', '满额包邮', 'market.basic/full_free', '10052', '100', '1551505016', '1551505016');
