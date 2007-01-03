-- phpMyAdmin SQL Dump
-- version 2.9.0.3
-- http://www.phpmyadmin.net
-- 
-- 主机: localhost
-- 生成日期: 2007 年 01 月 03 日 08:41
-- 服务器版本: 5.0.27
-- PHP 版本: 5.2.0
-- 
-- 数据库: `thinkphp`
-- 

-- --------------------------------------------------------

-- 
-- 表的结构 `thinkphp_access`
-- 

CREATE TABLE `thinkphp_access` (
  `groupId` smallint(6) unsigned NOT NULL,
  `nodeId` smallint(6) unsigned NOT NULL,
  `level` tinyint(1) NOT NULL,
  `parentNodeId` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `thinkphp_access`
-- 

INSERT INTO `thinkphp_access` (`groupId`, `nodeId`, `level`, `parentNodeId`) VALUES 
(1, 5, 2, 4),
(1, 6, 2, 4),
(1, 7, 2, 4),
(1, 8, 2, 4),
(1, 9, 2, 4),
(1, 10, 2, 4),
(1, 11, 3, 10),
(1, 2, 2, 1),
(5, 3, 3, 2),
(1, 13, 2, 12),
(1, 14, 2, 12),
(1, 15, 2, 12),
(1, 16, 2, 12),
(1, 17, 2, 12),
(1, 18, 2, 12),
(1, 19, 2, 12),
(1, 20, 2, 12),
(10, 13, 2, 12),
(10, 15, 2, 12),
(10, 16, 2, 12),
(10, 19, 2, 12),
(10, 21, 2, 12),
(1, 11, 3, 10),
(1, 11, 3, 10),
(1, 11, 3, 10),
(10, 23, 3, 21),
(2, 37, 2, 34),
(2, 40, 2, 34),
(2, 42, 2, 34),
(2, 43, 2, 34),
(2, 51, 2, 34),
(3, 11, 3, 10),
(3, 11, 3, 10),
(3, 11, 3, 10),
(3, 11, 3, 10),
(3, 11, 3, 10),
(3, 58, 3, 37),
(4, 34, 1, 0),
(4, 58, 3, 37),
(5, 34, 1, 0),
(5, 58, 3, 37),
(6, 68, 2, 35),
(6, 69, 2, 35),
(6, 70, 2, 35),
(6, 71, 2, 35),
(6, 72, 2, 35),
(6, 73, 3, 68),
(4, 37, 2, 34),
(4, 40, 2, 34),
(4, 43, 2, 34),
(4, 60, 2, 34),
(4, 82, 2, 34),
(3, 62, 3, 60),
(3, 63, 3, 60),
(3, 64, 3, 60),
(3, 65, 3, 60),
(3, 66, 3, 60),
(3, 83, 3, 60),
(3, 84, 3, 60),
(3, 85, 3, 60),
(3, 86, 3, 60),
(5, 37, 2, 34),
(5, 43, 2, 34),
(5, 60, 2, 34),
(5, 102, 2, 34),
(5, 66, 3, 60),
(5, 83, 3, 60),
(6, 74, 3, 70),
(6, 75, 3, 70),
(6, 76, 3, 70),
(6, 77, 3, 70),
(6, 78, 3, 70),
(6, 79, 3, 70),
(6, 80, 3, 70),
(6, 81, 3, 70),
(6, 99, 3, 70),
(6, 100, 3, 70),
(6, 110, 3, 70),
(3, 37, 2, 34),
(3, 40, 2, 34),
(3, 42, 2, 34),
(3, 43, 2, 34),
(3, 52, 2, 34),
(3, 60, 2, 34),
(3, 82, 2, 34),
(4, 115, 3, 40),
(4, 116, 3, 40),
(4, 62, 3, 60),
(4, 63, 3, 60),
(4, 64, 3, 60),
(4, 65, 3, 60),
(4, 66, 3, 60),
(4, 83, 3, 60),
(4, 84, 3, 60),
(4, 85, 3, 60),
(4, 117, 3, 60),
(10, 34, 1, 0),
(10, 35, 1, 0),
(10, 36, 2, 34),
(6, 34, 1, 0),
(6, 35, 1, 0),
(1, 1, 1, 0),
(1, 2, 1, 0),
(3, 2, 1, 0),
(3, 19, 2, 2),
(3, 20, 2, 2),
(2, 1, 1, 0),
(2, 3, 2, 1),
(2, 4, 2, 1),
(2, 5, 2, 1),
(2, 6, 2, 1),
(2, 7, 2, 1),
(2, 8, 2, 1),
(2, 9, 2, 1),
(2, 10, 2, 1),
(2, 11, 2, 1),
(2, 12, 2, 1),
(2, 13, 2, 1),
(2, 14, 2, 1),
(2, 15, 2, 1),
(2, 16, 2, 1),
(2, 17, 2, 1),
(2, 18, 2, 1),
(2, 21, 3, 3),
(2, 22, 3, 3),
(2, 23, 3, 3),
(2, 25, 3, 3),
(2, 26, 3, 3),
(2, 27, 3, 3),
(2, 28, 3, 3),
(2, 29, 3, 5),
(2, 30, 3, 5),
(2, 31, 3, 5),
(2, 32, 3, 5),
(2, 36, 3, 11);

-- --------------------------------------------------------

-- 
-- 表的结构 `thinkphp_article`
-- 

CREATE TABLE `thinkphp_article` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(15) NOT NULL default '',
  `userId` mediumint(5) unsigned NOT NULL default '0',
  `categoryId` smallint(5) unsigned NOT NULL,
  `title` varchar(255) NOT NULL default '',
  `content` longtext NOT NULL,
  `password` varchar(32) NOT NULL default '',
  `cTime` int(11) unsigned NOT NULL default '0',
  `aTime` int(11) unsigned NOT NULL default '0',
  `mTime` int(11) unsigned NOT NULL default '0',
  `status` tinyint(1) unsigned NOT NULL default '0',
  `isRecommend` tinyint(1) unsigned NOT NULL default '0',
  `isTop` tinyint(1) unsigned NOT NULL default '0',
  `commentStatus` tinyint(1) unsigned NOT NULL default '0',
  `guid` varchar(50) NOT NULL default '',
  `readCount` mediumint(5) unsigned NOT NULL default '0',
  `commentCount` mediumint(5) unsigned NOT NULL default '0',
  `type` tinyint(1) unsigned NOT NULL default '1',
  `seqNo` mediumint(5) unsigned NOT NULL,
  `trackback` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

-- 
-- 导出表中的数据 `thinkphp_article`
-- 

INSERT INTO `thinkphp_article` (`id`, `name`, `userId`, `categoryId`, `title`, `content`, `password`, `cTime`, `aTime`, `mTime`, `status`, `isRecommend`, `isTop`, `commentStatus`, `guid`, `readCount`, `commentCount`, `type`, `seqNo`, `trackback`) VALUES 
(1, '', 1, 1, '新年快乐', '新年快乐~!<IMG src="http://localhost/ThinkCMS/Public/Uploads/{BCD82759-E1DE-6515-6F97-9126A052D725}.gif">', '', 1167728390, 0, 1167742043, 0, 0, 1, 1, '', 0, 1, 1, 0, ''),
(2, '', 0, 1, '测试文章', '的地方地方', '', 1167730779, 0, 0, 3, 0, 0, 1, '', 0, 1, 1, 0, ''),
(3, '', 0, 1, 'ThinkPHP发表', '地方地方地方', '', 1167731132, 0, 1167731285, 3, 0, 1, 1, '', 0, 3, 1, 0, '');

-- --------------------------------------------------------

-- 
-- 表的结构 `thinkphp_attach`
-- 

CREATE TABLE `thinkphp_attach` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) default NULL,
  `size` varchar(20) NOT NULL,
  `extension` varchar(20) NOT NULL,
  `savepath` varchar(255) NOT NULL,
  `savename` varchar(255) NOT NULL,
  `module` varchar(100) NOT NULL,
  `recordId` int(11) NOT NULL,
  `userId` int(11) unsigned default NULL,
  `uploadTime` int(11) unsigned default NULL,
  `downloadTime` mediumint(9) unsigned default NULL,
  `hash` varchar(32) NOT NULL,
  PRIMARY KEY  (`id`,`recordId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

-- 
-- 导出表中的数据 `thinkphp_attach`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `thinkphp_board`
-- 

CREATE TABLE `thinkphp_board` (
  `id` mediumint(5) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `content` longtext NOT NULL,
  `bTime` int(11) unsigned NOT NULL default '0',
  `eTime` int(11) unsigned NOT NULL default '0',
  `status` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `thinkphp_board`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `thinkphp_cache`
-- 

CREATE TABLE `thinkphp_cache` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `cachekey` varchar(255) character set utf8 NOT NULL,
  `expire` int(11) NOT NULL,
  `data` blob,
  `datasize` int(11) default NULL,
  `datacrc` varchar(32) character set utf8 default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `thinkphp_cache`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `thinkphp_category`
-- 

CREATE TABLE `thinkphp_category` (
  `id` mediumint(5) unsigned NOT NULL auto_increment,
  `name` varchar(30) NOT NULL default '',
  `title` varchar(50) NOT NULL default '',
  `remark` varchar(255) NOT NULL default '',
  `seqNo` mediumint(5) unsigned NOT NULL default '0',
  `pid` mediumint(5) unsigned NOT NULL default '0',
  `level` smallint(2) unsigned NOT NULL default '0',
  `status` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- 
-- 导出表中的数据 `thinkphp_category`
-- 

INSERT INTO `thinkphp_category` (`id`, `name`, `title`, `remark`, `seqNo`, `pid`, `level`, `status`) VALUES 
(1, 'default', '默认分类', '', 0, 0, 1, 1);

-- --------------------------------------------------------

-- 
-- 表的结构 `thinkphp_comment`
-- 

CREATE TABLE `thinkphp_comment` (
  `id` mediumint(5) unsigned NOT NULL auto_increment,
  `recordId` int(11) unsigned NOT NULL default '0',
  `userId` mediumint(5) unsigned NOT NULL default '0',
  `author` varchar(50) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `url` varchar(255) NOT NULL default '',
  `ip` varchar(25) NOT NULL default '',
  `content` text NOT NULL,
  `cTime` int(11) unsigned NOT NULL default '0',
  `agent` int(11) unsigned NOT NULL default '0',
  `status` tinyint(1) unsigned NOT NULL default '0',
  `module` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=30 ;

-- 
-- 导出表中的数据 `thinkphp_comment`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `thinkphp_config`
-- 

CREATE TABLE `thinkphp_config` (
  `id` mediumint(9) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `title` varchar(255) default NULL,
  `value` varchar(255) default NULL,
  `remark` varchar(255) default NULL,
  `type` tinyint(1) unsigned NOT NULL,
  `extra` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=74 ;

-- 
-- 导出表中的数据 `thinkphp_config`
-- 

INSERT INTO `thinkphp_config` (`id`, `name`, `title`, `value`, `remark`, `type`, `extra`, `group`) VALUES 
(1, 'WEB_DOMAIN', '网站域名', 'http://localhost/', '', 0, '', ''),
(3, 'WEB_TITLE', '网站标题', 'ThinkPHP 开源的PHP开发框架', '网站标题', 0, '', ''),
(5, 'ERROR_MESSAGE', '错误提示', '你浏览的页面暂时发生了错误，请稍候再试！', '', 3, '', ''),
(8, 'DEBUG_MODE', '调试模式', '1', '', 2, '关闭,开启', ''),
(9, 'WEB_LOG_RECORD', '日志记录', '0', '', 2, '关闭,开启', ''),
(10, 'LOG_FILE_SIZE', '日志文件大小', '2097152', '', 1, '', ''),
(11, 'DISPATCH_ON', 'URL调度', '1', '', 2, '关闭,开启', ''),
(12, 'DISPATCH_NAME', '调度器名称', 'FCSDispatcher', '', 0, '', ''),
(13, 'URL_MODEL', 'URL模式', '1', '0 普通模式 1 PATH_INFO 2 URL_REWRITE', 4, '0,1,2', ''),
(14, 'TEMPLATE_CHARSET', '模版字符集', 'utf-8', '', 0, '', ''),
(15, 'OUTPUT_CHARSET', '输出字符集', 'utf-8', '', 0, '', ''),
(16, 'TMPL_ENGINE_TYPE', '模版引擎', 'fcs', '', 0, '', ''),
(17, 'HTML_CACHE_ON', '静态缓存', '0', '', 2, '关闭,开启', ''),
(18, 'HTML_CACHE_TIME', '静态缓存有效期', '-1', '', 0, '', ''),
(19, 'TMPL_CACHE_ON', '模版缓存', '1', '', 2, '关闭,开启', ''),
(20, 'TMPL_CACHE_TIME', '模版缓存有效期', '-1', '', 0, '', ''),
(21, 'DB_CACHE_ON', '数据缓存', '0', '', 2, '关闭,开启', ''),
(22, 'DB_CACHE_TIME', '数据缓存有效期', '1000', '', 1, '', ''),
(23, 'DATA_CACHE_TYPE', '数据缓存类型', 'File', '', 4, 'File,Db,Shomp', ''),
(24, 'SHOW_RUN_TIME', '显示运行时间', '1', '', 2, '不显示,显示', ''),
(26, 'LIST_NUMBERS', '分页记录数', '25', '', 0, '', ''),
(27, 'PAGE_NUMBERS', '分页页面数', '5', '', 0, '', ''),
(28, 'TMPL_L_DELIM', '模版起始标记', '{', '', 0, '', ''),
(29, 'TMPL_R_DELIM', '模版结束标记', '}', '', 0, '', ''),
(30, 'TAGLIB_BEGIN', '标签库开始标记', '<', '', 0, '', ''),
(31, 'TAGLIB_END', '标签库结束标记', '>', '', 0, '', ''),
(32, 'CACHE_SERIAL_HEADER', '文件缓存开始标记', '<?php\\n//', '', 0, '', ''),
(33, 'CACHE_SERIAL_FOOTER', '文件缓存结束标记', '\\n?>', '', 0, '', ''),
(34, 'CONFIG_FILE_TYPE', '配置文件类型', 'Define', '', 0, '', ''),
(35, 'SHARE_MEM_SIZE', '共享内存分配大小', '1048576', '', 1, '', ''),
(36, 'DATA_CACHE_ON', '数据缓存', '0', '', 2, '关闭,开启', ''),
(37, 'DATA_CACHE_TIME', '数据缓存有效期', '1000', '', 1, '', ''),
(38, 'DATA_CACHE_COMPRESS', '数据缓存压缩', '1', '', 2, '不压缩,压缩', ''),
(39, 'DATA_CACHE_CHECK', '数据缓存校验', '0', '', 2, '不校验,校验', ''),
(40, 'DATA_CACHE_TABLE', '数据缓存表名', 'fcs_cache', '', 0, '', ''),
(41, 'SESSION_NAME', '会话名称', 'FCSID', '', 0, '', ''),
(42, 'SESSION_TYPE', '会话类型', 'File', '', 0, '', ''),
(43, 'SESSION_EXPIRE', '会话有效期', '10000', '', 1, '', ''),
(44, 'SESSION_TABLE', '会话数据表', 'fcs_session', '', 0, '', ''),
(45, 'USER_AUTH_ON', '用户认证', '1', '', 2, '关闭,开启', ''),
(46, 'USER_AUTH_TYPE', '用户认证类型', '2', '', 4, '1,2', ''),
(47, 'AUTH_PWD_ENCODER', '用户密码加密方式', 'md5', '', 0, '', ''),
(48, 'USER_AUTH_PROVIDER', '用户认证委托', 'DaoAuthentictionProvider', '', 0, '', ''),
(49, 'USER_AUTH_GATEWAY', '用户认证网关', '/Public/login', '', 0, '', ''),
(50, 'NOT_AUTH_MODULE', '无需认证模块', '', '', 0, '', ''),
(51, 'REQUIRE_AUTH_MODULE', '需要认证模块', '', '', 0, '', ''),
(52, 'TEMPLATE_SUFFIX', '模版文件后缀', '.html', '', 0, '', ''),
(53, 'CACHFILE_SUFFIX', '缓存文件后缀', '.php', '', 0, '', ''),
(54, 'HTMLFILE_SUFFIX', '静态文件后缀', '.shtml', '', 0, '', ''),
(55, 'DEFAULT_MODULE', '默认模块名', 'Index', '', 0, '', ''),
(56, 'DEFAULT_ACTION', '默认操作名', 'Index', '', 0, '', ''),
(57, 'DEFAULT_TEMPLATE', '默认模版名', 'default', '', 0, '', ''),
(58, 'VAR_MODULE', '模块访问变量', 'm', '', 0, '', ''),
(59, 'VAR_ACTION', '操作访问变量', 'a', '', 0, '', ''),
(60, 'PATH_MODEL', 'URL路径模式', '3', '', 4, '0,1,2,3', ''),
(61, 'PATH_DEPR', 'URL路径变量分割符', ',', '', 4, '0,1,2', ''),
(62, 'VAR_LANGUAGE', '语言变量', 'l', '', 0, '', ''),
(63, 'VAR_TEMPLATE', '模版变量', 't', '', 0, '', ''),
(64, 'DEFAULT_LANGUAGE', '默认语言', 'zh-cn', '', 0, '', ''),
(65, 'BIG_2_GB', '简繁转换', '0', '', 2, '关闭,开启', ''),
(66, 'VAR_PAGE', '分页变量', 'p', '', 0, '', ''),
(67, 'ERROR_PAGE', '错误页面', '', '', 0, '', ''),
(68, 'DB_CHARSET', '数据库编码', 'utf8', '', 0, '', '一般设置'),
(69, 'TIME_ZONE', '时区设置', 'PRC', '', 0, '', ''),
(70, 'DB_CACHE_MAX', '数据库最大缓存数', '5000', '', 1, '', ''),
(71, 'COMPRESS_PAGE', '页面压缩', '1', '', 2, '不压缩,压缩', ''),
(72, 'COOKIE_EXPIRE', 'cookie过期时间', '3000', '', 1, '', ''),
(73, 'TMPL_DENY_FUNC_LIST', '模版禁用函数', 'echo,exit', '', 0, '', '');


-- --------------------------------------------------------

-- 
-- 表的结构 `thinkphp_group`
-- 

CREATE TABLE `thinkphp_group` (
  `id` smallint(6) unsigned NOT NULL auto_increment,
  `name` varchar(20) NOT NULL,
  `pid` smallint(6) default NULL,
  `status` tinyint(1) unsigned default NULL,
  `remark` varchar(255) default NULL,
  `ename` varchar(5) default NULL,
  `requireRate` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- 
-- 导出表中的数据 `thinkphp_group`
-- 

INSERT INTO `thinkphp_group` (`id`, `name`, `pid`, `status`, `remark`, `ename`, `requireRate`) VALUES 
(1, '管理员组', 0, 1, '管理员权限组', NULL, 0),
(2, '普通用户组', 0, 1, '一般会员权限', NULL, 0),
(3, '编辑组', 2, 1, '编辑权限', NULL, 0);

-- --------------------------------------------------------

-- 
-- 表的结构 `thinkphp_groupuser`
-- 

CREATE TABLE `thinkphp_groupuser` (
  `groupId` mediumint(9) unsigned default NULL,
  `userId` mediumint(9) unsigned default NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `thinkphp_groupuser`
-- 

INSERT INTO `thinkphp_groupuser` (`groupId`, `userId`) VALUES 
(1, 1),
(3, 1),
(3, 2),
(3, 3),
(2, 2),
(2, 3);

-- --------------------------------------------------------

-- 
-- 表的结构 `thinkphp_memo`
-- 

CREATE TABLE `thinkphp_memo` (
  `id` mediumint(6) unsigned NOT NULL auto_increment,
  `label` varchar(255) NOT NULL,
  `memo` text,
  `createTime` varchar(25) NOT NULL,
  `userId` mediumint(8) NOT NULL,
  `type` varchar(15) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- 
-- 导出表中的数据 `thinkphp_memo`
-- 

-- --------------------------------------------------------

-- 
-- 表的结构 `thinkphp_node`
-- 

CREATE TABLE `thinkphp_node` (
  `id` smallint(6) unsigned NOT NULL auto_increment,
  `name` varchar(20) NOT NULL,
  `title` varchar(50) default NULL,
  `status` tinyint(1) unsigned default NULL,
  `remark` varchar(255) default NULL,
  `seqNo` smallint(6) unsigned default NULL,
  `pid` smallint(6) unsigned NOT NULL,
  `level` tinyint(1) unsigned NOT NULL,
  `type` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=40 ;

-- 
-- 导出表中的数据 `thinkphp_node`
-- 

INSERT INTO `thinkphp_node` (`id`, `name`, `title`, `status`, `remark`, `seqNo`, `pid`, `level`, `type`) VALUES 
(1, 'Admin', '后台管理', 1, NULL, 1, 0, 1, 0),
(2, 'CMS', 'CMS前台', 1, '', 2, 0, 1, 0),
(3, 'Public', '公共模块', 1, '', 2, 1, 2, 0),
(4, 'Node', '节点管理', 1, '', 10, 1, 2, 0),
(5, 'Group', '权限管理', 1, '', 11, 1, 2, 0),
(6, 'Article', '文章管理', 1, '', 6, 1, 2, 0),
(7, 'Category', '分类管理', 1, '', 7, 1, 2, 0),
(8, 'Comment', '评论管理', 1, '', 8, 1, 2, 0),
(9, 'Attach', '附件管理', 1, '', 13, 1, 2, 0),
(10, 'User', '用户管理', 1, '', 9, 1, 2, 0),
(11, 'File', '文件管理', 1, '', 12, 1, 2, 0),
(12, 'System', '系统管理', 1, '', 14, 1, 2, 0),
(13, 'DBManager', '数据库管理', 1, '', 17, 1, 2, 0),
(14, 'Index', '默认模块', 1, '', 3, 1, 2, 0),
(15, 'Board', '公告管理', 1, '', 1, 1, 2, 0),
(16, 'Config', '系统配置', 1, '', 16, 1, 2, 0),
(17, 'Page', '页面管理', 1, '', 4, 1, 2, 0),
(18, 'PlugIn', '插件管理', 1, '', 15, 1, 2, 0),
(19, 'Index', '首页', 1, '', NULL, 2, 2, 0),
(20, 'Article', '文章查看', 1, '', NULL, 2, 2, 0),
(21, 'add', '新增', 1, '', NULL, 3, 3, 0),
(22, 'insert', '插入', 1, '', NULL, 3, 3, 0),
(23, 'edit', '编辑', 1, '', NULL, 3, 3, 0),
(24, 'update', '保存', 1, '', NULL, 3, 3, 0),
(25, 'index', '默认操作', 1, '', NULL, 3, 3, 0),
(26, 'sadd', '简易添加', 1, '', NULL, 3, 3, 0),
(27, 'sedit', '简易编辑', 1, '', NULL, 3, 3, 0),
(28, 'ssort', '简易排序', 1, '', NULL, 3, 3, 0),
(29, 'suser', '组用户列表', 1, '', NULL, 5, 3, 0),
(30, 'sapp', '项目授权', 1, '', NULL, 5, 3, 0),
(31, 'smodule', '模块授权', 1, '', NULL, 5, 3, 0),
(32, 'saction', '操作授权', 1, '', NULL, 5, 3, 0),
(33, 'setApp', '项目授权保存', 1, '', NULL, 5, 3, 0),
(34, 'setModule', '模块授权保存', 1, '', NULL, 5, 3, 0),
(35, 'setAction', '操作授权保存', 1, '', NULL, 5, 3, 0),
(36, 'sindex', '目录列表', 1, '', NULL, 11, 3, 0),
(37, 'select', '附件挑选', 1, '', NULL, 9, 3, 0);


-- --------------------------------------------------------

-- 
-- 表的结构 `thinkphp_plugin`
-- 

CREATE TABLE `thinkphp_plugin` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` tinyint(1) unsigned NOT NULL,
  `version` varchar(10) NOT NULL,
  `uri` varchar(255) NOT NULL,
  `file` varchar(255) NOT NULL,
  `app` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

-- 
-- 导出表中的数据 `thinkphp_plugin`
-- 

-- --------------------------------------------------------

-- 
-- 表的结构 `thinkphp_session`
-- 

CREATE TABLE `thinkphp_session` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `cachekey` varchar(255) character set utf8 NOT NULL,
  `expire` int(11) NOT NULL,
  `data` blob,
  `datasize` int(11) default NULL,
  `datacrc` varchar(32) character set utf8 default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ;

-- 
-- 导出表中的数据 `thinkphp_session`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `thinkphp_user`
-- 

CREATE TABLE `thinkphp_user` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(30) NOT NULL default '',
  `nickname` varchar(50) NOT NULL default '',
  `password` varchar(32) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `url` varchar(255) NOT NULL default '',
  `verify` varchar(8) NOT NULL default '',
  `rTime` int(11) unsigned NOT NULL default '0',
  `lTime` int(11) unsigned NOT NULL default '0',
  `guid` varchar(32) NOT NULL default '',
  `status` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- 
-- 导出表中的数据 `thinkphp_user`
-- 

INSERT INTO `thinkphp_user` (`id`, `name`, `nickname`, `password`, `email`, `url`, `verify`, `rTime`, `lTime`, `guid`, `status`) VALUES 
(1, 'admin', 'admin', '21232f297a57a5a743894a0e4a801fc3', 'liu21st@gmail.com', 'http://blog.topthink.com.cn', '0', 0, 1167809014, '', 1);
