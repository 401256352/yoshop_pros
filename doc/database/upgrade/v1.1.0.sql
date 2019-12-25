
# 微信小程序diy页面表：页面标题改为页面名称
ALTER TABLE `yoshop_wxapp_page`
CHANGE COLUMN `page_title` `page_name`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '页面名称' AFTER `page_type`;


# 微信小程序diy页面表：新增字段 软删除
ALTER TABLE `yoshop_wxapp_page`
ADD COLUMN `is_delete`  tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '软删除' AFTER `wxapp_id`;


# 微信小程序diy页面表：添加页面名称
UPDATE `yoshop_wxapp_page` SET `page_name` = '小程序首页' WHERE page_id = 10001;


# 订单记录表：新增字段 后台修改的订单金额（差价）
ALTER TABLE `yoshop_order`
ADD COLUMN `update_price`  decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '后台修改的订单金额（差价）' AFTER `pay_price`;


# 微信小程序分类页模板表
CREATE TABLE `yoshop_wxapp_category` (
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `category_style` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '分类页样式(10一级分类[大图] 11一级分类[小图] 20二级分类)',
  `share_title` varchar(10) NOT NULL DEFAULT '' COMMENT '分享标题',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`wxapp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信小程序分类页模板';

INSERT INTO `yoshop_wxapp_category` (`wxapp_id`, `category_style`, `share_title`, `create_time`, `update_time`) VALUES ('10001', '10', '', '1536373988', '1536375112');
