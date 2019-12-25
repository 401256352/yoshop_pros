
START TRANSACTION;


# 好物圈设置表
CREATE TABLE `yoshop_wow_setting` (
  `key` varchar(30) NOT NULL DEFAULT '' COMMENT '设置项标示',
  `describe` varchar(255) NOT NULL DEFAULT '' COMMENT '设置项描述',
  `values` mediumtext NOT NULL COMMENT '设置内容(json格式)',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  UNIQUE KEY `unique_key` (`key`,`wxapp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='好物圈设置表';


# 好物圈商品收藏记录表
CREATE TABLE `yoshop_wow_shoping` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `goods_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商品id',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `is_delete` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='好物圈商品收藏记录表';


# 好物圈订单同步记录表
CREATE TABLE `yoshop_wow_order` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `order_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单id',
  `order_type` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '订单类型(10商城订单 20拼团订单)',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '订单状态(3支付完成 4已发货 5已退款 100已完成)',
  `last_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '最后更新时间',
  `is_delete` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='好物圈订单同步记录表';


# 更新权限排序
UPDATE `yoshop_store_access` SET `sort`='105' WHERE (`access_id`='10335');
UPDATE `yoshop_store_access` SET `sort`='105' WHERE (`access_id`='10051');
UPDATE `yoshop_store_access` SET `sort`='115' WHERE (`access_id`='10387');


# 新增好物圈管理管理
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10400', '好物圈', 'apps.wow', '10074', '110', '1557037952', '1557037952');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10401', '商品收藏', 'apps.wow.shoping', '10400', '100', '1557037952', '1557037952');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10402', '订单信息', 'apps.wow.order', '10400', '105', '1557037952', '1557037952');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10403', '基础设置', 'apps.wow.setting/index', '10400', '110', '1557037952', '1557037952');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10404', '商品收藏记录', 'apps.wow.shoping/index', '10401', '100', '1557037952', '1557037952');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10405', '取消同步', 'apps.wow.shoping/delete', '10401', '105', '1557037952', '1557037952');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10406', '订单同步记录', 'apps.wow.order/index', '10402', '100', '1557037952', '1557037952');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10407', '取消同步', 'apps.wow.order/delete', '10402', '105', '1557037952', '1557037952');


# 新增用户充值权限
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10408', '用户充值', 'user/recharge', '10049', '110', '1557037952', '1557037952');


# 删除冗余的权限
DELETE FROM `yoshop_store_access` WHERE (`access_id`='10013');
DELETE FROM `yoshop_store_access` WHERE (`access_id`='10014');
DELETE FROM `yoshop_store_access` WHERE (`access_id`='10015');
DELETE FROM `yoshop_store_access` WHERE (`access_id`='10016');
DELETE FROM `yoshop_store_access` WHERE (`access_id`='10017');


# 整理地区表
UPDATE `yoshop_region` SET `name`='香港岛(废弃)' WHERE (`id`='3717');
UPDATE `yoshop_region` SET `name`='九龙(废弃)' WHERE (`id`='3722');
UPDATE `yoshop_region` SET `name`='新界(废弃)' WHERE (`id`='3728');

INSERT INTO `yoshop_region` VALUES ('3999', '3716', '香港', '香港特别行政区', '中国,香港特别行政区', '2', 'hongkong', null, null, null, null, null);
INSERT INTO `yoshop_region` VALUES ('4000', '3999', '中西区', '中西区', '中国,香港特别行政区,中西区', '3', 'zhongxin', null, null, null, null, null);
INSERT INTO `yoshop_region` VALUES ('4001', '3999', '东区', '东区', '中国,香港特别行政区,东区', '3', 'dong', null, null, null, null, null);
INSERT INTO `yoshop_region` VALUES ('4002', '3999', '九龙城区', '九龙城区', '中国,香港特别行政区,九龙城区', '3', 'jiulong', null, null, null, null, null);
INSERT INTO `yoshop_region` VALUES ('4003', '3999', '观塘区', '观塘区', '中国,香港特别行政区,观塘区', '3', 'guantang', null, null, null, null, null);
INSERT INTO `yoshop_region` VALUES ('4004', '3999', '南区', '南区', '中国,香港特别行政区,南区', '3', 'nan', null, null, null, null, null);
INSERT INTO `yoshop_region` VALUES ('4005', '3999', '深水埗区', '深水埗区', '中国,香港特别行政区,深水埗区', '3', 'shenshuibu', null, null, null, null, null);
INSERT INTO `yoshop_region` VALUES ('4006', '3999', '湾仔区', '湾仔区', '中国,香港特别行政区,湾仔区', '3', 'wanzi', null, null, null, null, null);
INSERT INTO `yoshop_region` VALUES ('4007', '3999', '黄大仙区', '黄大仙区', '中国,香港特别行政区,黄大仙区', '3', 'huangdaxian', null, null, null, null, null);
INSERT INTO `yoshop_region` VALUES ('4008', '3999', '油尖旺区', '油尖旺区', '中国,香港特别行政区,油尖旺区', '3', 'youjianwang', null, null, null, null, null);
INSERT INTO `yoshop_region` VALUES ('4009', '3999', '离岛区', '离岛区', '中国,香港特别行政区,离岛区', '3', 'lidao', null, null, null, null, null);
INSERT INTO `yoshop_region` VALUES ('4010', '3999', '葵青区', '葵青区', '中国,香港特别行政区,葵青区', '3', 'kuiqing', null, null, null, null, null);
INSERT INTO `yoshop_region` VALUES ('4011', '3999', '北区', '北区', '中国,香港特别行政区,北区', '3', 'bei', null, null, null, null, null);
INSERT INTO `yoshop_region` VALUES ('4012', '3999', '西贡区', '西贡区', '中国,香港特别行政区,西贡区', '3', 'xigong', null, null, null, null, null);
INSERT INTO `yoshop_region` VALUES ('4013', '3999', '沙田区', '沙田区', '中国,香港特别行政区,沙田区', '3', 'shatian', null, null, null, null, null);
INSERT INTO `yoshop_region` VALUES ('4014', '3999', '屯门区', '屯门区', '中国,香港特别行政区,屯门区', '3', 'tunmen', null, null, null, null, null);
INSERT INTO `yoshop_region` VALUES ('4015', '3999', '大埔区', '大埔区', '中国,香港特别行政区,大埔区', '3', 'dapu', null, null, null, null, null);
INSERT INTO `yoshop_region` VALUES ('4016', '3999', '荃湾区', '荃湾区', '中国,香港特别行政区,荃湾区', '3', 'quanwan', null, null, null, null, null);
INSERT INTO `yoshop_region` VALUES ('4017', '3999', '元朗区', '元朗区', '中国,香港特别行政区,元朗区', '3', 'yuanlang', null, null, null, null, null);

ALTER TABLE `yoshop_region` AUTO_INCREMENT=50001;

COMMIT;