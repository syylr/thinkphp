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
 * @version    $Id$
 +------------------------------------------------------------------------------
 */

/**
 +------------------------------------------------------------------------------
 * 文件类型共享内存类
 +------------------------------------------------------------------------------
 * @package   Util
 * @author    liu21st <liu21st@gmail.com>
 * @version   0.8.0
 +------------------------------------------------------------------------------
 */
class SharedMemory_File extends SharedMemory
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
        if(!empty($options['temp'])){
            $this->options['temp'] = $options['temp'];
        }else {
            $this->options['temp'] = TEMP_PATH;
        }
        $this->expire = isset($options['expire'])?$options['expire']:DATA_CACHE_TIME;
        if(!is_dir($this->options['temp'])){
            mkdir($this->options['temp']);
        }
        if(substr($this->options['temp'], -1) != "/")    $this->options['temp'] .= "/";
        $this->connected = is_dir($this->options['temp']) && is_writeable($this->options['temp']);
        $this->type = strtoupper(substr(__CLASS__,3));

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
     * 取得变量的存储文件名
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $name 共享内存变量名
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function filename($name)
    {
        return $this->options['temp'].$this->prefix.md5($name);
    }

    /**
     +----------------------------------------------------------
     * 验证共享内存是否有效
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $name 共享内存变量名
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
    function valid($name)
    {
        if (!$this->isConnected() || !file_exists($this->filename($name))) {
            return false;
        }
        if($this->expire == -1 || time() < filemtime($this->filename($name)) + $this->expire) { 
            //缓存是否过期
            return true;
        }else
            return false;
    }

    /**
     +----------------------------------------------------------
     * 读取共享内存
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $name 共享内存变量名
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    function get($name)
    {
        if (!$this->valid($name)) {
            return false;
        }
        //__DEBUG_START($name.'get');
        $filename   =   $this->filename($name);
        $content    =   file_get_contents($filename);
        if(false!== $content) {
            if(DATA_CACHE_COMPRESS && function_exists('gzcompress')) {
                $content   =   gzuncompress($content);
            }
            $content    =   unserialize($content);
            //__DEBUG_end($name.'get');
            return $content;
        }
        else {
            return false;
        }
    }

    /**
     +----------------------------------------------------------
     * 写入共享内存
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $name 共享内存变量名
     * @param mixed $value  存储数据
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
    function set($name, $value)
    {
        $filename   =   $this->filename($name);
        $data   =   serialize($value);
        if( DATA_CACHE_COMPRESS && function_exists('gzcompress')) {
            $data   =   gzcompress($data,3);
        }
        $result =   file_put_contents($filename,$data);
        if($result) {
            clearstatcache();
            return true;
        }else {
        	return false;
        }
    }

    /**
     +----------------------------------------------------------
     * 删除共享内存
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $name 共享内存变量名
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
    function rm($name)
    {
        return unlink($this->filename($name));
    }

}//类定义结束
?>