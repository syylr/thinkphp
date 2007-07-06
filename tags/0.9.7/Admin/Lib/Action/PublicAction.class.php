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
// $Id: PublicAction.class.php 78 2007-04-01 04:29:15Z liu21st $

import('@.Action.AdminAction');
/**
 +------------------------------------------------------------------------------
 * 公共模块
 +------------------------------------------------------------------------------
 * @package   core
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Ver$
 +------------------------------------------------------------------------------
 */
class PublicAction extends AdminAction
{//类定义开始

    /**
     +----------------------------------------------------------
     * 验证Vo对象数据有效性
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    function _validation() 
    {

        $validation = Validation::getInstance();
        //对所有需要验证的数据进行验证
        if(isset($_POST['name'])) {
            if(!$validation->check($_POST['name'],'require')) {
                $this->error    =   '用户名必填！';
				return false;
            }        
        }
        if(isset($_POST['oldpassword'])) {
            if(!$validation->check($_POST['oldpassword'],'require')) {
                $this->error    =   '旧密码有误';
				return false;
            }
        }
        if(isset($_POST['password'])) {
            if(!$validation->check($_POST['password'],'require')) {
                $this->error    =   '密码有误';
				return false;
            }
        }
        if(isset($_POST['repassword'])) {
            if(!$validation->check($_POST['repassword'],'require')) {
                $this->error    =   '请输入确认密码';
				return false;
            }
            if($_POST['repassword']!= $_POST['password']) {
                $this->error    =   '确认密码不正确';
				return false;
            }
        }
        if(isset($_POST['verify'])) {
            if(''==$_POST['verify'] ) {
                $this->error    =   '验证码必填！';
				return false;
            }
        }
        return true;
    }


    /**
     +----------------------------------------------------------
     * 登录操作
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function login() 
    {
        if(!Session::is_set(USER_AUTH_KEY)) {
            $this->display();
            return ;
        }else {
			redirect(__APP__);
        }
    }

	function index() 
	{
		//如果通过认证跳转到首页
		redirect(__APP__);
	}

    /**
     +----------------------------------------------------------
     * 用户登出
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function logout() 
    {
        if(Session::is_set(USER_AUTH_KEY)) {
            
			$loginId	=	Session::get('loginId');
            Session::clear();
			//保存登出记录
			$loginDao   =   D("Login");
			$map	=	new HashMap();
            $map->put('outTime',time());
			$map->put('id',$loginId);
            $loginDao->save($map);
            $this->assign("message",'登出成功！');
            $this->assign("jumpUrl",__URL__.'/login/');
        }else {
            $this->assign('error', '已经登出！');
        }
        $this->forward();
    }


    /**
     +----------------------------------------------------------
     * 用户登录检查
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function checkLogin() 
    {
        //生成认证条件
        $map   =   new HashMap();
        $map->put("name",$_POST['name']);
        $map->put("status",array('gt',0));
        $verifyCodeStr   = $_POST['verify'];
        $verifyCodeNum   = array_flip($_SESSION['verifyCode']);
        for($i=0; $i<strlen($_POST['verify']); $i++) {
        	$verify .=  $verifyCodeNum[$verifyCodeStr[$i]];
        }
        $authInfo = RBAC::authenticate($map);
        //使用用户名、密码和状态的方式进行认证
        if(false === $authInfo) {
            $this->error('用户名不存在或已禁用！');
        }else {
            if($authInfo->password != md5($_POST['password'])) {
            	$this->error('密码错误！');
            }
            if($authInfo->verify != $verify) {
            	$this->error('验证码错误！');
            }
            Session::set(USER_AUTH_KEY,$authInfo->id);
            Session::set('loginUserName',$authInfo->nickname);
            Session::set('lastLoginTime',$authInfo->lastLoginTime);
            if($authInfo->name=='admin') {
            	Session::setLocal('administrator',true);
            }
            //保存登录时间
            $dao    =   D("User");
            $map->clear();
            $map->put('id',$authInfo->id);
            $map->put('lastLoginTime',time());
            $dao->save($map);
			//保存登录日志
			$loginDao   =   D("Login");
            $map->clear();
            $map->put('userId',$authInfo->id);
            $map->put('inTime',time());
            $map->put('loginIp',$_SERVER["REMOTE_ADDR"]);
            $map->put('type',$authInfo->type);
            $loginId    =   $loginDao->add($map);
            Session::set('loginId',$loginId);
            RBAC::saveAccessList();
            $this->assign("message",'登录成功！');
            $this->assign("jumpUrl",__APP__);
        }

        $this->forward();

    }

    /**
     +----------------------------------------------------------
     * 修改用户信息
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
	function change() 
	{
        $dao = D("User");
		$this->_update($dao);
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
        $dao    =   D("User");
        $vo     =   $dao->find($map);
        if(!$vo) {
            $this->assign('error','旧密码不符或者用户名错误！');
        }else {
        	$map->put('password',$_POST['password']);
            $map->put('id',$vo['id']);
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

	function edit() 
	{
		if(Session::is_set(USER_AUTH_KEY)) {
            $dao = D("User");
            $vo  = $dao->getById(Session::get('userId'));
            $this->assign('vo',$vo);
            $this->display();
					
		}else {
			redirect(__APP__.'/Public/login');
		}
	}

}//类定义结束
?>