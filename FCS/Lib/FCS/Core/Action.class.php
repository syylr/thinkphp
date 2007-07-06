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
import("FCS.Core.Template");
import("FCS.Util.Validation");
import("FCS.Util.Page");

/**
 +------------------------------------------------------------------------------
 * Action控制器基础类
 +------------------------------------------------------------------------------
 * @package  core
 * @author   liu21st <liu21st@gmail.com>
 * @version  0.8.0
 +------------------------------------------------------------------------------
 */
class Action extends Base
{//类定义开始

    /**
     +----------------------------------------------------------
     * 模板实例对象
     +----------------------------------------------------------
     * @var object
     * @access protected
     +----------------------------------------------------------
     */
    var $tpl    ;

    /**
     +----------------------------------------------------------
     * 上次错误信息
     +----------------------------------------------------------
     * @var string
     * @access protected
     +----------------------------------------------------------
     */
    var $error    ;


   /**
     +----------------------------------------------------------
     * 架构函数 取得模板对象实例
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     */
    function __construct()
    {
        //实例化默认模板类
        $this->tpl = Template::getInstance();    
        //控制器初始化
        $this->_initialize();
    }

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
        //自动加载当前模块所需的Dao类文件
        import(APP_NAME.'.Dao.'.$this->getDao(),APPS_PATH);

        //如果定义了validation方法，则进行表单验证
        if(method_exists($this,'_validation')) {
            $valid	=	$this->_validation();
            //如果验证无效则提示相应错误
            if(!$valid) {
                $this->assign('error',$this->error);
                $this->forward();
            }
        }
        return ;
    }

    /**
     +----------------------------------------------------------
     * 默认操作定义
     * 
     +----------------------------------------------------------
     */

    /**
     +----------------------------------------------------------
     * 默认列表操作 支持分页、查询和排序 
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function index()
    {
        //列表过滤器，生成查询Map对象
        $map = $this->_search();		
        if(method_exists($this,'_filter')) {
            $this->_filter($map);
        }
        $dao    =   $this->getDaoClass();
        $this->_list($dao,$map);

        return;
    }

    /**
     +----------------------------------------------------------
     * 根据表单生成查询条件
     * 进行列表过滤
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function _search($vo='') 
    {
        //生成查询条件
        $map        = new HashMap();
        if(empty($vo)) {
            $vo    = $this->getVo();
        }
        //自动引入同名VO类
        import(APP_NAME.'.Vo.'.$vo,APPS_PATH);
        $vars       = get_class_vars($vo);

        foreach($vars as $key=>$val) {
            if(isset($_REQUEST[$key]) && $_REQUEST[$key]!='') {
                $map->put($key,$_REQUEST[$key]);
            }
        }

        return $map;
    }

    /**
     +----------------------------------------------------------
     * 根据表单生成查询条件
     * 进行列表过滤
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function _list($dao,$map) 
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
        //取得满足条件的记录数
        $count      = $dao->getCount($map);
        //创建分页对象
        $p          = new Page($count);
        //分页查询数据
        $voList     = $dao->findAll($map,'','*',$order.' '.$sort,$p->firstRow.','.$p->listRows);

        //分页跳转的时候保证查询条件
        $condition  = $map->toArray();
        foreach($condition as $key=>$val) {
            $p->parameter   .=   "$key=$val&";            	
        }
        //分页显示
        $page       = $p->show();
        //列表排序显示
        $sortImg    = $sort ;                                   //排序图标
        $sortAlt    = $sort == 'desc'?'升序排列':'倒序排列';    //排序提示
        $sort       = $sort == 'desc'? 1:0;                     //排序方式

        //模板赋值显示
        $this->assign('list',       $voList);
        $this->assign('sort',       $sort);
        $this->assign('order',      $order);
        $this->assign('sortImg',    $sortImg);
        $this->assign('sortType',   $sortAlt);
        $this->assign("page",       $page);
        $this->display();
        return ;
    }


    /**
     +----------------------------------------------------------
     * 默认新增操作 
     *
     * 如果子类的新增操作比较简单可以直接调用，无需再定义add方法
     * 如果需要额外操作可以重定义操作方法，
     * 并且在子类中通过parent::add调用
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function add() 
    {
        //默认增加操作只是调用模板显示
        $this->display();
        return ;
    }

    /**
     +----------------------------------------------------------
     * 默认新增保存操作
     * 
     * 如果需要额外操作可以重定义操作方法，
     * 并且在子类中通过parent::insert调用
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function insert() 
    {
        //对表单提交处理进行处理或者增加非表单数据
        if(method_exists($this,'_operation')) {
            $this->_operation();
        }

        //保存新增数据对象
        $dao        = $this->getDaoClass();
        $this->_insert($dao);
    }

    /**
     +----------------------------------------------------------
     * 插入记录
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function _insert($dao) 
    {
        $vo = $dao->createVo();
        //保存当前Vo对象
        $id = $dao->add($vo);
        if($id) { //保存成功
            $vo->{$dao->pk} =   $id;
            //增加缓存数据对象
            $this->_cacheVo($vo,$id);
            if(SAVE_PARENT_VO && 0!==strcasecmp(get_parent_class($vo),'Vo')) {
                //如果启用保存父类Vo功能
                //并且父类不是Vo基类，则首先保存父类Vo对象
                //目前仅支持上一级Vo的保存
                $voClass    =   get_parent_class($vo);
                $map        =   $vo->toMap();
                $map->put('childId',$id);
                $extendsVo  =   new $voClass($map);
                $daoClass   =   $extendsVo->getDao();
                $extendsDao =   new $daoClass();
                $parentId   =   $extendsDao->add($extendsVo);
                if(!$parentId) {
                    $this->error =  '父类Vo对象'.$extendsVo.'新增失败' ;
                    $this->forward();
                    return ;
                }
            }
            //数据保存触发器
            if(method_exists($this,'_trigger')) {
                $this->_trigger($vo);
            }
            if(!empty($_FILES)) {//如果有文件上传
                //调用上传操作上传文件
                //并且保存附件信息到数据库
                $this->_upload(MODULE_NAME,$id);
            }
            //成功提示
            $this->assign("message",'新增成功');
            $this->assign("jumpUrl",$this->getReturnUrl());
        }else { 
            //失败提示
            $this->assign('error',  '新增失败');
        }
        //页面跳转
        $this->forward();    	
    }

    /**
     +----------------------------------------------------------
     * 默认编辑操作
     * 
     * 如果需要额外操作可以重定义操作方法，
     * 并且在子类中通过parent::edit调用
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function edit()
    {

        //取得编辑的Vo对象
        $dao    = $this->getDaoClass();
        $id     = $_GET[$dao->pk];
        //id 安全验证
        $validation = Validation::getInstance();
        if(!$validation->check($id,'number')) {
            throw_exception('非法Id');
        }
        $vo     = $dao->find($dao->pk."=$id");
        if(!$vo) {
            throw_exception('要编辑的项目不存在！');
        }
        //读取附件信息
        import(APP_NAME.".Dao.AttachDao");
        $attachDao = new AttachDao();
        $attachs = $attachDao->findAll("module='".MODULE_NAME."' and recordId=$id");

        //模板变量赋值
        $this->assign("attach",$attachs);
        $this->assign('vo',$vo);
        $this->display();
        return;
    }

    /**
     +----------------------------------------------------------
     * 默认查看详细操作
     * 这里调用了edit方法，区别在于模板不同
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function read()
    {
        $this->edit();
        return;
    }


    /**
     +----------------------------------------------------------
     * 默认更新操作
     * 
     * 如果需要额外操作可以重定义操作方法，
     * 并且在子类中通过parent::update调用
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function update() 
    {
        //处理表单数据
        if(method_exists($this,'_operation')) {
            $this->_operation();
        }

        //更新数据对象
        $dao    = $this->getDaoClass();
        $this->_update($dao);
    }

    /**
     +----------------------------------------------------------
     * 更新一个数据对象
     * 
     +----------------------------------------------------------
     * @access protected 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function _update($dao) 
    {
        $vo     = $dao->createVo('edit');
        $id     = $dao->save($vo);

        if($id) {
            //更新缓存数据对象
            $this->_cacheVo($vo,$vo->{$dao->pk});

            //数据保存触发器
            if(method_exists($this,'_trigger')) {
                $this->_trigger($vo);
            }
            if(!empty($_FILES)) {//如果有文件上传
                //执行默认上传操作
                //保存附件信息到数据库
                $this->_upload($dao->getTableName(),$vo->{$dao->pk});
            }
            //成功提示
            $this->assign("message",'更新成功');
            $this->assign("jumpUrl",$this->getReturnUrl());
        }else {
            //错误提示
            $this->assign('error',  '更新失败');
        }
        //页面跳转
        $this->forward();    	
    }

    /**
     +----------------------------------------------------------
     * 默认上传操作 支持多文件上传
     * 把表单中的文件上传到服务器默认目录
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $module 附件保存的模块名称
     * @param integer $id 附件保存的模块记录号
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function _upload($module,$id) 
    {
        import("FCS.Util.UploadFile");
        $upload = new UploadFile();
        //检查客户端上传文件参数设置
        if(isset($_POST['_uploadFileSize']) && is_numeric($_POST['_uploadFileSize'])) {
            //设置上传文件大小
            $upload->maxSize  = $_POST['_uploadFileSize'] ;
        }
        if(isset($_POST['_uploadFileType'])) {
            //设置上传文件类型
            $upload->allowExts  = explode(',',$_POST['_uploadFileType']);
        }
        if(isset($_POST['_uploadSavePath'])) {
            //设置附件上传目录
            $upload->savePath =  $_POST['_uploadSavePath']; 
        }
        if(isset($_POST['_uploadFileTable'])) {
            //设置附件关联数据表
            $module =  $_POST['_uploadFileTable']; 
        }

        //执行上传操作
        if(!$upload->upload()) {
            //捕获上传异常
            throw_exception($upload->getErrorMsg());
        }
        //取得成功上传的文件信息
        $uploadList = $upload->getUploadFileInfo();
        //保存附件信息到数据库
        import(APP_NAME.".Dao.AttachDao");
        $attachDao    = new AttachDao();
        //启动事务
        $attachDao->startTrans();
        foreach($uploadList->getIterator() as $key=>$file) {
            //记录模块信息
            $file['module']     =   $module;
            $file['recordId']   =   $id;
            $file['userId']     =   Session::is_set(USER_AUTH_KEY)?Session::get(USER_AUTH_KEY):-1;
            $file['uploadTime'] =   time();
            //保存附件信息到数据库
            $attachDao->add($file);
        }
        //提交事务
        $attachDao->commit();
        return ;
                	
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
        import("FCS.Util.Http");
		import(APP_NAME.'.Dao.AttachDao');
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
        import("FCS.Util.Image");
        Image::buildImageVerify();
    }

    /**
     +----------------------------------------------------------
     * 查看源文件
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function source() 
    {
        $content = ob_end_clean();
        header("Content-Type:text/html; charset=utf-8");
        highlight_file($_GET[VAR_FILE]);
        exit ;
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
        import("FCS.Util.Image");
		import(APP_NAME.'.Dao.AttachDao');
        $dao	=	new AttachDao();
        $attach	=	$dao->getById($id);
        $imgFile=	$attach->savepath.$attach->savename;
        if(is_file($imgFile)) {
            Image::showImg($imgFile);
        }
        
    }


    /**
     +----------------------------------------------------------
     * 默认删除操作
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function delete()
    {
        //删除指定记录
        $dao        = $this->getDaoClass();
        $id         = $_GET[$dao->pk];
        $condition = $dao->pk.' in ('.$id.')'; 
        if($dao->delete($condition)){
            $this->assign("message",'删除成功！');
            $this->assign("jumpUrl",$this->getReturnUrl());
        }else {
            $this->assign('error'   ,'删除失败！');
        }
        $this->forward();
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
        import(APP_NAME.".Dao.AttachDao");
        $dao        = new AttachDao();
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
        $condition = $dao->pk.' in ('.$id.')'; 
        if($dao->forbid($condition)){
            $this->assign("message",'状态禁用成功！');
            $this->assign("jumpUrl",$this->getReturnUrl());
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
        $condition = $dao->pk.' in ('.$id.')'; 
        if($dao->resume($condition)){
            $this->assign("message",'状态恢复成功！');
            $this->assign("jumpUrl",$this->getReturnUrl());
        }else {
            $this->assign('error',  '状态恢复失败！');
        }
        $this->forward();
    }

    /**
     +----------------------------------------------------------
     * 缓存Vo对象
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param Vo $vo 要缓存的Vo对象
     * @param integer $id 要缓存的Vo对象ID
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function _cacheVo($vo,$id) 
    {
        if(DATA_CACHE_ON){            //如果启用动态缓存
            //取得共享内存实例
            $sm     = SharedMemory::getInstance();
            $guid   = strtoupper(get_class($vo)).'_'.$id;

            //Vo对象缓存
            $sm->set($guid,$vo);
        }
    }

    /**
     +----------------------------------------------------------
     * 取得当前Dao对象的名称
     * 用于自动导入Dao对象
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function getDao() 
    {
        return substr($this->__toString(),0,-6).'Dao';
    }

    /**
     +----------------------------------------------------------
     * 取得当前Vo对象的名称
     * 用于自动导入Vo对象
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function getVo() 
    {
        return substr($this->__toString(),0,-6).'Vo';
    }

    /**
     +----------------------------------------------------------
     * 取得数据访问类的实例
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function getDaoClass() 
    {
        $daoClass   = $this->getDao();
        $dao        = new $daoClass();
        return $dao;
    }

    /**
     +----------------------------------------------------------
     * 取得操作成功后要返回的URL地址
     * 默认返回当前模块的默认操作 
     * 可以在action控制器中重载
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function getReturnUrl() 
    {
        return __URL__.'?'.VAR_MODULE.'='.MODULE_NAME.'&'.VAR_ACTION.'='.DEFAULT_ACTION;
    }


    /**
     +----------------------------------------------------------
     * 模板显示
     * 调用内置的模板引擎显示方法，
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $templateFile 指定要调用的模板文件
     * 默认为空 由系统自动定位模板文件
     * @param string $varPrefix 模板变量前缀
     * @param string $charset 输出编码
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    function display($templateFile='',$charset=OUTPUT_CHARSET,$contentType='text/html',$varPrefix='')
    {
        $this->tpl->display($templateFile,$charset,$contentType,$varPrefix);
    }


    /**
     +----------------------------------------------------------
     * 模板变量赋值
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $name 要显示的模板变量
     * @param string $value 变量的值
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    function assign($name,$value)
    {
        $this->tpl->assign($name,$value);
    }


    /**
     +----------------------------------------------------------
     * 取得模板显示变量的值
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $name 模板显示变量
     +----------------------------------------------------------
     * @return mixed 
     +----------------------------------------------------------
     */
    function get($name)
    {
        return $this->tpl->get($name);
    }



    /**
     +----------------------------------------------------------
     * 页面跳转提示 调用模板显示 默认为public目录下面的success页面
     * 提示页面为可配置 支持模板标签
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $url 要跳转的URL地址
     * @param string $templateFile 提示页面的模板文件
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    function forward($url='',$templateFile='')
    {
        if(empty($templateFile)){
            $templateFile = TEMPLATE_PATH.'/Public/success'.TEMPLATE_SUFFIX;
        }
        if(!empty($url)){
            $this->assign('jumpUrl',$url);
        }
        //样式表文件
        $this->assign('publicCss',SYS_PUBLIC_URL."/css/FCS.css");
        //if(isset($this->error)) {
        //    $this->assign('error',$this->error);
        //}
        if($this->get('error') ) {
            $msgTitle    =   '<IMG SRC="'.SYS_PUBLIC_URL.'/images/warn.gif" align="absmiddle" BORDER="0"> <span class="red">操作失败</span>';
        }else {
            $msgTitle    =   '<IMG SRC="'.SYS_PUBLIC_URL.'/images/ok.gif" align="absmiddle" BORDER="0"> <span class="black">操作成功</span>';
        }
        //提示标题
        $this->assign('msgTitle',$msgTitle);
        if($this->get('message')) { //发送成功信息
            //成功操作后停留1秒
            if(!$this->get('waitSecond')) 
                $this->assign('waitSecond',"1");
            //默认操作成功自动返回操作前页面
            if(!$this->get('jumpUrl')) 
                $this->assign("jumpUrl",$_SERVER["HTTP_REFERER"]);
        }
        if($this->get('error')) { //发送错误信息
            //发生错误时候停留3秒
            if(!$this->get('waitSecond')) 
                $this->assign('waitSecond',"3");
            //默认发生错误的话自动返回上页
            if(!$this->get('jumpUrl')) 
                $this->assign('jumpUrl',"javascript:history.back(-1);");
        }
        //如果设置了关闭窗口，则提示完毕后自动关闭窗口
        if($this->get('closeWin')) {
            $this->assign('jumpUrl','javascript:window.close();');
        }
        //调用模板显示
        $this->display($templateFile);

        //显示页面运行时间
        if(SHOW_RUN_TIME) {
            echo '<div style="text-align:center;width:100%">Process: '.number_format((array_sum(split(' ', microtime())) - $GLOBALS['_beginTime']), 6).'s</div>';        	
        }
        exit ;
    }

}//类定义结束
?>