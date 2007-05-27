<?php 
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
$GLOBALS['_beginTime'] = array_sum(explode(' ', microtime()));
//ThinkPHP系统目录定义
if(!defined('THINK_PATH')) define('THINK_PATH', dirname(__FILE__));

//加载系统定义文件和系统函数库
require_once(THINK_PATH."/Common/defines.php");
require_once(THINK_PATH."/Common/functions.php");

//如果PHP4导入兼容函数库
//if(version_compare(PHP_VERSION, '5.1.2', '<')) 
    require_once (THINK_PATH."/Common/compat.php");

//加载ThinkPHP基类
import("Think.Core.Base");

//加载异常处理类
import("Think.Exception.ThinkException");

//加载Think核心类
import("Think.Core.App");
import("Think.Core.Vo");
import("Think.Core.Dao");
import("Think.Core.Action");
$GLOBALS['_loadTime'] = array_sum(explode(' ', microtime()));
?>