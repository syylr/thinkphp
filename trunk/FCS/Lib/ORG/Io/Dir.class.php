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

import("FCS.Util.ListIterator");
/**
 +------------------------------------------------------------------------------
 * DirectoryIterator实现类 PHP5以上内置了DirectoryIterator类
 +------------------------------------------------------------------------------
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class Dir extends ListIterator 
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
                    $dir[$i]['ext']      =  is_file($path)?strtolower(substr(strrchr($file, '.'),1)):'';
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
            $cmp_func = create_function('$a,$b','
            $k  =  "isDir";  
            if($a[$k]  ==  $b[$k])  return  0;  
            return  $a[$k]>$b[$k]?-1:1;
            ');
            usort($dir,$cmp_func);  
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

    /**
     +----------------------------------------------------------
     * 判断目录是否为空
     +----------------------------------------------------------
     * @access static 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    function is_empty($directory)
    {
        $handle = opendir($directory);
        while (($file = readdir($handle)) !== false)
        {
            if ($file != "." && $file != "..")
            {
                closedir($handle);
                return false;
            }
        }
        closedir($handle);
        return true;
    }

    /**
     +----------------------------------------------------------
     * 判断目录是否为空
     +----------------------------------------------------------
     * @access static 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    function getList($directory)
    {
        return scandir($directory);
    }

    /**
     +----------------------------------------------------------
     * 删除目录（包括下面的文件）
     +----------------------------------------------------------
     * @access static 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    function delDir($directory,$subdir=true)
    {
        if (is_dir($directory) == false)
        {
            exit("The Directory Is Not Exist!");
        }
        $handle = opendir($directory);
        while (($file = readdir($handle)) !== false)
        {
            if ($file != "." && $file != "..")
            {
            is_dir("$directory/$file")? 
                Dir::del("$directory/$file"):
                unlink("$directory/$file");
            }
        }
        if (readdir($handle) == false)
        {
            closedir($handle);
            rmdir($directory);
        }
    }

    /**
     +----------------------------------------------------------
     * 删除目录下面的所有文件，但不删除目录
     +----------------------------------------------------------
     * @access static 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    function del($directory)
    {
        if (is_dir($directory) == false)
        {
            exit("The Directory Is Not Exist!");
        }
        $handle = opendir($directory);
        while (($file = readdir($handle)) !== false)
        {
            if ($file != "." && $file != ".." && is_file("$directory/$file"))
            {
                unlink("$directory/$file");
            }
        }
        closedir($handle);
    }

    /**
     +----------------------------------------------------------
     * 复制目录
     +----------------------------------------------------------
     * @access static 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    function copy_dir($source, $destination)
    {
        if (is_dir($source) == false)
        {
            exit("The Source Directory Is Not Exist!");
        }
        if (is_dir($source) == false)
        {
            mkdir($destination, 0700);
        }
        $handle=opendir($source);
        while (false !== ($file = readdir($handle)))
        {
            if ($file != "." && $file != "..")
            {
                is_dir("$source/$file")?
                Dir::copy_dir("$source/$file", "$destination/$file"):
                copy("$source/$file", "$destination/$file");
            }
        }
        closedir($handle);
    }

}//类定义结束

?>