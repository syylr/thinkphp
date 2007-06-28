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

/**
 +------------------------------------------------------------------------------
 * 语言包原理类
 +------------------------------------------------------------------------------
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class Language extends Base 
{

	// 语言包数组
	var $_lang = array();

	// 实例化
    function getInstance() 
    {
        return get_instance_of(__CLASS__);
    }

	// 加载语言文件
	// 支持php数组定义、常量定义
	function load($file,$return=false) {
		if(file_exists($file)) {
			$before = get_defined_constants();
			// 数组返回值方式定义
			$lang = include $file;
			if(!is_array($lang)) {
				if(isset($_lang)) {
					// 采用 $_lang['aaa']= value 方式
					$lang = &$_lang;
				}else{
					// 采用define('aaa',value) 方式
					$after  = get_defined_constants();
					$define = array_diff_assoc($after,$before);
					$lang = $define;
				}
			}
			$this->_lang = array_merge($this->_lang, array_change_key_case($lang));
			unset($lang);
			if($return) {
				return $this->_lang;
			}
		}else{
			return false;
		}
	}

	// 追加参数
	function append($array,$override=true) {
		if($override) {
			// 覆盖模式追加
			$this->_lang = array_merge($this->_lang,array_change_key_case($array));
		}else{
			$this->_lang = array_merge(array_change_key_case($array),$this->_lang);
		}
	}

	// 设置参数
	function set($name,$value) {
		$this->__set($name,$value);
	}

	// 获取参数
	function get($name) {
		return $this->__get($name);
	}

	function __set($name,$value) {
		$this->_lang[$name] = $value;
	}

	function __get($name) {
		return $this->_lang[$name];
	}
}
?>