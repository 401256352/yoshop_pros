

# 新增权限url：分销中心下级用户列表
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10336', '下级用户列表', 'apps.dealer.user/fans', '10079', '100', '1545189676', '1545189676');


# 微信小程序分类页模板：分享标题字段增加 varchar长度
ALTER TABLE `yoshop_wxapp_category`
MODIFY COLUMN `share_title`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '分享标题' AFTER `category_style`;



-- 主商品 -- 
ALTER TABLE `yoshop_goods` ADD COLUMN `is_ind_dealer` TINYINT (3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否开启单独分销(0关闭 1开启)' AFTER `delivery_id`,
 ADD COLUMN `dealer_money_type` TINYINT (3) UNSIGNED NOT NULL DEFAULT 10 COMMENT '分销佣金类型(10百分比 20固定金额)' AFTER `is_ind_dealer`,
 ADD COLUMN `first_money` DECIMAL (10, 2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '分销佣金(一级)' AFTER `dealer_money_type`,
 ADD COLUMN `second_money` DECIMAL (10, 2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '分销佣金(二级)' AFTER `first_money`,
 ADD COLUMN `third_money` DECIMAL (10, 2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '分销佣金(三级)' AFTER `second_money`;


ALTER TABLE `yoshop_order_goods` ADD COLUMN `is_ind_dealer` TINYINT (3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否开启单独分销(0关闭 1开启)' AFTER `total_pay_price`,
 ADD COLUMN `dealer_money_type` TINYINT (3) UNSIGNED NOT NULL DEFAULT 10 COMMENT '分销佣金类型(10百分比 20固定金额)' AFTER `is_ind_dealer`,
 ADD COLUMN `first_money` DECIMAL (10, 2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '分销佣金(一级)' AFTER `dealer_money_type`,
 ADD COLUMN `second_money` DECIMAL (10, 2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '分销佣金(二级)' AFTER `first_money`,
 ADD COLUMN `third_money` DECIMAL (10, 2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '分销佣金(三级)' AFTER `second_money`;

 

 -- 拼团 --

 ALTER TABLE `yoshop_sharing_goods` ADD COLUMN `is_ind_dealer` TINYINT (3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否开启单独分销(0关闭 1开启)' AFTER `delivery_id`,
 ADD COLUMN `dealer_money_type` TINYINT (3) UNSIGNED NOT NULL DEFAULT 10 COMMENT '分销佣金类型(10百分比 20固定金额)' AFTER `is_ind_dealer`,
 ADD COLUMN `first_money` DECIMAL (10, 2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '分销佣金(一级)' AFTER `dealer_money_type`,
 ADD COLUMN `second_money` DECIMAL (10, 2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '分销佣金(二级)' AFTER `first_money`,
 ADD COLUMN `third_money` DECIMAL (10, 2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '分销佣金(三级)' AFTER `second_money`;


ALTER TABLE `yoshop_sharing_order_goods` ADD COLUMN `is_ind_dealer` TINYINT (3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否开启单独分销(0关闭 1开启)' AFTER `total_pay_price`,
 ADD COLUMN `dealer_money_type` TINYINT (3) UNSIGNED NOT NULL DEFAULT 10 COMMENT '分销佣金类型(10百分比 20固定金额)' AFTER `is_ind_dealer`,
 ADD COLUMN `first_money` DECIMAL (10, 2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '分销佣金(一级)' AFTER `dealer_money_type`,
 ADD COLUMN `second_money` DECIMAL (10, 2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '分销佣金(二级)' AFTER `first_money`,
 ADD COLUMN `third_money` DECIMAL (10, 2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '分销佣金(三级)' AFTER `second_money`;

