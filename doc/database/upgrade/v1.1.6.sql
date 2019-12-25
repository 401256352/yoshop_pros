
# 微信小程序记录表：删除冗余字段
ALTER TABLE `yoshop_wxapp`
DROP COLUMN `app_name`,
DROP COLUMN `is_service`,
DROP COLUMN `service_image_id`,
DROP COLUMN `is_phone`,
DROP COLUMN `phone_no`,
DROP COLUMN `phone_image_id`;


# 微信小程序记录表：新增是否回收字段
ALTER TABLE `yoshop_wxapp`
ADD COLUMN `is_recycle`  tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否回收' AFTER `apikey`;


# 微信小程序记录表：新增伪删除字段
ALTER TABLE `yoshop_wxapp`
ADD COLUMN `is_delete`  tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除' AFTER `is_recycle`;


# 超管用户记录表
DROP TABLE IF EXISTS `yoshop_admin_user`;
CREATE TABLE `yoshop_admin_user` (
  `admin_user_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `user_name` varchar(255) NOT NULL DEFAULT '' COMMENT '用户名',
  `password` varchar(255) NOT NULL DEFAULT '' COMMENT '登录密码',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`admin_user_id`),
  KEY `user_name` (`user_name`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='超管用户记录表';

INSERT INTO `yoshop_admin_user` (`user_name`, `password`, `create_time`, `update_time`) VALUES ('admin', '9ae7b2e6f25c907a1fc81b503b16e25f', '1529926348', '1540194026');


# 商家用户记录表：新增是否为超级管理员字段
ALTER TABLE `yoshop_store_user`
ADD COLUMN `is_super`  tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否为超级管理员' AFTER `password`;


# 商家用户记录表：新增伪删除字段
ALTER TABLE `yoshop_store_user`
ADD COLUMN `is_delete`  tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除' AFTER `is_super`;


# 商家用户记录表：新增姓名字段
ALTER TABLE `yoshop_store_user`
ADD COLUMN `real_name`  varchar(255) NOT NULL DEFAULT '' COMMENT '姓名' AFTER `password`;


# 商家用户记录表：设置默认姓名
UPDATE `yoshop_store_user` SET `real_name` = '管理员' WHERE 1;


# 商家用户记录表：删除用户名唯一索引
ALTER TABLE `yoshop_store_user`
DROP INDEX `user_name` ,
ADD INDEX `user_name` (`user_name`);


# 商家用户权限表
CREATE TABLE `yoshop_store_access` (
  `access_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '权限名称',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '权限url',
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '父级id',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '100' COMMENT '排序(数字越小越靠前)',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`access_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10108 DEFAULT CHARSET=utf8 COMMENT='商家用户权限表';


INSERT INTO `yoshop_store_access` VALUES ('10001', '首页', 'index/index', '0', '100', '1540628721', '1540781975');
INSERT INTO `yoshop_store_access` VALUES ('10002', '管理员', 'store', '0', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10003', '管理员管理', 'store.user', '10002', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10004', '管理员列表', 'store.user/index', '10003', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10005', '添加管理员', 'store.user/add', '10003', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10006', '编辑管理员', 'store.user/edit', '10003', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10007', '删除管理员', 'store.user/delete', '10003', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10008', '角色管理', 'store.role', '10002', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10009', '角色列表', 'store.role/index', '10008', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10010', '添加角色', 'store.role/add', '10008', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10011', '编辑角色', 'store.role/edit', '10008', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10012', '删除角色', 'store.role/delete', '10008', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10013', '权限管理', 'store.access', '10002', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10014', '权限列表', 'store.access/index', '10013', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10015', '添加权限', 'store.access/add', '10013', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10016', '编辑权限', 'store.access/edit', '10013', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10017', '删除权限', 'store.access/delete', '10013', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10018', '商品管理', 'goods', '0', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10019', '商品管理', 'goods', '10018', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10020', '商品列表', 'goods/index', '10019', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10021', '添加商品', 'goods/add', '10019', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10022', '编辑商品', 'goods/edit', '10019', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10023', '复制商品', 'goods/copy', '10019', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10024', '删除商品', 'goods/delete', '10019', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10025', '商品上下架', 'goods/state', '10019', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10026', '商品分类', 'goods.category', '10018', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10027', '分类列表', 'goods.category/index', '10026', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10028', '添加分类', 'goods.category/add', '10026', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10029', '编辑分类', 'goods.category/edit', '10026', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10030', '删除分类', 'goods.category/delete', '10026', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10031', '商品评价', 'goods.comment', '10018', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10032', '评价列表', 'goods.comment/index', '10031', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10033', '评价详情', 'goods.comment/detail', '10031', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10034', '删除评价', 'goods.comment/delete', '10031', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10035', '订单管理', 'order', '0', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10036', '订单列表', '', '10035', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10037', '待发货', 'order/delivery_list', '10036', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10038', '待收货', 'order/receipt_list', '10036', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10039', '待付款', 'order/pay_list', '10036', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10040', '已完成', 'order/complete_list', '10036', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10041', '已取消', 'order/cancel_list', '10036', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10042', '全部订单', 'order/all_list', '10036', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10043', '订单详情', '', '10035', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10044', '详情信息', 'order/detail', '10043', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10045', '确认发货', 'order/delivery', '10043', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10046', '修改订单价格', 'order/updateprice', '10043', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10047', '订单导出', 'order.operate/export', '10035', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10048', '批量发货', 'order.operate/batchdelivery', '10035', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10049', '用户管理', 'user', '0', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10050', '用户列表', 'user/index', '10049', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10051', '删除用户', 'user/delete', '10049', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10052', '营销设置', 'market', '0', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10053', '优惠券', 'coupon', '10052', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10054', '优惠券列表', 'market.coupon/index', '10053', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10055', '新增优惠券', 'market.coupon/add', '10053', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10056', '编辑优惠券', 'market.coupon/edit', '10053', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10057', '删除优惠券', 'market.coupon/delete', '10053', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10058', '领取记录', 'market.coupon/receive', '10053', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10059', '小程序', 'wxapp', '0', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10060', '小程序设置', 'wxapp/setting', '10059', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10061', '页面管理', 'wxapp.page', '10059', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10062', '页面设计', '', '10061', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10063', '页面列表', 'wxapp.page/index', '10062', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10064', '新增页面', 'wxapp.page/add', '10062', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10065', '编辑页面', 'wxapp.page/edit', '10062', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10066', '设为首页', 'wxapp.page/sethome', '10062', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10067', '分类页模板', 'wxapp.page/category', '10061', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10068', '页面链接', 'wxapp.page/links', '10061', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10069', '帮助中心', 'wxapp.help', '10059', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10070', '帮助列表', 'wxapp.help/index', '10069', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10071', '新增帮助', 'wxapp.help/add', '10069', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10072', '编辑帮助', 'wxapp.help/edit', '10069', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10073', '删除帮助', 'wxapp.help/delete', '10069', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10074', '应用中心', 'apps', '0', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10075', '分销中心', 'apps.dealer', '10074', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10076', '入驻申请', 'apps.dealer.apply', '10075', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10077', '申请列表', 'apps.dealer.apply/index', '10076', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10078', '分销商审核', 'apps.dealer.apply/submit', '10076', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10079', '分销商用户', 'apps.dealer.user', '10075', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10080', '分销商列表', 'apps.dealer.user/index', '10079', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10081', '删除分销商', 'apps.dealer.user/delete', '10079', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10082', '分销商二维码', 'apps.dealer.user/qrcode', '10079', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10083', '分销订单', 'apps.dealer.order/index', '10075', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10084', '提现申请', 'apps.dealer.withdraw', '10075', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10085', '申请列表', 'apps.dealer.withdraw/index', '10084', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10086', '提现审核', 'apps.dealer.withdraw/submit', '10084', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10087', '确认打款', 'apps.dealer.withdraw/money', '10084', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10088', '分销设置', 'apps.dealer.setting/index', '10075', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10089', '分销海报', 'apps.dealer.setting/qrcode', '10075', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10090', '设置', 'setting', '0', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10091', '商城设置', 'setting/store', '10090', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10092', '交易设置', 'setting/trade', '10090', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10093', '配送设置', 'setting.delivery', '10090', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10094', '运费模板列表', 'setting.delivery/index', '10093', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10095', '新增运费模板', 'setting.delivery/add', '10093', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10096', '编辑运费模板', 'setting.delivery/edit', '10093', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10097', '删除运费模板', 'setting.delivery/delete', '10093', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10098', '物流公司', 'setting.express', '10090', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10099', '物流公司列表', 'setting.express/index', '10098', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10100', '新增物流公司', 'setting.express/add', '10098', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10101', '编辑物流公司', 'setting.express/edit', '10098', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10102', '删除物流公司', 'setting.express/delete', '10098', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10103', '短信通知', 'setting/sms', '10090', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10104', '模板消息', 'setting/tplmsg', '10090', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10105', '上传设置', 'setting/storage', '10090', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10106', '其他', '', '10090', '100', '1540628721', '1540628721');
INSERT INTO `yoshop_store_access` VALUES ('10107', '清理缓存', 'setting.cache/clear', '10106', '100', '1540628721', '1540628721');


# 商家用户角色表
CREATE TABLE `yoshop_store_role` (
  `role_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '角色id',
  `role_name` varchar(50) NOT NULL DEFAULT '' COMMENT '角色名称',
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '父级角色id',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '100' COMMENT '排序(数字越小越靠前)',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='商家用户角色表';


# 商家用户角色权限关系表
CREATE TABLE `yoshop_store_role_access` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `role_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '角色id',
  `access_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '权限id',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='商家用户角色权限关系表';


# 商家用户角色记录表
CREATE TABLE `yoshop_store_user_role` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `store_user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '超管用户id',
  `role_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '角色id',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `admin_user_id` (`store_user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='商家用户角色记录表';

