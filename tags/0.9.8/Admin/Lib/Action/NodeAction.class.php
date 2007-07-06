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
// $Id: NodeAction.class.php 86 2007-04-01 12:56:20Z liu21st $

/**
 +------------------------------------------------------------------------------
 * ThinkPHP 节点管理
 +------------------------------------------------------------------------------
 * @author liu21st <liu21st@gmail.com>
 * @version  $Id: NodeAction.class.php 86 2007-04-01 12:56:20Z liu21st $
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
		if(strtoupper(ACTION_NAME)=='INDEX') {
            if(!$map->containsKey('pid') ) {
            	$map->put('pid',0);
            }
            
            Session::set('currentNodeId',$map->get('pid'));
            //获取上级节点
            $dao  = D("Node");
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
		if(Session::is_set('currentNodeId')) {
			$vo = $dao->getById(Session::get('currentNodeId'));
	        $this->assign('parentNode',$vo->name);
			$this->assign('level',$vo->level+1);
			$this->assign('pid',$vo->id);
		}else{
			$this->assign('level',1);
		}
		$this->display();
	}
    // 节点访问权限
    function access() 
    {
        //读取系统权限组列表
        $groupDao    =   D("GroupDao");
        $list		=	$groupDao->findAll('','','id,name');
        $groupList	=	$list->getCol('id,name');

		$nodeDao    =   new NodeDao();
        $list   =  $nodeDao->findAll('pid='.Session::get('currentNodeId'),'','id,title');
        $nodeList = $list->getCol('id,title');
		$this->assign("nodeList",$nodeList);

        //获取当前节点信息
        $nodeId =  isset($_GET['id'])?$_GET['id']:'';
		$nodeGroupList = array();
		if(!empty($nodeId)) {
			$this->assign("selectNodeId",$nodeId);
			//获取当前节点的权限组列表
            $dao = new NodeDao();
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
		$nodeDao    =   new NodeDao();
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

    // 创建项目或者模块
    function build() 
    {
    	$id   =  $_REQUEST['id'];
        // 获取要创建的节点
        $dao = D("Node");
        $node   = $dao->getById($id);
        switch($node->level) {
        	case 1:// 属于项目节点则创建项目目录结构
                $appName = $node->name;
                $this->buildApp($appName);
                $this->success('项目创建成功！');
                break;
            case 2:// 模块节点
            	// 获取所属项目节点
                $parent = $dao->getById($node->pid);
                // 创建模块类文件
                $this->buildModule($parent->name,$node->name);
                $this->success('模块创建成功！');
            	break;
            default:
                $this->error('只有项目和模块可以创建!');
        }
    }

    // 读取当前数据库的数据表自动创建项目的所有模块
    function buildAllModule() 
    {
    	$id   =  $_REQUEST['id'];
        // 获取要创建的节点
        $dao = D("Node");
        $node   = $dao->getById($id);
        if($node==1) {
        	$appName = $node->name;
            import('Think.Db.Table');
            $table   =  new Table($appName);
            $table->build();
            $this->success('项目：'.$appName.'类库自动创建成功！');
        }else {
        	$this->error('非项目节点不允许当前操作！');
        }
    }

    // 自动创建项目目录结构、配置文件和入口文件
    function buildApp($name) 
    {
        $appPath  =  str_replace('Admin',$name,ADMIN_PATH);
       // 创建项目目录
        if(!mk_dir($appPath)) {
            $this->error('创建项目目录失败，确认目录是否可写！');
        }
        // 创建项目Cache目录
        if(!mk_dir($appPath.'Cache')) {
            $this->error('创建项目模块缓存目录失败，确认目录是否可写！');
        }
        // 创建项目Cache目录
        if(!mk_dir($appPath.'Temp')) {
            $this->error('创建项目缓存目录失败，确认目录是否可写！');
        }
        // 创建项目Cache目录
        if(!mk_dir($appPath.'Logs')) {
            $this->error('创建项目日志目录失败，确认目录是否可写！');
        }
        // 创建项目Cache目录
        if(!mk_dir($appPath.'Tpl')) {
            $this->error('创建项目模版目录失败，确认目录是否可写！');
        }else {
            if(!mk_dir($appPath.'Tpl/default/')) {
                $this->error('创建默认项目模版目录失败，确认目录是否可写！');
            }        	
        }
        // 创建项目Cache目录
        if(!mk_dir($appPath.'Conf')) {
            $this->error('创建项目配置目录失败，确认目录是否可写！');
        }else {
        	// 创建项目配置文件
            copy(CONFIG_PATH.'_appConfig.php',$appPath.'Conf/_appConfig.php');
        }
        // 创建项目配置目录
        if(!mk_dir($appPath.'Lib')) {
            $this->error('创建项目类库目录失败，确认目录是否可写！');
        }else {
            if(!mk_dir($appPath.'Lib/Action/')) {
                $this->error('创建项目Action目录失败，确认目录是否可写！');
            }    
            if(!mk_dir($appPath.'Lib/Dao/')) {
                $this->error('创建项目Dao目录失败，确认目录是否可写！');
            }  
            if(!mk_dir($appPath.'Lib/Vo/')) {
                $this->error('创建项目Vo目录失败，确认目录是否可写！');
            }              
        }
        // 创建项目Cache目录
        if(!mk_dir($appPath.'Common')) {
            $this->error('创建项目公共目录失败，确认目录是否可写！');
        }
        // 创建项目Cache目录
        if(!mk_dir($appPath.'Lang')) {
            $this->error('创建项目语言目录失败，确认目录是否可写！');
        }
        // 创建入口文件
        $indexFile =  $appPaht.'index.php';
        $this->buildIndex($name,$filename);
    }

    // 创建项目入口文件
    function buildIndex($app,$filename='') 
    {
        $filename  = empty($filename)? str_replace('Admin',$app,ADMIN_PATH).'index.php':$filename;
        if(!file_exists($filename)) {
            $content   =  "<?php \n";
            $content  .=  "// 该入口文件由ThinkPHP自动生成 \n";
            $content  .=  "define('THINK_PATH', '".THINK_PATH."');\n";
            $content  .= "define('WEB_ROOT','../');\n";
            $content  .= "//定义项目名称，如果不定义，默认为入口文件名称\n";
            $content  .= "define('APP_NAME', '{$app}');\n";
            $content  .= "define('APP_PATH', '.');\n";
            $content  .= "//加载ThinkPHP框架公共入口文件 \n";
            $content  .= "require(THINK_PATH.'/ThinkPHP.php');\n";
            $content  .= "//实例化一个网站应用实例\n";
            $content  .= "require('../config.php');\n";
            $content  .= '$App = new App();'."\n";
            $content  .= "//应用程序初始化\n";
            $content  .= '$App->init();'."\n";
            $content  .= "//启动应用程序\n";
            $content  .= '$App->exec();'."\n";    
            $content  .= "\n?>";
            file_put_contents($filename,$content);
        }
    }

    // 自动创建模块类库文件和模版目录
    function buildModule($app,$module) 
    {
        // 创建项目的模块类库文件
    	import('Think.Db.Table');
        $table   =  new Table($app);
        $table->build($module);
        $appPath  =  realpath(str_replace('Admin',$app,ADMIN_PATH));
        // 创建模块模版文件目录
        if(!mk_dir($appPath.'/Tpl/default/'.$module)) {
            $this->error('创建项目模版失败，确认目录是否可写！');
        }        
    }
}//类定义结束
?>