// +----------------------------------------------------------------------+
// | ThinkPHP                                                             |
// +----------------------------------------------------------------------+
// | Copyright (c) 2006 liu21st.com All rights reserved.                  |
// +----------------------------------------------------------------------+
// | Licensed under the Apache License, Version 2.0 (the 'License');      |
// | you may not use this file except in compliance with the License.     |
// | You may obtain a copy of the License at                              |
// | http://www.apache.org/licenses/LICENSE-2.0                           |
// | Unless required by applicable law or agreed to in writing, software  |
// | distributed under the License is distributed on an 'AS IS' BASIS,    |
// | WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or      |
// | implied. See the License for the specific language governing         |
// | permissions and limitations under the License.                       |
// +----------------------------------------------------------------------+
// | Author: liu21st <liu21st@gmail.com>                                  |
// +----------------------------------------------------------------------+
// $Id$

ThinkPHP是什么
ThinkPHP是一个快速、兼容而且简单的面向对象的轻量级WEB开发框架，是为了简化企业级应用开发而诞生的，借鉴了Java的Struts结构，使用面向对象结构和MVC模式，并且模拟实现了Struts的标签库，在PHP4的兼容性方面表现不凡，其模版引擎、缓存机制、认证机制和扩展性方面更是ThinkPHP的特色功能。
使用ThinkPHP，你可以更方便和快捷的开发和部署应用，当然不仅仅是企业级应用，任何PHP应用开发都可以从ThinkPHP的简单、兼容和快速的特性中受益。简洁、快速和实用是ThinkPHP发展秉承的宗旨，ThinkPHP会不断吸收和融入更好的技术以保证其新鲜和活力！

ThinkPHP功能特色
面向对象和MVC模式，单一入口模式 
一个项目独立目录，并且可以部署在非WEB目录下面，应用更安全 
兼容PHP4和PHP5，性能一样卓越 
自动编码转换和utf-8支持 
内置模板引擎、标签库技术支持，支持其他模版引擎 
多元化动态缓存机制，支持File、Shmop、Memcache、Db、Sqlite等多种缓存方式 
内置抽象数据库访问层和OO-RDMS Mapping 
内置CURD和常用操作，常用开发不用写任何代码 
多数据库支持，目前包括对Mysql、Sqlite、PgSql、MsSql、Oracle的支持 
强大的官方基类库支持，提供更多实用类库，应用开发更加方便 
集成RBAC权限访问控制插件，让您的权限控制很简单 
强大的插件功能，轻松扩展，灵活部署 
除错和日志功能让你调试更方便 
AJAX支持，内置SmartAjax类库

官方网站： http://thinkphp.cn
官方支持Blog：http://thinkphp.cn/blog
SVN地址：http://thinkphp.googlecode.com/svn/trunk/

[具备条件]
ThinkPHP可以运行在windows/Unix主机上面
支持以下数据库（可扩展）
Mysql（Mysqli）
Mssql
Oracle
Pgsql
Sqlite

建议版本
Apache 1.33以上
PHP 4.3.0 以上
MySql 4.1.0 以上

[目录结构]
┎━ThinkPHP 框架系统目录
┃  ┝ Common	公共文件目录
┃  ┝ Lang	语言包目录
┃  ┝ PlugIns	内置插件目录
┃  ┗ Lib	应用类库目录
┃     ┝ FCS	基类库目录
┃     ┗ ORG	ORG类库包
┃
┝━Admin 示例项目
┃  ┝ Cache	模版缓存目录
┃  ┝ Common	公共文件目录
┃  ┝ Conf	项目配置目录
┃  ┝ Html	静态文件目录
┃  ┝ Lib		应用类库目录
┃  ┝ PlugIns	项目插件目录
┃  ┝ Tpl		模版文件目录
┃  ┝ Lang	语言包目录
┃  ┝ Logs	日志文件目录
┃  ┝ Temp	数据缓存目录
┃  ┗ Uploads	上传文件目录
┃
┝━Public 网站公共目录
┃  ┗ Js	ThinkPHP JS类库目录
┃  ┗ Uploads	公共上传目录

[安装说明]
把ThinkPHP、Admin、CMS、Public目录和相关文件直接上传（或拷贝到）服务器web目录下面
修改安装根目录下面的配置文件config.php设置好数据库访问信息
如果是Unix类环境，请保证下面目录可写（设置为777）
Admin目录下面的
Cache 
Conf
Temp
Logs
Uploads
导入Admin目录下面的thinkphp.sql数据库脚本到mysql数据库
（如果是mysql4.1以下版本，请修改thinkphp.sql文件，把DEFAULT CHARSET=utf8去掉）
注意示例程序mysql保存数据采用utf-8编码，请注意mysql相应设置。

配置完成后运行 根目录下面的index.php文件就可以运行示例项目。
注意：第一次运行请到后台管理点击插件管理，生成插件缓存。
后台初始登录帐户名 admin 
密码 admin 
验证码0 对应的字母


[版本更新]
+------------------------+
|0.9.5 版本更新            |
+------------------------+
插件管理
项目配置管理
系统管理
数据库管理
文件管理
Ajax支持和SmartAjax类库内置
优化的Action和Dao

+------------------------+
|0.9 版本更新            |
+------------------------+
优化动态缓存模块
增加插件功能
调整目录结构，更加方便应用部署
增加ORG类库包
把内置模版引擎分离成FCSTemplate插件
把内置的RBAC功能分离成RBAC插件
内置支持mysql和mysqli数据库，其他数据库驱动转换成插件
内置支持File方式缓存，其他方式转换成插件
增加Smarty模版引擎支持插件
DbSession支持
增加简繁转换、ActionCache、htmlCache、BrowserCache插件
整体性能优化

+------------------------+
|0.8 版本更新            |
+------------------------+
数据动态缓存实现；
增加分页显示功能；
表单数据验证和文件上传；
模板标签库功能；
增加配置文件类；
更好的PHP5处理；
代码规范化改进；
认证委托管理器实现，支持多种方式进行认证；
RBAC基于角色的权限控制实现；
Dao类中增加了getOne getCount getMax getMin getSum方法；
Action类的更加通用化；
Vo类和Dao类的自动创建；
Session类的增加和完善，支持命名空间支持；
Page类的改进；
增加了Date类；
增加了varFilter、Auth和AccessDecision过滤器；
增加了Config_Dao配置文件类，支持数据库作为配置文件解析；
缓存机制的完善，不再缓存全部列表，而是分页缓存，以及及时更新缓存；
增加前置操作和后置操作；
增加操作预处理器和触发器；
内置Action类增加select方法和source，用于挑选和查看源码功能；
模板输出支持编码更改和contentType更改；
增加var_filter_deep、msubstr、rand_string、unserialize_callback函数；
异常处理页面采用可定制模板；
增加JS类库和导入标签；
运行时间显示开关；

+------------------------+
|0.7 版本更新            |
+------------------------+
增加抽象数据库访问层，支持多种数据库
增加数据模型对象和数据访问对象
FCS系统目录可以放置在非WEB目录下面
增加FCS基类库概和应用类库概念
支持企业级的应用开发、协作和并发项目
独立的项目配置文件、语言包和日志调试
分离内置模板引擎，增加对数据对象和列表的支持
增强异常和错误处理方式以及调试功能
全新的应用类库命名空间加载方式

+------------------------+
|0.6 版本                |
+------------------------+
最基础的MVC框架 袖珍型OOP框架
^ 面向对象的开发框架
^ 基于应用组件和类库组件构建应用
^ 类库导入支持命名空间
^ 基于模块和操作方式访问
^ 程序和页面模板100%分离
^ 自动编码转换和UTF-8支持
^ 搜索引擎友好URLs支持

[配置说明]
// 调试设置 
DEBUG_MODE		//是否启用调试模式
WEB_LOG_RECORD	//是否记录日志
LOG_FILE_SIZE		//日志文件大小
ERROR_PAGE		//错误定向页面
ERROR_MESSAGE	//错误提示信息
SHOW_RUN_TIME	//是否显示运行时间

// 路径设置
TMPL_PATH		//模块路径
HTML_PATH		//静态页面路径

// 框架设置
DEFAULT_MODULE	//默认模块名称
DEFAULT_ACTION	//默认操作名称
DEFAULT_TEMPLATE //默认模版名称
VAR_LANGUAGE	//语言GET变量
VAR_TEMPLATE		//模版GET变量
VAR_MODULE		//模块变量
VAR_ACTION		//操作变量
VAR_FILE			//文件变量

SAVE_PARENT_VO		//是否保存到父类Vo对象
UPDATE_PARENT_VO	//是否更新父类Vo对象
DENY_REMOTE_SUBMIT	//是否允许远程提交

// 插件设置
PLUGIN_CACHE_ON		//是否缓存插件

// 语言设置
DEFAULT_LANGUAGE	 //默认语言 需要对应语言包文件

// 输出编码
OUTPUT_CHARSET

// 时区设置 PHP5需要
TIME_ZONE

// 模版设置
TMPL_ENGINE_TYPE		//模版引擎名称
TEMPLATE_SUFFIX		//模版文件后缀
CACHFILE_SUFFIX		//模版缓存后缀
TEMPLATE_CHARSET		//模版文件编码
TMPL_CACHE_ON		//模版缓存开启
TMPL_CACHE_TIME		//模版缓存时间
TMPL_DENY_FUNC_LIST	//模版禁用函数
TMPL_L_DELIM			//模版开始标记
TMPL_R_DELIM			//模版结束标记
TAGLIB_BEGIN			//标签库开始标记
TAGLIB_END			//标签库结束标记

// Session设置
SESSION_NAME		//session name
SESSION_TYPE		//session使用方式 支持DB
SESSION_EXPIRE	//session 有效时间
SESSION_TABLE	//session 数据库表名
COOKIE_DOMAIN	//session 跨域设置

// 动态数据缓存
DATA_CACHE_ON		//是否启用动态数据缓存
DATA_CACHE_TYPE		//数据缓存类型
DATA_CACHE_TIME		//数据缓存有效期
DATA_CACHE_MAX		//最大数据缓存数目
DATA_CACHE_COMPRESS //是否启用数据压缩
DATA_CACHE_CHECK	//是否启用数据校验
DATA_CACHE_TABLE		//缓存数据库表名 当DATA_CACHE_TYPE为DB时候有效
CACHE_SERIAL_HEADER	//文件方式缓存头部设置
CACHE_SERIAL_FOOTER	//文件方式缓存尾部设置
SHARE_MEM_SIZE		//使用shmop方式时候内存大小设置

// 数据库设置
DB_TYPE		//数据库类型
DB_HOST		//数据库主机地址
DB_NAME		//数据库名称
DB_USER		//数据库用户名
DB_PWD		//数据库密码
DB_PORT		//数据库端口
DB_PREFIX	//数据库前缀
DB_CHARSET	//数据库编码

// 数据库缓存
DB_CACHE_ON		//是否启用数据库缓存
DB_CACHE_TIME	//数据库缓存有效期
DB_CACHE_MAX	//缓存最大记录数

// 分页设置
LIST_NUMBERS		//分页每页列表数目
PAGE_NUMBERS	//显示页数
VAR_PAGE		//分页变量

//-------------------------------------------------------------
// 插件设置项目 根据加载插件的不同进行设置
// 具体插件的设置项请参考插件文件说明
//-------------------------------------------------------------

// FCSDispatch插件
DISPATCH_NAME	 //Dispatch名称
URL_MODEL		//URL模式
PATH_MODEL		//Pathinfo类型	
PATH_DEPR		//pathinfo分割符

// 静态缓存插件
HTMLFILE_SUFFIX	//静态文件后缀
HTML_CACHE_ON	//是否启用静态缓存
HTML_CACHE_TIME	//静态缓存有效期

// RBAC插件
USER_AUTH_ON		//是否需要用户认证
USER_AUTH_TYPE		//用户认证方式
USER_AUTH_KEY		//用户认证标识号
AUTH_PWD_ENCODER	//用户认证密码加密方式
USER_AUTH_PROVIDER	//委托认证类型
USER_AUTH_GATEWAY	//认证网关地址
REQUIRE_AUTH_MODULE	//需要认证的模块，用逗号分割多个，优先于NOT_AUTH_MODULE
NOT_AUTH_MODULE		//无需认证的模块 用逗号分割多个

// 页面压缩输出插件
COMPRESS_PAGE
COMPRESS_METHOD 
COMPRESS_LEVEL

//浏览器缓存插件
BROWSER_CACHE

// 简繁转换插件
BIG_2_GB

// 任何项目需要的设置项目可以在这里统一设置 可选项目 
WEB_TITLE	
WEB_DOMAIN