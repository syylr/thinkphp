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
 * @version    $Id: LogAction.class.php 73 2006-11-08 10:08:01Z fcs $
 +------------------------------------------------------------------------------
 */
import('@.Action.AdminAction');
/**
 +------------------------------------------------------------------------------
 * 日志管理
 +------------------------------------------------------------------------------
 * @package   core
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Ver$
 +------------------------------------------------------------------------------
 */
class LogAction extends AdminAction
{//类定义开始

	function _filter(&$map) 
	{

       if(!empty($_POST['name']) ){
        	$dao = new UserDao();
            $user = $dao->find('name="'.$_POST['name'].'"');
            if($user) {
            	$map->put('userId',$user->id);
            }
        }
		//查看时间段记录
		if(!empty($_POST['startTime1']) && !empty($_POST['startTime2'])) {
			$map->put('time', array(strtotime($_POST['startTime1']),strtotime($_POST['startTime2'])+86400));
		}elseif(!empty($_POST['startTime1'])) {
			$map->put('time', array('gt',strtotime($_POST['startTime1'])));
		}elseif(!empty($_POST['startTime2'])) {
			$map->put('time', array('lt',strtotime($_POST['startTime2'])+86400));
		}
	}


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
		$dao	=	new LogDao();
		$result =	$dao->clear();
		if($result) {
                //成功提示
                $this->assign("message",'日志清空成功');
                $this->assign("jumpUrl",$this->getReturnUrl());
            }else { 
                //失败提示
                $this->assign("error",'清空失败');
            }
		$this->forward();
		return ;
	}


}//类定义结束
?>