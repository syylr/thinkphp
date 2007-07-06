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
// $Id: Cache_Memcache.class.php 11 2007-01-04 03:57:34Z liu21st $

/**
 +------------------------------------------------------------------------------
 * Memcache缓存类
 +------------------------------------------------------------------------------
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id: Cache_Memcache.class.php 11 2007-01-04 03:57:34Z liu21st $
 +------------------------------------------------------------------------------
 */
class Cache_Memcache extends Cache
{//类定义开始


    /**
     +----------------------------------------------------------
     * 架构函数
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     */
    function __construct($options)
    {
        if ( !extension_loaded('memcache') ) {    
            throw_exception('系统不支持memcache');
        }
        if(empty($options)) {
            $options = array
            (
                'host'  => '127.0.0.1',
                'port'  => 11211,
                'timeout' => false,
                'persistent' => false
            );
        }
        $func = $options['persistent'] ? 'pconnect' : 'connect';
        $this->expire = isset($options['expire'])?$options['expire']:DATA_CACHE_TIME;
        $this->handler  = &new Memcache;
        $this->connected = $options['timeout'] === false ?
            $this->handler->$func($host, $port) :
            $this->handler->$func($host, $port, $timeout);
        $this->type = strtoupper(substr(__CLASS__,6));
    }

    /**
     +----------------------------------------------------------
     * 是否连接
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
    function isConnected()
    {
        return $this->connected;
    }

    /**
     +----------------------------------------------------------
     * 读取缓存
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $name 缓存变量名
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    function get($name)
    {
        return $this->handler->get($name);
    }

    /**
     +----------------------------------------------------------
     * 写入缓存
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $name 缓存变量名
     * @param mixed $value  存储数据
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
    function set($name, $value, $ttl = null)
    {
        if(isset($ttl) && is_int($ttl))
            $expire = $ttl;
        else 
            $expire = $this->expire;
        return $this->handler->set($name, $value, 0, $expire);
    }

    /**
     +----------------------------------------------------------
     * 删除缓存
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $name 缓存变量名
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
    function rm($name, $ttl = false)
    {
        return $ttl === false ? 
            $this->handler->delete($name) :
            $this->handler->delete($name, $ttl);
    }

}//类定义结束
?>