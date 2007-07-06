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
 * @version    $Id: ConfigAction.class.php 2 2007-01-03 07:52:09Z liu21st $
 +------------------------------------------------------------------------------
 */

import('@.Action.AdminAction');
/**
 +------------------------------------------------------------------------------
 * 系统配置管理
 +------------------------------------------------------------------------------
 * @package   core
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Ver$
 +------------------------------------------------------------------------------
 */
class ConfigAction extends AdminAction
{//类定义开始

    function saveConfig() 
    {
        $dao = new ConfigDao();
    	foreach($_POST as $key=>$val) {
            $config    = Array();
            $config['value']  =  $val;
            $where =  "name='".$key."'";
    		$dao->save($config,'',$where);
    	}
        $this->success('配置修改成功！');
    }

    function _operation() 
    {
        $dao = new ConfigDao();
        if(!empty($_POST['id'])) {
        	$result = $dao->find("name='".$_POST['name']."' and id !='".$_POST['id']."'");
        }else {
        	$result = $dao->find("name='".$_POST['name']."'");
        }
        if($result) {
        	$this->assign("error",'配置项已经存在！');
            $this->forward();
        }	    	
    }
    /**
     +----------------------------------------------------------
     * 系统配置列表
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
	function build() 
	{
		//生成配置参数的定义文件
		$config	= &new Config();
		$result  = $config->parse("@.Dao.ConfigDao",'dao');
		$result->toConst(CONFIG_PATH.'Chat/_config.php','conf_');
        copy(CONFIG_PATH.'Chat/_config.php',CONFIG_PATH.'Admin/_config.php');
		$this->assign("jumpUrl",$this->getReturnUrl());
        $this->assign("message",'配置文件已经刷新');
		$this->forward();
	}
}//类定义结束
?>