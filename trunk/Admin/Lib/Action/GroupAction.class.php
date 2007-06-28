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
// $Id: GroupAction.class.php 91 2007-04-04 10:45:32Z liu21st $

/**
 +------------------------------------------------------------------------------
 * ThinkPHP权限管理
 +------------------------------------------------------------------------------
 * @author liu21st <liu21st@gmail.com>
 * @version  $Id: GroupAction.class.php 91 2007-04-04 10:45:32Z liu21st $
 +------------------------------------------------------------------------------
 */

import('@.Action.AdminAction');

class GroupAction extends AdminAction
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
        if(isset($_POST['groupId'])) {
            if(!$validation->check($_POST['groupId'],'require')) {
                $this->error    =   '没有选择组名';
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
     * 增加组操作权限
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function setApp() 
    {
        $id     = $_POST['groupAppId'];
		$groupId	=	$_POST['groupId'];
		$groupDao    =   new GroupDao();
		$groupDao->delGroupApp($groupId);
		$result = $groupDao->setGroupApps($groupId,$id);

		if($result===false) {
			$this->assign('error','项目授权失败！');
		}else {
			$this->assign("message",'项目授权成功！');
		}

		$this->forward();
    }


    /**
     +----------------------------------------------------------
     * 组操作权限列表
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function app() 
    {
        //读取系统的项目列表
        $dao    =  D("Node");
        $list         =  $dao->findAll('level=1','','id,title');
        $appList   =  $list->getCol('id,title');

        //读取系统组列表
		$groupDao   =  new GroupDao();
        $list       =  $groupDao->findAll('','','id,name');
        $groupList  =  $list->getCol('id,name');
		$this->assign("groupList",$groupList);

        //获取当前用户组项目权限信息
        $groupId =  isset($_GET['groupId'])?$_GET['groupId']:'';
		$groupAppList = array();
		if(!empty($groupId)) {
			$this->assign("selectGroupId",$groupId);
			//获取当前组的操作权限列表
            $selectAppList = $groupDao->getGroupAppList($groupId);
            $groupAppList = $selectAppList->getCol('id,id');
		}
		
		$this->assign('groupAppList',$groupAppList);
		//$appList = array_diff_key($appList,$groupAppList);
        $this->assign('appList',$appList);

        $this->display();

        return;
    }

     /**
     +----------------------------------------------------------
     * 增加组操作权限
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function setModule() 
    {
        $id     = $_POST['groupModuleId'];
		$groupId	=	$_POST['groupId'];
        $appId	=	$_POST['appId'];
		$groupDao    =   D("Group");
		$groupDao->delGroupModule($groupId,$appId);
		$result = $groupDao->setGroupModules($groupId,$id);

		if($result===false) {
			$this->assign('error','模块授权失败！');
		}else {
			$this->assign("message",'模块授权成功！');
		}

		$this->forward();
    }


    /**
     +----------------------------------------------------------
     * 组操作权限列表
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function module() 
    {
        $groupId =  $_GET['groupId'];
        $appId  = $_GET['appId'];

		$groupDao   =  D("Group");
        //读取系统组列表
        $list       =  $groupDao->findAll('','','id,name');
        $groupList  =  $list->getCol('id,name');
		$this->assign("groupList",$groupList);

        if(!empty($groupId)) {
			$this->assign("selectGroupId",$groupId);
            //读取系统组的授权项目列表
            $list       =  $groupDao->getGroupAppList($groupId);
            $appList  =  $list->getCol('id,title');
            $this->assign("appList",$appList);
        }

        $dao    =  D("Node");
        if(!empty($appId)) {
            $this->assign("selectAppId",$appId);
        	//读取当前项目的模块列表
            $list         =  $dao->findAll('level=2 and pid='.$appId,'','id,title');
            $moduleList   =  $list->getCol('id,title');
        }

        //获取当前项目的授权模块信息
		$groupModuleList = array();
		if(!empty($groupId) && !empty($appId)) {
            $selectModuleList = $groupDao->getGroupModuleList($groupId,$appId);
            $groupModuleList = $selectModuleList->getCol('id,id');
		}
		
		$this->assign('groupModuleList',$groupModuleList);
		//$moduleList = array_diff_key($moduleList,$groupModuleList);
        $this->assign('moduleList',$moduleList);

        $this->display();

        return;
    }

     /**
     +----------------------------------------------------------
     * 增加组操作权限
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function setAction() 
    {
        $id     = $_POST['groupActionId'];
		$groupId	=	$_POST['groupId'];
        $moduleId	=	$_POST['moduleId'];
		$groupDao    =   D("Group");
		$groupDao->delGroupAction($groupId,$moduleId);
		$result = $groupDao->setGroupActions($groupId,$id);

		if($result===false) {
			$this->assign('error','操作授权失败！');
		}else {
			$this->assign("message",'操作授权成功！');
		}

		$this->forward();
    }


    /**
     +----------------------------------------------------------
     * 组操作权限列表
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function action() 
    {
        $groupId =  $_GET['groupId'];
        $appId  = $_GET['appId'];
        $moduleId  = $_GET['moduleId'];

		$groupDao   =  D("Group");
        //读取系统组列表
        $list       =  $groupDao->findAll('','','id,name');
        $groupList  =  $list->getCol('id,name');
		$this->assign("groupList",$groupList);

        if(!empty($groupId)) {
			$this->assign("selectGroupId",$groupId);
            //读取系统组的授权项目列表
            $list       =  $groupDao->getGroupAppList($groupId);
            $appList  =  $list->getCol('id,title');
            $this->assign("appList",$appList);
        }
        if(!empty($appId)) {
            $this->assign("selectAppId",$appId);
        	//读取当前项目的授权模块列表
            $list         =  $groupDao->getGroupModuleList($groupId,$appId);
            $moduleList   =  $list->getCol('id,title');
            $this->assign("moduleList",$moduleList);
        }
        $dao    =  D("Node");

        if(!empty($moduleId)) {
            $this->assign("selectModuleId",$moduleId);
        	//读取当前项目的操作列表
            $list         =  $dao->findAll('level=3 and pid='.$moduleId,'','id,title');
            $actionList   = $list->getCol('id,title');
        }


        //获取当前用户组操作权限信息
		$groupActionList = array();
		if(!empty($groupId) && !empty($moduleId)) {
			//获取当前组的操作权限列表
            $selectActionList = $groupDao->getGroupActionList($groupId,$moduleId);
            $groupActionList = $selectActionList->getCol('id,id');
		}
		
		$this->assign('groupActionList',$groupActionList);
		//$actionList = array_diff_key($actionList,$groupActionList);
        $this->assign('actionList',$actionList);

        $this->display();

        return;
    }

    /**
     +----------------------------------------------------------
     * 增加组操作权限
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function setUser() 
    {
        $id     = $_POST['groupUserId'];
		$groupId	=	$_POST['groupId'];
		$groupDao    =   D("Group");
		$groupDao->delGroupUser($groupId);
		$result = $groupDao->setGroupUsers($groupId,$id);
		if($result===false) {
			$this->assign('error','授权失败！');
		}else {
			$this->assign("message",'授权成功！');
			$this->assign("jumpUrl",$_SERVER["HTTP_REFERER"]);
		}

		$this->forward();
    }

    /**
     +----------------------------------------------------------
     * 组操作权限列表
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function user() 
    {
        //读取系统的用户列表
        $userDao    =   D("User");
        $list		=	$userDao->findAll('','','id,nickname,name');
        $userList	=	$list->getCol('id,name,nickname',' ');

		$groupDao    =   D("Group");
        $list   =  $groupDao->findAll('','','id,name');
        $groupList = $list->getCol('id,name');
		$this->assign("groupList",$groupList);

        //获取当前用户组信息
        $groupId =  isset($_GET['id'])?$_GET['id']:'';
		$groupUserList = array();
		if(!empty($groupId)) {
			$this->assign("selectGroupId",$groupId);
			//获取当前组的用户列表
            $list = $groupDao->getGroupUserList($groupId);
            $groupUserList = $list->getCol('id,id');
                
		}
        //$userList = array_diff_key($userList,$groupUserList);
		$this->assign('groupUserList',$groupUserList);
        $this->assign('userList',$userList);
        $this->display();

        return;
    }


	function userList() 
	{
		import('@.Dao.GroupUserDao');
		        //排序字段 默认为主键名
        if(isset($_REQUEST['order'])) {
            $order = $_REQUEST['order'];
        }else {
            $order = $dao->pk;
        }
        //排序方式默认按照倒序排列
        //接受 sost参数 0 表示倒序 非0都 表示正序
        if(isset($_REQUEST['sort'])) {
            $sort = $_REQUEST['sort']?'asc':'desc';
        }else {
            $sort = 'desc';
        }
        //列表排序显示
        $sortImg    = $sort ;                                   //排序图标
        $sortAlt    = $sort == 'desc'?'升序排列':'倒序排列';    //排序提示
        $sort       = $sort == 'desc'? 1:0;                     //排序方式

		$groupId	=	$_REQUEST['id'];
		$dao	=	new GroupDao();
		$group	=	$dao->getById($groupId);
		$dao	=	new GroupUserDao();
		$rs		=	$dao->db->query("select a.* from ".DB_PREFIX."_user as a ,".DB_PREFIX."_groupUser as b where b.userId=a.id and b.groupId=".$groupId." order by ".$order." ".$sort);
		$voList	=	$dao->rsToVoList($rs,'UserVo');
		$p          = new Page($voList->size());
		$this->assign('group',$group);
        $this->assign('list',$voList);
        $this->assign('sort',$sort);
        $this->assign('order',$order);
        $this->assign('sortImg',$sortImg);
        $this->assign('sortType',$sortAlt);
        $this->assign("page",$page);
        $this->display();
		return ;
	}

}//类定义结束
?>