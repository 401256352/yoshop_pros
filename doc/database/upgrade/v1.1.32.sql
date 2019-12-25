


ALTER TABLE `yoshop_order_goods`
ADD COLUMN `goods_source_id`  int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '来源记录id' AFTER `user_id`;


ALTER TABLE `yoshop_order`
MODIFY COLUMN `order_source`  tinyint(3) UNSIGNED NOT NULL DEFAULT 10 COMMENT '订单来源(10普通订单 20砍价订单 30秒杀订单)' AFTER `is_comment`;


UPDATE `yoshop_store_access` SET `sort`='105' WHERE (`access_id`='10300');
UPDATE `yoshop_store_access` SET `sort`='110' WHERE (`access_id`='10426');
UPDATE `yoshop_store_access` SET `sort`='120' WHERE (`access_id`='10400');



INSERT INTO `yoshop_store_access` VALUES ('10444', '整点秒杀', 'apps.sharp', '10074', '115', '1564449650', '1564449650');

INSERT INTO `yoshop_store_access` VALUES ('10445', '秒杀商品', 'apps.sharp.goods', '10444', '100', '1564449650', '1564449650');
INSERT INTO `yoshop_store_access` VALUES ('10446', '商品列表', 'apps.sharp.goods/index', '10445', '100', '1564449650', '1564449650');
INSERT INTO `yoshop_store_access` VALUES ('10447', '新增商品', 'apps.sharp.goods/add', '10445', '105', '1564449650', '1564449650');
INSERT INTO `yoshop_store_access` VALUES ('10448', '编辑商品', 'apps.sharp.goods/edit', '10445', '110', '1564449650', '1564449650');
INSERT INTO `yoshop_store_access` VALUES ('10449', '删除商品', 'apps.sharp.goods/delete', '10445', '115', '1564449650', '1564449650');

INSERT INTO `yoshop_store_access` VALUES ('10450', '活动会场', 'apps.sharp.active', '10444', '105', '1564449650', '1564449650');
INSERT INTO `yoshop_store_access` VALUES ('10451', '会场列表', 'apps.sharp.active/index', '10450', '100', '1564449650', '1564449650');
INSERT INTO `yoshop_store_access` VALUES ('10452', '新增会场', 'apps.sharp.active/add', '10450', '105', '1564449650', '1564449650');
INSERT INTO `yoshop_store_access` VALUES ('10453', '修改活动状态', 'apps.sharp.active/state', '10450', '110', '1564449650', '1564449650');
INSERT INTO `yoshop_store_access` VALUES ('10454', '删除会场', 'apps.sharp.active/delete', '10450', '115', '1564449650', '1564449650');

INSERT INTO `yoshop_store_access` VALUES ('10455', '场次管理', 'apps.sharp.active_time', '10450', '120', '1564449650', '1564449650');
INSERT INTO `yoshop_store_access` VALUES ('10456', '场次列表', 'apps.sharp.active_time/index', '10455', '100', '1564449650', '1564449650');
INSERT INTO `yoshop_store_access` VALUES ('10457', '新增场次', 'apps.sharp.active_time/add', '10455', '105', '1564449650', '1564449650');
INSERT INTO `yoshop_store_access` VALUES ('10458', '编辑场次', 'apps.sharp.active_time/edit', '10455', '110', '1564449650', '1564449650');
INSERT INTO `yoshop_store_access` VALUES ('10459', '修改活动状态', 'apps.sharp.active_time/state', '10455', '115', '1564449650', '1564449650');
INSERT INTO `yoshop_store_access` VALUES ('10460', '删除场次', 'apps.sharp.active_time/delete', '10455', '120', '1564449650', '1564449650');

INSERT INTO `yoshop_store_access` VALUES ('10461', '基础设置', 'apps.sharp.setting/index', '10444', '125', '1564449650', '1564449650');



CREATE TABLE `yoshop_sharp_active` (
  `active_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '活动会场ID',
  `active_date` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '活动日期',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '活动状态(0禁用 1启用)',
  `is_delete` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`active_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='整点秒杀-活动会场表';


CREATE TABLE `yoshop_sharp_active_goods` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `active_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '活动会场ID',
  `active_time_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '活动场次ID',
  `sharp_goods_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '秒杀商品ID',
  `sales_actual` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '实际销量',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='整点秒杀-活动会场与商品关联表';


CREATE TABLE `yoshop_sharp_active_time` (
  `active_time_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '场次ID',
  `active_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '活动会场ID',
  `active_time` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '场次时间(0点-23点)',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '活动状态(0禁用 1启用)',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`active_time_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='整点秒杀-活动会场场次表';


CREATE TABLE `yoshop_sharp_goods` (
  `sharp_goods_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '秒杀商品ID',
  `goods_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商品ID',
  `deduct_stock_type` tinyint(3) unsigned DEFAULT '10' COMMENT '库存计算方式(10下单减库存 20付款减库存)',
  `limit_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '限购数量',
  `seckill_stock` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商品库存总量',
  `total_sales` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '累积销量',
  `sort` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商品排序(数字越小越靠前)',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '商品状态(0下架 1上架)',
  `is_delete` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`sharp_goods_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='整点秒杀-商品表';

CREATE TABLE `yoshop_sharp_goods_sku` (
  `goods_sku_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '商品规格id',
  `spec_sku_id` varchar(255) NOT NULL DEFAULT '0' COMMENT '商品sku记录索引 (由规格id组成)',
  `sharp_goods_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '秒杀商品id',
  `seckill_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '商品价格',
  `seckill_stock` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '秒杀库存数量',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`goods_sku_id`),
  UNIQUE KEY `sku_idx` (`sharp_goods_id`,`spec_sku_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='整点秒杀-秒杀商品sku信息表';

CREATE TABLE `yoshop_sharp_setting` (
  `key` varchar(30) NOT NULL DEFAULT '' COMMENT '设置项标示',
  `describe` varchar(255) NOT NULL DEFAULT '' COMMENT '设置项描述',
  `values` mediumtext NOT NULL COMMENT '设置内容(json格式)',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  UNIQUE KEY `unique_key` (`key`,`wxapp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='整点秒杀设置表';



