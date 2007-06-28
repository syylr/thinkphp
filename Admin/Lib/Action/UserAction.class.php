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
// $Id: UserAction.class.php 78 2007-04-01 04:29:15Z liu21st $

import('@.Action.AdminAction');
/**
 +------------------------------------------------------------------------------
 * 系统用户管理
 +------------------------------------------------------------------------------
 * @package   core
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Ver$
 +------------------------------------------------------------------------------
 */
class UserAction extends AdminAction
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
		if( in_array(ACTION_NAME,array('insert','update'))) {

        $validation = Validation::getInstance();

        //对所有需要验证的数据进行验证
        if(isset($_POST['password']) ) {
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
        /*
        if(isset($_POST['verify'])) {
            if($_SESSION['verify'] != md5($_POST['verify'])) {
                $this->error    =   '验证码错误！';
				return false;
            }
        }*/
				}
        return true;
    }


    function checkName($return=true) 
    {
        import("ORG.Text.Validation");
        $validation = Validation::getInstance();
        if(!$validation->check($_POST['name'],'/^[a-z]\w{5,}$/i')) {
            $this->error( '用户名必须是以字母打头，且6位以上！');
        } 
		$dao = D("UserDao");
        // 检测用户名是否冲突
        $name  =  $_REQUEST['name'];
        $result  =  $dao->find("name='$name'");
        if($result) {
        	$this->error('该用户名已经存在！');
        }else {
            if($return) {
            	$this->success('该用户名可以使用！');
            }
        }    	
    }

    /**
     +----------------------------------------------------------
     * 表单过滤
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
	function _filter(&$map) 
	{
		//生成用户类型下拉列表
		import('@.Dao.UserTypeDao');
		$dao = new UserTypeDao();
		$userType = $dao->findall();
		$this->assign('userType',$userType);

	}


    function _before_add() 
    {
    	import('@.Dao.UserTypeDao');
        $dao = new UserTypeDao();
        $list  = $dao->findAll();
        $this->assign('UserType',$list);
    }

    function _before_edit() 
    {
    	$this->_before_add();
    }
    /**
     +----------------------------------------------------------
     * 删除相关类型用户
     * 并且删除该用户
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function _delete(&$dao,$type) 
    {
    	 //删除指定记录
        $id        = $_REQUEST[$dao->pk];
        $ids      = explode(',',$id);
        $userDao  =  D("UserDao");
        if(count($ids)==1) {
            $dao->startTrans();
            $result  =  $dao->deleteById($id);
            if($result) {
                $result  =  $userDao->delete("type=".$type." and childId=".$id);
            }
            if($result) {
                $dao->commit();
                $this->assign("message",'删除成功！');
                $this->assign("jumpUrl",$this->getReturnUrl());
            }else {
                $dao->rollback();
                $this->assign("error",'删除失败！');
            }                    	
        }else {
            foreach($ids as $key=>$id) {
                $dao->startTrans();
                $result  =  $dao->deleteById($id);
                if($result) {
                    $result  =  $userDao->delete("type=".$type." and childId=".$id);
                }
                if($result) {
                    $dao->commit();
                }else {
                    $dao->rollback();
                    $error   = 1;
                }            
                
            }  
            if(empty($error)) {
                $this->assign("message",'删除成功！');
                $this->assign("jumpUrl",$this->getReturnUrl());            	
            }else {
            	$this->assign("error",'有部分数据删除失败！');
            }
        }

        $this->forward();
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
		if(Session::is_set(C('USER_AUTH_KEY'))) {
            if(Session::is_set('userId')) {
                import('@.Dao.UserDao');
                $dao = new UserDao();
                $vo  = $dao->getById(Session::get('userId'));
            }
            
            $this->assign('vo',$vo);
            $this->display();
					
		}else {
			redirect(__APP__.'/Public/login');
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
        if(Session::is_set(C('USER_AUTH_KEY'))) {
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
        $encoder = C('AUTH_PWD_ENCODER');
        $map    =   new HashMap();
        if(!empty($encoder) && function_exists($encoder)) {
            $_POST['password']      =   $encoder($_POST['password']);
            $_POST['oldpassword']      =   $encoder($_POST['oldpassword']);
        }
        $map->put('password',$_POST['oldpassword']);
        if(isset($_POST['name'])) {
            $map->put('name',$_POST['name']);
        }elseif(Session::is_set(C('USER_AUTH_KEY'))) {
            $map->put('id',Session::get(C('USER_AUTH_KEY')));
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

    //重置密码
    function resetPwd() 
    {
    	$id  =  $_POST['id'];
        $password = $_POST['password'];
        if(''== trim($password)) {
        	$this->error('密码不能为空！');
        }
        $dao = D('UserDao');
        $user = new UserVo();
        $user->password   = md5($password);
        $user->id  = $id;
        $result  =  $dao->save($user);
        if($result) {
            $this->assign('message',"密码修改为$password");
        }else {
        	$this->error('重置密码失败！');
        }
        $this->forward();
    }

}//类定义结束
?>