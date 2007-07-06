<?php 
// +---------------------------------------------------------------------------+
// | FCS -- Fast,Compatible & Simple OOP PHP Framework                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2005-2006 liu21st.com.  All rights reserved.                |
// | Website: http://www.fcs.org.cn/                                           |
// | Author : Liu21st 流年 <liu21st@gmail.com>                                 |
// +---------------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify it   |
// | under the terms of the GNU General Public License as published by the     |
// | Free Software Foundation; either version 2 of the License,  or (at your   |
// | option) any later version.                                                |
// |                                                                           |
// | This program is distributed in the hope that it will be useful,  but      |
// | WITHOUT ANY WARRANTY; without even the implied warranty of                |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General |
// | Public License for more details.                                          |
// +---------------------------------------------------------------------------+

/**
 +------------------------------------------------------------------------------
 * FCS公共文件
 +------------------------------------------------------------------------------
 * @package    Core
 * @link       http://www.fcs.org.cn
 * @copyright  Copyright (c) 2005-2006 liu21st.com.  All rights reserved. 
 * @author     liu21st <liu21st@gmail.com>
 * @version    $Id$
 +------------------------------------------------------------------------------
 */

//FCS目录定义
if(!defined('FCS_PATH')) define('FCS_PATH', dirname(__FILE__));

//加载常量定义文件和公共函数库
require_once(FCS_PATH."/Common/defines.php");
require_once(FCS_PATH."/Common/functions.php");
//如果PHP4导入兼容函数库
if(version_compare(PHP_VERSION, '5.1.0', '<')) 
    require_once (FCS_PATH."/Common/compatible.php");
//加载FCS基类
import("FCS.Core.Base");
//加载FCS核心类
import("FCS.Core.Vo");
import("FCS.Core.Dao");
import('FCS.Core.Action');
import("FCS.Core.App");
import("FCS.Core.*");
//加载FCS异常基类
import("FCS.Exception.FcsException");
//加载FCS Session类
import('FCS.Util.Session');
?>