<?php
/**
+------------------------------------------------------------------------------
* 项目配置文件 本配置文件由系统自动生成
+------------------------------------------------------------------------------
*/
if (!defined('THINK_PATH')) exit();
return array(
	'DB_TYPE'=>'mysql',
	'DB_HOST'=>'localhost',
	'DB_NAME'=>'blog',
	'DB_USER'=>'root',
	'DB_PWD'=>'',
	'DB_PORT'=>'3306',
	'DB_PREFIX'=>'think_',
	'default_module'=>'Blog',
	'debug_mode'=>false,
	'html_cache_on'=>false,
	'LIMIT_RESFLESH_ON'=>false,	// 默认关闭防刷新机制
	'LIMIT_REFLESH_TIMES'=>5,	// 页面防刷新时间 默认3秒
	'DATA_RESULT_TYPE'=>1,
	'SHOW_RUN_TIME'=>true,			// 运行时间显示
	'SHOW_ADV_TIME'=>true,			// 显示详细的运行时间
	'SHOW_DB_TIMES'=>true,			// 显示数据库查询和写入次数
	'SHOW_CACHE_TIMES'=>true,		// 显示缓存操作次数
	'SHOW_USE_MEM'=>true,			// 显示内存开销
);

//配置文件定义结束
?>