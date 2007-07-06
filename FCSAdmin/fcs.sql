-- phpMyAdmin SQL Dump
-- version 2.8.0.3
-- http://www.phpmyadmin.net
-- 
-- 主机: localhost
-- 生成日期: 2006 年 09 月 26 日 23:38
-- 服务器版本: 5.0.18
-- PHP 版本: 4.4.2
-- 
-- 数据库: `fcs`
-- 

-- --------------------------------------------------------

-- 
-- 表的结构 `fcs_access`
-- 

CREATE TABLE `fcs_access` (
  `groupId` smallint(6) unsigned NOT NULL,
  `nodeId` smallint(6) unsigned NOT NULL,
  `level` tinyint(1) NOT NULL,
  `parentNodeId` smallint(6) NOT NULL
) ENGINE=InnoDB ;

-- 
-- 导出表中的数据 `fcs_access`
-- 
-- --------------------------------------------------------

-- 
-- 表的结构 `fcs_attach`
-- 

CREATE TABLE `fcs_attach` (
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
-- 导出表中的数据 `fcs_attach`
-- 



-- --------------------------------------------------------

-- 
-- 表的结构 `fcs_cache`
-- 

CREATE TABLE `fcs_cache` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `cachekey` varchar(255) character set utf8 NOT NULL,
  `expire` int(11) NOT NULL,
  `data` blob,
  `datasize` int(11) default NULL,
  `datacrc` varchar(32) character set utf8 default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ;

-- 
-- 导出表中的数据 `fcs_cache`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `fcs_config`
-- 

CREATE TABLE `fcs_config` (
  `id` mediumint(9) NOT NULL auto_increment,
  `title` varchar(255) default NULL,
  `value` varchar(255) default NULL,
  `remark` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8  ;

-- 
-- 导出表中的数据 `fcs_config`

-- --------------------------------------------------------

-- 
-- 表的结构 `fcs_group`
-- 

CREATE TABLE `fcs_group` (
  `id` smallint(6) unsigned NOT NULL auto_increment,
  `name` varchar(20) NOT NULL,
  `parentId` smallint(6) default NULL,
  `status` tinyint(1) unsigned default NULL,
  `remark` varchar(255) default NULL,
  `ename` varchar(5) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8  ;

-- 
-- 导出表中的数据 `fcs_group`
-- 

INSERT INTO `fcs_group` (`id`, `name`, `parentId`, `status`, `remark`, `ename`) VALUES (1, '管理员组', 0, 1, '管理员权限', NULL),
(2, '测试组', 0, 1, '测试用途', NULL);


-- --------------------------------------------------------

-- 
-- 表的结构 `fcs_groupuser`
-- 

CREATE TABLE `fcs_groupuser` (
  `groupId` mediumint(9) unsigned default NULL,
  `userId` mediumint(9) unsigned default NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `fcs_groupuser`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `fcs_log`
-- 

CREATE TABLE `fcs_log` (
  `id` int(11) NOT NULL auto_increment,
  `module` varchar(20) NOT NULL,
  `action` varchar(20) NOT NULL,
  `time` varchar(20) default NULL,
  `userId` int(11) default NULL,
  `remark` varchar(500) NOT NULL,
  `url` varchar(500) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `fcs_log`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `fcs_login`
-- 

CREATE TABLE `fcs_login` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `userId` int(11) unsigned default NULL,
  `inTime` varchar(25) default NULL,
  `loginIp` varchar(50) default NULL,
  `type` tinyint(4) unsigned default NULL,
  `outTime` varchar(25) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8  ;

-- 
-- 导出表中的数据 `fcs_login`
-- 

-- --------------------------------------------------------

-- 
-- 表的结构 `fcs_node`
-- 

CREATE TABLE `fcs_node` (
  `id` smallint(6) unsigned NOT NULL auto_increment,
  `name` varchar(20) NOT NULL,
  `title` varchar(50) default NULL,
  `status` tinyint(1) unsigned default NULL,
  `remark` varchar(255) default NULL,
  `seqNo` smallint(6) unsigned default NULL,
  `parentId` smallint(6) unsigned NOT NULL,
  `level` tinyint(1) unsigned NOT NULL,
  `type` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `fcs_node`
-- 

INSERT INTO `fcs_node` VALUES (1, 'FCSAdmin', 'FCS框架后台', 1, '', 1, 0, 1, 0);
INSERT INTO `fcs_node` VALUES (2, 'Node', '节点管理', 1, '', NULL, 1, 2, 0);
INSERT INTO `fcs_node` VALUES (3, 'add', '增加', 1, '', NULL, 2, 3, 0);
INSERT INTO `fcs_node` VALUES (5, 'Node', '节点管理', 1, '', 6, 4, 2, 0);
INSERT INTO `fcs_node` VALUES (6, 'Group', '组管理', 1, '', 5, 4, 2, 0);
INSERT INTO `fcs_node` VALUES (7, 'User', '用户管理', 1, '', 1, 4, 2, 0);
INSERT INTO `fcs_node` VALUES (9, 'Log', '日志管理', 1, '', 3, 4, 2, 1);
INSERT INTO `fcs_node` VALUES (10, 'Index', '首页模块', 1, '', 4, 4, 2, 1);
INSERT INTO `fcs_node` VALUES (11, 'index', '列表操作', 1, '', NULL, 10, 3, 1);
INSERT INTO `fcs_node` VALUES (13, 'Node', '项目管理', 1, '', 1, 12, 2, 1);
INSERT INTO `fcs_node` VALUES (14, 'Group', '权限管理', 1, '', 2, 12, 2, 1);
INSERT INTO `fcs_node` VALUES (15, 'Config', '配置管理', 1, '', 3, 12, 2, 1);
INSERT INTO `fcs_node` VALUES (16, 'Log', '日志管理', 1, '', 7, 12, 2, 1);
INSERT INTO `fcs_node` VALUES (17, 'UserType', '用户类型管理', 1, '', 6, 12, 2, 1);
INSERT INTO `fcs_node` VALUES (19, 'Attach', '资源管理', 1, '', 4, 12, 2, 1);
INSERT INTO `fcs_node` VALUES (20, 'User', '用户管理', 1, '', 5, 12, 2, 1);
INSERT INTO `fcs_node` VALUES (21, 'Index', '默认模块', 1, '', NULL, 12, 2, 1);
INSERT INTO `fcs_node` VALUES (22, 'Public', '公共模块', 1, '', NULL, 12, 2, 1);
INSERT INTO `fcs_node` VALUES (23, 'index', '列表操作', 1, '', NULL, 21, 3, 0);
INSERT INTO `fcs_node` VALUES (25, 'Group', '权限管理', 1, '', NULL, 1, 2, 0);
INSERT INTO `fcs_node` VALUES (26, 'User', '用户管理', 1, '', NULL, 1, 2, 0);
INSERT INTO `fcs_node` VALUES (27, 'UserType', '用户类型管理', 1, '', NULL, 1, 2, 0);
INSERT INTO `fcs_node` VALUES (28, 'Log', '日志管理', 1, '', NULL, 1, 2, 0);
INSERT INTO `fcs_node` VALUES (29, 'Attach', '资源管理', 1, '', NULL, 1, 2, 0);
INSERT INTO `fcs_node` VALUES (30, 'Index', '首页模块', 1, '', NULL, 1, 2, 0);
INSERT INTO `fcs_node` VALUES (31, 'Index', '默认操作', 1, '', NULL, 30, 3, 0);


-- --------------------------------------------------------

-- 
-- 表的结构 `fcs_session`
-- 

CREATE TABLE `fcs_session` (
  `session_id` varchar(255) character set latin1 collate latin1_bin NOT NULL default '',
  `session_expires` int(10) unsigned NOT NULL default '0',
  `session_data` text character set utf8,
  PRIMARY KEY  (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `fcs_session`
-- 

-- --------------------------------------------------------


-- --------------------------------------------------------

-- 
-- 表的结构 `fcs_user`
-- 

CREATE TABLE `fcs_user` (
  `id` int(10) NOT NULL auto_increment,
  `nickname` varchar(30) NOT NULL,
  `password` varchar(32) NOT NULL,
  `name` varchar(30) NOT NULL,
  `registerTime` varchar(25) default NULL,
  `status` tinyint(1) NOT NULL,
  `remark` varchar(255) default NULL,
  `verify` varchar(32) default NULL,
  `type` int(3) unsigned default NULL,
  `email` varchar(150) default NULL,
  `childId` int(11) unsigned default NULL,
  `lastLoginTime` varchar(25) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

-- 
-- 导出表中的数据 `fcs_user`
-- 

INSERT INTO `fcs_user` (`id`, `nickname`, `password`, `name`, `registerTime`, `status`, `remark`, `verify`, `type`, `email`, `childId`, `lastLoginTime`) VALUES (1, '管理员', '21232f297a57a5a743894a0e4a801fc3', 'admin', '1148194044', 1, 'dfdfdf', NULL, 1, NULL, NULL, '1159324414');

-- --------------------------------------------------------

-- 
-- 表的结构 `fcs_usertype`
-- 

CREATE TABLE `fcs_usertype` (
  `id` tinyint(2) unsigned NOT NULL auto_increment,
  `name` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `remark` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8  ;

-- 
-- 导出表中的数据 `fcs_usertype`
-- 

INSERT INTO `fcs_usertype` (`id`, `name`, `status`, `remark`) VALUES (1, '管理员', 1, '后台管理')
