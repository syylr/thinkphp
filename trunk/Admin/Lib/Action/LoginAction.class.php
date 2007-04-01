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

import('@.Action.AdminAction');
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


	function _filter(&$map) 
	{
        import('@.Dao.UserDao');
		if($map->containsKey('id') && $map->containsKey('type')) {
			$dao = new UserDao();
			$user = $dao->find('type='.$map->get('type').' and childId='.$map->get('id'));
			if($user) {
				$map->put('userId',$user->id);
			}else {
				$map->put('userId',$map->get('id'));
			}
			$map->remove('id');
		}
		$this->assign('currentLoginName',$user->nickname);
        if(!empty($_POST['name']) ){
        	$dao = new UserDao();
            $user = $dao->find('name="'.$_POST['name'].'"');
            if($user) {
            	$map->put('userId',$user->id);
            }
            
        }
		//如果是主持人，查看自己聊天记录
		if(!Session::is_set('userId')) {
			$map->put('userId',Session::get(USER_AUTH_KEY));
		}
		//查看时间段记录
		if(!empty($_POST['startTime1']) && !empty($_POST['startTime2'])) {
			$map->put('inTime', array(strtotime($_POST['startTime1']),strtotime($_POST['startTime2'])+86400));
		}elseif(!empty($_POST['startTime1'])) {
			$map->put('inTime', array('gt',strtotime($_POST['startTime1'])));
		}elseif(!empty($_POST['startTime2'])) {
			$map->put('inTime', array('lt',strtotime($_POST['startTime2'])+86400));
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
		$dao	=	new LoginDao();
		$result =	$dao->clear();
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

}//类定义结束
?>