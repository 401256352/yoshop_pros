

# 小票打印机记录表
CREATE TABLE `yoshop_printer` (
  `printer_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '打印机id',
  `printer_name` varchar(255) NOT NULL DEFAULT '' COMMENT '打印机名称',
  `printer_type` varchar(255) NOT NULL DEFAULT '' COMMENT '打印机类型',
  `printer_config` text NOT NULL COMMENT '打印机配置',
  `print_times` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '打印联数(次数)',
  `sort` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '排序 (数字越小越靠前)',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`printer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='小票打印机记录表';



# 新增权限url：分销商提现微信付款
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10348', '微信付款', 'apps.dealer.withdraw/wechat_pay', '10084', '100', '1548232045', '1548232045');


# 新增权限url：小票打印机管理
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10349', '小票打印机', 'setting.printer', '10090', '100', '1548738285', '1548738285');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10350', '打印机管理', 'setting.printer', '10349', '100', '1548738718', '1548738718');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10351', '小票打印设置', 'setting/printer', '10349', '100', '1548738720', '1548738720');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10352', '小票打印机列表', 'setting.printer/index', '10350', '100', '1548738420', '1548738420');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10353', '新增小票打印机', 'setting.printer/add', '10350', '100', '1548738443', '1548738443');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10354', '编辑小票打印机', 'setting.printer/edit', '10350', '100', '1548738443', '1548738443');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10355', '删除小票打印机', 'setting.printer/delete', '10350', '100', '1548738443', '1548738443');


