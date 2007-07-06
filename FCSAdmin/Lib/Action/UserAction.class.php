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
 * @version    $Id: UserAction.class.php 73 2006-11-08 10:08:01Z fcs $
 +------------------------------------------------------------------------------
 */

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
        if(isset($_POST['name'])) {
            if(!$validation->check($_POST['name'],'username')) {
                $this->error    =   '用户名必须是3位以上字母！';
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
				}
        return true;
    }

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

    /**
     +----------------------------------------------------------
     * 触发器定义
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
	function _trigger($vo) 
	{
		if(ACTION_NAME=='insert') {
			//新增用户自动加入相应权限组
			import('@.Dao.GroupDao');
			$group = new GroupDao();
			import('@.Dao.UserDao');
			$dao = new UserDao();
			if(strtoupper(MODULE_NAME)=='USER') {
				//自动加入管理员权限组
				$group->setGroupUser(7,$vo->id);				
			}elseif(strtoupper(MODULE_NAME)=='AGENCY') {
				//自动加入代理商权限组
				$user = $dao->find('type=2 and childId='.$vo->id,'','id');
				$group->setGroupUser(2,$user->id);				
			}elseif(strtoupper(MODULE_NAME)=='DEALER') {
				$user = $dao->find('type=3 and childId='.$vo->id,'','id');
				if($vo->parentId) {
				//自动加入主持人经销商权限组
				$group->setGroupUser(4,$user->id);						
				}else {
				//自动加入一级经销商权限组
				$group->setGroupUser(3,$user->id);						
				}
			
			}elseif(strtoupper(MODULE_NAME)=='GIRL') {
				//自动加入主持人权限组
				$user = $dao->find('type=4 and childId='.$vo->id,'','id');
				$group->setGroupUser(5,$user->id);				
			}
		}

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
        $id        = $_GET[$dao->pk];
        $ids      = explode(',',$id);
        $userDao  =  new UserDao();
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

}//类定义结束
?>