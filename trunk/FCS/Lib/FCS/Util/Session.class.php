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

define('SESSION_PREFIX',      'FCS_');
define("HTTP_SESSION_STARTED",      1);
define("HTTP_SESSION_CONTINUED",    2);
/**
 +------------------------------------------------------------------------------
 * Session管理类
 +------------------------------------------------------------------------------
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class Session extends Base
{//类定义开始


    /**
     +----------------------------------------------------------
     * 启动Session
     * 
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @param string $name session名称 默认为SessionID
     * @param string $id   session id
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    function start($name = 'SessionID', $id = null,$path='',$callback='')
    {
		Session::name($name);

        if (is_null(Session::detectID())) {
            Session::id($id ? $id : uniqid(dechex(mt_rand())));
        }

        if(!empty($callback)) Session::setCallback($callback);
        if(!empty($path)) Session::path($path);
        session_start();
        if (!isset($_SESSION['__HTTP_Session_Info'])) {
            $_SESSION['__HTTP_Session_Info'] = HTTP_SESSION_STARTED;
        } else {
            $_SESSION['__HTTP_Session_Info'] = HTTP_SESSION_CONTINUED;
        }
        Session::setExpire(SESSION_EXPIRE);
    }

    /**
     +----------------------------------------------------------
     * 暂停Session
     * 
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    function pause()
    {
        session_write_close();
    }


    /**
     +----------------------------------------------------------
     * 清空Session
     * 
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    function clearLocal()
    {
        $local = Session::localName();
        unset($_SESSION[$local]);
    }

    /**
     +----------------------------------------------------------
     * 清空Session
     * 
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    function clear()
    {
		session_destroy();
        unset($_SESSION);
    }


    /**
     +----------------------------------------------------------
     * 销毁Session
     * 
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    function destroy()
    {
        Session::clear();
        session_destroy();
    }


    /**
     +----------------------------------------------------------
     * 检测SessionID 
     * 
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    function detectID()
    {
        if (Session::useCookies()) {
            if (isset($_COOKIE[Session::name()])) {
                return $_COOKIE[Session::name()];
            }
        } else {
            if (isset($_GET[Session::name()])) {
                return $_GET[Session::name()];
            }
            if (isset($_POST[Session::name()])) {
                return $_POST[Session::name()];
            }
        }
        return null;
    }

    /**
     +----------------------------------------------------------
     * 设置或者获取当前Session name
     * 
     +----------------------------------------------------------
     * @param string $name session名称 
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @return string 返回之前的Session name
     +----------------------------------------------------------
     */
    function name($name = null)
    {
        return isset($name) ? session_name($name) : session_name();
    }

    /**
     +----------------------------------------------------------
     * 设置或者获取当前SessionID
     * 
     +----------------------------------------------------------
     * @param string $id sessionID
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @return void 返回之前的sessionID
     +----------------------------------------------------------
     */
    function id($id = null)
    {
        return isset($id) ? session_id($id) : session_id();
    }

    /**
     +----------------------------------------------------------
     * 设置或者获取当前Session保存路径
     * 
     +----------------------------------------------------------
     * @param string $path 保存路径名
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    function path($path = null) 
    {
        return isset($path)? session_save_path($path):session_save_path();
    }

    /**
     +----------------------------------------------------------
     * 设置Session 过期时间
     * 
     +----------------------------------------------------------
     * @param integer $time 过期时间 
     * @param boolean $add  是否为增加时间 
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    function setExpire($time, $add = false)
    {
        if ($add) {
            if (!isset($_SESSION['__HTTP_Session_Expire_TS'])) {
                $_SESSION['__HTTP_Session_Expire_TS'] = time() + $time;
            }

            // update session.gc_maxlifetime
            $currentGcMaxLifetime = Session::setGcMaxLifetime(null);
            Session::setGcMaxLifetime($currentGcMaxLifetime + $time);

        } elseif (!isset($_SESSION['__HTTP_Session_Expire_TS'])) {
                $_SESSION['__HTTP_Session_Expire_TS'] = $time;
        }
    }

    /**
     +----------------------------------------------------------
     * 设置Session 闲置时间
     * 
     +----------------------------------------------------------
     * @param integer $time 闲置时间 
     * @param boolean $add  是否为增加时间 
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    function setIdle($time, $add = false)
    {
        if ($add) {
            $_SESSION['__HTTP_Session_Idle'] = $time;
        } else {
            $_SESSION['__HTTP_Session_Idle'] = $time - time();
        }
    }

    /**
     +----------------------------------------------------------
     * 取得Session 有效时间
     * 
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    function sessionValidThru()
    {
        if (!isset($_SESSION['__HTTP_Session_Idle_TS']) || !isset($_SESSION['__HTTP_Session_Idle'])) {
            return 0;
        } else {
            return $_SESSION['__HTTP_Session_Idle_TS'] + $_SESSION['__HTTP_Session_Idle'];
        }
    }

    /**
     +----------------------------------------------------------
     * 检查Session 是否过期
     * 
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    function isExpired()
    {
        if (isset($_SESSION['__HTTP_Session_Expire_TS']) && $_SESSION['__HTTP_Session_Expire_TS'] < time()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     +----------------------------------------------------------
     * 检查Session 是否闲置
     * 
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    function isIdle()
    {
        if (isset($_SESSION['__HTTP_Session_Idle_TS']) && (($_SESSION['__HTTP_Session_Idle_TS'] + $_SESSION['__HTTP_Session_Idle']) < time())) {
            return true;
        } else {
            return false;
        }
    }

    /**
     +----------------------------------------------------------
     * 更新Session 闲置时间
     * 
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    function updateIdle()
    {
        $_SESSION['__HTTP_Session_Idle_TS'] = time();
    }

    /**
     +----------------------------------------------------------
     * 设置Session 对象反序列化时候的回调函数
     * 返回之前设置
     +----------------------------------------------------------
     * @param string $callback  回调函数方法名 
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    function setCallback($callback = null)
    {
        $return = ini_get('unserialize_callback_func');
        if (isset($callback)) {
            ini_set('unserialize_callback_func',$callback);
        }
        return $return;
    }


    /**
     +----------------------------------------------------------
     * 设置Session 是否使用cookie
     * 返回之前设置
     +----------------------------------------------------------
     * @param boolean $useCookies  是否使用cookie 
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    function useCookies($useCookies = null)
    {
        $return = ini_get('session.use_cookies') ? true : false;
        if (isset($useCookies)) {
            ini_set('session.use_cookies', $useCookies ? 1 : 0);
        }
        return $return;
    }

    /**
     +----------------------------------------------------------
     * 检查Session 是否新建
     * 
     +----------------------------------------------------------
     * @param boolean $useCookies  是否使用cookie 
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    function isNew()
    {
        return !isset($_SESSION['__HTTP_Session_Info']) ||
            $_SESSION['__HTTP_Session_Info'] == HTTP_SESSION_STARTED;
    }


    /**
     +----------------------------------------------------------
     * 取得当前项目的Session 值
     * 返回之前设置
     +----------------------------------------------------------
     * @param string $name   
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    function getLocal($name)
    {
        $local = Session::localName();
        if (!is_array($_SESSION[$local])) {
            $_SESSION[$local] = array();
        }
        return $_SESSION[$local][$name];
    }

    /**
     +----------------------------------------------------------
     * 取得当前项目的Session 值
     * 返回之前设置
     +----------------------------------------------------------
     * @param string $name   
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    function get($name)
    {
        if(isset($_SESSION[$name])) {
        	return $_SESSION[$name];
        }else {
        	return null;
        }
    }

    /**
     +----------------------------------------------------------
     * 设置当前项目的Session 值
     * 返回之前设置
     +----------------------------------------------------------
     * @param string $name   
     * @param mixed  $value   
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    function setLocal($name, $value)
    {
        $local = Session::localName();
        if (!is_array($_SESSION[$local])) {
            $_SESSION[$local] = array();
        }
        if (null === $value) {
            unset($_SESSION[$local][$name]);
        } else {
            $_SESSION[$local][$name] = $value;
        }
        return;
    }

    /**
     +----------------------------------------------------------
     * 设置当前项目的Session 值
     * 返回之前设置
     +----------------------------------------------------------
     * @param string $name   
     * @param mixed  $value   
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    function set($name, $value)
    {
        if (null === $value) {
            unset($_SESSION[$name]);
        } else {
            $_SESSION[$name] = $value;
        }
        return ;
    }

    /**
     +----------------------------------------------------------
     * 检查Session 值是否已经设置
     * 
     +----------------------------------------------------------
     * @param string $name   
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    function is_setLocal($name)
    {
        $local = Session::localName();
        return isset($_SESSION[$local][$name]);
    }

    /**
     +----------------------------------------------------------
     * 检查Session 值是否已经设置
     * 
     +----------------------------------------------------------
     * @param string $name   
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    function is_set($name)
    {
        return isset($_SESSION[$name]);
    }

    /**
     +----------------------------------------------------------
     * 设置或者获取 Session localname
     * 
     +----------------------------------------------------------
     * @param string $name   
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    function localName($name = null)
    {
        $return = (isset($GLOBALS['__HTTP_Session_Localname'])) ? $GLOBALS['__HTTP_Session_Localname'] : null;
        if (!empty($name)) {
            $GLOBALS['__HTTP_Session_Localname'] = md5($name);
        }
        return $return;
    }

    /**
     +----------------------------------------------------------
     * Session 初始化
     * 
     +----------------------------------------------------------
     * @static
     * @access private 
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    function _init()
    {
        ini_set('session.auto_start', 0);
        //设置当前项目运行脚本作为Session本地名
        Session::localName(APP_NAME);
    }

    /**
     +----------------------------------------------------------
     * 设置Session use_trans_sid
     * 返回之前设置
     +----------------------------------------------------------
     * @param string $useTransSID   
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    function useTransSID($useTransSID = null)
    {
        $return = ini_get('session.use_trans_sid') ? true : false;
        if (isset($useTransSID)) {
            ini_set('session.use_trans_sid', $useTransSID ? 1 : 0);
        }
        return $return;
    }

    /**
     +----------------------------------------------------------
     * 设置Session cookie_domain
     * 返回之前设置
     +----------------------------------------------------------
     * @param string $sessionDomain   
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    function setCookieDomain($sessionDomain = null)
    {
        $return = ini_get('session.cookie_domain');
        if(!empty($sessionDomain)) {
            ini_set('session.cookie_domain', $sessionDomain);//跨域访问Session
        }
        return $return;
    }


    /**
     +----------------------------------------------------------
     * 设置Session gc_maxlifetime值
     * 返回之前设置
     +----------------------------------------------------------
     * @param string $gc_maxlifetime   
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    function setGcMaxLifetime($gcMaxLifetime = null)
    {
        $return = ini_get('session.gc_maxlifetime');
        if (isset($gcMaxLifetime) && is_int($gcMaxLifetime) && $gcMaxLifetime >= 1) {
            ini_set('session.gc_maxlifetime', $gcMaxLifetime);
        }
        return $return;
    }

    /**
     +----------------------------------------------------------
     * 设置Session gc_probability 值
     * 返回之前设置
     +----------------------------------------------------------
     * @param string $gc_maxlifetime   
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    function setGcProbability($gcProbability = null)
    {
        $return = ini_get('session.gc_probability');
        if (isset($gcProbability) && is_int($gcProbability) && $gcProbability >= 1 && $gcProbability <= 100) {
            ini_set('session.gc_probability', $gcProbability);
        }
        return $return;
    }

    /**
     +----------------------------------------------------------
     * 当前Session文件名
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    function getFilename() 
    {
        return Session::path().'/sess_'.session_id();
    }

}//类定义结束

Session::_init();
?>