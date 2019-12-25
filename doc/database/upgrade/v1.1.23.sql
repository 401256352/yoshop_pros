
UPDATE `yoshop_store_access` SET `name`='营销管理' WHERE (`access_id`='10052')

ALTER TABLE `yoshop_order`
ADD COLUMN `is_delete`  tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除' AFTER `user_id`;

ALTER TABLE `yoshop_sharing_order`
ADD COLUMN `is_delete`  tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除' AFTER `user_id`;

