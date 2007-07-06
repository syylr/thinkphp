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
// $Id: Config_Array.class.php 11 2007-01-04 03:57:34Z liu21st $

import('FCS.Util.Config');
/**
 +------------------------------------------------------------------------------
 * 数组配置文件类
 +------------------------------------------------------------------------------
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id: Config_Array.class.php 11 2007-01-04 03:57:34Z liu21st $
 +------------------------------------------------------------------------------
 */
class Config_Array extends Config
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
        if(is_array($config)) {
            $this->_config = $config;
            $this->_connect = true;        	
        }elseif(is_file($config)) {
            $this->_config = include $config;
            $this->_connect = true;
        }
        else
        {
            $this->_connect = false;
        }

    }

    /**
     +----------------------------------------------------------
     * 是否正常加载配置文件
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function connect() 
    {
        return $this->_connect;
    }


}//类定义结束
?>