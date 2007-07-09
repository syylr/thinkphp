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

define('COOKIE_PREFIX',      'THINK_');
/**
 +------------------------------------------------------------------------------
 * Cookie 静态封装类 
 +------------------------------------------------------------------------------
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class Cookie extends Base
{
	// 判断Cookie是否存在
	function is_set($name) {
		return isset($_COOKIE[COOKIE_PREFIX][$name]);
	}

	// 获取某个Cookie值
	function get($name) {
		return $_COOKIE[COOKIE_PREFIX][$name];
	}

	// 设置某个Cookie值
	function set($name,$value,$expire='',$path='',$domain='') {
		if($expire=='') {
			$expire	=	C('COOKIE_EXPIRE');
		}
		if(empty($path)) {
			$path = C('COOKIE_PATH');
		}
		if(empty($domain)) {
			$domain	=	C('COOKIE_DOMAIN');
		}
		setcookie(COOKIE_PREFIX.'['.$name.']', $value,$expire,$path,$domain);
		$_COOKIE[COOKIE_PREFIX][$name]	=	$value;
	}

	// 删除某个Cookie值
	function delete($name) {
		setcookie(COOKIE_PREFIX.'['.$name.']', "", time() - 3600);
		unset($_COOKIE[COOKIE_PREFIX][$name]);
	}
	
	// 清空Cookie值
	function clear() {
		unset($_COOKIE[COOKIE_PREFIX]);
	}
}
?>