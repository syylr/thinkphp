-- ThinkCMS SQL Dump
-- http://www.topthink.com.cn

-- 
-- 表的结构 `thinkphp_access`
-- 
CREATE TABLE `thinkphp_access` (
`groupId` smallint(6) unsigned NOT NULL  ,
`nodeId` smallint(6) unsigned NOT NULL  ,
`level` tinyint(1) NOT NULL  ,
`parentNodeId` smallint(6) NOT NULL  
) ENGINE=InnoDB DEFAULT CHARSET=utf8
-- 
-- 导出表中的数据 `thinkphp_access`
--
INSERT INTO `thinkphp_access` VALUES ('1','5','2','4');
INSERT INTO `thinkphp_access` VALUES ('1','6','2','4');
INSERT INTO `thinkphp_access` VALUES ('1','7','2','4');
INSERT INTO `thinkphp_access` VALUES ('1','8','2','4');
INSERT INTO `thinkphp_access` VALUES ('1','9','2','4');
INSERT INTO `thinkphp_access` VALUES ('1','10','2','4');
INSERT INTO `thinkphp_access` VALUES ('1','11','3','10');
INSERT INTO `thinkphp_access` VALUES ('1','2','2','1');
INSERT INTO `thinkphp_access` VALUES ('5','3','3','2');
INSERT INTO `thinkphp_access` VALUES ('1','13','2','12');
INSERT INTO `thinkphp_access` VALUES ('1','14','2','12');
INSERT INTO `thinkphp_access` VALUES ('1','15','2','12');
INSERT INTO `thinkphp_access` VALUES ('1','16','2','12');
INSERT INTO `thinkphp_access` VALUES ('1','17','2','12');
INSERT INTO `thinkphp_access` VALUES ('1','18','2','12');
INSERT INTO `thinkphp_access` VALUES ('1','19','2','12');
INSERT INTO `thinkphp_access` VALUES ('1','20','2','12');
INSERT INTO `thinkphp_access` VALUES ('10','13','2','12');
INSERT INTO `thinkphp_access` VALUES ('10','15','2','12');
INSERT INTO `thinkphp_access` VALUES ('10','16','2','12');
INSERT INTO `thinkphp_access` VALUES ('10','19','2','12');
INSERT INTO `thinkphp_access` VALUES ('10','21','2','12');
INSERT INTO `thinkphp_access` VALUES ('1','11','3','10');
INSERT INTO `thinkphp_access` VALUES ('1','11','3','10');
INSERT INTO `thinkphp_access` VALUES ('1','11','3','10');
INSERT INTO `thinkphp_access` VALUES ('10','23','3','21');
INSERT INTO `thinkphp_access` VALUES ('2','37','2','34');
INSERT INTO `thinkphp_access` VALUES ('2','40','2','34');
INSERT INTO `thinkphp_access` VALUES ('2','42','2','34');
INSERT INTO `thinkphp_access` VALUES ('2','43','2','34');
INSERT INTO `thinkphp_access` VALUES ('2','51','2','34');
INSERT INTO `thinkphp_access` VALUES ('3','11','3','10');
INSERT INTO `thinkphp_access` VALUES ('3','11','3','10');
INSERT INTO `thinkphp_access` VALUES ('3','11','3','10');
INSERT INTO `thinkphp_access` VALUES ('3','11','3','10');
INSERT INTO `thinkphp_access` VALUES ('3','11','3','10');
INSERT INTO `thinkphp_access` VALUES ('3','58','3','37');
INSERT INTO `thinkphp_access` VALUES ('4','34','1','0');
INSERT INTO `thinkphp_access` VALUES ('4','58','3','37');
INSERT INTO `thinkphp_access` VALUES ('5','34','1','0');
INSERT INTO `thinkphp_access` VALUES ('5','58','3','37');
INSERT INTO `thinkphp_access` VALUES ('6','68','2','35');
INSERT INTO `thinkphp_access` VALUES ('6','69','2','35');
INSERT INTO `thinkphp_access` VALUES ('6','70','2','35');
INSERT INTO `thinkphp_access` VALUES ('6','71','2','35');
INSERT INTO `thinkphp_access` VALUES ('6','72','2','35');
INSERT INTO `thinkphp_access` VALUES ('6','73','3','68');
INSERT INTO `thinkphp_access` VALUES ('4','37','2','34');
INSERT INTO `thinkphp_access` VALUES ('4','40','2','34');
INSERT INTO `thinkphp_access` VALUES ('4','43','2','34');
INSERT INTO `thinkphp_access` VALUES ('4','60','2','34');
INSERT INTO `thinkphp_access` VALUES ('4','82','2','34');
INSERT INTO `thinkphp_access` VALUES ('3','62','3','60');
INSERT INTO `thinkphp_access` VALUES ('3','63','3','60');
INSERT INTO `thinkphp_access` VALUES ('3','64','3','60');
INSERT INTO `thinkphp_access` VALUES ('3','65','3','60');
INSERT INTO `thinkphp_access` VALUES ('3','66','3','60');
INSERT INTO `thinkphp_access` VALUES ('3','83','3','60');
INSERT INTO `thinkphp_access` VALUES ('3','84','3','60');
INSERT INTO `thinkphp_access` VALUES ('3','85','3','60');
INSERT INTO `thinkphp_access` VALUES ('3','86','3','60');
INSERT INTO `thinkphp_access` VALUES ('5','37','2','34');
INSERT INTO `thinkphp_access` VALUES ('5','43','2','34');
INSERT INTO `thinkphp_access` VALUES ('5','60','2','34');
INSERT INTO `thinkphp_access` VALUES ('5','102','2','34');
INSERT INTO `thinkphp_access` VALUES ('5','66','3','60');
INSERT INTO `thinkphp_access` VALUES ('5','83','3','60');
INSERT INTO `thinkphp_access` VALUES ('6','74','3','70');
INSERT INTO `thinkphp_access` VALUES ('6','75','3','70');
INSERT INTO `thinkphp_access` VALUES ('6','76','3','70');
INSERT INTO `thinkphp_access` VALUES ('6','77','3','70');
INSERT INTO `thinkphp_access` VALUES ('6','78','3','70');
INSERT INTO `thinkphp_access` VALUES ('6','79','3','70');
INSERT INTO `thinkphp_access` VALUES ('6','80','3','70');
INSERT INTO `thinkphp_access` VALUES ('6','81','3','70');
INSERT INTO `thinkphp_access` VALUES ('6','99','3','70');
INSERT INTO `thinkphp_access` VALUES ('6','100','3','70');
INSERT INTO `thinkphp_access` VALUES ('6','110','3','70');
INSERT INTO `thinkphp_access` VALUES ('3','37','2','34');
INSERT INTO `thinkphp_access` VALUES ('3','40','2','34');
INSERT INTO `thinkphp_access` VALUES ('3','42','2','34');
INSERT INTO `thinkphp_access` VALUES ('3','43','2','34');
INSERT INTO `thinkphp_access` VALUES ('3','52','2','34');
INSERT INTO `thinkphp_access` VALUES ('3','60','2','34');
INSERT INTO `thinkphp_access` VALUES ('3','82','2','34');
INSERT INTO `thinkphp_access` VALUES ('4','115','3','40');
INSERT INTO `thinkphp_access` VALUES ('4','116','3','40');
INSERT INTO `thinkphp_access` VALUES ('4','62','3','60');
INSERT INTO `thinkphp_access` VALUES ('4','63','3','60');
INSERT INTO `thinkphp_access` VALUES ('4','64','3','60');
INSERT INTO `thinkphp_access` VALUES ('4','65','3','60');
INSERT INTO `thinkphp_access` VALUES ('4','66','3','60');
INSERT INTO `thinkphp_access` VALUES ('4','83','3','60');
INSERT INTO `thinkphp_access` VALUES ('4','84','3','60');
INSERT INTO `thinkphp_access` VALUES ('4','85','3','60');
INSERT INTO `thinkphp_access` VALUES ('4','117','3','60');
INSERT INTO `thinkphp_access` VALUES ('10','34','1','0');
INSERT INTO `thinkphp_access` VALUES ('10','35','1','0');
INSERT INTO `thinkphp_access` VALUES ('10','36','2','34');
INSERT INTO `thinkphp_access` VALUES ('6','34','1','0');
INSERT INTO `thinkphp_access` VALUES ('6','35','1','0');
INSERT INTO `thinkphp_access` VALUES ('1','1','1','0');
INSERT INTO `thinkphp_access` VALUES ('1','2','1','0');
INSERT INTO `thinkphp_access` VALUES ('3','2','1','0');
INSERT INTO `thinkphp_access` VALUES ('3','19','2','2');
INSERT INTO `thinkphp_access` VALUES ('3','20','2','2');
INSERT INTO `thinkphp_access` VALUES ('2','1','1','0');
INSERT INTO `thinkphp_access` VALUES ('2','3','2','1');
INSERT INTO `thinkphp_access` VALUES ('2','4','2','1');
INSERT INTO `thinkphp_access` VALUES ('2','5','2','1');
INSERT INTO `thinkphp_access` VALUES ('2','6','2','1');
INSERT INTO `thinkphp_access` VALUES ('2','7','2','1');
INSERT INTO `thinkphp_access` VALUES ('2','8','2','1');
INSERT INTO `thinkphp_access` VALUES ('2','9','2','1');
INSERT INTO `thinkphp_access` VALUES ('2','10','2','1');
INSERT INTO `thinkphp_access` VALUES ('2','11','2','1');
INSERT INTO `thinkphp_access` VALUES ('2','12','2','1');
INSERT INTO `thinkphp_access` VALUES ('2','13','2','1');
INSERT INTO `thinkphp_access` VALUES ('2','14','2','1');
INSERT INTO `thinkphp_access` VALUES ('2','15','2','1');
INSERT INTO `thinkphp_access` VALUES ('2','16','2','1');
INSERT INTO `thinkphp_access` VALUES ('2','17','2','1');
INSERT INTO `thinkphp_access` VALUES ('2','18','2','1');
INSERT INTO `thinkphp_access` VALUES ('2','21','3','3');
INSERT INTO `thinkphp_access` VALUES ('2','22','3','3');
INSERT INTO `thinkphp_access` VALUES ('2','23','3','3');
INSERT INTO `thinkphp_access` VALUES ('2','25','3','3');
INSERT INTO `thinkphp_access` VALUES ('2','26','3','3');
INSERT INTO `thinkphp_access` VALUES ('2','27','3','3');
INSERT INTO `thinkphp_access` VALUES ('2','28','3','3');
INSERT INTO `thinkphp_access` VALUES ('2','29','3','5');
INSERT INTO `thinkphp_access` VALUES ('2','30','3','5');
INSERT INTO `thinkphp_access` VALUES ('2','31','3','5');
INSERT INTO `thinkphp_access` VALUES ('2','32','3','5');
INSERT INTO `thinkphp_access` VALUES ('2','36','3','11');
-- 
-- 表的结构 `thinkphp_article`
-- 
CREATE TABLE `thinkphp_article` (
`id` int(11) unsigned NOT NULL  auto_increment,
`name` varchar(15) NOT NULL  ,
`userId` mediumint(5) unsigned NOT NULL default 0 ,
`categoryId` smallint(5) unsigned NOT NULL default 0 ,
`title` varchar(255) NOT NULL default 0 ,
`content` longtext NOT NULL default 0 ,
`password` varchar(32) NOT NULL default 0 ,
`cTime` int(11) unsigned NOT NULL default 0 ,
`aTime` int(11) unsigned NOT NULL default 0 ,
`mTime` int(11) unsigned NOT NULL default 0 ,
`status` tinyint(1) unsigned NOT NULL default 0 ,
`isRecommend` tinyint(1) unsigned NOT NULL default 0 ,
`isTop` tinyint(1) unsigned NOT NULL default 0 ,
`commentStatus` tinyint(1) unsigned NOT NULL default 0 ,
`guid` varchar(50) NOT NULL default 0 ,
`readCount` mediumint(5) unsigned NOT NULL default 0 ,
`commentCount` mediumint(5) unsigned NOT NULL default 0 ,
`type` tinyint(1) unsigned NOT NULL default 0 ,
`seqNo` mediumint(5) unsigned NOT NULL default 0 ,
`trackback` varchar(255) NOT NULL default 0 ,
PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
-- 
-- 导出表中的数据 `thinkphp_article`
--
INSERT INTO `thinkphp_article` VALUES ('1','','1','1','新年快乐','新年快乐~!<IMG src=\"http://localhost/ThinkCMS/Public/Uploads/{BCD82759-E1DE-6515-6F97-9126A052D725}.gif\">','','1167728390','0','1167742043','0','0','1','1','','0','1','1','0','');

-- 表的结构 `thinkphp_attach`
-- 
CREATE TABLE `thinkphp_attach` (
`id` int(11) NOT NULL default 0 auto_increment,
`name` varchar(255) NOT NULL default 0 ,
`type` varchar(255) NOT NULL default 0 ,
`size` varchar(20) NOT NULL default 0 ,
`extension` varchar(20) NOT NULL default 0 ,
`savepath` varchar(255) NOT NULL default 0 ,
`savename` varchar(255) NOT NULL default 0 ,
`module` varchar(100) NOT NULL default 0 ,
`recordId` int(11) NOT NULL default 0 ,
`userId` int(11) unsigned NOT NULL default 0 ,
`uploadTime` int(11) unsigned NOT NULL default 0 ,
`downloadTime` mediumint(9) unsigned NOT NULL default 0 ,
`hash` varchar(32) NOT NULL default 0 ,
PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
-- 
-- 导出表中的数据 `thinkphp_attach`
--

-- 表的结构 `thinkphp_board`
-- 
CREATE TABLE `thinkphp_board` (
`id` mediumint(5) unsigned NOT NULL default 0 auto_increment,
`title` varchar(255) NOT NULL default 0 ,
`content` longtext NOT NULL default 0 ,
`bTime` int(11) unsigned NOT NULL default 0 ,
`eTime` int(11) unsigned NOT NULL default 0 ,
`status` tinyint(1) unsigned NOT NULL default 0 ,
PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
-- 
-- 导出表中的数据 `thinkphp_board`
--
-- 
-- 表的结构 `thinkphp_cache`
-- 
CREATE TABLE `thinkphp_cache` (
`id` int(11) unsigned NOT NULL default 0 auto_increment,
`cachekey` varchar(255) NOT NULL default 0 ,
`expire` int(11) NOT NULL default 0 ,
`data` blob NOT NULL default 0 ,
`datasize` int(11) NOT NULL default 0 ,
`datacrc` varchar(32) NOT NULL default 0 ,
PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
-- 
-- 导出表中的数据 `thinkphp_cache`
--
-- 
-- 表的结构 `thinkphp_category`
-- 
CREATE TABLE `thinkphp_category` (
`id` mediumint(5) unsigned NOT NULL default 0 auto_increment,
`name` varchar(30) NOT NULL default 0 ,
`title` varchar(50) NOT NULL default 0 ,
`remark` varchar(255) NOT NULL default 0 ,
`seqNo` mediumint(5) unsigned NOT NULL default 0 ,
`pid` mediumint(5) unsigned NOT NULL default 0 ,
`level` smallint(2) unsigned NOT NULL default 0 ,
`status` tinyint(1) unsigned NOT NULL default 0 ,
PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
-- 
-- 导出表中的数据 `thinkphp_category`
--
INSERT INTO `thinkphp_category` VALUES ('1','default','默认分类','','0','0','1','1');
-- 
-- 表的结构 `thinkphp_comment`
-- 
CREATE TABLE `thinkphp_comment` (
`id` mediumint(5) unsigned NOT NULL default 0 auto_increment,
`recordId` int(11) unsigned NOT NULL default 0 ,
`userId` mediumint(5) unsigned NOT NULL default 0 ,
`author` varchar(50) NOT NULL default 0 ,
`email` varchar(255) NOT NULL default 0 ,
`url` varchar(255) NOT NULL default 0 ,
`ip` varchar(25) NOT NULL default 0 ,
`content` text NOT NULL default 0 ,
`cTime` int(11) unsigned NOT NULL default 0 ,
`agent` int(11) unsigned NOT NULL default 0 ,
`status` tinyint(1) unsigned NOT NULL default 0 ,
`module` varchar(50) NOT NULL default 0 ,
PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
-- 
-- 导出表中的数据 `thinkphp_comment`
--
INSERT INTO `thinkphp_comment` VALUES ('11','93','0','刘晨','liu21st@gmail.com','http://blog.liu21st.com','127.0.0.1','测试评论!','1167727595','0','0','article');

-- 表的结构 `thinkphp_config`
-- 
CREATE TABLE `thinkphp_config` (
`id` mediumint(9) NOT NULL default 0 auto_increment,
`name` varchar(255) NOT NULL default 0 ,
`title` varchar(255) NOT NULL default 0 ,
`value` varchar(255) NOT NULL default 0 ,
`remark` varchar(255) NOT NULL default 0 ,
`type` tinyint(1) unsigned NOT NULL default 0 ,
`extra` varchar(255) NOT NULL default 0 ,
`group` varchar(255) NOT NULL default 0 ,
PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
-- 
-- 导出表中的数据 `thinkphp_config`
--
INSERT INTO `thinkphp_config` VALUES ('1','WEB_DOMAIN','网站域名','http://localhost/','','0','','');
INSERT INTO `thinkphp_config` VALUES ('3','WEB_TITLE','网站标题','ThinkPHP 开源的PHP开发框架','网站标题','0','','');
INSERT INTO `thinkphp_config` VALUES ('5','ERROR_MESSAGE','错误提示','你浏览的页面暂时发生了错误，请稍候再试！','','3','','');
INSERT INTO `thinkphp_config` VALUES ('8','DEBUG_MODE','调试模式','1','','2','关闭,开启','');
INSERT INTO `thinkphp_config` VALUES ('9','WEB_LOG_RECORD','日志记录','0','','2','关闭,开启','');
INSERT INTO `thinkphp_config` VALUES ('10','LOG_FILE_SIZE','日志文件大小','2097152','','1','','');
INSERT INTO `thinkphp_config` VALUES ('11','DISPATCH_ON','URL调度','1','','2','关闭,开启','');
INSERT INTO `thinkphp_config` VALUES ('12','DISPATCH_NAME','调度器名称','FCSDispatcher','','0','','');
INSERT INTO `thinkphp_config` VALUES ('13','URL_MODEL','URL模式','1','0 普通模式 1 PATH_INFO 2 URL_REWRITE','4','0,1,2','');
INSERT INTO `thinkphp_config` VALUES ('14','TEMPLATE_CHARSET','模版字符集','utf-8','','0','','');
INSERT INTO `thinkphp_config` VALUES ('15','OUTPUT_CHARSET','输出字符集','utf-8','','0','','');
INSERT INTO `thinkphp_config` VALUES ('16','TMPL_ENGINE_TYPE','模版引擎','fcs','','0','','');
INSERT INTO `thinkphp_config` VALUES ('17','HTML_CACHE_ON','静态缓存','0','','2','关闭,开启','');
INSERT INTO `thinkphp_config` VALUES ('18','HTML_CACHE_TIME','静态缓存有效期','-1','','0','','');
INSERT INTO `thinkphp_config` VALUES ('19','TMPL_CACHE_ON','模版缓存','1','','2','关闭,开启','');
INSERT INTO `thinkphp_config` VALUES ('20','TMPL_CACHE_TIME','模版缓存有效期','-1','','0','','');
INSERT INTO `thinkphp_config` VALUES ('21','DB_CACHE_ON','数据缓存','0','','2','关闭,开启','');
INSERT INTO `thinkphp_config` VALUES ('22','DB_CACHE_TIME','数据缓存有效期','1000','','1','','');
INSERT INTO `thinkphp_config` VALUES ('23','DATA_CACHE_TYPE','数据缓存类型','File','','4','File,Db,Shomp','');
INSERT INTO `thinkphp_config` VALUES ('24','SHOW_RUN_TIME','显示运行时间','1','','2','不显示,显示','');
INSERT INTO `thinkphp_config` VALUES ('26','LIST_NUMBERS','分页记录数','25','','0','','');
INSERT INTO `thinkphp_config` VALUES ('27','PAGE_NUMBERS','分页页面数','5','','0','','');
INSERT INTO `thinkphp_config` VALUES ('28','TMPL_L_DELIM','模版起始标记','{','','0','','');
INSERT INTO `thinkphp_config` VALUES ('29','TMPL_R_DELIM','模版结束标记','}','','0','','');
INSERT INTO `thinkphp_config` VALUES ('30','TAGLIB_BEGIN','标签库开始标记','<','','0','','');
INSERT INTO `thinkphp_config` VALUES ('31','TAGLIB_END','标签库结束标记','>','','0','','');
INSERT INTO `thinkphp_config` VALUES ('32','CACHE_SERIAL_HEADER','文件缓存开始标记','<?php\\n//','','0','','');
INSERT INTO `thinkphp_config` VALUES ('33','CACHE_SERIAL_FOOTER','文件缓存结束标记','\\n?>','','0','','');
INSERT INTO `thinkphp_config` VALUES ('34','CONFIG_FILE_TYPE','配置文件类型','Define','','0','','');
INSERT INTO `thinkphp_config` VALUES ('35','SHARE_MEM_SIZE','共享内存分配大小','1048576','','1','','');
INSERT INTO `thinkphp_config` VALUES ('36','DATA_CACHE_ON','数据缓存','0','','2','关闭,开启','');
INSERT INTO `thinkphp_config` VALUES ('37','DATA_CACHE_TIME','数据缓存有效期','1000','','1','','');
INSERT INTO `thinkphp_config` VALUES ('38','DATA_CACHE_COMPRESS','数据缓存压缩','1','','2','不压缩,压缩','');
INSERT INTO `thinkphp_config` VALUES ('39','DATA_CACHE_CHECK','数据缓存校验','0','','2','不校验,校验','');
INSERT INTO `thinkphp_config` VALUES ('40','DATA_CACHE_TABLE','数据缓存表名','fcs_cache','','0','','');
INSERT INTO `thinkphp_config` VALUES ('41','SESSION_NAME','会话名称','FCSID','','0','','');
INSERT INTO `thinkphp_config` VALUES ('42','SESSION_TYPE','会话类型','File','','0','','');
INSERT INTO `thinkphp_config` VALUES ('43','SESSION_EXPIRE','会话有效期','10000','','1','','');
INSERT INTO `thinkphp_config` VALUES ('44','SESSION_TABLE','会话数据表','fcs_session','','0','','');
INSERT INTO `thinkphp_config` VALUES ('45','USER_AUTH_ON','用户认证','1','','2','关闭,开启','');
INSERT INTO `thinkphp_config` VALUES ('46','USER_AUTH_TYPE','用户认证类型','2','','4','1,2','');
INSERT INTO `thinkphp_config` VALUES ('47','AUTH_PWD_ENCODER','用户密码加密方式','md5','','0','','');
INSERT INTO `thinkphp_config` VALUES ('48','USER_AUTH_PROVIDER','用户认证委托','DaoAuthentictionProvider','','0','','');
INSERT INTO `thinkphp_config` VALUES ('49','USER_AUTH_GATEWAY','用户认证网关','/Public/login','','0','','');
INSERT INTO `thinkphp_config` VALUES ('50','NOT_AUTH_MODULE','无需认证模块','','','0','','');
INSERT INTO `thinkphp_config` VALUES ('51','REQUIRE_AUTH_MODULE','需要认证模块','','','0','','');
INSERT INTO `thinkphp_config` VALUES ('52','TEMPLATE_SUFFIX','模版文件后缀','.html','','0','','');
INSERT INTO `thinkphp_config` VALUES ('53','CACHFILE_SUFFIX','缓存文件后缀','.php','','0','','');
INSERT INTO `thinkphp_config` VALUES ('54','HTMLFILE_SUFFIX','静态文件后缀','.shtml','','0','','');
INSERT INTO `thinkphp_config` VALUES ('55','DEFAULT_MODULE','默认模块名','Index','','0','','');
INSERT INTO `thinkphp_config` VALUES ('56','DEFAULT_ACTION','默认操作名','Index','','0','','');
INSERT INTO `thinkphp_config` VALUES ('57','DEFAULT_TEMPLATE','默认模版名','default','','0','','');
INSERT INTO `thinkphp_config` VALUES ('58','VAR_MODULE','模块访问变量','m','','0','','');
INSERT INTO `thinkphp_config` VALUES ('59','VAR_ACTION','操作访问变量','a','','0','','');
INSERT INTO `thinkphp_config` VALUES ('60','PATH_MODEL','URL路径模式','3','','4','0,1,2,3','');
INSERT INTO `thinkphp_config` VALUES ('61','PATH_DEPR','URL路径变量分割符',',','','4','0,1,2','');
INSERT INTO `thinkphp_config` VALUES ('62','VAR_LANGUAGE','语言变量','l','','0','','');
INSERT INTO `thinkphp_config` VALUES ('63','VAR_TEMPLATE','模版变量','t','','0','','');
INSERT INTO `thinkphp_config` VALUES ('64','DEFAULT_LANGUAGE','默认语言','zh-cn','','0','','');
INSERT INTO `thinkphp_config` VALUES ('65','BIG_2_GB','简繁转换','0','','2','关闭,开启','');
INSERT INTO `thinkphp_config` VALUES ('66','VAR_PAGE','分页变量','p','','0','','');
INSERT INTO `thinkphp_config` VALUES ('67','ERROR_PAGE','错误页面','','','0','','');
INSERT INTO `thinkphp_config` VALUES ('68','DB_CHARSET','数据库编码','utf8','','0','','一般设置');
INSERT INTO `thinkphp_config` VALUES ('69','TIME_ZONE','时区设置','PRC','','0','','');
INSERT INTO `thinkphp_config` VALUES ('70','DB_CACHE_MAX','数据库最大缓存数','5000','','1','','');
INSERT INTO `thinkphp_config` VALUES ('71','COMPRESS_PAGE','页面压缩','1','','2','不压缩,压缩','');
INSERT INTO `thinkphp_config` VALUES ('72','COOKIE_EXPIRE','cookie过期时间','3000','','1','','');
INSERT INTO `thinkphp_config` VALUES ('73','TMPL_DENY_FUNC_LIST','模版禁用函数','echo,exit','','0','','');

-- 表的结构 `thinkphp_group`
-- 
CREATE TABLE `thinkphp_group` (
`id` smallint(6) unsigned NOT NULL default 0 auto_increment,
`name` varchar(20) NOT NULL default 0 ,
`pid` smallint(6) NOT NULL default 0 ,
`status` tinyint(1) unsigned NOT NULL default 0 ,
`remark` varchar(255) NOT NULL default 0 ,
`ename` varchar(5) NOT NULL default 0 ,
`requireRate` tinyint(1) unsigned NOT NULL default 0 ,
PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
-- 
-- 导出表中的数据 `thinkphp_group`
--
INSERT INTO `thinkphp_group` VALUES ('1','管理员组','0','1','管理员权限组',,'0');
INSERT INTO `thinkphp_group` VALUES ('2','普通用户组','0','1','一般会员权限',,'0');
INSERT INTO `thinkphp_group` VALUES ('3','编辑组','2','1','编辑权限',,'0');
-- 
-- 表的结构 `thinkphp_groupuser`
-- 
CREATE TABLE `thinkphp_groupuser` (
`groupId` mediumint(9) unsigned NOT NULL default 0 ,
`userId` mediumint(9) unsigned NOT NULL default 0 
) ENGINE=InnoDB DEFAULT CHARSET=utf8
-- 
-- 导出表中的数据 `thinkphp_groupuser`
--
INSERT INTO `thinkphp_groupuser` VALUES ('1','1');
INSERT INTO `thinkphp_groupuser` VALUES ('3','1');
INSERT INTO `thinkphp_groupuser` VALUES ('3','2');
INSERT INTO `thinkphp_groupuser` VALUES ('3','3');
INSERT INTO `thinkphp_groupuser` VALUES ('2','2');
INSERT INTO `thinkphp_groupuser` VALUES ('2','3');
-- 
-- 表的结构 `thinkphp_memo`
-- 
CREATE TABLE `thinkphp_memo` (
`id` mediumint(6) unsigned NOT NULL default 0 auto_increment,
`label` varchar(255) NOT NULL default 0 ,
`memo` text NOT NULL default 0 ,
`createTime` varchar(25) NOT NULL default 0 ,
`userId` mediumint(8) NOT NULL default 0 ,
`type` varchar(15) NOT NULL default 0 ,
PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
-- 
-- 导出表中的数据 `thinkphp_memo`
--
-- 
-- 表的结构 `thinkphp_node`
-- 
CREATE TABLE `thinkphp_node` (
`id` smallint(6) unsigned NOT NULL default 0 auto_increment,
`name` varchar(20) NOT NULL default 0 ,
`title` varchar(50) NOT NULL default 0 ,
`status` tinyint(1) unsigned NOT NULL default 0 ,
`remark` varchar(255) NOT NULL default 0 ,
`seqNo` smallint(6) unsigned NOT NULL default 0 ,
`pid` smallint(6) unsigned NOT NULL default 0 ,
`level` tinyint(1) unsigned NOT NULL default 0 ,
`type` tinyint(1) NOT NULL default 0 ,
PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
-- 
-- 导出表中的数据 `thinkphp_node`
--
INSERT INTO `thinkphp_node` VALUES ('1','Admin','后台管理','1',,'1','0','1','0');
INSERT INTO `thinkphp_node` VALUES ('2','CMS','CMS前台','1','','2','0','1','0');
INSERT INTO `thinkphp_node` VALUES ('3','Public','公共模块','1','','2','1','2','0');
INSERT INTO `thinkphp_node` VALUES ('4','Node','节点管理','1','','10','1','2','0');
INSERT INTO `thinkphp_node` VALUES ('5','Group','权限管理','1','','11','1','2','0');
INSERT INTO `thinkphp_node` VALUES ('6','Article','文章管理','1','','6','1','2','0');
INSERT INTO `thinkphp_node` VALUES ('7','Category','分类管理','1','','7','1','2','0');
INSERT INTO `thinkphp_node` VALUES ('8','Comment','评论管理','1','','8','1','2','0');
INSERT INTO `thinkphp_node` VALUES ('9','Attach','附件管理','1','','13','1','2','0');
INSERT INTO `thinkphp_node` VALUES ('10','User','用户管理','1','','9','1','2','0');
INSERT INTO `thinkphp_node` VALUES ('11','File','文件管理','1','','12','1','2','0');
INSERT INTO `thinkphp_node` VALUES ('12','System','系统管理','1','','14','1','2','0');
INSERT INTO `thinkphp_node` VALUES ('13','DBManager','数据库管理','1','','17','1','2','0');
INSERT INTO `thinkphp_node` VALUES ('14','Index','默认模块','1','','3','1','2','0');
INSERT INTO `thinkphp_node` VALUES ('15','Board','公告管理','1','','1','1','2','0');
INSERT INTO `thinkphp_node` VALUES ('16','Config','系统配置','1','','16','1','2','0');
INSERT INTO `thinkphp_node` VALUES ('17','Page','页面管理','1','','4','1','2','0');
INSERT INTO `thinkphp_node` VALUES ('18','PlugIn','插件管理','1','','15','1','2','0');
INSERT INTO `thinkphp_node` VALUES ('19','Index','首页','1','',,'2','2','0');
INSERT INTO `thinkphp_node` VALUES ('20','Article','文章查看','1','的反对法',,'2','2','0');
INSERT INTO `thinkphp_node` VALUES ('21','add','新增','1','',,'3','3','0');
INSERT INTO `thinkphp_node` VALUES ('22','insert','插入','1','',,'3','3','0');
INSERT INTO `thinkphp_node` VALUES ('23','edit','编辑','1','',,'3','3','0');
INSERT INTO `thinkphp_node` VALUES ('24','update','保存','1','',,'3','3','0');
INSERT INTO `thinkphp_node` VALUES ('25','index','默认操作','1','',,'3','3','0');
INSERT INTO `thinkphp_node` VALUES ('26','sadd','简易添加','1','',,'3','3','0');
INSERT INTO `thinkphp_node` VALUES ('27','sedit','简易编辑','1','',,'3','3','0');
INSERT INTO `thinkphp_node` VALUES ('28','ssort','简易排序','1','',,'3','3','0');
INSERT INTO `thinkphp_node` VALUES ('29','suser','组用户列表','1','',,'5','3','0');
INSERT INTO `thinkphp_node` VALUES ('30','sapp','项目授权','1','',,'5','3','0');
INSERT INTO `thinkphp_node` VALUES ('31','smodule','模块授权','1','',,'5','3','0');
INSERT INTO `thinkphp_node` VALUES ('32','saction','操作授权','1','',,'5','3','0');
INSERT INTO `thinkphp_node` VALUES ('33','setApp','项目授权保存','1','',,'5','3','0');
INSERT INTO `thinkphp_node` VALUES ('34','setModule','模块授权保存','1','',,'5','3','0');
INSERT INTO `thinkphp_node` VALUES ('35','setAction','操作授权保存','1','',,'5','3','0');
INSERT INTO `thinkphp_node` VALUES ('36','sindex','目录列表','1','',,'11','3','0');
INSERT INTO `thinkphp_node` VALUES ('37','select','附件挑选','1','',,'9','3','0');


-- 
-- 表的结构 `thinkphp_plugin`
-- 
CREATE TABLE `thinkphp_plugin` (
`id` mediumint(8) unsigned NOT NULL default 0 auto_increment,
`name` varchar(255) NOT NULL default 0 ,
`author` varchar(255) NOT NULL default 0 ,
`description` text NOT NULL default 0 ,
`status` tinyint(1) unsigned NOT NULL default 0 ,
`version` varchar(10) NOT NULL default 0 ,
`uri` varchar(255) NOT NULL default 0 ,
`file` varchar(255) NOT NULL default 0 ,
`app` varchar(50) NOT NULL default 0 ,
PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8

-- 表的结构 `thinkphp_session`
-- 
CREATE TABLE `thinkphp_session` (
`id` int(11) unsigned NOT NULL default 0 auto_increment,
`cachekey` varchar(255) NOT NULL default 0 ,
`expire` int(11) NOT NULL default 0 ,
`data` blob NOT NULL default 0 ,
`datasize` int(11) NOT NULL default 0 ,
`datacrc` varchar(32) NOT NULL default 0 ,
PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8


-- 表的结构 `thinkphp_user`
-- 
CREATE TABLE `thinkphp_user` (
`id` int(11) unsigned NOT NULL default 0 auto_increment,
`name` varchar(30) NOT NULL default 0 ,
`nickname` varchar(50) NOT NULL default 0 ,
`password` varchar(32) NOT NULL default 0 ,
`email` varchar(255) NOT NULL default 0 ,
`url` varchar(255) NOT NULL default 0 ,
`verify` varchar(8) NOT NULL default 0 ,
`rTime` int(11) unsigned NOT NULL default 0 ,
`lTime` int(11) unsigned NOT NULL default 0 ,
`guid` varchar(32) NOT NULL default 0 ,
`status` tinyint(1) unsigned NOT NULL default 0 ,
PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
-- 
-- 导出表中的数据 `thinkphp_user`
--
INSERT INTO `thinkphp_user` VALUES ('1','admin','admin','21232f297a57a5a743894a0e4a801fc3','liu21st@gmail.com','http://blog.topthink.com.cn','0','0','1167809014','','1');
INSERT INTO `thinkphp_user` VALUES ('2','guest','客人','11111','','','','0','0','','1');
INSERT INTO `thinkphp_user` VALUES ('3','test','测试用户','e10adc3949ba59abbe56e057f20f883e','','','0','0','1167560244','','1');
