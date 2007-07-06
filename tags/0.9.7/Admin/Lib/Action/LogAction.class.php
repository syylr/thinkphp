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
// $Id: LogAction.class.php 78 2007-04-01 04:29:15Z liu21st $

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