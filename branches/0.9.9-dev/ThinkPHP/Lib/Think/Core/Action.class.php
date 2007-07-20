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
import("Think.Core.Template");

/**
 +------------------------------------------------------------------------------
 * Action控制器基础类
 +------------------------------------------------------------------------------
 * @package  core
 * @author   liu21st <liu21st@gmail.com>
 * @version  $Id$
 +------------------------------------------------------------------------------
 */
class Action extends Base
{//类定义开始

	// Action控制器名称
	var $name;

    // 模板实例对象
    var $tpl;

    // 上次错误信息
    var $error;

   /**
     +----------------------------------------------------------
     * 架构函数 取得模板对象实例
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     */
    function __construct()
    {
        //实例化模板类
        $this->tpl = Template::getInstance();    
		$this->name	=	substr($this->__toString(),0,-6);
        //控制器初始化
        $this->_initialize();
    }

    /**
     +----------------------------------------------------------
     * 控制器初始化操作
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function _initialize() 
    {
        //自动加载当前模块所需的Dao类文件
        import(APP_NAME.'.Dao.'.$this->getDao());
        if(isset($_REQUEST['ajax']) || (isset($_REQUEST['_AJAX_SUBMIT_']) && $_REQUEST['_AJAX_SUBMIT_']) ) {
            // 判断Ajax方式提交
            $this->assign('ajax',true);
        }
        //如果定义了validation方法，则进行表单验证
        if(method_exists($this,'_validation')) {
            $valid	=	$this->_validation();
            //如果验证无效则提示相应错误并终止执行
            if(!$valid) {
                $this->error($this->error);
            }
        }
        return ;
    }

    /**
     +----------------------------------------------------------
     * 在不开启数据缓存的情况下
     * 缓存当前页面ResultSet对象
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param VoList $voList 要缓存的VoList对象
     * @param String $identify  缓存标识
     * @param String $name  缓存的数据名称
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function cacheResultSet($voList,$identify,$name='') 
    {
		if(empty($name)) {
			$name	=	$this->name;
		}
        $guid   = $name.'List_'.$identify;
        //VoList对象缓存
        S($guid,$voList);
    }

    /**
     +----------------------------------------------------------
     * 获取缓存ResultSet对象
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param String $identify 缓存标识
     * @param String $name 缓存的名称
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function getCacheResultSet($identify,$name='') 
    {
		if(empty($name)) {
			$name	=	$this->name;
		}
        $guid   = $name.'List_'.$identify;
        // 获取VoList对象缓存
        return  S($guid);
    }

    /**
     +----------------------------------------------------------
     * 删除VoList对象缓存
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param String $identify 缓存标识
     * @param String $name 缓存的名称
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function delCacheResultSet($identify,$name='') 
    {
		if(empty($name)) {
			$name	=	$this->name;
		}
        $guid   = $name.'List_'.$identify;
        // 删除VoList对象缓存
        return  S($guid,null);
    }

    /**
     +----------------------------------------------------------
     * 在不开启数据缓存的情况下缓存Vo对象
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param Vo $vo 要缓存的Vo对象
     * @param integer $id 要缓存的Vo对象ID
     * @param String $name 缓存的名称
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function cacheVo($vo,$id,$name='') 
    {
		if(empty($name)) {
			$name	=	$this->name;
		}
        $guid   = $name.'_'.$id;
        S($guid,$vo);
    }

    /**
     +----------------------------------------------------------
     * 在不开启数据缓存情况下缓存Vo对象
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param integer $id 缓存的Vo ID
     * @param String $name 缓存的名称
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function getCacheVo($id,$name='') 
    {
		if(empty($name)) {
			$name	=	$this->name;
		}
        $guid   = $name.'_'.$id;
        //Vo对象缓存
        $vo      =  S($guid);
		// 取出缓存数据的时候记录乐观锁
		$dao = D($name);
		if($dao->optimLock) {
			if(is_array($vo) && isset($vo[$dao->optimLock])) {
				Session::set($guid.'_lock_version',$vo[$dao->optimLock]);
			}elseif(isset($vo->{$dao->optimLock})){
				Session::set($guid.'_lock_version',$vo->{$dao->optimLock});
			}
		}
        return $vo;
    }

    /**
     +----------------------------------------------------------
     * 删除Vo对象缓存
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param String $voClass 缓存的Vo类名称
     * @param integer $id 缓存的Vo ID
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function delCacheVo($id,$name='') 
    {
		if(empty($name)) {
			$name	=	$this->name;
		}
        $guid   = $name.'_'.$id;
        //删除Vo对象缓存
        return  S($guid,null);
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
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function getDao() 
    {
        return $this->name.'Dao';
    }

    /**
     +----------------------------------------------------------
     * 取得数据访问类的实例
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function getDaoClass() 
    {
        $dao        = D($this->name);
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
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function getReturnUrl() 
    {
        return __URL__.'?'.C('VAR_MODULE').'='.MODULE_NAME.'&'.C('VAR_ACTION').'='.C('DEFAULT_ACTION');
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
     * @param string $charset 输出编码
     * @param string $contentType 输出类型
     * @param string $varPrefix 模板变量前缀
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    function display($templateFile='',$charset='',$contentType='text/html',$varPrefix='')
    {
        $this->tpl->display($templateFile,$charset,$contentType,$varPrefix);
    }

    /**
     +----------------------------------------------------------
     *  获取输出页面内容
     * 调用内置的模板引擎fetch方法，
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $templateFile 指定要调用的模板文件
     * 默认为空 由系统自动定位模板文件
     * @param string $charset 输出编码
     * @param string $contentType 输出类型
     * @param string $varPrefix 模板变量前缀
     * @param string $display 是否直接显示
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    function fetch($templateFile='',$charset='',$contentType='text/html',$varPrefix='')
    {
        return $this->tpl->fetch($templateFile,$charset,$contentType,$varPrefix,false);
    }

    /**
     +----------------------------------------------------------
     * 模板变量赋值
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $name 要显示的模板变量
     * @param mixed $value 变量的值
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    function assign($name,$value='')
    {
        $this->tpl->assign($name,$value);
    }

    /**
     +----------------------------------------------------------
     * 取得模板显示变量的值
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
     * 操作错误跳转的快捷方法
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $errorMsg 错误信息
     * @param Boolean $ajax 是否为Ajax方式
     +----------------------------------------------------------
     * @return void 
     +----------------------------------------------------------
     */
    function error($errorMsg,$ajax=false) 
    {
        if($ajax || $this->get('ajax')) {
        	$this->ajaxReturn('',$errorMsg,0);
        }else {
            $this->assign('error',$errorMsg);
            $this->forward();        	
        }
    }

    /**
     +----------------------------------------------------------
     * 操作成功跳转的快捷方法
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $message 提示信息
     * @param Boolean $ajax 是否为Ajax方式
     +----------------------------------------------------------
     * @return void 
     +----------------------------------------------------------
     */
    function success($message,$ajax=false) 
    {
        if($ajax || $this->get('ajax')) {
        	$this->ajaxReturn('',$message,1);
        }else {
        	$this->assign('message',$message);
            $this->forward();
        }
        
    }

    /**
     +----------------------------------------------------------
     * Ajax方式返回数据到客户端
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $data 要返回的数据
     * @param String $info 提示信息
     * @param String $status 返回状态
     +----------------------------------------------------------
     * @return void 
     +----------------------------------------------------------
     */
    function ajaxReturn($data='',$info='',$status='',$type='') 
    {
        $result  =  array();
        if($status === '') {
        	$status  = $this->get('error')?0:1;
        }
        if($info=='') {
            if($this->get('error')) { 
                $info =   $this->get('error');                	
            }elseif($this->get('message')) {
                $info =   $this->get('message');                	
            }         	
        }
        if($status==1) { 
            $info =   ' <span style="color:blue">'.$info.'</span>';
        }else {
            $info =   ' <span style="color:red">'.$info.'</span>';
        }  
        $result['status']  =  $status;
   	    $result['info'] =  $info;
        $result['data'] = $data;
		if(empty($type)) $type	=	C('AJAX_RETURN_TYPE');
		if(strtoupper($type)=='JSON') {
			// 返回JSON数据格式到客户端 包含状态信息
			header("Content-Type:text/html; charset=utf-8");
			exit(json_encode($result));
		}elseif(strtoupper($type)=='XML'){
			// 返回xml格式数据
			header("Content-Type:text/xml; charset=utf-8");
			exit(xml_encode($result));
		}else{
			// TODO 增加其它格式
		}
    }
    /**
     +----------------------------------------------------------
     * Action跳转 支持指定模块和延时跳转
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $action 要跳转的Action 默认为_dispatch_jump
     * @param string $module 要跳转的Module 默认为当前模块
     * @param string $app 要跳转的App 默认为当前项目
     * @param boolean $exit  是否继续执行
     * @param integer $delay 延时跳转的时间 单位为秒
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    function forward($action='_dispatch_jump',$module=MODULE_NAME,$app=APP_NAME,$exit=false,$delay=0)
    {
        if(!empty($delay)) {
            //指定延时跳转 单位为秒
        	sleep(intval($delay));
        }
        if(is_array($action)) {
            //通过类似 array(&$module,$action)的方式调用
        	call_user_func($action);
        }else {
            if( MODULE_NAME!= $module) {
                // 跳转执行指定模块的操作
                $moduleClass = $module.'Action';
                import($app.'.Action.'.$moduleClass);
                $class  = & new $moduleClass();
                call_user_func(array(&$class,$action));
            }else {
                // 执行当前模块操作
                $this->{$action}();
            }
        }
        if($exit) {
        	exit();
        }else {
        	return ;
        }
    }

    /**
     +----------------------------------------------------------
     * Action跳转 支持指定模块和延时跳转
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $action 要跳转的Action 
     * @param string $module 要跳转的Module 默认为当前模块
     * @param string $app 要跳转的App 默认为当前项目
     * @param integer $delay 延时跳转的时间 单位为秒
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
	function redirect($action,$module=MODULE_NAME,$app=APP_NAME,$delay=0,$msg='') {
		$url	=	str_replace(APP_NAME,$app,__APP__).'/'.$module.'/'.$action;
		redirect($url,$delay,$msg);
	}

    /**
     +----------------------------------------------------------
     * 默认操作定义
     * 
     +----------------------------------------------------------
     */

    /**
     +----------------------------------------------------------
     * 默认跳转操作 支持错误导向和正确跳转 
     * 调用模板显示 默认为public目录下面的success页面
     * 提示页面为可配置 支持模板标签
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function _dispatch_jump() 
    {
        if($this->get('ajax') ) {
            // 用于Ajax附件上传 显示信息
            if($this->get('_ajax_upload_')) {
                header("Content-Type:text/html; charset=utf-8");
            	exit($this->get('_ajax_upload_'));
            }else {
            	$this->ajaxReturn();
            }
        }
        // 普通方式跳转
        $templateFile = TEMPLATE_PATH.'/Public/success'.C('TEMPLATE_SUFFIX');
        //样式表文件
        if($this->get('error') ) {
            $msgTitle    =   '<IMG SRC="'.APP_PUBLIC_URL.'/images/warn.gif" align="absmiddle" BORDER="0"> <span class="red">'.L('_OPERATION_FAIL_').'</span>';
        }else {
            $msgTitle    =   '<IMG SRC="'.APP_PUBLIC_URL.'/images/ok.gif" align="absmiddle" BORDER="0"> <span class="black">'.L('_OPERATION_SUCCESS_').'</span>';
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
    	$this->display($templateFile);
       
        // 中止执行  避免出错后继续执行
        exit ;       
    }

    /**
     +----------------------------------------------------------
     * 默认列表操作 支持分页、查询和排序 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws ThinkExecption
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
        if(!empty($dao)) {
        	$this->_list($dao,$map);
        }
		$this->display();
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
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function _search($name='') 
    {
        //生成查询条件
        $map        = I('Think.Util.HashMap');
		if(empty($name)) {
			$name	=	$this->name;
		}
		$dao	=	D($name);
        foreach($dao->fields as $key=>$val) {
            if(isset($_REQUEST[$val]) && $_REQUEST[$val]!='') {
                $map->put($val,$_REQUEST[$val]);
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
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function _list($dao,$map,$sortBy='',$asc=true) 
    {
        //排序字段 默认为主键名
        if(isset($_REQUEST['order'])) {
            $order = $_REQUEST['order'];
        }else {
            $order = !empty($sortBy)? $sortBy: $dao->pk;
        }
        //排序方式默认按照倒序排列
        //接受 sost参数 0 表示倒序 非0都 表示正序
        if(isset($_REQUEST['sort'])) {
            $sort = $_REQUEST['sort']?'asc':'desc';
        }else {
            $sort = $asc?'desc':'asc';
        }

        //取得满足条件的记录数
        $count      = $dao->getCount($map);
        import("ORG.Util.Page");
        //创建分页对象
        if(!empty($_REQUEST['listRows'])) {
        	$listRows  =  $_REQUEST['listRows'];
        }else {
        	$listRows  =  '';
        }
        $p          = new Page($count,$listRows);
        //分页查询数据
        $voList     = $dao->findAll($map,'*',$order.' '.$sort,$p->firstRow.','.$p->listRows);
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
        return ;
    }

	// 默认的新增操作
	function add() {
		$this->display();
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
     * @throws ThinkExecption
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
        if(!empty($dao)) {
        	$this->_insert($dao);
        }
    }

    /**
     +----------------------------------------------------------
     * 插入记录
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function _insert($dao) 
    {
        $vo = $dao->createVo();
        if(false === $vo) {
        	$this->error($dao->error);
        }
        //保存当前Vo对象
        $id = $dao->add($vo);
        if($id) { //保存成功
			if(is_array($vo)) {
	           	$vo[$dao->pk]  =  $id;
			}else{
	           	$vo->{$dao->pk}  =  $id;
			}
            // 缓存数据对象
            $this->cacheVo($vo,$id,$dao->name);
            //数据保存触发器
            if(method_exists($this,'_trigger')) {
                $this->_trigger($vo);
            }
            if(!empty($_FILES)) {//如果有文件上传
                //调用上传操作上传文件
                //并且保存附件信息到数据库
                $this->_upload($dao->getTableName(),$id);
            }
            //成功提示
            $this->success(L('_INSERT_SUCCESS_'));
        }else { 
            //失败提示
            $this->error(L('_INSERT_FAIL_'));
        }
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
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function edit($dao='')
    {
        //取得编辑的Vo对象
        if(empty($dao)) {
        	$dao    = $this->getDaoClass();
        }
        if(!empty($dao)) {
            $id     = (int)$_REQUEST[$dao->pk];		
            // 判断是否存在缓存Vo
            $vo=$this->getCacheVo($id,$dao->name);
            if(false === $vo) {
                $vo     = $dao->find($dao->pk."='$id'");
                if(!$vo) {
                    throw_exception(L('_SELECT_NOT_EXIST_'));
                }
                // 缓存Vo对象，便于下次显示
				if(is_array($vo)) {
	                $this->cacheVo($vo,$vo[$dao->pk],$dao->name);
				}else{
	                $this->cacheVo($vo,$vo->{$dao->pk},$dao->name);
				}

            }
            $this->assign('vo',$vo);
            if($this->get('ajax')) {
                $this->ajaxReturn($vo);
            }
        }
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
     * @throws ThinkExecption
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
     * @throws ThinkExecption
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
        if(!empty($dao)) {
        	$this->_update($dao);
        }
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
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function _update($dao) 
    {
        $vo = $dao->createVo('edit');
		if(!$vo) {
			$this->error($dao->error);
		}
    	$result  = $dao->save($vo);
        if($result) {
			$id	=	is_array($vo)?$vo[$dao->pk]:$vo->{$dao->pk};
			$vo	=	$dao->getById($id);
            // 保存成功，更新缓存Vo对象
            $this->cacheVo($vo,$id,$dao->name);
            //数据保存触发器
            if(method_exists($this,'_trigger')) {
                $this->_trigger($vo);
            }
            if(!empty($_FILES)) {//如果有文件上传
                //执行默认上传操作
                //保存附件信息到数据库
                $this->_upload($dao->getTableName(),$id);
            }
            //成功提示
            $this->success(L('_UPDATE_SUCCESS_'));
        }else {
            //错误提示
            $this->error($dao->error);
        }
    }

	// 默认上传操作
	function upload() {
		if(!empty($_FILES)) {//如果有文件上传
			// 上传附件并保存信息到数据库
			$this->_upload(MODULE_NAME);
			$this->forward();
		}
	}
	
    /**
     +----------------------------------------------------------
     * 文件上传功能，支持多文件上传、保存数据库、自动缩略图
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $module 附件保存的模块名称
     * @param integer $id 附件保存的模块记录号
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function _upload($module='',$id='') 
    {
        import("ORG.Net.UploadFile");
        $upload = new UploadFile();
        //检查客户端上传文件参数设置
        if(isset($_POST['_uploadFileSize']) && is_numeric($_POST['_uploadFileSize'])) {
            //设置上传文件大小
            $upload->maxSize  = $_POST['_uploadFileSize'] ;
        }
        if(!empty($_POST['_uploadFileType'])) {
            //设置上传文件类型
            $upload->allowExts  = explode(',',$_POST['_uploadFileType']);
        }
        if(!empty($_POST['_uploadSavePath'])) {
            //设置附件上传目录
            $upload->savePath =  $_POST['_uploadSavePath']; 
        }
        if(!empty($_POST['_uploadSaveRule'])) {
            //设置附件命名规则
            $upload->saveRule =  $_POST['_uploadSaveRule']; 
        }
        if(!empty($_POST['_uploadFileTable'])) {
            //设置附件关联数据表
            $module =  $_POST['_uploadFileTable']; 
        }
        if(!empty($_POST['_uploadRecordId'])) {
            //设置附件关联记录ID
            $id =  $_POST['_uploadRecordId']; 
        }
        if(!empty($_POST['_uploadFileVerify'])) {
            //设置附件关联记录ID
            $verify =  $_POST['_uploadFileVerify']; 
        }
        if(!empty($_POST['_uploadUserId'])) {
            //设置附件关联记录ID
            $userId =  $_POST['_uploadUserId']; 
        }else {
        	$userId = Session::is_set(C('USER_AUTH_KEY'))?Session::get(C('USER_AUTH_KEY')):0;
        }
        if(!empty($_POST['_uploadImgThumb'])) {
            //设置需要生成缩略图，仅对图像文件有效
            $upload->thumb =  $_POST['_uploadImgThumb']; 
        }
        if(!empty($_POST['_uploadThumbSuffix'])) {
            //设置需要生成缩略图的文件后缀
            $upload->thumbSuffix =  $_POST['_uploadThumbSuffix']; 
        }
        if(!empty($_POST['_uploadThumbMaxWidth'])) {
            //设置缩略图最大宽度
            $upload->thumbMaxWidth =  $_POST['_uploadThumbMaxWidth']; 
        }
        if(!empty($_POST['_uploadThumbMaxHeight'])) {
            //设置缩略图最大高度
            $upload->thumbMaxHeight =  $_POST['_uploadThumbMaxHeight']; 
        }
        $uploadReplace =  false;
        if(isset($_POST['_uploadReplace']) && 1==$_POST['_uploadReplace']) {
            //设置附件是否覆盖
            $uploadReplace =  true;
        }
        $uploadRecord  =  true;
        if(isset($_POST['_uploadRecord']) && 0==$_POST['_uploadRecord']) {
            //设置附件数据是否保存到数据库
            $uploadRecord =  false;
        }
        // 记录上传成功ID
        $uploadId =  array();
        $savename = array();
        //执行上传操作
        if(!$upload->upload()) {
            if($this->get('ajax') && isset($_POST['_uploadFileResult'])) {
                $uploadSuccess =  false;
                $ajaxMsg  =  $upload->getErrorMsg();
            }else {
                //捕获上传异常
                $this->error($upload->getErrorMsg());            	
            }
        }else {
            if($uploadRecord) {
            	// 附件数据需要保存到数据库
                 //取得成功上传的文件信息
                $uploadList = $upload->getUploadFileInfo();
                //保存附件信息到数据库
                $attachDao    = D('AttachDao');
                //启动事务
                $attachDao->startTrans();
                foreach($uploadList->toArray() as $key=>$file) {
                    //记录模块信息
                    $file['module']     =   $module;
                    $file['recordId']   =   $id;
                    $file['userId']     =   $userId;
					$file['verify']	=	$verify?$verify:'';
                    //保存附件信息到数据库
                    if($uploadReplace ) {
                        $vo  =  $attachDao->find("module='".$module."' and recordId='".$id."'");
                        if(false !== $vo) {
                            // 如果附件为覆盖方式 且已经存在记录，则进行替换 
							$id	=	is_array($vo)?$vo[$attachDao->pk]:$vo->{$attachDao->pk};
                            $attachDao->save($file,"id='".$id."'");     
                            $uploadId[]   = $id;
                        }else {
                            $uploadId[] = $attachDao->add($file);
                        }
                    }else {
                        //保存附件信息到数据库
                        $uploadId[] =  $attachDao->add($file);
                    }
                    $savename[] =  $file['savename'];
                }
                //提交事务
                $attachDao->commit();
            }
            $uploadSuccess =  true;
            $ajaxMsg  =  '';
        }

        // 判断是否有Ajax方式上传附件
        // 并且设置了结果显示Html元素
        if($this->get('ajax') && isset($_POST['_uploadFileResult']) ) {
            // Ajax方式上传参数信息
            $info = Array();
            $info['success']  =  $uploadSuccess;
            $info['message']   = $ajaxMsg;
            //设置Ajax上传返回元素Id
            $info['uploadResult'] =  $_POST['_uploadFileResult']; 
            if(isset($_POST['_uploadFormId'])) {
                //设置Ajax上传表单Id
                $info['uploadFormId'] =  $_POST['_uploadFormId']; 
            }
            if(isset($_POST['_uploadResponse'])) {
                //设置Ajax上传响应方法名称
                $info['uploadResponse'] =  $_POST['_uploadResponse']; 
            }
            if(!empty($uploadId)) {
                $info['uploadId'] = implode(',',$uploadId);            	
            }
            $info['savename']   = implode(',',$savename);
            $this->ajaxUploadResult($info);
        }
        return ;
                	
    }

    /**
     +----------------------------------------------------------
     * Ajax上传页面返回信息
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function ajaxUploadResult($info) 
    {
        // Ajax方式附件上传提示信息设置
        // 默认使用mootools opacity效果
        $show   = '<script language="JavaScript" src="'.WEB_PUBLIC_URL.'/Js/mootools.js"></script><script language="JavaScript" type="text/javascript">'."\n";    
        $show  .= ' var parDoc = window.parent.document;';    
        $show  .= ' var result = parDoc.getElementById("'.$info['uploadResult'].'");';   
        if(isset($info['uploadFormId'])) {
   	        $show  .= ' parDoc.getElementById("'.$info['uploadFormId'].'").reset();';
        }
        $show  .= ' result.style.display = "block";';   
        $show .= " var myFx = new Fx.Style(result, 'opacity',{duration:600}).custom(0.1,1);";
        if($info['success']) {
            // 提示上传成功
            $show .=  'result.innerHTML = "<div style=\"color:#3333FF\"><IMG SRC=\"'.APP_PUBLIC_URL.'/images/ok.gif\" align=\"absmiddle\" BORDER=\"0\"> 文件上传成功！</div>";';        
            // 如果定义了成功响应方法，执行客户端方法
            // 参数为上传的附件id，多个以逗号分割
            if(isset($info['uploadResponse'])) {
                $show  .= 'window.parent.'.$info['uploadResponse'].'("'.$info['uploadId'].'","'.$info['savename'].'");';
            }
        }else {
            // 上传失败
            // 提示上传失败
            $show .=  'result.innerHTML = "<div style=\"color:#FF0000\"><IMG SRC=\"'.APP_PUBLIC_URL.'/images/update.gif\" align=\"absmiddle\" BORDER=\"0\"> 上传失败：'.$info['message'].'</div>";';           	
        }
        $show .= "\n".'</script>';   
        $this->assign('_ajax_upload_',$show);   
        return ;
   	}

    /**
     +----------------------------------------------------------
     * 默认删除操作
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function delete($dao='')
    {
        //删除指定记录
		if(empty($dao)) {
	        $dao        = $this->getDaoClass();
		}
        if(!empty($dao)) {
            $id         = $_REQUEST[$dao->pk];
            if(isset($id)) {
                $condition = $dao->pk.' in ('.$id.')'; 
                if($dao->delete($condition)){
                    $this->success(L('_DELETE_SUCCESS_'));
                }else {
                    $this->error(L('_DELETE_FAIL_'));
                }        	
            }else {
                $this->error(L('_ERROR_ACTION_'));
            }        	
        }
    }

    /**
     +----------------------------------------------------------
     * 验证码显示
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function verify() 
    {
		$type	 =	 isset($_GET['type'])?$_GET['type']:'gif';
        import("ORG.Util.Image");
        Image::buildImageVerify(4,1,$type);
    }

}//类定义结束
?>