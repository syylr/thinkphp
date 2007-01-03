<?php 
// +----------------------------------------------------------------------+
// | ThinkCMS                                                             |
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
 * CMS 会员管理
 +------------------------------------------------------------------------------
 * @author liu21st <liu21st@gmail.com>
 * @version  $Id$
 +------------------------------------------------------------------------------
 */
 import('@.Action.AdminAction');
class UserAction extends AdminAction 
{
/**
     +----------------------------------------------------------
     * 处理表单提交数据
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    function _operation() 
    {
        //对表单提交的密码进行加密
        if(isset($_POST['password'])) {
            $encoder = AUTH_PWD_ENCODER;
            if(!empty($encoder) && function_exists($encoder)) {
                $_POST['password']      =   $encoder($_POST['password']);
            }            	
        }
        return ;
    }

        /**
     +----------------------------------------------------------
     * 编辑用户资料
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
	function profile() 
	{
            $dao = D("UserDao");
            $vo  = $dao->getById(Session::get(USER_AUTH_KEY));

            $this->assign('vo',$vo);
            $this->display();
	}

    /**
     +----------------------------------------------------------
     * 修改密码
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function password() 
    {
        if(Session::is_set(USER_AUTH_KEY)) {
            $this->assign("login",true);
        }
    	$this->display();
        return ;
    }

    /**
     +----------------------------------------------------------
     * 更换密码
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function changePwd() 
    {
        //对表单提交处理进行处理或者增加非表单数据
        $encoder = AUTH_PWD_ENCODER;
        $map    =   new HashMap();
        if(!empty($encoder) && function_exists($encoder)) {
            $_POST['password']      =   $encoder($_POST['password']);
            $_POST['oldpassword']      =   $encoder($_POST['oldpassword']);
        }
        $map->put('password',$_POST['oldpassword']);
        if(isset($_POST['name'])) {
            $map->put('name',$_POST['name']);
        }elseif(Session::is_set(USER_AUTH_KEY)) {
            $map->put('id',Session::get(USER_AUTH_KEY));
        }else {
        	
        }
        //检查用户
        $dao    =   new UserDao();
        $vo     =   $dao->find($map);
        if($vo->isEmpty()) {
            $this->assign('error','旧密码不符或者用户名错误！');
        }else {
        	$map->put('password',$_POST['password']);
            $map->put('id',$vo->id);
            $user = new UserVo($map);
            $result = $dao->save($user);
            if($result) {
                 $this->assign("message",'密码修改成功');
                 $this->assign("jumpUrl",__APP__);
            }else {
            $this->assign('error','密码修改失败！');            	
            }
        }
        $this->forward();
    }

    //重置主持人密码
    function resetPwd() 
    {
    	$userId  =  $_POST['id'];
        $password = $_POST['password'];
        if(''== trim($password)) {
        	$this->error('密码不能为空！');
        }
        $dao = D('UserDao');
        $user = new UserVo();
        $user->password   = md5($password);

        $result  =  $dao->save($user,'','id="'.$userId.'"');
        if($result) {
            $this->success("密码修改为$password");
        }else {
        	$this->error('重置密码失败！');
        }
    }
}//end class
?>