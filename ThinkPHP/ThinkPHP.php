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
// $Id: ThinkPHP.php 11 2007-01-04 03:57:34Z liu21st $

/**
 +------------------------------------------------------------------------------
 * FCS公共文件
 +------------------------------------------------------------------------------
 * @copyright  Copyright (c) 2005-2006 liu21st.com.  All rights reserved. 
 * @author     liu21st <liu21st@gmail.com>
 * @version    $Id: ThinkPHP.php 11 2007-01-04 03:57:34Z liu21st $
 +------------------------------------------------------------------------------
 */
//FCS系统目录定义
if(!defined('FCS_PATH')) define('FCS_PATH', dirname(__FILE__));
    
//加载系统定义文件和公共函数库
require_once(FCS_PATH."/Common/defines.php");
require_once(FCS_PATH."/Common/functions.php");

//如果PHP4导入兼容函数库
//if(version_compare(PHP_VERSION, '5.1.2', '<')) 
    require_once (FCS_PATH."/Common/compat.php");

//加载FCS基类
import("FCS.Core.Base");

//加载异常处理类
import("FCS.Exception.FcsException");

//加载FCS核心类
import("FCS.Core.App");
import("FCS.Core.Vo");
import("FCS.Core.Dao");
import("FCS.Core.Action");
import("FCS.Core.VoList");
import("FCS.Core.Template");	

//加载日志处理类
import("FCS.Util.Log");
?>