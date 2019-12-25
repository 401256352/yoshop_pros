
# 用户记录表：新增伪删除字段
ALTER TABLE `yoshop_user`
ADD COLUMN `is_delete`  tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除' AFTER `money`;

