-- --------------------------------------------------------

-- 
-- 表的结构 `think_blob`
-- 

CREATE TABLE `think_blob` (
  `id` smallint(4) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `content` varchar(255) NOT NULL,
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
  `status` tinyint(1) unsigned NOT NULL,
  `email` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- 
-- 导出表中的数据 `think_form`
-- 

-- --------------------------------------------------------

-- 
-- 表的结构 `think_user`
-- 

CREATE TABLE `think_user` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `account` varchar(64) NOT NULL,
  `nickname` varchar(50) NOT NULL,
  `password` char(32) NOT NULL,
  `verify` varchar(32) default NULL,
  `email` varchar(50) NOT NULL,
  `remark` varchar(255) NOT NULL,
  `create_time` int(11) unsigned NOT NULL,
  `update_time` int(11) unsigned NOT NULL,
  `status` tinyint(1) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- 
-- 导出表中的数据 `think_user`
-- 

INSERT INTO `think_user` (`id`, `account`, `nickname`, `password`, `verify`, `email`, `remark`, `create_time`, `update_time`, `status`) VALUES 
(3, 'admin', '管理员', '21232f297a57a5a743894a0e4a801fc3', '8888', 'liu21st@gmail.com', '', 0, 0, 1);

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


-- --------------------------------------------------------

-- 
-- 表的结构 `think_access`
-- 

CREATE TABLE `think_access` (
  `groupId` smallint(6) unsigned NOT NULL,
  `nodeId` smallint(6) unsigned NOT NULL,
  `level` tinyint(1) NOT NULL,
  `parentNodeId` smallint(6) NOT NULL,
  `status` tinyint(1) default NULL,
  KEY `groupId` (`groupId`),
  KEY `nodeId` (`nodeId`),
  KEY `level` (`level`),
  KEY `parentNodeId` (`parentNodeId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;