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
 * @version    $Id: NodeAction.class.php 73 2006-11-08 10:08:01Z fcs $
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
            if(!$map->containsKey('parentId')) {
            	$map->put('parentId',0);
            }
            Session::set('currentNodeId',$map->get('parentId'));
            //获取上级节点
            $dao  = new NodeDao();
            $vo = $dao->getById($map->get('parentId'));
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
	 /*
	function _operation() 
	{
		//记录上级经销商编号
		if(Session::is_set('providerId')) {
			$_POST['parentId'] = Session::get('providerId');
		}else {
			$_POST['parentId'] = 0;
		}
		parent::_operation();
	}*/

	function _before_insert() 
	{
        if($_POST['type']==1) {//公共类型节点
        	$_POST['parentId']	=	0;
        }else {
            //私有节点
        	$_POST['parentId']	=	Session::get('currentNodeId');
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
        $parentId  = Session::get('currentNodeId');
		$vo = $dao->getById(Session::get('currentNodeId'));
        if($vo) {
        	$level   =  $vo->level+1;
        }else {
        	$level   =  1;
        }
        $this->assign('level',$level);
        $sortList   =   $dao->findAll('parentId='.$parentId.' and level='.$level,'','*','seqNo asc');
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
        if($map->containsKey('parentId')) {
            $vo		=	$dao->find('id='.$map->get('parentId'));
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