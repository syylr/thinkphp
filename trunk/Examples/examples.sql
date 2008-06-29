-- --------------------------------------------------------

-- 
-- 表的结构 `think_blob`
-- 

CREATE TABLE `think_blob` (
  `id` smallint(4) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `content` varchar(255) default NULL,
  `create_time` int(11) unsigned NOT NULL,
  `status` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- 
-- 导出表中的数据 `think_blob`
-- 

-- --------------------------------------------------------

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
  `downCount` mediumint(9) unsigned default '0',
  `hash` varchar(32) NOT NULL,
  `verify` varchar(8) NOT NULL,
  `remark` varchar(255) default NULL,
  `version` mediumint(6) unsigned NOT NULL default '0',
  `updateTime` int(12) unsigned default NULL,
  `downloadTime` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `module` (`module`),
  KEY `recordId` (`recordId`),
  KEY `userId` (`userId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `think_attach`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `think_blog`
-- 

CREATE TABLE `think_blog` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(15) NOT NULL default '',
  `userId` mediumint(5) unsigned NOT NULL default '0',
  `categoryId` smallint(5) unsigned NOT NULL,
  `title` varchar(255) NOT NULL default '',
  `content` longtext,
  `cTime` int(11) unsigned NOT NULL default '0',
  `mTime` int(11) unsigned NOT NULL default '0',
  `status` tinyint(1) unsigned NOT NULL default '0',
  `readCount` mediumint(5) unsigned NOT NULL default '0',
  `commentCount` mediumint(5) unsigned NOT NULL default '0',
  `tags` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `think_blog`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `think_category`
-- 

CREATE TABLE `think_category` (
  `id` mediumint(5) unsigned NOT NULL auto_increment,
  `name` varchar(30) NOT NULL default '',
  `title` varchar(50) NOT NULL default '',
  `remark` varchar(255) NOT NULL default '',
  `status` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `think_category`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `think_comment`
-- 

CREATE TABLE `think_comment` (
  `id` mediumint(5) unsigned NOT NULL auto_increment,
  `recordId` int(11) unsigned NOT NULL default '0',
  `author` varchar(50) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `url` varchar(255) NOT NULL default '',
  `ip` varchar(25) NOT NULL default '',
  `content` text NOT NULL,
  `cTime` int(11) unsigned NOT NULL default '0',
  `agent` varchar(255) default NULL,
  `status` tinyint(1) unsigned NOT NULL default '0',
  `module` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `think_comment`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `think_tag`
-- 

CREATE TABLE `think_tag` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `count` mediumint(6) unsigned NOT NULL,
  `module` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `name` (`name`),
  KEY `module` (`module`),
  KEY `count` (`count`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `think_tag`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `think_tagged`
-- 

CREATE TABLE `think_tagged` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `userId` int(11) unsigned NOT NULL,
  `recordId` int(11) unsigned NOT NULL,
  `tagId` int(11) NOT NULL,
  `tagTime` int(11) NOT NULL,
  `module` varchar(25) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `module` (`module`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `think_tagged`
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- 导出表中的数据 `think_group`
--

INSERT INTO `think_group` (`id`, `name`, `pid`, `status`, `remark`, `ename`) VALUES
(1, '管理员组', NULL, 1, '具有一般管理员权限', NULL),
(2, '普通用户组', NULL, 1, '一般用户权限', NULL);

-- --------------------------------------------------------

--
-- 表的结构 `think_groupuser`
--

CREATE TABLE `think_groupuser` (
  `groupId` mediumint(9) unsigned default NULL,
  `userId` mediumint(9) unsigned default NULL,
  KEY `groupId` (`groupId`),
  KEY `userId` (`userId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 导出表中的数据 `think_groupuser`
--

INSERT INTO `think_groupuser` (`groupId`, `userId`) VALUES
(1, 3),
(2, 2);

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
  `pid` smallint(6) unsigned NOT NULL default '0',
  `level` tinyint(1) unsigned NOT NULL,
  `type` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `name` (`name`),
  KEY `parentId` (`pid`),
  KEY `level` (`level`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

--
-- 导出表中的数据 `think_node`
--

INSERT INTO `think_node` (`id`, `name`, `title`, `status`, `remark`, `seqNo`, `pid`, `level`, `type`) VALUES
(1, 'Admin', '后台项目', 1, '后台管理项目', NULL, 0, 1, 0),
(2, 'Public', '公共模块', 1, '项目公共模块', NULL, 1, 2, 0),
(3, 'Index', '默认模块', 1, '项目默认模块', NULL, 1, 2, 0),
(4, 'Node', '节点管理', 1, '授权节点管理', NULL, 1, 2, 0),
(5, 'Group', '权限管理', 1, '权限管理模块', NULL, 1, 2, 0),
(6, 'User', '用户管理', 1, '用户模块', NULL, 1, 2, 0),
(7, 'Form', '数据管理', 1, '数据管理模块', NULL, 1, 2, 0),
(8, 'index', '列表', 1, '', NULL, 2, 3, 0),
(9, 'add', '增加', 1, '', NULL, 2, 3, 0),
(10, 'edit', '编辑', 1, '', NULL, 2, 3, 0),
(11, 'insert', '写入', 1, '', NULL, 2, 3, 0),
(12, 'update', '更新', 1, '', NULL, 2, 3, 0),
(13, 'delete', '删除', 1, '', NULL, 2, 3, 0),
(14, 'forbid', '禁用', 1, '', NULL, 2, 3, 0),
(15, 'resume', '恢复', 1, '', NULL, 2, 3, 0),
(16, 'resetPwd', '重置密码', 1, '', NULL, 6, 3, 0);

-- --------------------------------------------------------

--
-- 表的结构 `think_user`
--

CREATE TABLE `think_user` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `account` varchar(64) NOT NULL,
  `nickname` varchar(50) NOT NULL,
  `password` char(32) NOT NULL,
  `remark` varchar(255) NOT NULL,
  `create_time` int(11) unsigned NOT NULL,
  `update_time` int(11) unsigned NOT NULL,
  `status` tinyint(1) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- 导出表中的数据 `think_user`
--

INSERT INTO `think_user` (`id`, `account`, `nickname`, `password`, `remark`, `create_time`, `update_time`, `status`) VALUES
(1, 'admin', '管理员', '21232f297a57a5a743894a0e4a801fc3', '', 0, 0, 1),
(2, 'test', '测试用户', 'e10adc3949ba59abbe56e057f20f883e', '测试用户', 1212716492, 0, 1),
(3, 'leader', '领导', 'e10adc3949ba59abbe56e057f20f883e', '具有一般管理权限的用户', 1212716969, 0, 1);

CREATE TABLE `think_access` (
  `groupId` smallint(6) unsigned NOT NULL,
  `nodeId` smallint(6) unsigned NOT NULL,
  `level` tinyint(1) NOT NULL,
  `parentNodeId` smallint(6) NOT NULL default '0',
  `status` tinyint(1) default NULL,
  KEY `groupId` (`groupId`),
  KEY `nodeId` (`nodeId`),
  KEY `level` (`level`),
  KEY `parentNodeId` (`parentNodeId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 导出表中的数据 `think_access`
--

INSERT INTO `think_access` (`groupId`, `nodeId`, `level`, `parentNodeId`, `status`) VALUES
(1, 1, 1, 0, NULL),
(1, 6, 2, 1, NULL),
(1, 3, 2, 1, NULL),
(1, 2, 2, 1, NULL),
(2, 1, 1, 0, NULL),
(2, 7, 2, 1, NULL),
(2, 3, 2, 1, NULL),
(2, 8, 3, 2, NULL),
(2, 9, 3, 2, NULL),
(2, 10, 3, 2, NULL),
(1, 7, 2, 1, NULL),
(1, 12, 3, 2, NULL),
(1, 11, 3, 2, NULL),
(1, 10, 3, 2, NULL),
(1, 9, 3, 2, NULL),
(1, 8, 3, 2, NULL),
(1, 13, 3, 2, NULL),
(1, 14, 3, 2, NULL),
(1, 15, 3, 2, NULL),
(1, 16, 3, 6, NULL),
(2, 2, 2, 1, NULL);