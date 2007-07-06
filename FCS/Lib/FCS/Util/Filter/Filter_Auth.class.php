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
 * FCS
 +------------------------------------------------------------------------------
 * @package    Core
 * @link       http://www.fcs.org.cn
 * @copyright  Copyright (c) 2005-2006 liu21st.com.  All rights reserved. 
 * @author     liu21st <liu21st@gmail.com>
 * @version    $Id$
 +------------------------------------------------------------------------------
 */

/**
 +------------------------------------------------------------------------------
 * 用户验证过滤类
 +------------------------------------------------------------------------------
 * @package   core
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Ver$
 +------------------------------------------------------------------------------
 */
class Filter_Auth extends Base
{//类定义开始

	function execute() 
	{
        //如果项目要求认证，并且当前模块需要认证，则进行权限认证
        if( USER_AUTH_ON ){
            //取得系统不需要认证的模块列表
            $notAuthModuleList = explode(',',NOT_AUTH_MODULE);
            if(!in_array(MODULE_NAME,$notAuthModuleList)) {
                //检查认证识别号
                if(!Session::is_set(USER_AUTH_KEY)) {
                    //如果认证识别号不存在，则跳转到验证入口
                    redirect($_SERVER["SCRIPT_NAME"].USER_AUTH_GATEWAY);
                }
            }
        }
        return ;
	}
}//类定义结束
?>