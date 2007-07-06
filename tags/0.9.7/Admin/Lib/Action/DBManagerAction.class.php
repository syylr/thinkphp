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
// $Id: DBManagerAction.class.php 78 2007-04-01 04:29:15Z liu21st $

import('@.Action.AdminAction');
/**
 +------------------------------------------------------------------------------
 * 数据库管理
 +------------------------------------------------------------------------------
 * @package   core
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Ver$
 +------------------------------------------------------------------------------
 */
class DBManagerAction extends AdminAction
{//类定义开始

       function index() 
    {
        $db      =  DB::getInstance();
        $tables   =  $db->getTables();
        $this->assign('tables',$tables);
        $dao     = D('MemoDao');
        $labels  = $dao->findall('type="sql" AND userId="'.Session::get(USER_AUTH_KEY).'"');
        $this->assign('labels',$labels);
    	$this->display();
        return ;
    }

    function output() 
    {
    	$tables   =  explode(',',$_REQUEST['table']);
        $db  =  DB::getInstance();
        $sql  = "-- ThinkPHP SQL Dump\n"
                    ."-- http://www.topthink.com.cn\n\n";
        foreach($tables as $key=>$table) {
            $autoKey   = '';
            $sql  .= "-- \n-- 表的结构 `$table`\n-- \n";
            $sql  .= "CREATE TABLE `$table` (\n";
            $fields  =  $db->getFields($table);
            foreach($fields as $key=>$val) {
                $null = $val['notnull']?'NULL':'NOT NULL';
                if($val['autoInc']) {
                	$autoInc  = 'auto_increment';
                    $autoKey   = $val['name'];
                }else {
                	$autoInc  = '';
                }
                if($val['default']=="''") {
                	$default = "default ''";
                }elseif($val['default']=='0') {
                	$default = "default 0";
                }elseif(strtoupper($val['default'])=='NULL') {
                	$default = "default NULL";
                }
            	$sql  .= '`'.$val['name'].'` '.$val['type'].' '.$null.' '.$default.' '.$autoInc.','."\n";
            }
            if(!empty($autoKey)) {
            	$sql  .= 'PRIMARY KEY  (`'.$autoKey.'`)';
            }else {
            	$sql  = substr($sql,0,-2);
            }
            $sql  .= "\n) ENGINE=InnoDB DEFAULT CHARSET=utf8";
            $sql  .= "\n-- \n-- 导出表中的数据 `$table`\n--\n"; 
            $result  = $db->_query("select * from $table "); 
            $fields = array_keys($result->get(0));
            foreach($result->toArray() as $key=>$val) {
                array_walk($val, array($db, 'fieldFormat'));
            	$sql  .= "INSERT INTO `$table` VALUES (".implode(',',$val).");\n";
            }
        }
        import("ORG.Net.Http");
        $filename  = $_REQUEST['table'].'.sql';
        Http::download ("$filename", '',$sql);
    }
    function execute() 
    {
    	$sql  = trim($_REQUEST['sql']);
        $db  =  DB::getInstance();
        if($db->isMainIps($sql)) {
            $result=    $db->_execute($sql);
            $type = 'execute';
        }else {
            $result=    $db->_query($sql);
            $type = 'query';
        }
        if(false !== $result) {
             if(!empty($_POST['label'])) {//保存SQL标签
                $dao = D("MemoDao");
                $map= new HashMap();
                $map->put('memo',$sql);
                $map->put('label',$_POST['label']);
                $map->put('createTime',time());
                $map->put('type','sql');
                $map->put('userId',Session::get(USER_AUTH_KEY));
                $dao->add($map);
            }
            if($type == 'query') {
                $fields = array_keys($result->get(0));
                $result->unshift($fields);
                foreach($result->getIterator() as $key=>$val) {
                	$val  = array_values($val);
                    $result->set($key,$val);
                }
                $result  =  $result->toArray();
                $this->ajaxReturn($result,'SQL执行成功！',1);
            }else {
                $this->success('SQL执行成功！');
            }
        }else {
            $this->error('SQL执行失败！');
        }
    }
    function getLabel() 
    {
    	if(!empty($_POST['id'])) {
    		$dao = D("MemoDao");
            $label   =  $dao->getById($_POST['id']);
            $this->ajaxReturn($label->memo,'标签获取成功！',1);
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