-- phpMyAdmin SQL Dump
-- version 2.9.0.3
-- http://www.phpmyadmin.net
-- 
-- 主机: localhost
-- 生成日期: 2007 年 03 月 29 日 15:40
-- 服务器版本: 5.0.27
-- PHP 版本: 4.4.4
-- 
-- 数据库: `admin`
-- 

-- --------------------------------------------------------

-- 
-- 表的结构 `think_access`
-- 

CREATE TABLE `think_access` (
  `groupId` smallint(6) unsigned NOT NULL,
  `nodeId` smallint(6) unsigned NOT NULL,
  `level` tinyint(1) NOT NULL,
  `parentNodeId` smallint(6) NOT NULL,
  `status` tinyint(1) ,
  KEY `groupId` (`groupId`),
  KEY `nodeId` (`nodeId`),
  KEY `level` (`level`),
  KEY `parentNodeId` (`parentNodeId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- 导出表中的数据 `think_access`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `think_attach`
-- 

CREATE TABLE `think_attach` (
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
  PRIMARY KEY  (`id`,`recordId`),
  KEY `module` (`module`),
  KEY `recordId` (`recordId`),
  KEY `userId` (`userId`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8  ;

-- 
-- 导出表中的数据 `think_attach`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `think_cache`
-- 

CREATE TABLE `think_cache` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `cachekey` varchar(255) character set utf8 NOT NULL,
  `expire` int(11) NOT NULL,
  `data` blob,
  `datasize` int(11) default NULL,
  `datacrc` varchar(32) character set utf8 default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `think_cache`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `think_config`
-- 

CREATE TABLE `think_config` (
  `id` mediumint(9) NOT NULL auto_increment,
  `title` varchar(255) default NULL,
  `value` varchar(255) default NULL,
  `remark` varchar(255) default NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

-- 
-- 导出表中的数据 `think_config`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `think_group`
-- 

CREATE TABLE `think_group` (
  `id` smallint(6) unsigned NOT NULL auto_increment,
  `name` varchar(20) NOT NULL,
  `pid` smallint(6) default NULL,
  `status` tinyint(1) unsigned default NULL,
  `remark` varchar(255) default NULL,
  `ename` varchar(5) default NULL,
  PRIMARY KEY  (`id`),
  KEY `parentId` (`pid`),
  KEY `ename` (`ename`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- 
-- 导出表中的数据 `think_group`
-- 

INSERT INTO `think_group` (`id`, `name`, `pid`, `status`, `remark`, `ename`) VALUES 
(1, '管理员', 0, 1, '具有管理员权限', NULL),
(2, '普通用户', 0, 1, '普通用户', NULL);

-- --------------------------------------------------------

-- 
-- 表的结构 `think_groupuser`
-- 

CREATE TABLE `think_groupuser` (
  `groupId` mediumint(9) unsigned default NULL,
  `userId` mediumint(9) unsigned default NULL,
  KEY `groupId` (`groupId`),
  KEY `userId` (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `think_groupuser`
-- 

INSERT INTO `think_groupuser` (`groupId`, `userId`) VALUES 
(2, 2),
(1, 1);

-- --------------------------------------------------------

-- 
-- 表的结构 `think_log`
-- 

CREATE TABLE `think_log` (
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
-- 导出表中的数据 `think_log`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `think_login`
-- 

CREATE TABLE `think_login` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `userId` int(11) unsigned default NULL,
  `inTime` varchar(25) default NULL,
  `loginIp` varchar(50) default NULL,
  `type` tinyint(4) unsigned default NULL,
  `outTime` varchar(25) default NULL,
  PRIMARY KEY  (`id`),
  KEY `userId` (`userId`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `think_login`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `think_memo`
-- 

CREATE TABLE `think_memo` (
  `id` mediumint(6) unsigned NOT NULL auto_increment,
  `label` varchar(255) NOT NULL,
  `memo` text,
  `createTime` varchar(25) NOT NULL,
  `userId` mediumint(8) NOT NULL,
  `type` varchar(15) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `label` (`label`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- 
-- 导出表中的数据 `think_memo`
-- 

-- --------------------------------------------------------

-- 
-- 表的结构 `think_node`
-- 

CREATE TABLE `think_node` (
  `id` smallint(6) unsigned NOT NULL auto_increment,
  `name` varchar(20) NOT NULL,
  `title` varchar(50) default NULL,
  `status` tinyint(1) unsigned default NULL,
  `remark` varchar(255) default NULL,
  `seqNo` smallint(6) unsigned default NULL,
  `pid` smallint(6) unsigned NOT NULL,
  `level` tinyint(1) unsigned NOT NULL,
  `type` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `name` (`name`),
  KEY `parentId` (`pid`),
  KEY `level` (`level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

-- 
-- 导出表中的数据 `think_node`
-- 

INSERT INTO `think_node` (`id`, `name`, `title`, `status`, `remark`, `seqNo`, `pid`, `level`, `type`) VALUES 
(1, 'Admin', 'ThinkPHP后台管理', 1, 'ThinkPHP示例管理', 1, 0, 1, 0),
(3, 'User', '用户管理', 1, '', 6, 1, 2, 0),
(5, 'Group', '权限管理', 1, '', 7, 1, 2, 0),
(6, 'PlugIn', '插件管理', 1, '', 8, 1, 2, 0),
(7, 'Node', '节点管理', 1, '', 9, 1, 2, 0),
(8, 'System', '系统管理', 1, '', 10, 1, 2, 0),
(9, 'DBManager', '数据库管理', 1, '', 1, 1, 2, 0),
(10, 'Public', '公共模块', 1, '', 4, 1, 2, 0),
(11, 'Index', '默认模块', 1, '', 3, 1, 2, 0),
(12, 'add', '新增', 1, '', 1, 10, 3, 0),
(13, 'insert', '插入', 1, '插入操作', 2, 10, 3, 0),
(14, 'edit', '编辑', 1, '编辑操作', 3, 10, 3, 0),
(15, 'update', '保存', 1, '保存操作', 4, 10, 3, 0),
(16, 'index', '列表', 1, '默认操作', 5, 10, 3, 0),
(17, 'forbid', '禁用', 1, '禁用操作', 6, 10, 3, 0),
(18, 'resume', '恢复', 1, '恢复操作', 7, 10, 3, 0),
(20, 'Node', '节点管理', 1, '', NULL, 19, 2, 0),
(23, 'user', '用户管理', 1, '', NULL, 21, 2, 0),
(24, 'HOME', '默认项目', 1, '', NULL, 0, 1, 0),
(25, 'UserType', '用户类型', 1, '', 2, 1, 2, 0),
(27, 'User', '用户管理', 1, '', NULL, 26, 2, 0);

-- --------------------------------------------------------

-- 
-- 表的结构 `think_plugin`
-- 

CREATE TABLE `think_plugin` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `think_plugin`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `think_session`
-- 

CREATE TABLE `think_session` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `cachekey` varchar(255) character set utf8 NOT NULL,
  `expire` int(11) NOT NULL,
  `data` blob,
  `datasize` int(11) default NULL,
  `datacrc` varchar(32) character set utf8 default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `think_session`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `think_user`
-- 

CREATE TABLE `think_user` (
  `id` int(10) NOT NULL auto_increment,
  `nickname` varchar(30) NOT NULL,
  `password` varchar(32) NOT NULL,
  `name` varchar(30) NOT NULL,
  `registerTime` varchar(25) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `remark` varchar(255) default NULL,
  `verify` varchar(32) default NULL,
  `type` int(3) unsigned default NULL,
  `email` varchar(150) default NULL,
  `childId` int(11) unsigned default NULL,
  `lastLoginTime` varchar(25) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `type` (`type`),
  KEY `childId` (`childId`),
  KEY `status` (`status`),
  KEY `verify` (`verify`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- 
-- 导出表中的数据 `think_user`
-- 

INSERT INTO `think_user` (`id`, `nickname`, `password`, `name`, `registerTime`, `status`, `remark`, `verify`, `type`, `email`, `childId`, `lastLoginTime`) VALUES 
(1, '超级管理员', '21232f297a57a5a743894a0e4a801fc3', 'admin', '1148194044', 1, 'Super Webmaster', '0000', 1, NULL, NULL, '1175151980'),
(2, '测试用户', '21232f297a57a5a743894a0e4a801fc3', 'test', '', 1, '', '1111', 1, NULL, NULL, '1174750565');

-- --------------------------------------------------------

-- 
-- 表的结构 `think_usertype`
-- 

CREATE TABLE `think_usertype` (
  `id` tinyint(2) unsigned NOT NULL auto_increment,
  `name` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `remark` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

-- 
-- 导出表中的数据 `think_usertype`
-- 

INSERT INTO `think_usertype` (`id`, `name`, `status`, `remark`) VALUES 
(1, '后台管理', 1, '后台管理人员'),
(5, '会员', 1, '会员');


CREATE TABLE `think_time` (
  `id` mediumint(5) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `create_at` int(12) unsigned NOT NULL,
  `update_at` int(12) unsigned NOT NULL,
  `password` varchar(32) NOT NULL,
  `age` smallint(3) unsigned NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;
