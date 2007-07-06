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

if(!class_exists('DirectoryIterator')){//PHP5以上内置了DirectoryIterator类，不需要重新定义

    import("FCS.Util.ListIterator");
    /**
     +------------------------------------------------------------------------------
     * DirectoryIterator实现类 PHP5以上内置了DirectoryIterator类
     +------------------------------------------------------------------------------
     * @package   Util
     * @author    liu21st <liu21st@gmail.com>
     * @version   0.8.0
     +------------------------------------------------------------------------------
     */
    class DirectoryIterator extends ListIterator 
    {//类定义开始

        /**
         +----------------------------------------------------------
         * 目录数组
         +----------------------------------------------------------
         * @var array
         * @access protected
         +----------------------------------------------------------
         */
        var $_dir = array();

        /**
         +----------------------------------------------------------
         * 架构函数
         * 
         +----------------------------------------------------------
         * @access public 
         +----------------------------------------------------------
         * @param string $path  目录路径
         +----------------------------------------------------------
         */
        function __construct($path)
        {
            if(substr($path, -1) != "/")    $path .= "/";
            $this->listFile($path);
            parent::__construct($this->_dir);
        }

        /**
         +----------------------------------------------------------
         * 取得目录下面的文件信息
         * 
         +----------------------------------------------------------
         * @access public 
         +----------------------------------------------------------
         * @param mixed $pathname 路径
         +----------------------------------------------------------
         */
        function listFile($pathname) 
        {
            static $ListDirs = array();
            if(!isset($ListDirs[$pathname])){
                $handle = opendir($pathname);
                $i =0;
                $dir = array();
                while ( false !== ($file = readdir($handle)) ) {
                    if ($file != "." && $file != "..") {
                        $path = $pathname . $file;
                        $dir[$i]['filename']    = $file;
                        $dir[$i]['pathname']    = realpath($path);
                        $dir[$i]['owner']        = fileowner($path);
                        $dir[$i]['perms']        = fileperms($path);
                        $dir[$i]['inode']        = fileinode($path);
                        $dir[$i]['group']        = filegroup($path);
                        $dir[$i]['path']        = dirname($path);
                        $dir[$i]['atime']        = fileatime($path);
                        $dir[$i]['ctime']        = filectime($path);
                        $dir[$i]['size']        = filesize($path);
                        $dir[$i]['type']        = filetype($path);
                        $dir[$i]['mtime']        = filemtime($path);
                        $dir[$i]['isDir']        = is_dir($path);
                        $dir[$i]['isFile']        = is_file($path);
                        $dir[$i]['isLink']        = is_link($path);
                        //$dir[$i]['isExecutable']= function_exists('is_executable')?is_executable($path):'';
                        $dir[$i]['isReadable']    = is_readable($path);
                        $dir[$i]['isWritable']    = is_writable($path);
                    }
                    $i++;
                }
                closedir($handle);
                $this->_dir = $dir;
                $ListDirs[$pathname] = $dir;
            }else{
                $this->_dir = $ListDirs[$pathname];
            }
        }

        /**
         +----------------------------------------------------------
         * 文件上次访问时间
         * 
         +----------------------------------------------------------
         * @access public 
         +----------------------------------------------------------
         * @return integer
         +----------------------------------------------------------
         */
        function getATime()
        {
            $current = $this->current($this->_dir);
            return $current['atime'];
        }

        /**
         +----------------------------------------------------------
         * 取得文件的 inode 修改时间
         * 
         +----------------------------------------------------------
         * @access public 
         +----------------------------------------------------------
         * @return integer
         +----------------------------------------------------------
         */
        function getCTime()
        {
            $current = $this->current($this->_dir);
            return $current['ctime'];
        }

        /**
         +----------------------------------------------------------
         * 遍历子目录文件信息
         * 
         +----------------------------------------------------------
         * @access public 
         +----------------------------------------------------------
         * @return DirectoryIterator
         +----------------------------------------------------------
         */
        function getChildren()
        {
            $current = $this->current($this->_dir);
            if($current['isDir']){
                return new DirectoryIterator($current['pathname']);
            }
            return false;
        }

        /**
         +----------------------------------------------------------
         * 取得文件名
         * 
         +----------------------------------------------------------
         * @access public 
         +----------------------------------------------------------
         * @return string
         +----------------------------------------------------------
         */
        function getFilename()
        {
            $current = $this->current($this->_dir);
            return $current['filename'];
        }

        /**
         +----------------------------------------------------------
         * 取得文件的组
         * 
         +----------------------------------------------------------
         * @access public 
         +----------------------------------------------------------
         * @return integer
         +----------------------------------------------------------
         */
        function getGroup()
        {
            $current = $this->current($this->_dir);
            return $current['group'];
        }

        /**
         +----------------------------------------------------------
         * 取得文件的 inode
         * 
         +----------------------------------------------------------
         * @access public 
         +----------------------------------------------------------
         * @return integer
         +----------------------------------------------------------
         */
        function getInode()
        {
            $current = $this->current($this->_dir);
            return $current['inode'];
        }

        /**
         +----------------------------------------------------------
         * 取得文件的上次修改时间
         * 
         +----------------------------------------------------------
         * @access public 
         +----------------------------------------------------------
         * @return integer
         +----------------------------------------------------------
         */
        function getMTime()
        {
            $current = $this->current($this->_dir);
            return $current['mtime'];
        }
        
        /**
         +----------------------------------------------------------
         * 取得文件的所有者
         * 
         +----------------------------------------------------------
         * @access public 
         +----------------------------------------------------------
         * @return string
         +----------------------------------------------------------
         */
        function getOwner()
        {
            $current = $this->current($this->_dir);
            return $current['owner'];
        }

        /**
         +----------------------------------------------------------
         * 取得文件路径，不包括文件名
         * 
         +----------------------------------------------------------
         * @access public 
         +----------------------------------------------------------
         * @return string
         +----------------------------------------------------------
         */
        function getPath()
        {
            $current = $this->current($this->_dir);
            return $current['path'];
        }

        /**
         +----------------------------------------------------------
         * 取得文件的完整路径，包括文件名
         * 
         +----------------------------------------------------------
         * @access public 
         +----------------------------------------------------------
         * @return string
         +----------------------------------------------------------
         */
        function getPathname()
        {
            $current = $this->current($this->_dir);
            return $current['pathname'];
        }

        /**
         +----------------------------------------------------------
         * 取得文件的权限
         * 
         +----------------------------------------------------------
         * @access public 
         +----------------------------------------------------------
         * @return integer
         +----------------------------------------------------------
         */
        function getPerms()
        {
            $current = $this->current($this->_dir);
            return $current['perms'];
        }

        /**
         +----------------------------------------------------------
         * 取得文件的大小
         * 
         +----------------------------------------------------------
         * @access public 
         +----------------------------------------------------------
         * @return integer
         +----------------------------------------------------------
         */
        function getSize()
        {
            $current = $this->current($this->_dir);
            return $current['size'];
        }

        /**
         +----------------------------------------------------------
         * 取得文件类型
         * 
         +----------------------------------------------------------
         * @access public 
         +----------------------------------------------------------
         * @return string
         +----------------------------------------------------------
         */
        function getType()
        {
            $current = $this->current($this->_dir);
            return $current['type'];
        }

        /**
         +----------------------------------------------------------
         * 是否为目录
         * 
         +----------------------------------------------------------
         * @access public 
         +----------------------------------------------------------
         * @return boolen
         +----------------------------------------------------------
         */
        function isDir()
        {
            $current = $this->current($this->_dir);
            return $current['isDir'];
        }

        /**
         +----------------------------------------------------------
         * 是否为文件
         * 
         +----------------------------------------------------------
         * @access public 
         +----------------------------------------------------------
         * @return boolen
         +----------------------------------------------------------
         */
        function isFile()
        {
            $current = $this->current($this->_dir);
            return $current['isFile'];
        }

        /**
         +----------------------------------------------------------
         * 文件是否为一个符号连接
         * 
         +----------------------------------------------------------
         * @access public 
         +----------------------------------------------------------
         * @return boolen
         +----------------------------------------------------------
         */
        function isLink()
        {
            $current = $this->current($this->_dir);
            return $current['isLink'];
        }


        /**
         +----------------------------------------------------------
         * 文件是否可以执行
         * 
         +----------------------------------------------------------
         * @access public 
         +----------------------------------------------------------
         * @return boolen
         +----------------------------------------------------------
         */
        function isExecutable()
        {
            $current = $this->current($this->_dir);
            return $current['isExecutable'];
        }


        /**
         +----------------------------------------------------------
         * 文件是否可读
         * 
         +----------------------------------------------------------
         * @access public 
         +----------------------------------------------------------
         * @return boolen
         +----------------------------------------------------------
         */
        function isReadable()
        {
            $current = $this->current($this->_dir);
            return $current['isReadable'];
        }

        /**
         +----------------------------------------------------------
         * 获取foreach的遍历方式
         * 
         +----------------------------------------------------------
         * @access public 
         +----------------------------------------------------------
         * @return string
         +----------------------------------------------------------
         */
        function getIterator()
        {
             return new ArrayObject($this->_dir);
        }

    }//类定义结束
}
?>