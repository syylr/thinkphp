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
// $Id: Config_Dao.class.php 33 2007-02-25 07:06:02Z liu21st $

import('Think.Util.Config');
/**
 +------------------------------------------------------------------------------
 * 数据库配置文件类
 +------------------------------------------------------------------------------
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id: Config_Dao.class.php 33 2007-02-25 07:06:02Z liu21st $
 +------------------------------------------------------------------------------
 */
class Config_Dao extends Config
{//类定义开始

    /**
     +----------------------------------------------------------
     * 架构函数
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     */
    function __construct($config)
    {

        import($config);
		$daoClass	=	substr(strrchr($config, '.'),1);
        $configDao  =   new $daoClass();
        $configList =   $configDao->findAll();
        if($configList->isEmpty()) {
        	$this->_connect = false;
        }else {
            $this->_config = $configList->getCol('name,value');
            $this->_connect = true;
        }
    }

    /**
     +----------------------------------------------------------
     * 是否正常加载配置文件
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function connect() 
    {
        return $this->_connect;
    }


}//类定义结束
?>