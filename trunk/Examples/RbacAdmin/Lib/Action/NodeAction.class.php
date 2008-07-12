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

	public function add()
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
	public function delete()
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
    public function access()
    {
        //读取系统权限组列表
        $groupDao    =   D("Group");
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
    public function setAccess()
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

	// 节点排序
    public function sort()
    {
		$Node	= D("Node");
        if(!empty($_GET['pid'])) {
        	$pid  = $_GET['pid'];
        }else {
   	        $pid  = Session::get('currentNodeId');
        }
		$vo = $Node->getById($pid);
        if($vo) {
        	$level   =  $vo['level']+1;
        }else {
        	$level   =  1;
        }
        $this->assign('level',$level);
        $sortList   =   $Node->where('pid='.$pid.' and level='.$level)->order('seqNo asc')->findAll();
        $this->assign("sortList",$sortList);
        $this->display();
        return ;
    }

    function saveSort()
    {
        $seqNoList  =   $_POST['seqNoList'];
        if(!empty($seqNoList)) {
            //更新数据对象
            $Node    = D('Node');
            $col    =   explode(',',$seqNoList);
            //启动事务
            $Node->startTrans();
            foreach($col as $val) {
                $val    =   explode(':',$val);
                $Node->id	=	$val[0];
                $Node->seqNo	=	$val[1];
                $result =   $Node->save();
                if(!$result) {
                    break;
                }
            }
            //提交事务
            $Node->commit();
            if($result) {
                //采用普通方式跳转刷新页面
                $this->success('更新成功');
            }else {
                $this->error($Node->getError());
            }
        }
    }

	public function index()
	{
		$Node = D("Node");

		$field = '*'; // 如果是查询的视图,这里必须写清楚查那些列
		if(isset($_GET['pid']))
		{
			$where = 'pid='.$_GET['pid'];
			Session::set('currentNodeId',$_GET['pid']);
		}
		else
			$where = 'pid=0';
		//获取上级节点
		$vo = $Node->getById($_GET['pid']);
		if($vo) {
			$this->assign('level',$vo['level']+1);
			$this->assign('nodeName',$vo['name']);
		}else {
			$this->assign('level',1);
		}
		$count= $Node->count($where);

		import("ORG.Util.Page");
		if(!empty($_REQUEST['listRows'])) {
			$listRows = $_REQUEST['listRows'];
			}else{
			$listRows=20;
		}
		$p= new Page($count,$listRows);
 		$list=$Node->findAll($where,$field,'id desc',$p->firstRow.','.$p->listRows);
		$page=$p->show();
		$this->assign('list',$list);
		$this->assign('page',$page);
		$this->display();
	}
}
?>