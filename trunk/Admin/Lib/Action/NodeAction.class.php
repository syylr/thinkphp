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
 * CMS 节点管理
 +------------------------------------------------------------------------------
 * @author liu21st <liu21st@gmail.com>
 * @version  $Id$
 +------------------------------------------------------------------------------
 */
import('@.Action.AdminAction');
/**
 +------------------------------------------------------------------------------
 * 节点管理
 +------------------------------------------------------------------------------
 * @package   core
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Ver$
 +------------------------------------------------------------------------------
 */
class NodeAction extends AdminAction
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
     * @throws FcsException
     +----------------------------------------------------------
     */
	function _filter(&$map) 
	{
		if(ACTION_NAME=='index') {
            if(!$map->containsKey('pid') ) {
            	$map->put('pid',0);
            }
            Session::set('currentNodeId',$map->get('pid'));
            //获取上级节点
            $dao  = new NodeDao();
            $vo = $dao->getById($map->get('pid'));
            if($vo) {
                $this->assign('level',$vo->level+1);
            	$this->assign('nodeName',$vo->name);
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
     * @throws FcsException
     +----------------------------------------------------------
     */
	function _operation() 
	{
       	if(Session::is_set('currentNodeId')) {
       		$_POST['pid']	=	Session::get('currentNodeId');
       	}else {
       		$_POST['pid']	=	0;
       	}
		$dao = new NodeDao();
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
     * @throws FcsException
     +----------------------------------------------------------
     */
	function add() 
	{

		$dao	= new NodeDao();
		$vo = $dao->getById(Session::get('currentNodeId'));
        $this->assign('parentNode',$vo->name);
		$this->assign('level',$vo->level+1);

		parent::add();
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
     * @throws FcsException
     +----------------------------------------------------------
     */
    function sort() 
    {
		$dao	= new NodeDao();
        if(!empty($_GET['pid'])) {
        	$pid  = $_GET['pid'];
        }else {
   	        $pid  = Session::get('currentNodeId');
        }
		$vo = $dao->getById($pid);
        if($vo) {
        	$level   =  $vo->level+1;
        }else {
        	$level   =  1;
        }
        $this->assign('level',$level);
        $sortList   =   $dao->findAll('pid='.$pid.' and level='.$level,'','*','seqNo asc');
        $this->assign("sortList",$sortList);
        $this->display();
        return ;
    }

    /**
     +----------------------------------------------------------
     * 生成树型列表XML文件
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function tree() 
    {
        $dao	=	$this->getDaoClass();
        $map	=	$this->_search();
        $level	=	$dao->getMin('level',$map);
        $map->put('level',$level);
        $list	=	$dao->findall($map,'','*','seqNo');
        header("content-type:text/xml;charset=utf-8");
        $xml	=  '<?xml version="1.0" encoding="utf-8" ?>';
        if($map->containsKey('pid')) {
            $vo		=	$dao->find('id='.$map->get('pid'));
            $xml	.= '<tree caption="'.$vo->title.'" >';
        }else {
            $xml	.= '<tree caption="节点选择" >';
        }
        $xml	.=	$this->_toXmlTree($list,'title');
        $xml	.= '</tree>'; 
        exit($xml);
    }


}//类定义结束
?>