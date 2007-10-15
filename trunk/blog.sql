-- phpMyAdmin SQL Dump
-- version 2.10.1
-- http://www.phpmyadmin.net
-- 
-- 主机: localhost
-- 生成日期: 2007 年 10 月 12 日 06:24
-- 服务器版本: 5.0.37
-- PHP 版本: 5.2.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- 数据库: `blog`
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
  `content` longtext NOT NULL,
  `cTime` int(11) unsigned NOT NULL default '0',
  `mTime` int(11) unsigned NOT NULL default '0',
  `status` tinyint(1) unsigned NOT NULL default '0',
  `readCount` mediumint(5) unsigned NOT NULL default '0',
  `commentCount` mediumint(5) unsigned NOT NULL default '0',
  `tags` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 导出表中的数据 `think_tagged`
-- 

