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
            if($_SESSION['verify'] != md5($_POST['verify'])) {
                $this->error    =   '验证码错误！';
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
            import('Admin.Dao.LoginDao');
			$loginDao   =   new LoginDao();
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
        //调用委托管理器进行身份认证
        import("FCS.RBAC.ProviderManager");
        $authProvider   =   ProviderManager::getInstance();
        //生成认证条件
        $map            =   new HashMap();
        if($_POST['name']=='admin') {
            $map->put("name",$_POST['name']);
        }else {
            $map->put("name",$_POST['loginAs'].$_POST['name']);
        }
        
        //检查密码加密方式
        $encoder = AUTH_PWD_ENCODER;
        if(!empty($encoder) && function_exists($encoder)) {
            $map->put("password",$encoder($_POST['password']));
        }
        else {
        	$map->put("password",$_POST['password']);
        }
        $map->put("status",1);

        //使用用户名、密码和状态的方式进行认证
        if(!$authProvider->authenticate($map)) {
            $this->assign('error','登录失败，请检查用户名和密码是否有误！');
        }else {
            $authInfo   =   $authProvider->data;
            Session::set(USER_AUTH_KEY,$authInfo->id);
			$_SESSION[USER_AUTH_KEY]	=	$authInfo->id;
            Session::set('loginUserName',$authInfo->nickname);
            Session::set('lastLoginTime',$authInfo->lastLoginTime);
            //保存登录时间
            import('Admin.Dao.UserDao');
            $dao    =   new UserDao();
            $map->clear();
            $map->put('id',$authInfo->id);
            $map->put('lastLoginTime',time());
            $dao->save($map);
            Session::set('userId',$authInfo->id);

			//保存登录日志
            import('Admin.Dao.LoginDao');
			$loginDao   =   new LoginDao();
            $map->clear();
            $map->put('userId',$authInfo->id);
            $map->put('inTime',time());
            $map->put('loginIp',$_SERVER["REMOTE_ADDR"]);
            $map->put('type',$authInfo->type);
            $loginId    =   $loginDao->add($map);
            Session::set('loginId',$loginId);
            //如果使用普通权限模式，保存当前用户的访问权限列表
            if(USER_AUTH_TYPE!=2) {
                //获取权限访问列表
                import("FCS.RBAC.AccessDecisionManager");
                $accessManager = new AccessDecisionManager();
                //获取模块访问列表
                $accessModuleList = $accessManager->getModuleAccessList($authInfo->id);
                Session::set('_moduleList',$accessModuleList);
                //获取操作访问列表
                $accessActionList = $accessManager->getActionAccessList($authInfo->id);
                Session::set('_actionList',$accessActionList);
            }
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
		import('Admin.Dao.UserDao');
		$dao = new UserDao();
		$this->_update($dao);
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
	function edit() 
	{
		if(Session::is_set(USER_AUTH_KEY)) {
		if(Session::is_set('userId')) {
			import('Admin.Dao.UserDao');
			$dao = new UserDao();
			$vo  = $dao->getById(Session::get('userId'));
		}
		
		$this->assign('vo',$vo);
		$this->display();
					
		}else {
			$this->login();
		}
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
}//类定义结束
?>