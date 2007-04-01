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

import('@.Action.AdminAction');
/**
 +------------------------------------------------------------------------------
 * 系统管理
 +------------------------------------------------------------------------------
 * @package   core
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Ver$
 +------------------------------------------------------------------------------
 */
class SystemAction extends AdminAction
{//类定义开始

    function index() 
    {
        $dao = D('NodeDao');
        $app = $dao->findAll('status=1 AND level=1');
        $this->assign('app',$app);
        $dao     = D('MemoDao');
        $labels  = $dao->findall('type="php" AND userId="'.Session::get(USER_AUTH_KEY).'"');
        $this->assign('labels',$labels);
    	$this->display();
    }

    function runPhp() 
    {
    	$php = stripslashes($_POST['command']); 
        if(strlen($php)>4) {
            // 生成临时执行文件
            $tempPhpFile = TEMP_PATH."_@run".md5(rand_string(12)).".php";
            $result  =  file_put_contents($tempPhpFile, $php);
            if($result) {
                @require($tempPhpFile);
                @unlink($tempPhpFile);   
                if(!empty($_POST['label'])) {//保存SQL标签
                    $dao = D("MemoDao");
                    $map= new HashMap();
                    $map->put('memo',$php);
                    $map->put('label',$_POST['label']);
                    $map->put('createTime',time());
                    $map->put('type','php');
                    $map->put('userId',Session::get(USER_AUTH_KEY));
                    $dao->add($map);
                }  
                header("Content-Type:text/html; charset=utf-8");
                exit('PHP语句已经成功执行！');  
            }else {
            header("Content-Type:text/html; charset=utf-8");
        	exit('执行错误！');            	
            }
      	
        }else {
            header("Content-Type:text/html; charset=utf-8");
        	exit('执行错误！');
        }

    }

    function clearTmplCache() 
    {
    	$app = $_POST['app'];
        if(!empty($app)) {
            $tmplPath =  str_replace('.','../'.$app,CACHE_PATH);
            import("ORG.Io.Dir");
            Dir::del($tmplPath);        	
            $this->success('模版缓存已经清空！');
        }
    }

    function getLabel() 
    {
    	if(!empty($_POST['id'])) {
    		$dao = D("MemoDao");
            $label   =  $dao->getById($_POST['id']);
            $this->ajaxReturn($label->memo,'标签获取成功',1);
    	}else {
    		exit();
    	}
    }
    function delLabel() 
    {
    	$id = $_POST['id'];
        if(!empty($id)) {
            $dao = D("MemoDao");
            $result = $dao->deleteById($id);      	
            if($result !== false) {
            	$this->success('标签删除成功！');
            }else {
            	$this->error('标签删除失败！');
            }
        }
    }
}//类定义结束
?>