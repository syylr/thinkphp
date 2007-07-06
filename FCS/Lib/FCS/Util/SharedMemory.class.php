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
 * 共享内存类
 +------------------------------------------------------------------------------
 * @package   Util
 * @author    liu21st <liu21st@gmail.com>
 * @version   0.8.0
 +------------------------------------------------------------------------------
 */

class SharedMemory extends Base
{//类定义开始

    /**
     +----------------------------------------------------------
     * 是否连接
     +----------------------------------------------------------
     * @var string
     * @access protected
     +----------------------------------------------------------
     */
    var $connected  ;

    /**
     +----------------------------------------------------------
     * 操作句柄
     +----------------------------------------------------------
     * @var string
     * @access protected
     +----------------------------------------------------------
     */
    var $handler    ;

    /**
     +----------------------------------------------------------
     * 共享内存存储前缀
     +----------------------------------------------------------
     * @var string
     * @access protected
     +----------------------------------------------------------
     */
    var $prefix='~@';

    /**
     +----------------------------------------------------------
     * 共享内存连接参数
     +----------------------------------------------------------
     * @var integer
     * @access protected
     +----------------------------------------------------------
     */
    var $options = array();

    /**
     +----------------------------------------------------------
     * 共享内存类型
     +----------------------------------------------------------
     * @var integer
     * @access protected
     +----------------------------------------------------------
     */
    var $type       ;

    /**
     +----------------------------------------------------------
     * 缓存过期时间
     +----------------------------------------------------------
     * @var integer
     * @access protected
     +----------------------------------------------------------
     */
    var $expire     ;


    /**
     +----------------------------------------------------------
     * 架构函数
     * 
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     */
    function __construct()
    {
    }

    /**
     +----------------------------------------------------------
     * 连接共享内存
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $type 共享内存类型
     * @param array $options  配置数组
     +----------------------------------------------------------
     * @return object
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function connect($type='',$options=array())
    {
        if(empty($type)){
            $type = SharedMemory::getSharedMemoryType();
        }
        $shareMemPath = dirname(__FILE__).'/SharedMemory/';
        $smClass = 'SharedMemory_'.ucwords(strtolower($type));
        if(file_exists($shareMemPath.$smClass.'.class.php')){
            include_once($shareMemPath.$smClass.'.class.php');
        }
        if(class_exists($smClass)){
            $sm = &new $smClass($options);
        }else {
            throw_exception('无法加载共享内存');
        }
        return $sm;
    }

    /**
     +----------------------------------------------------------
     * 取得共享内存实例
     * 
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function getInstance() 
    {
        return get_instance_of(__CLASS__,'connect');
    }

    /**
     +----------------------------------------------------------
     * 取得共享内存方式
     * 如果没有定义使用的共享内存类型，则使用该方法自动获取支持
     * 的共享内存类型，支持的类型有：
     * 'Eaccelerator',   // Eaccelerator (Turck MMcache fork)
     * 'Mmcache',        // Turck MMCache
     * 'Memcache',       // Memched
     * 'Shmop',          // Shmop
     * 'Apc',            // APC
     * 'Apachenote',     // Apache note
     * 'Sqlite',         // SQLite
     * 'File',           // Plain text
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function getSharedMemoryType()
    {
        $detect = array(
            'file'         => 'File' ,          // Plain text
            'eaccelerator' => 'Eaccelerator',   // Eaccelerator (Turck MMcache fork)
            'mmcache'      => 'Mmcache',        // Turck MMCache
            'Memcache'     => 'Memcache',       // Memched
            'shmop_open'   => 'Shmop',          // Shmop
            'apc_fetch'    => 'Apc',            // APC
            'apache_note'  => 'Apachenote',     // Apache note
            'sqlite_open'  => 'Sqlite',         // SQLite

        );
       if(USE_SHARE_MEM && in_array(SHARE_MEM_TYPE,$detect) ){
           //使用设定的共享内存方式
           return SHARE_MEM_TYPE;
       }
       //如果没有设置或者设置错误自动侦测
       foreach ($detect as $func=>$val) {
            if (function_exists($func) || class_exists($func))
                return $val;
        }
    }

}//类定义结束
?>