-- 
-- 表的结构 `think_form`
-- 

CREATE TABLE `think_form` (
  `id` smallint(4) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `content` varchar(255) NOT NULL,
  `create_time` int(11) unsigned NOT NULL,
  `update_time` int(11) unsigned NOT NULL,
  `status` tinyint(1) unsigned NOT NULL,
  `email` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- 
-- 导出表中的数据 `think_form`
-- 
INSERT INTO `think_form` (`id`, `title`, `content`, `create_time`, `update_time`, `status`, `email`) VALUES
(1, '这是测试数据', 'dfdf', 1212724876, 0, 1, 'dddd@ddd.com');
