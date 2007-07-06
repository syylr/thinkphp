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
import('Admin.Action.AdminAction');
/**
 +------------------------------------------------------------------------------
 * 登录管理
 +------------------------------------------------------------------------------
 * @package   core
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Ver$
 +------------------------------------------------------------------------------
 */
class LoginAction extends AdminAction
{//类定义开始


    /**
     +----------------------------------------------------------
     * 清空充值卡
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
	function clear() 
	{
		$dao	=	new LoginDao();
		$result =	$dao->delete();
		if($result) {
                //成功提示
                $this->assign("message",'登录日志清空成功');
                $this->assign("jumpUrl",$this->getReturnUrl());
            }else { 
                //失败提示
                $this->assign("error",'清空失败');
            }
		$this->forward();
		return ;
	}
	function _filter(&$map) 
	{
		if(!empty($_POST['startTime']) && !empty($_POST['endTime'])) {
			$map->put('inTime', array(strtotime($_POST['startTime']),strtotime($_POST['endTime'])+86400));
		}elseif(!empty($_POST['startTime'])) {
			$map->put('inTime', array('gt',strtotime($_POST['startTime'])));
		}elseif(!empty($_POST['endTime'])) {
			$map->put('inTime', array('lt',strtotime($_POST['endTime'])+86400));
		}
	}
}//类定义结束
?>