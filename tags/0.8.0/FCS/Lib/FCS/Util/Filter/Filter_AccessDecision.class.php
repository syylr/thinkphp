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
 * 决策访问过滤类
 +------------------------------------------------------------------------------
 * @package   core
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Ver$
 +------------------------------------------------------------------------------
 */
class Filter_AccessDecision extends Base
{//类定义开始

	function execute() 
	{
        //如果项目要求认证，并且当前模块需要认证，则进行权限认证
        if( USER_AUTH_ON ){
            //取得系统不需要认证的模块列表
            $notAuthModuleList = explode(',',NOT_AUTH_MODULE);
            if(!in_array(MODULE_NAME,$notAuthModuleList)) {
				dump($_SESSION);
                //检查认证识别号
                if(!Session::is_set(USER_AUTH_KEY)) {
                    //如果认证识别号不存在，则跳转到验证入口
                    redirect($_SERVER["SCRIPT_NAME"].USER_AUTH_GATEWAY);
                }
                //存在认证识别号，则进行进一步的访问决策
                if(USER_AUTH_TYPE==2) {
                    //加强验证和即时验证模式 更加安全 后台权限修改可以即时生效
                    //该模式下同时检查模块和操作权限
                    //如果当前操作已经验证过 无需再次验证
                    $accessGuid   =   md5(MODULE_NAME.ACTION_NAME);
                    if(Session::is_set($accessGuid)) {
                        if(true !== Session::get($accessGuid)) 
                            throw_exception('访问未授权！');
                    }else {
                        //通过数据库进行访问检查
                        import("FCS.RBAC.AccessDecisionManager");
                        $accessManager = new AccessDecisionManager();
                        if(!$accessManager->decide(Session::get(USER_AUTH_KEY))) 
                            throw_exception('访问未授权！');
                        else 
                            Session::set($accessGuid,true);
                    }                   	
                }else {
                    //登录验证模式，比较登录后保存的权限访问列表
                    $moduleList =   Session::get('_moduleList');
                    $actionList =   Session::get('_actionList');

                    //是否具有当前模块权限
                    if(!in_array(MODULE_NAME,$moduleList))
                        throw_exception(MODULE_NAME.'模块访问未授权！');
                    //是否具有当前操作权限
                    if(!in_array(ACTION_NAME,$actionList))
                        throw_exception('没有操作权限：'.ACTION_NAME);
                }
            }
        }
        return ;
	}
}//类定义结束
?>