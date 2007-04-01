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
// $Id$

// 导入引用类库
import("ORG.Text.Validation");
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
            //显示菜单项
            $menu  = array();
            if(Session::is_set('menu'.Session::get(USER_AUTH_KEY))) {
                //如果已经缓存，直接读取缓存
                $menu   =   Session::get('menu'.Session::get(USER_AUTH_KEY));
            }else {
                //读取数据库模块列表生成菜单项
                $dao    =   D("Node"); 
                $list   =   $dao->findAll('level=2 and pid='.getAppId(),'','id,name,title','seqNo asc'); 
                $accessList = Session::get('_ACCESS_LIST');
                foreach($list->getIterator() as $key=>$module) {
                     if(isset($accessList[strtoupper(APP_NAME)][strtoupper($module->name)]) || Session::is_setLocal('administrator')) {
                        //设置模块访问权限
                        $module->access =   1;
                        $menu[$key]  = $module->toArray();
                    }
                }
                //缓存菜单访问
                Session::set('menu'.Session::get(USER_AUTH_KEY),$menu);        	
            }
            $this->assign('menu',$menu);
            $this->assign("login",true);
            //显示登录用户名称
            $this->assign('loginUserName',Session::get('loginUserName'));
        }
        // 读取配置文件
        parent::_initialize();
    }

    /**
     +----------------------------------------------------------
     * 默认列表选择操作
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function select()
    {
        $map = $this->_search();
        if(method_exists($this,'_filter')) {
            $this->_filter($map);
        }
        //创建Dao对象
        $dao = $this->getDaoClass();

        //查找满足条件的列表数据
        $voList     = $dao->findAll($map,'','*');
        $this->assign('list',$voList);
        $this->display();
        return;
    }

    /**
     +----------------------------------------------------------
     * 默认列表多选操作
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function multiSelect()
    {
        $map = $this->_search();
        if(method_exists($this,'_filter')) {
            $this->_filter($map);
        }
        //创建Dao对象
        $dao = $this->getDaoClass();

        //查找满足条件的列表数据
        $voList     = $dao->findAll($map,'','*');
        $this->assign('list',$voList);

        $this->display();
        return;
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
        $caption    = $_GET['caption'];
        $level	=	$dao->getMin('level',$map);
        $map->put('level',$level);
        $list	=	$dao->findall($map,'','*','seqNo');
        header("content-type:text/xml;charset=utf-8");
        $xml	=  '<?xml version="1.0" encoding="utf-8" ?>';
        if($map->containsKey('parentId')) {
            $vo		=	$dao->find('id='.$map->get('parentId'));
            $xml	.= '<tree caption="'.$vo->name.'" >';
        }else {
            $xml	.= '<tree caption="'.$caption.'" >';
        }
        $xml	.=	$this->_toXmlTree($list,'name');
        $xml	.= '</tree>'; 
        exit($xml);
    }

    /**
     +----------------------------------------------------------
     * 把树型列表数据转换为XML
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function _toXmlTree($list,$caption='name',$subTree=true) 
    {
        $dao	=	$this->getDaoClass();
        foreach($list->getIterator() as $key=>$val) {
            $list2	=	$dao->findall('parentId='.$val->id,'','*','seqNo');
            if($list2->size()==0) {
                $xml	.= '<level'.$val->level.' id="'.$val->id.'" level="'.$val->level.'" parentId="'.$val->parentId.'" caption="'.$val->{$caption}.'" />';
            }else {
                $xml	.= '<level'.$val->level.' id="'.$val->id.'" level="'.$val->level.'" parentId="'.$val->parentId.'" caption="'.$val->{$caption}.'" >';
                if($subTree) {
                    $xml	.=	$this->_toXmlTree($list2,$caption);
                }
                $xml	.=	'</level'.$val->level.'>';				
            }
        }
        return $xml;
    }

      /**
     +----------------------------------------------------------
     * 下载附件
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function download()
    {
        import("ORG.Net.Http");
        import('@.Dao.AttachDao');
        $id         =   $_GET['id'];
        $dao        =   new AttachDao();
        $attach	    =   $dao->getById($id);
        $filename   =   $attach->savepath.$attach->savename;
        if(is_file($filename)) {
            Http::download($filename,$attach->name);
        }
    }



    /**
     +----------------------------------------------------------
     * 默认删除附件操作
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function delAttach()
    {
        //删除指定记录
        import("@.Dao.AttachDao");
        $dao        = new AttachDao();
        $id         = $_REQUEST[$dao->pk];
        //id 安全验证
        $validation = Validation::getInstance();
        if(!$validation->check($id,'/^\d+(\,\d+)?$/')) {
            throw_exception('非法Id');
        }
        $condition = $dao->pk.' in ('.$id.')'; 
        if($dao->delete($condition)){
            $this->assign("message",'删除成功！');
        }else {
            $this->assign('error',  '删除失败！');
        }
        $this->forward();
    }

    /**
     +----------------------------------------------------------
     * 默认导出操作 导出列表数据 到csv格式
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function output($dao='',$map='')
    {
        //取得数据列表，并转换为字串
        if(empty($dao)) {
        	$dao        = $this->getDaoClass();
        }
        if(empty($map) && Session::is_set('_map')) {
            $map    = Session::get('_map');	
        }
        $voList     = $dao->findAll($map);
        $content    = $voList->toString();
        if(!empty($content)) {
            import("ORG.Net.Http");
            //转换为gb2312编码
            Http::download('',time().'.csv',auto_charset($content,OUTPUT_CHARSET,'big5'));			
        }else {
            $this->assign('error','目前没有任何数据!');
            $this->forward();
        }
    }

    /**
     +----------------------------------------------------------
     * 验证码显示
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function verify() 
    {
        import("ORG.Util.Image");
        Image::showAdvVerify('gif'); 
    }


    /**
     +----------------------------------------------------------
     * 上传图片显示
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function image() 
    {
        $id = $_GET['id'];
        import("ORG.Util.Image");
        $dao	=	D("AttachDao");
        $attach	=	$dao->getById($id);
        $imgFile=	$attach->savepath.$attach->savename;
        if(is_file($imgFile)) {
            Image::showImg($imgFile);
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
        //id 安全验证
        $validation = Validation::getInstance();
        if(!$validation->check($id,'/^\d+(\,\d+)?$/')) {
            throw_exception('非法Id');
        }
        $condition = $dao->pk.' in ('.$id.')'; 
        if($dao->forbid($condition)){
            $this->assign("message",'状态禁用成功！');
            $this->assign('jumpUrl',"javascript:history.back(-2);");
            //$this->assign("jumpUrl",$this->getReturnUrl());
        }else {
            $this->assign('error',  '状态禁用失败！');
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
        //id 安全验证
        $validation = Validation::getInstance();
        if(!$validation->check($id,'/^\d+(\,\d+)?$/')) {
            throw_exception('非法Id');
        }
        $condition = $dao->pk.' in ('.$id.')'; 
        if($dao->resume($condition)){
            $this->assign("message",'状态恢复成功！');
            $this->assign('jumpUrl',"javascript:history.back(-2);");
            //$this->assign("jumpUrl",$this->getReturnUrl());
        }else {
            $this->assign('error',  '状态恢复失败！');
        }
        $this->forward();
    }

        /**
     +----------------------------------------------------------
     * 清空数据表
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
	function clear() 
	{
		$dao    = $this->getDaoClass();
		$result =	$dao->clear();
		if($result) {
                //成功提示
                $this->assign("message",'清空成功');
                $this->assign("jumpUrl",$this->getReturnUrl());
            }else { 
                //失败提示
                $this->assign("error",'清空失败');
            }
		$this->forward();
		return ;
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
     * 析构函数 在应用程序类结束的时候进行日志记录，提高效率
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     */
    function __destruct()
    {
		/*
        if(MODULE_NAME!='log' && Session::is_set(USER_AUTH_KEY)) {
            import('@.Dao.LogDao');
            $dao    =   new LogDao();
            $map    =   new HashMap();
            $map->put('module',MODULE_NAME);
            $map->put('action',ACTION_NAME);
            $map->put('time',time());
            $map->put('userId',Session::get(USER_AUTH_KEY));
            $map->put('url',$_SERVER["PHP_SELF"]);
            $dao->add($map);
        }*/
    }


}//类定义结束
?>