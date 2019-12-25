
START TRANSACTION;

CREATE TABLE `yoshop_bargain_active` (
  `active_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '砍价活动id',
  `goods_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商品id',
  `start_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '活动开始时间',
  `end_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '活动结束时间',
  `expiryt_time` int(11) unsigned NOT NULL DEFAULT '1' COMMENT '砍价有效期(单位：小时)',
  `floor_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '砍价底价',
  `peoples` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '帮砍人数',
  `is_self_cut` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '可自砍一刀(0禁止 1允许)',
  `is_floor_buy` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '必须底价购买(0否 1是)',
  `share_title` varchar(500) NOT NULL DEFAULT '' COMMENT '分享标题',
  `prompt_words` varchar(500) NOT NULL DEFAULT '' COMMENT '砍价助力语',
  `actual_sales` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '活动销量(实际的)',
  `initial_sales` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '虚拟销量',
  `sort` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '排序(数字越小越靠前)',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '活动状态(1启用 0禁用)',
  `is_delete` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`active_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='砍价活动表';


CREATE TABLE `yoshop_bargain_setting` (
  `key` varchar(30) NOT NULL DEFAULT '' COMMENT '设置项标示',
  `describe` varchar(255) NOT NULL DEFAULT '' COMMENT '设置项描述',
  `values` mediumtext NOT NULL COMMENT '设置内容(json格式)',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  UNIQUE KEY `unique_key` (`key`,`wxapp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='砍价活动设置表';


CREATE TABLE `yoshop_bargain_task` (
  `task_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '砍价任务id',
  `active_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '砍价活动id',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id(发起人)',
  `goods_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商品id',
  `spec_sku_id` varchar(255) NOT NULL DEFAULT '' COMMENT '商品sku标识',
  `goods_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '商品原价',
  `floor_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '砍价底价',
  `peoples` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '帮砍人数',
  `cut_people` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '已砍人数',
  `section` text NOT NULL COMMENT '砍价金额区间',
  `cut_money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '已砍金额',
  `actual_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '实际购买金额',
  `is_floor` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否已砍到底价(0否 1是)',
  `end_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '任务截止时间',
  `is_buy` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否购买(0未购买 1已购买)',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '任务状态 (0已结束 1砍价中)',
  `is_delete` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`task_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='砍价任务表';


CREATE TABLE `yoshop_bargain_task_help` (
  `help_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `active_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '砍价活动id',
  `task_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '砍价任务id',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `is_creater` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否为发起人(0否 1是)',
  `cut_money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '砍掉的金额',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`help_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='砍价任务助力记录表';


ALTER TABLE `yoshop_order`
ADD COLUMN `order_source`  tinyint(3) UNSIGNED NOT NULL DEFAULT 10 COMMENT '订单来源(10普通订单 20砍价订单)' AFTER `is_comment`;


ALTER TABLE `yoshop_order`
ADD COLUMN `order_source_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '来源记录id' AFTER `order_source`;


INSERT INTO `yoshop_store_access` VALUES ('10426', '砍价活动', 'apps.bargain', '10074', '100', '1559615418', '1559615441');
INSERT INTO `yoshop_store_access` VALUES ('10427', '砍价活动管理', 'apps.bargain.active', '10426', '100', '1559615566', '1559615566');
INSERT INTO `yoshop_store_access` VALUES ('10428', '砍价活动列表', 'apps.bargain.active/index', '10427', '100', '1559615601', '1559615601');
INSERT INTO `yoshop_store_access` VALUES ('10429', '新增砍价活动', 'apps.bargain.active/add', '10427', '105', '1559615601', '1559615601');
INSERT INTO `yoshop_store_access` VALUES ('10430', '编辑砍价活动', 'apps.bargain.active/edit', '10427', '110', '1559615601', '1559615601');
INSERT INTO `yoshop_store_access` VALUES ('10431', '删除砍价活动', 'apps.bargain.active/delete', '10427', '115', '1559615601', '1559615601');
INSERT INTO `yoshop_store_access` VALUES ('10432', '砍价记录', 'apps.bargain.task', '10426', '105', '1559615788', '1559615788');
INSERT INTO `yoshop_store_access` VALUES ('10433', '砍价记录列表', 'apps.bargain.task/index', '10432', '100', '1559615815', '1559615815');
INSERT INTO `yoshop_store_access` VALUES ('10434', '砍价助力榜', 'apps.bargain.task/help', '10432', '105', '1559615850', '1559615850');
INSERT INTO `yoshop_store_access` VALUES ('10435', '删除砍价记录', 'apps.bargain.task/delete', '10432', '110', '1559615878', '1559615878');
INSERT INTO `yoshop_store_access` VALUES ('10436', '砍价设置', 'apps.bargain.setting/index', '10426', '110', '1559615946', '1559615979');

INSERT INTO `yoshop_store_access` VALUES ('10437', '复制主商城商品', 'apps.sharing.goods/copy_master', '10306', '112', '1559615946', '1559615979');





UPDATE `yoshop_store_access` SET `sort`='105' WHERE (`access_id`='10021');
UPDATE `yoshop_store_access` SET `sort`='110' WHERE (`access_id`='10022');
UPDATE `yoshop_store_access` SET `sort`='115' WHERE (`access_id`='10023');
UPDATE `yoshop_store_access` SET `sort`='120' WHERE (`access_id`='10024');
UPDATE `yoshop_store_access` SET `sort`='125' WHERE (`access_id`='10025');

UPDATE `yoshop_store_access` SET `sort`='105' WHERE (`access_id`='10307');
UPDATE `yoshop_store_access` SET `sort`='110' WHERE (`access_id`='10308');
UPDATE `yoshop_store_access` SET `sort`='115' WHERE (`access_id`='10309');
UPDATE `yoshop_store_access` SET `sort`='120' WHERE (`access_id`='10310');
UPDATE `yoshop_store_access` SET `sort`='125' WHERE (`access_id`='10311');




COMMIT;