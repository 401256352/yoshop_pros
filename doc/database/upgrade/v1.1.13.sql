

# 新增文章记录表
CREATE TABLE `yoshop_article` (
  `article_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '文章id',
  `article_title` varchar(300) NOT NULL DEFAULT '' COMMENT '文章标题',
  `show_type` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '列表显示方式(10小图展示 20大图展示)',
  `category_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '文章分类id',
  `image_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '封面图id',
  `article_content` longtext NOT NULL COMMENT '文章内容',
  `article_sort` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '文章排序(数字越小越靠前)',
  `article_status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '文章状态(0隐藏 1显示)',
  `virtual_views` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '虚拟阅读量(仅用作展示)',
  `actual_views` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '实际阅读量',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`article_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='文章记录表';


# 新增文章分类表
CREATE TABLE `yoshop_article_category` (
  `category_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '商品分类id',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '分类名称',
  `sort` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '排序方式(数字越小越靠前)',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='文章分类表';


# 新增权限url：后台文章管理
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10337', '内容管理', 'content', '0', '100', '1547018818', '1547018818');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10338', '文章管理', 'content.article', '10337', '100', '1547018849', '1547018869');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10339', '文章列表', 'content.article/index', '10338', '100', '1547018885', '1547018885');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10340', '添加文章', 'content.article/add', '10338', '100', '1547018901', '1547018901');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10341', '编辑文章', 'content.article/edit', '10338', '100', '1547018922', '1547018922');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10342', '删除文章', 'content.article/delete', '10338', '100', '1547018937', '1547018937');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10343', '文章分类', 'content.article.category', '10337', '100', '1547018972', '1547018972');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10344', '分类列表', 'content.article.category/index', '10343', '100', '1547018992', '1547018992');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10345', '添加分类', 'content.article.category/add', '10343', '100', '1547019008', '1547019017');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10346', '编辑分类', 'content.article.category/edit', '10343', '100', '1547019008', '1547019017');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10347', '删除分类', 'content.article.category/delete', '10343', '100', '1547019008', '1547019017');


