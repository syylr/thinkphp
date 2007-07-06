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
 * @version    $Id: Cache_Db.class.php 102 2006-11-14 10:15:09Z fcs $
 +------------------------------------------------------------------------------
 */

/**
 +------------------------------------------------------------------------------
 * 数据库类型缓存类
     CREATE TABLE FCS_CACHE (
       id int(11) unsigned NOT NULL auto_increment,
       cachekey varchar(255) NOT NULL,
       expire int(11) NOT NULL,
       data blob,
       datasize int(11),
       datacrc int(32),
       PRIMARY KEY (id)
     );
 +------------------------------------------------------------------------------
 * @package   Util
 * @author    liu21st <liu21st@gmail.com>
 * @version   0.8.0
 +------------------------------------------------------------------------------
 */
class Cache_Db extends Cache
{//类定义开始

    /**
     +----------------------------------------------------------
     * 缓存数据库对象 采用数据库方式有效
     +----------------------------------------------------------
     * @var string
     * @access protected
     +----------------------------------------------------------
     */
    var $db     ;

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
        if(empty($options)){
            $options= array
            (
                'db'        => DB_NAME,
                'table'     => DATA_CACHE_TABLE,
                'expire'    => DATA_CACHE_TIME,
            );
        }
        $this->options = $options;
        $this->db  = DB::getInstance();
        $this->handler = $this->db->connect();
        $this->connected = is_resource($this->handler);
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
        $name  =  addslashes($name);
        $result  =  $this->db->getRow('select `data`,`datacrc`,`datasize` from `'.$this->options['table'].'` where `cachekey`=\''.$name.'\' and `expire` !=-1 and `expire`<'.time().' limit 0,1');
        if(false !== $result ) {
            if(is_object($result)) {
            	$result  =  get_object_vars($result);
            }
            if(DATA_CACHE_CHECK) {//开启数据校验
                if($result['datacrc'] != md5($result['data'])) {//校验错误
                    return false;
                }
            }
            $content   =  $result['data'];
            if(DATA_CACHE_COMPRESS && function_exists('gzcompress')) {
                //启用数据压缩
                $content   =   gzuncompress($content);
            }
            $content    =   unserialize($content);
            return $content;
        }
        else {
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
     * @param integer $expire  有效时间（秒）
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
    function set($name, $value,$expireTime=0)
    {
        $data   =   serialize($value);
        $name  =  addslashes($name);
        if( DATA_CACHE_COMPRESS && function_exists('gzcompress')) {
            //数据压缩
            $data   =   gzcompress($data,3);
        }
        if(DATA_CACHE_CHECK) {//开启数据校验
        	$crc  =  md5($data);
        }else {
        	$crc  =  '';
        }
        $expire =  !empty($expireTime)? $expireTime : $this->options['expire'];
        $map    = new HashMap();
        $map->put('cachekey',$name);
        $map->put('data',$data);
        $map->put('datacrc',$crc);
        $map->put('expire',($expireTime==-1)?-1: (time()+$expire) );//缓存有效期为－1表示永久缓存
        $map->put('datasize',strlen($data));
        $result  =  $this->db->getRow('select `id` from `'.$this->options['table'].'` where `cachekey`=\''.$name.'\' limit 0,1');
        if(false !== $result ) {
        	//更新记录
            $result  =  $this->db->save($map,$this->options['table'],'`cachekey`=\''.$name.'\'');
        }else {
        	//新增记录
             $result  =  $this->db->add($map,$this->options['table']);
        }
        if($result) {
            return true;
        }else {
        	return false;
        }
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
        $name  =  addslashes($name);
        return $this->db->_execute('delete from `'.$this->options['table'].'` where `cachekey`=\''.$name.'\'');
    }

    /**
     +----------------------------------------------------------
     * 清除缓存
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
    function clear()
    {
        return $this->db->_execute('truncate table `'.$this->options['table'].'`');
    }

}//类定义结束
?>