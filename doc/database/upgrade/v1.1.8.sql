
# 商品规格表：规格图片
ALTER TABLE `yoshop_goods_sku`
ADD COLUMN `image_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '规格图片id' AFTER `spec_sku_id`;


# 用户收货地址表：新市辖区
ALTER TABLE `yoshop_user_address`
ADD COLUMN `district`  varchar(255) NULL DEFAULT '' COMMENT '新市辖区(该字段用于记录region表中没有的市辖区)' AFTER `region_id`;


