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
 * 使用Sqlite作为共享内存类
 +------------------------------------------------------------------------------
 * @package   Util
 * @author    liu21st <liu21st@gmail.com>
 * @version   0.8.0
 +------------------------------------------------------------------------------
 */
class SharedMemory_Sqlite extends SharedMemory
{

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
        if ( !extension_loaded('sqlite') ) {    
            throw_exception('系统不支持sqlite');
        }
        if(empty($options)){
            $options= array
            (
                'db'        => ':memory:',
                'table'     => 'sharedmemory',
                'var'       => 'var',
                'value'     => 'value',
                'persistent'=> false
            );
        }
        $this->options = $options;
        $func = $this->options['persistent'] ? 'sqlite_popen' : 'sqlite_open';
        $this->handler = $func($this->options['db']);
        $this->connected = is_resource($this->handler);
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
        $name   = sqlite_escape_string($name);
        $sql = 'SELECT '.$this->options['value'].
               'FROM '.$this->options['table'].
               'WHERE '.$this->options['var'].'=\''.$name.'\''.
               'LIMIT 1';

        $result = sqlite_query($this->handler, $sql);
        if (sqlite_num_rows($result)) {
            return unserialize(sqlite_fetch_single($result));
        }
        return false;
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
        $name  = sqlite_escape_string($name);
        $value = sqlite_escape_string(serialize($value));
        $sql  = 'REPLACE INTO '.$this->options['table'].
                ' ('.$this->options['var'].', '.$this->options['value'].
                'VALUES (\''.$name.'\', \''.$value.'\')';

        sqlite_query($this->handler, $sql);
        return true;
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
        $name  = sqlite_escape_string($name);
        $sql  = 'DELETE FROM '.$this->options['table'].
               ' WHERE '.$this->options['var'].'=\''.$name.'\'';
        sqlite_query($this->handler, $sql);
        return true;
    }

}//类定义结束
?>