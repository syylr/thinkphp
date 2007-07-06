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
 * @copyright  Copyright (c) 2005-2006 liu21st.com.  All rights reserved. 
 * @author     liu21st <liu21st@gmail.com>
 * @version    $Id$
 +------------------------------------------------------------------------------
 */

// 导入引用类库

/**
 +------------------------------------------------------------------------------
 * Action控制器基础类
 +------------------------------------------------------------------------------
 * @package  core
 * @author   liu21st <liu21st@gmail.com>
 * @version  0.8.0
 +------------------------------------------------------------------------------
 */
class AdminAction extends Action
{//类定义开始

    /**
     +----------------------------------------------------------
     * 控制器初始化操作
     *
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function _initialize() 
    {
        if(Session::is_set(USER_AUTH_KEY)) {
            $this->assign("login",true);
        }
        //显示登录用户名称
        $this->assign('loginUserName',Session::get('loginUserName'));
        parent::_initialize();
    }

    /**
     +----------------------------------------------------------
     * 默认操作定义
     * 
     +----------------------------------------------------------
     */


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
        $dao        =   $this->getDaoClass();
        $sortList   =   $dao->findAll('','','*','seqNo asc');
        $this->assign("sortList",$sortList);
        $this->display();
        return ;
    }


    /**
     +----------------------------------------------------------
     * 默认排序保存操作
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function saveSort() 
    {
        $seqNoList  =   $_POST['seqNoList'];
        if(!empty($seqNoList)) {
            //更新数据对象
            $dao    = $this->getDaoClass();
            $map    =   new HashMap();
            $col    =   explode(',',$seqNoList);
            //启动事务
            $dao->startTrans();
            foreach($col as $val) {
                $val    =   explode(':',$val);
                $map->put('id',$val[0]);
                $map->put('seqNo',$val[1]);
                $result =   $dao->save($map);
                if(!$result) {
                    break;
                }
            }
            //提交事务
            $dao->commit();
            if($result) {
                //采用普通方式跳转刷新页面
                $this->assign("message",'更新成功');
                $this->assign("jumpUrl",$this->getReturnUrl());
            }else {
                $this->error = $dao->error;
            }
            //页面跳转
            $this->forward();        	
        }

    }


    /**
     +----------------------------------------------------------
     * 默认导出操作 导出列表全部数据 到csv格式
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function output()
    {
        //取得数据列表，并转换为字串
        $dao        = $this->getDaoClass();
        $voList     = $dao->findAll();
        $content    = $voList->toString();
		if(!empty($content)) {
        import("FCS.Util.Http");
        //转换为gb2312编码
        Http::download('',time().'.csv',auto_charset($content,OUTPUT_CHARSET,'gb2312'));			
		}else {
			$this->assign('error','目前没有任何数据!');
			$this->forward();
		}


    }


    /**
     +----------------------------------------------------------
     * 默认禁用操作
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function forbid()
    {
        //禁用指定记录
        $dao        = $this->getDaoClass();
        $id         = $_GET[$dao->pk];
        $condition = $dao->pk.' in('.$id.')'; 
        if($dao->forbid($condition)){
            $this->assign("message",'状态禁用成功！');
            $this->assign("jumpUrl",$this->getReturnUrl());
        }else {
            $this->assign('msgType','error');
        	$this->assign("message",'状态禁用失败！');
        }
        $this->forward();
    }

    /**
     +----------------------------------------------------------
     * 默认恢复操作
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function resume()
    {
        //恢复指定记录
        $dao        = $this->getDaoClass();
        $id         = $_GET[$dao->pk];
        $condition = $dao->pk.' in('.$id.')'; 
        if($dao->resume($condition)){
            $this->assign("message",'状态恢复成功！');
            $this->assign("jumpUrl",$this->getReturnUrl());
        }else {
            $this->assign('msgType','error');
        	$this->assign("message",'状态恢复失败！');
        }
        $this->forward();
    }


    /**
     +----------------------------------------------------------
     * 析构函数 在应用程序类结束的时候进行日志记录，提高效率
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     */
    function __destruct()
    {
		
        if(MODULE_NAME!='log' && Session::is_set(USER_AUTH_KEY)) {
            import('Admin.Dao.LogDao');
            $dao    =   new LogDao();
            $map    =   new HashMap();
            $map->put('module',MODULE_NAME);
            $map->put('action',ACTION_NAME);
            $map->put('time',time());
            $map->put('userId',Session::get(USER_AUTH_KEY));
            $map->put('url',$_SERVER["PHP_SELF"]);
            $dao->add($map);
        }
    }


}//类定义结束
?>