-- phpMyAdmin SQL Dump
-- version 2.8.1
-- http://www.phpmyadmin.net
-- 
-- 主机: localhost
-- 生成日期: 2006 年 07 月 24 日 15:03
-- 服务器版本: 5.0.22
-- PHP 版本: 4.4.2
-- 
-- 数据库: `fcs`
-- 

-- --------------------------------------------------------

-- 
-- 表的结构 `fcs_action`
-- 

CREATE TABLE `fcs_action` (
  `id` smallint(6) unsigned NOT NULL auto_increment,
  `name` varchar(20) NOT NULL,
  `title` varchar(50) default NULL,
  `status` tinyint(1) unsigned default NULL,
  `remark` varchar(255) default NULL,
  `groupId` mediumint(6) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=34 ;

-- 
-- 导出表中的数据 `fcs_action`
-- 

INSERT INTO `fcs_action` VALUES (1, 'add', '新增', 1, '', 0);
INSERT INTO `fcs_action` VALUES (2, 'insert', '保存', 1, '', 0);
INSERT INTO `fcs_action` VALUES (3, 'edit', '编辑', 1, '', 0);
INSERT INTO `fcs_action` VALUES (4, 'update', '更新', 1, '', 0);
INSERT INTO `fcs_action` VALUES (5, 'delete', '删除', 1, '', 0);
INSERT INTO `fcs_action` VALUES (6, 'forbid', '禁用', 1, '', 0);
INSERT INTO `fcs_action` VALUES (7, 'resume', '恢复', 1, '', 0);
INSERT INTO `fcs_action` VALUES (8, 'index', '列表', 1, '', 0);
INSERT INTO `fcs_action` VALUES (9, 'action', '操作权限查看', 1, '', 0);
INSERT INTO `fcs_action` VALUES (10, 'module', '模块权限查看', 1, '', 0);
INSERT INTO `fcs_action` VALUES (11, 'user', '组用户查看', 1, '', 0);
INSERT INTO `fcs_action` VALUES (12, 'setAction', '操作权限设置', 1, '', 0);
INSERT INTO `fcs_action` VALUES (13, 'setModule', '模块权限设置', 1, '', 0);
INSERT INTO `fcs_action` VALUES (14, 'download', '下载', 1, '', 0);
INSERT INTO `fcs_action` VALUES (15, 'output', '导出', 1, '', 0);
INSERT INTO `fcs_action` VALUES (16, 'delAttach', '删除附件', 1, '', 0);
INSERT INTO `fcs_action` VALUES (17, 'build', '生成配置', 1, '', 0);
INSERT INTO `fcs_action` VALUES (18, 'image', '显示图片', 1, '', 0);
INSERT INTO `fcs_action` VALUES (19, 'setUser', '设置组用户', 1, '', 0);
INSERT INTO `fcs_action` VALUES (20, 'sort', '排序', 1, '', 0);
INSERT INTO `fcs_action` VALUES (21, 'saveSort', '保存排序', 1, '', 0);
INSERT INTO `fcs_action` VALUES (22, 'select', '选择', 1, '', 0);
INSERT INTO `fcs_action` VALUES (23, 'batch', '批量添加', 1, '', 0);
INSERT INTO `fcs_action` VALUES (24, 'clear', '清空', 1, '', 0);
INSERT INTO `fcs_action` VALUES (25, 'read', '查看', 1, '', 0);
INSERT INTO `fcs_action` VALUES (26, 'userList', '组用户列表', 1, '', 0);
INSERT INTO `fcs_action` VALUES (28, 'photo', '查看照片', 1, '', 0);
INSERT INTO `fcs_action` VALUES (31, 'Send', '发送', 1, '', 0);
INSERT INTO `fcs_action` VALUES (32, 'multiSelect', '多选', 1, '', 0);
INSERT INTO `fcs_action` VALUES (33, 'tree', '生成树', 1, '', 0);

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
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

-- 
-- 导出表中的数据 `fcs_attach`
-- 

-- --------------------------------------------------------

-- 
-- 表的结构 `fcs_board`
-- 

CREATE TABLE `fcs_board` (
  `id` smallint(6) unsigned NOT NULL auto_increment,
  `title` varchar(200) NOT NULL,
  `content` text,
  `beginDate` varchar(25) NOT NULL,
  `endDate` varchar(25) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

-- 
-- 导出表中的数据 `fcs_board`
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

-- 
-- 导出表中的数据 `fcs_config`
-- 


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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

-- 
-- 导出表中的数据 `fcs_group`
-- 

INSERT INTO `fcs_group` VALUES (1, '管理员组', 0, 1, '管理员权限', NULL);

-- --------------------------------------------------------

-- 
-- 表的结构 `fcs_groupAction`
-- 

CREATE TABLE `fcs_groupAction` (
  `groupId` mediumint(9) unsigned default NULL,
  `actionId` mediumint(9) unsigned default NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `fcs_groupAction`
-- 

INSERT INTO `fcs_groupAction` VALUES (1, 1);
INSERT INTO `fcs_groupAction` VALUES (1, 2);
INSERT INTO `fcs_groupAction` VALUES (1, 3);
INSERT INTO `fcs_groupAction` VALUES (1, 4);
INSERT INTO `fcs_groupAction` VALUES (1, 5);
INSERT INTO `fcs_groupAction` VALUES (1, 6);
INSERT INTO `fcs_groupAction` VALUES (1, 7);
INSERT INTO `fcs_groupAction` VALUES (1, 8);
INSERT INTO `fcs_groupAction` VALUES (1, 9);
INSERT INTO `fcs_groupAction` VALUES (1, 10);
INSERT INTO `fcs_groupAction` VALUES (1, 11);
INSERT INTO `fcs_groupAction` VALUES (1, 12);
INSERT INTO `fcs_groupAction` VALUES (1, 13);
INSERT INTO `fcs_groupAction` VALUES (1, 14);
INSERT INTO `fcs_groupAction` VALUES (1, 15);
INSERT INTO `fcs_groupAction` VALUES (1, 16);
INSERT INTO `fcs_groupAction` VALUES (1, 17);
INSERT INTO `fcs_groupAction` VALUES (1, 18);
INSERT INTO `fcs_groupAction` VALUES (1, 19);
INSERT INTO `fcs_groupAction` VALUES (1, 20);
INSERT INTO `fcs_groupAction` VALUES (1, 21);
INSERT INTO `fcs_groupAction` VALUES (1, 22);
INSERT INTO `fcs_groupAction` VALUES (1, 23);
INSERT INTO `fcs_groupAction` VALUES (1, 24);
INSERT INTO `fcs_groupAction` VALUES (1, 25);
INSERT INTO `fcs_groupAction` VALUES (1, 26);
INSERT INTO `fcs_groupAction` VALUES (1, 27);
INSERT INTO `fcs_groupAction` VALUES (1, 28);
INSERT INTO `fcs_groupAction` VALUES (1, 29);
INSERT INTO `fcs_groupAction` VALUES (1, 30);
INSERT INTO `fcs_groupAction` VALUES (1, 31);
INSERT INTO `fcs_groupAction` VALUES (1, 32);
INSERT INTO `fcs_groupAction` VALUES (1, 33);
INSERT INTO `fcs_groupAction` VALUES (1, 34);

-- --------------------------------------------------------

-- 
-- 表的结构 `fcs_groupModule`
-- 

CREATE TABLE `fcs_groupModule` (
  `groupId` mediumint(9) unsigned default NULL,
  `moduleId` mediumint(9) unsigned default NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `fcs_groupModule`
-- 

INSERT INTO `fcs_groupModule` VALUES (1, 1);
INSERT INTO `fcs_groupModule` VALUES (1, 2);
INSERT INTO `fcs_groupModule` VALUES (1, 3);
INSERT INTO `fcs_groupModule` VALUES (1, 4);
INSERT INTO `fcs_groupModule` VALUES (1, 5);
INSERT INTO `fcs_groupModule` VALUES (1, 6);
INSERT INTO `fcs_groupModule` VALUES (1, 7);
INSERT INTO `fcs_groupModule` VALUES (1, 8);
INSERT INTO `fcs_groupModule` VALUES (1, 9);
INSERT INTO `fcs_groupModule` VALUES (1, 10);



-- --------------------------------------------------------

-- 
-- 表的结构 `fcs_groupUser`
-- 

CREATE TABLE `fcs_groupUser` (
  `groupId` mediumint(9) unsigned default NULL,
  `userId` mediumint(9) unsigned default NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- 导出表中的数据 `fcs_groupUser`
-- 

INSERT INTO `fcs_groupUser` VALUES (1, 1);

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
  `remark` varchar(255) default NULL,
  `url` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

-- 
-- 导出表中的数据 `fcs_login`
-- 


-- --------------------------------------------------------

-- 
-- 表的结构 `fcs_message`
-- 

CREATE TABLE `fcs_message` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `fromId` mediumint(9) unsigned default NULL,
  `sendId` mediumint(9) unsigned default NULL,
  `title` varchar(100) NOT NULL,
  `content` text,
  `status` tinyint(1) NOT NULL,
  `sendTime` varchar(25) NOT NULL,
  `readTime` varchar(25) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

-- 
-- 导出表中的数据 `fcs_message`
-- 
-- --------------------------------------------------------

-- 
-- 表的结构 `fcs_module`
-- 

CREATE TABLE `fcs_module` (
  `id` smallint(6) unsigned NOT NULL auto_increment,
  `name` varchar(20) NOT NULL,
  `title` varchar(50) default NULL,
  `status` tinyint(1) unsigned default NULL,
  `remark` varchar(255) default NULL,
  `seqNo` smallint(6) unsigned default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

-- 
-- 导出表中的数据 `fcs_module`
-- 

INSERT INTO `fcs_module` VALUES (1, 'Config', '配置管理', 1, '', 1);
INSERT INTO `fcs_module` VALUES (2, 'Module', '模块管理', 1, '', 5);
INSERT INTO `fcs_module` VALUES (3, 'Action', '操作管理', 1, '', 6);
INSERT INTO `fcs_module` VALUES (4, 'Group', '组管理', 1, '', 8);
INSERT INTO `fcs_module` VALUES (5, 'Index', '默认模块', 1, '', 3);
INSERT INTO `fcs_module` VALUES (6, 'User', '用户管理', 1, '', 7);
INSERT INTO `fcs_module` VALUES (7, 'public', '公共模块', 1, '', 4);
INSERT INTO `fcs_module` VALUES (8, 'Log', '日志管理', 1, '', 9);
INSERT INTO `fcs_module` VALUES (9, 'Login', '登录管理', 1, '', 10);
INSERT INTO `fcs_module` VALUES (10, 'Board', '公告管理', 1, '', 2);

-- --------------------------------------------------------

-- 
-- 表的结构 `fcs_user`
-- 

CREATE TABLE `fcs_user` (
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
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- 
-- 导出表中的数据 `fcs_user`
-- 

INSERT INTO `fcs_user` VALUES (1, '管理员', '21232f297a57a5a743894a0e4a801fc3', 'admin', '1148194044', 1, 'dfdfdf', NULL, 1, NULL, NULL, '1153721830');
