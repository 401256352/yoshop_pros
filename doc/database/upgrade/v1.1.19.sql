

# 文件库记录表：新增 "是否已回收" 字段
ALTER TABLE `yoshop_upload_file` 
ADD COLUMN `is_recycle`  tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否已回收' AFTER `is_user`;


# 文件库分组记录表：新增 "是否删除" 字段
ALTER TABLE `yoshop_upload_group` 
ADD COLUMN `is_delete`  tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除' AFTER `sort`;


# 新增权限url：后台文件库管理

INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10375', '文件库管理', 'content.files.group', '10337', '105', '1552634170', '1552634170');

INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10376', '文件分组', 'content.files.group', '10375', '100', '1552634170', '1552634170');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10377', '分组列表', 'content.files.group/index', '10376', '100', '1552634170', '1552634170');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10378', '添加分组', 'content.files.group/add', '10376', '100', '1552634170', '1552634170');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10379', '编辑分组', 'content.files.group/edit', '10376', '100', '1552634170', '1552634170');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10380', '删除分组', 'content.files.group/delete', '10376', '100', '1552634170', '1552634170');

INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10381', '文件管理', 'content.files.group', '10375', '100', '1552634170', '1552634170');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10382', '文件列表', 'content.files.group/index', '10381', '105', '1552634170', '1552634170');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10383', '回收站列表', 'content.files.group/recycle', '10381', '110', '1552634170', '1552634170');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10384', '移入回收站', 'content.files.group/add', '10381', '115', '1552634170', '1552634170');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10385', '回收站还原', 'content.files.group/edit', '10381', '120', '1552634170', '1552634170');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10386', '删除文件', 'content.files.group/delete', '10381', '125', '1552634170', '1552634170');

