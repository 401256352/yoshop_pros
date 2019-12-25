
START TRANSACTION;

ALTER TABLE `yoshop_user_grade_log` ADD COLUMN `remark`  varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '管理员备注' AFTER `change_type`;

COMMIT;