<?php 
// +----------------------------------------------------------------------
// | ThinkPHP                                                             
// +----------------------------------------------------------------------
// | Copyright (c) 2008 http://thinkphp.cn All rights reserved.      
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>                                  
// +----------------------------------------------------------------------
// $Id$
// Modify 2007-11-28 yhustc

/**
 +------------------------------------------------------------------------------
 * ThinkPHP权限管理
 +------------------------------------------------------------------------------
 * @author liu21st <liu21st@gmail.com>
 * @version  $Id: GroupAction.class.php 91 2007-04-04 10:45:32Z liu21st $
 +------------------------------------------------------------------------------
 */

class GroupAction extends PublicAction
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
		import("ORG.Text.Validation");
        //对所有需要验证的数据进行验证
        if(isset($_POST['groupId'])) {
            if(!Validation::check($_POST['groupId'],'require')) {
                $this->error    =   '没有选择组名';
				return false;
            }        
        }
        if(isset($_POST['password'])) {
            if(!Validation::check($_POST['password'],'require')) {
                $this->error    =   '密码有误';
				return false;
            }
        }
        if(isset($_POST['repassword'])) {
            if(!Validation::check($_POST['repassword'],'require')) {
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

	// 删除用户组
	function delete()
	{
		$dao=D("Group");
		$pkey=$_GET['id'];
		$akey=split(",",$pkey);
		if (count($akey)<=0){
			$this->error("出错!请选择删除的条目");
		}
		foreach($akey as $key){
			$dao->delete("id=$key");
		}
		$this->assign('jumpUrl',__URL__);
		$this->success("删除成功!");
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
     * @throws ThinkException
     +----------------------------------------------------------
     */
    function setApp() 
    {
        $id     = $_POST['groupAppId'];
		$groupId	=	$_POST['groupId'];
		$groupDao    =   D("Group");
		$groupDao->delGroupApp($groupId);
		$result = $groupDao->setGroupApps($groupId,$id);

		if($result===false) {
			$this->error('项目授权失败！');
		}else {
			$this->success('项目授权成功！');
		}
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
     * @throws ThinkException
     +----------------------------------------------------------
     */
    function app() 
    {
        //读取系统的项目列表
        $dao    =  D("Node");
        $list         =  $dao->findAll('level=1','id,title');
        $appList   =  $dao->getCols($list,'id,title');

        //读取系统组列表
		$groupDao   =  D("Group");
        $list       =  $groupDao->findAll('','id,name');
        $groupList  =  $groupDao->getCols($list,'id,name');
		$this->assign("groupList",$groupList);

        //获取当前用户组项目权限信息
        $groupId =  isset($_GET['groupId'])?$_GET['groupId']:'';
		$groupAppList = array();
		if(!empty($groupId)) {
			$this->assign("selectGroupId",$groupId);
			//获取当前组的操作权限列表
            $selectAppList = $groupDao->getGroupAppList($groupId);
            $groupAppList = $groupDao->getCols($selectAppList,'id,id');
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
     * @throws ThinkException
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
			$this->error('模块授权失败！');
		}else {
			$this->success('模块授权成功！');
		}
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
     * @throws ThinkException
     +----------------------------------------------------------
     */
    function module() 
    {
        $groupId =  $_GET['groupId'];
        $appId  = $_GET['appId'];

		$groupDao   =  D("Group");
        //读取系统组列表
        $list       =  $groupDao->findAll('','id,name');
        $groupList  =  $groupDao->getCols($list,'id,name');
		$this->assign("groupList",$groupList);

        if(!empty($groupId)) {
			$this->assign("selectGroupId",$groupId);
            //读取系统组的授权项目列表
            $list       =  $groupDao->getGroupAppList($groupId);
            $appList  =  $groupDao->getCols($list,'id,title');
            $this->assign("appList",$appList);
        }

        $dao    =  D("Node");
        if(!empty($appId)) {
            $this->assign("selectAppId",$appId);
        	//读取当前项目的模块列表
            $list         =  $dao->findAll('level=2 and pid='.$appId,'id,title');
            $moduleList   =  $dao->getCols($list,'id,title');
        }

        //获取当前项目的授权模块信息
		$groupModuleList = array();
		if(!empty($groupId) && !empty($appId)) {
            $selectModuleList = $groupDao->getGroupModuleList($groupId,$appId);
            $groupModuleList = $groupDao->getCols($selectModuleList,'id,id');
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
     * @throws ThinkException
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
			$this->error('操作授权失败！');
		}else {
			$this->success('操作授权成功！');
		}
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
     * @throws ThinkException
     +----------------------------------------------------------
     */
    function action() 
    {
        $groupId =  $_GET['groupId'];
        $appId  = $_GET['appId'];
        $moduleId  = $_GET['moduleId'];

		$groupDao   =  D("Group");
        //读取系统组列表
        $list       =  $groupDao->findAll('','id,name');
        $groupList  =  $groupDao->getCols($list,'id,name');
		$this->assign("groupList",$groupList);

        if(!empty($groupId)) {
			$this->assign("selectGroupId",$groupId);
            //读取系统组的授权项目列表
            $list       =  $groupDao->getGroupAppList($groupId);
            $appList  =  $groupDao->getCols($list,'id,title');
            $this->assign("appList",$appList);
        }
        if(!empty($appId)) {
            $this->assign("selectAppId",$appId);
        	//读取当前项目的授权模块列表
            $list         =  $groupDao->getGroupModuleList($groupId,$appId);
            $moduleList   =  $groupDao->getCols($list,'id,title');
            $this->assign("moduleList",$moduleList);
        }
        $dao    =  D("Node");

        if(!empty($moduleId)) {
            $this->assign("selectModuleId",$moduleId);
        	//读取当前项目的操作列表
            $list         =  $dao->findAll('level=3 and pid='.$moduleId,'id,title');
            $actionList   = $dao->getCols($list,'id,title');
        }


        //获取当前用户组操作权限信息
		$groupActionList = array();
		if(!empty($groupId) && !empty($moduleId)) {
			//获取当前组的操作权限列表
            $selectActionList = $groupDao->getGroupActionList($groupId,$moduleId);
            $groupActionList = $groupDao->getCols($selectActionList,'id,id');
		}

		$this->assign('groupActionList',$groupActionList);
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
     * @throws ThinkException
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
			$this->error('授权失败！');
		}else {
			$this->success('授权成功！');
		}
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
     * @throws ThinkException
     +----------------------------------------------------------
     */
    function user() 
    {
        //读取系统的用户列表
		//以下三句请根据实际情况进行修改
        $userDao    =   D("User");
        $list		=	$userDao->findAll('','id,account');
        $userList	=	$userDao->getCols($list,'id,account');

		$groupDao    =   D("Group");
        $list   =  $groupDao->findAll('','id,name');
        $groupList = $groupDao->getCols($list,'id,name');
		$this->assign("groupList",$groupList);

        //获取当前用户组信息
        $groupId =  isset($_GET['id'])?$_GET['id']:'';
		$groupUserList = array();
		if(!empty($groupId)) {
			$this->assign("selectGroupId",$groupId);
			//获取当前组的用户列表
            $list = $groupDao->getGroupUserList($groupId);
            $groupUserList = $groupDao->getCols($list,'id,id');
                
		}
        //$userList = array_diff_key($userList,$groupUserList);
		$this->assign('groupUserList',$groupUserList);
        $this->assign('userList',$userList);
        $this->display();

        return;
    }


	function userList() 
	{
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
		$dao	=	D("Group");
		$group	=	$dao->getById($groupId);
		$dao	=	D("GroupUser");
		$rs		=	$dao->db->query("select a.* from ".C("DB_PREFIX")."_user as a ,".C("DB_PREFIX")."_groupUser as b where b.userId=a.id and b.groupId=".$groupId." order by ".$order." ".$sort,false);
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

	function index() 
	{ 
		$Brand = D("Group"); 
		$count= $Brand->count(); 
 
		import("ORG.Util.Page"); 
		if(!empty($_REQUEST['listRows'])) { 
			$listRows = $_REQUEST['listRows']; 
			}else{ 
			$listRows=20; 
		} 
		$p= new Page($count,$listRows); 
 
		$field = '*'; // 如果是查询的视图,这里必须写清楚查那些列 
		$list=$Brand->findAll('',$field,'id desc',$p->firstRow.','.$p->listRows); 
		$page=$p->show(); 
		$this->assign('list',$list); 
		$this->assign('page',$page); 
		$this->display(); 
	}
}//类定义结束
?>