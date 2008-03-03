<?php 
// +----------------------------------------------------------------------
// | ThinkPHP                                                             
// +----------------------------------------------------------------------
// | Copyright (c) 2008 http://thinkphp.cn All rights reserved.      
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>                                  
// +----------------------------------------------------------------------
// $Id$

/**
 +------------------------------------------------------------------------------
 * Cookie管理类
 +------------------------------------------------------------------------------
 * @category   Think
 * @package  Think
 * @subpackage  Util
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class Cookie extends Base
{
	// 判断Cookie是否存在
	static function is_set($name) {
		if(C('COOKIE_PREFIX')) {
			return isset($_COOKIE[C('COOKIE_PREFIX')][$name]);
		}else{
			return isset($_COOKIE[$name]);
		}
	}

	// 获取某个Cookie值
	static function get($name) {
		if(C('COOKIE_PREFIX')) {
			return $_COOKIE[C('COOKIE_PREFIX')][$name];
		}else{
			return $_COOKIE[$name];
		}
	}

	// 设置某个Cookie值
	static function set($name,$value,$expire='',$path='',$domain='') {
		if($expire=='') {
			$expire	=	C('COOKIE_EXPIRE');
		}
		if(empty($path)) {
			$path = C('COOKIE_PATH');
		}
		if(empty($domain)) {
			$domain	=	C('COOKIE_DOMAIN');
		}
		if(C('COOKIE_PREFIX')) {
			setcookie(C('COOKIE_PREFIX').'['.$name.']', $value,time()+$expire,$path,$domain);
			$_COOKIE[C('COOKIE_PREFIX')][$name]	=	$value;
		}else{
			setcookie($name, $value,time()+$expire,$path,$domain);
			$_COOKIE[$name]	=	$value;
		}
	}

	// 删除某个Cookie值
	static function delete($name) {
		Cookie::set($name,'',time()-3600);
		if(C('COOKIE_PREFIX')) {
			unset($_COOKIE[C('COOKIE_PREFIX')][$name]);
		}else{
			unset($_COOKIE[$name]);
		}
	}
	
	// 清空Cookie值
	static function clear() {
		if(C('COOKIE_PREFIX')) {
			unset($_COOKIE[C('COOKIE_PREFIX')]);
		}else{
			unset($_COOKIE);
		}
	}
}
?>