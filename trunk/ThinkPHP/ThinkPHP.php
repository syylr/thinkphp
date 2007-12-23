<?php 
// +----------------------------------------------------------------------+
// | ThinkPHP                                                             |
// +----------------------------------------------------------------------+
// | Copyright (c) 2006~2007 http://thinkphp.cn All rights reserved.      |
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

/**
 +------------------------------------------------------------------------------
 * ThinkPHP公共文件
 +------------------------------------------------------------------------------
 * @copyright  Copyright (c) 2005-2006 liu21st.com.  All rights reserved. 
 * @author     liu21st <liu21st@gmail.com>
 * @version    $Id$
 +------------------------------------------------------------------------------
 */

//记录开始运行时间
$GLOBALS['_beginTime'] = microtime(TRUE);

// ThinkPHP系统目录定义
if(!defined('THINK_PATH')) define('THINK_PATH', dirname(__FILE__));
if(!defined('APP_PATH')) define('APP_PATH', dirname(THINK_PATH).'/'.APP_NAME);
if(!defined('RUNTIME_PATH')) define('RUNTIME_PATH',APP_PATH);

if(file_exists(RUNTIME_PATH.'/~runtime.php')) {
	// 加载框架核心缓存文件
	// 如果有修改核心文件请删除该缓存
	require RUNTIME_PATH.'/~runtime.php';
}else{
	// 加载系统定义文件
	require THINK_PATH."/Common/defines.php";
	// 系统函数库
	require THINK_PATH."/Common/functions.php";

	//加载ThinkPHP基类
	import("Think.Core.Base");
	//加载异常处理类
	import("Think.Exception.ThinkException");
	// 加载日志类
	import("Think.Util.Log");
	//加载Think核心类
	import("Think.Core.App");
	import("Think.Core.Action");
	import("Think.Core.Model");
	import("Think.Core.View");

	// 生成核心文件的缓存 去掉文件空白以减少大小
	$content	 =	 php_strip_whitespace(THINK_PATH.'/Common/defines.php');
	$content	.=	 php_strip_whitespace(THINK_PATH.'/Common/functions.php');
	$content	.=	 php_strip_whitespace(THINK_PATH.'/Lib/Think/Core/Base.class.php');
	$content	.=	 php_strip_whitespace(THINK_PATH.'/Lib/Think/Exception/ThinkException.class.php');
	$content	.=	 php_strip_whitespace(THINK_PATH.'/Lib/Think/Util/Log.class.php');
	$content	.=	 php_strip_whitespace(THINK_PATH.'/Lib/Think/Core/App.class.php');
	$content	.=	 php_strip_whitespace(THINK_PATH.'/Lib/Think/Core/Action.class.php');
	$content	.=	 php_strip_whitespace(THINK_PATH.'/Lib/Think/Core/Model.class.php');
	$content	.=	 php_strip_whitespace(THINK_PATH.'/Lib/Think/Core/View.class.php');
	if(version_compare(PHP_VERSION,'5.2.0','<') ) {
		// 加载兼容函数
		$content .=	 THINK_PATH.'/Common/compat.php,';	
	}
	file_put_contents(RUNTIME_PATH.'/~runtime.php',$content);
	unset($content);
}
// 记录加载文件时间
$GLOBALS['_loadTime'] = microtime(TRUE);
?>