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
 * @package    Util
 * @link       http://www.fcs.org.cn
 * @copyright  Copyright (c) 2005-2006 liu21st.com.  All rights reserved. 
 * @author     liu21st <liu21st@gmail.com>
 * @version    $Id: Cache_Shmop.class.php 92 2006-11-11 08:47:04Z fcs $
 +------------------------------------------------------------------------------
 */

/**
 +------------------------------------------------------------------------------
 * 使用Shmop作为缓存类
 +------------------------------------------------------------------------------
 * @package   Util
 * @author    liu21st <liu21st@gmail.com>
 * @version   0.8.0
 +------------------------------------------------------------------------------
 */
class Cache_Shmop extends Cache
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
        if ( !extension_loaded('shmop') ) {    
            throw_exception('系统不支持shmop');
        }
        if(!empty($options)){
            $options = array(
                'size' => SHARE_MEM_SIZE,
                'tmp'  => TEMP_PATH,
                'project' => 's'
                );
        }
        $this->options = $options;
        $this->handler = $this->_ftok($this->options['project']);
        $this->type = strtoupper(substr(__CLASS__,6));

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
    function get($name = false)
    {
        $id = shmop_open($this->handler, 'c', 0600, 0);
        if ($id !== false) {
            $ret = unserialize(shmop_read($id, 0, shmop_size($id)));
            shmop_close($id);

            if ($name === false) {
                return $ret;
            }
            if(isset($ret[$name])) {
                $content   =  $ret[$name];
                if(DATA_CACHE_COMPRESS && function_exists('gzcompress')) {
                    //启用数据压缩
                    $content   =   gzuncompress($content);
                }
                return $content;
            }else {
            	return null;
            }
        }else {
            return false;
        }

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
    function set($name, $value)
    {
        $lh = $this->_lock();
        $val = $this->get();
        if (!is_array($val)) {
            $val = array();
        }
        if( DATA_CACHE_COMPRESS && function_exists('gzcompress')) {
            //数据压缩
            $value   =   gzcompress($value,3);
        }
        $val[$name] = $value;
        $val = serialize($val);
        return $this->_write($val, $lh);
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
    function rm($name)
    {
        $lh = $this->_lock();

        $val = $this->get();
        if (!is_array($val)) {
            $val = array();
        }
        unset($val[$name]);
        $val = serialize($val);

        return $this->_write($val, $lh);
    }


    /**
     +----------------------------------------------------------
     * 生成IPC key
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $project 项目标识名
     +----------------------------------------------------------
     * @return integer
     +----------------------------------------------------------
     */
    function _ftok($project)
    {
        if (function_exists('ftok')) {
            return ftok(__FILE__, $project);
        }
        if(strtoupper(PHP_OS) == 'WINNT'){
            $s = stat(__FILE__);
            return sprintf("%u", (($s['ino'] & 0xffff) | (($s['dev'] & 0xff) << 16) |
            (($project & 0xff) << 24)));
        }else {
            $filename = __FILE__ . (string) $project;
            for($key = array(); sizeof($key) < strlen($filename); $key[] = ord(substr($filename, sizeof($key), 1)));
            return dechex(array_sum($key));
        }

    }


    /**
     +----------------------------------------------------------
     * 写入操作
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $name 缓存变量名
     +----------------------------------------------------------
     * @return integer|boolen
     +----------------------------------------------------------
     */
    function _write(&$val, &$lh)
    {
        $id  = shmop_open($this->handler, 'c', 0600, $this->options['size']);
        if ($id) {
           $ret = shmop_write($id, $val, 0) == strlen($val);
           shmop_close($id);
           $this->_unlock($lh);
           return $ret;
        }

        $this->_unlock($lh);
        return false;
    }


    /**
     +----------------------------------------------------------
     * 共享锁定
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $name 缓存变量名
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
    function &_lock()
    {
        if (function_exists('sem_get')) {
            $fp = sem_get($this->handler, 1, 0600, 1);
            sem_acquire ($fp);
        } else {
            $fp = fopen($this->options['tmp'].$this->prefix.md5($this->handler), 'w');
            flock($fp, LOCK_EX);
        }

        return $fp;
    }


    /**
     +----------------------------------------------------------
     * 解除共享锁定
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $name 缓存变量名
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
    function _unlock(&$fp)
    {
        if (function_exists('sem_release')) {
            sem_release($fp);
        } else {
            fclose($fp);
        }
    }

}//类定义结束
?>