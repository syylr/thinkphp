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
// | Author: yhustc                                                       |
// +----------------------------------------------------------------------+
// $Id: NodeAction.class.php 86 2007-04-01 12:56:20Z liu21st $
// Modify 2007-11-28 yhustc

/**
 +------------------------------------------------------------------------------
 * ThinkPHP 节点管理
 +------------------------------------------------------------------------------
 * @author liu21st <liu21st@gmail.com>
 * @version  $Id: NodeAction.class.php 86 2007-04-01 12:56:20Z liu21st $
 +------------------------------------------------------------------------------
 */

/**
 +------------------------------------------------------------------------------
 * 节点管理
 +------------------------------------------------------------------------------
 * @package   core
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Ver$
 +------------------------------------------------------------------------------
 */
class NodeAction extends PublicAction
{//类定义开始


    /**
     +----------------------------------------------------------
     * 列表过滤
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param object $map 条件Map
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws ThinkException
     +----------------------------------------------------------
     */
	function _filter(&$map) 
	{
		if(strtoupper(ACTION_NAME)=='INDEX') {
            if(!$map->containsKey('pid') ) {
            	$map->put('pid',0);
            }
            
            Session::set('currentNodeId',$map->get('pid'));
            //获取上级节点
            $dao  = D("Node");
            $vo = $dao->getById($map->get('pid'));
            if($vo) {
                $this->assign('level',$vo['level']+1);
            	$this->assign('nodeName',$vo['name']);
            }else {
            	$this->assign('level',1);
            }
		}
	}

    /**
     +----------------------------------------------------------
     * 表单提交预处理
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws ThinkException
     +----------------------------------------------------------
     */
	function _operation() 
	{
		$dao = D("Node");
        if(!empty($_POST['id'])) {
        	$result = $dao->find("name='".$_POST['name']."' and id !='".$_POST['id']."' and pid='".$_POST['pid']."'");
        }else {
        	$result = $dao->find("name='".$_POST['name']."' and pid='".$_POST['pid']."'");
        }
        if($result) {
        	$this->assign("error",'节点已经存在！');
            $this->forward();
        }		
	}

    /**
     +----------------------------------------------------------
     * 新增页面重载
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param object $map 条件Map
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws ThinkException
     +----------------------------------------------------------
     */
	function add() 
	{
		$dao = D("Node");
		if(Session::is_set('currentNodeId')) {
			$vo = $dao->getById(Session::get('currentNodeId'));
	        $this->assign('parentNode',$vo['name']);
			$this->assign('level',$vo['level']+1);
			$this->assign('pid',$vo['id']);
		}else{
			$this->assign('level',1);
		}
		$this->display();
	}

	// 删除节点
	function delete()
	{
		$dao=D("Node");
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

    // 节点访问权限
    function access() 
    {
        //读取系统权限组列表
        $groupDao    =   D("GroupDao");
        $list		=	$groupDao->findAll('','','id,name');
        $groupList	=	$list->getCol('id,name');

		$nodeDao    =   D("Node");
        $list   =  $nodeDao->findAll('pid='.Session::get('currentNodeId'),'','id,title');
        $nodeList = $list->getCol('id,title');
		$this->assign("nodeList",$nodeList);

        //获取当前节点信息
        $nodeId =  isset($_GET['id'])?$_GET['id']:'';
		$nodeGroupList = array();
		if(!empty($nodeId)) {
			$this->assign("selectNodeId",$nodeId);
			//获取当前节点的权限组列表
            $dao = D("Node");
            $list = $dao->getNodeGroupList($nodeId);
            $nodeGroupList = $list->getCol('id,id');
                
		}
		$this->assign('nodeGroupList',$nodeGroupList);
        $this->assign('groupList',$groupList);
        $this->display();    	
    }

    // 设置节点权限
    function setAccess() 
    {
        $id     = $_POST['nodeGroupId'];
		$nodeId	=	$_POST['nodeId'];
		$nodeDao    =   D("Node");
		$nodeDao->delNodeGroup($nodeId);
		$result = $nodeDao->setNodeGroups($nodeId,$id);
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
     * 默认排序操作
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws ThinkException
     +----------------------------------------------------------
     */
    function sort() 
    {
		$dao	= D("Node");
        if(!empty($_GET['pid'])) {
        	$pid  = $_GET['pid'];
        }else {
   	        $pid  = Session::get('currentNodeId');
        }
		$vo = $dao->getById($pid);
        if($vo) {
        	$level   =  $vo['level']+1;
        }else {
        	$level   =  1;
        }
        $this->assign('level',$level);
        $sortList   =   $dao->findAll('pid='.$pid.' and level='.$level,'','*','seqNo asc');
        $this->assign("sortList",$sortList);
        $this->display();
        return ;
    }

	function index() 
	{ 
		$Brand = D("Node"); 

		$field = '*'; // 如果是查询的视图,这里必须写清楚查那些列
		if(isset($_GET['pid']))
		{
			$where = 'pid='.$_GET['pid'];
			Session::set('currentNodeId',$_GET['pid']);
		}
		else
			$where = 'pid=0';
		//获取上级节点
		$vo = $Brand->getById($_GET['pid']);
		if($vo) {
			$this->assign('level',$vo['level']+1);
			$this->assign('nodeName',$vo['name']);
		}else {
			$this->assign('level',1);
		}
		$count= $Brand->count($where); 
 
		import("ORG.Util.Page"); 
		if(!empty($_REQUEST['listRows'])) { 
			$listRows = $_REQUEST['listRows']; 
			}else{ 
			$listRows=20; 
		} 
		$p= new Page($count,$listRows); 
 
		$list=$Brand->findAll($where,$field,'id desc',$p->firstRow.','.$p->listRows); 
		$page=$p->show();
		//dump($page);
		$this->assign('list',$list); 
		$this->assign('page',$page); 
		$this->display(); 
	}
}
?>