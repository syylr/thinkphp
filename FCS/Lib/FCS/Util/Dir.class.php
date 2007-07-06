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
 * @package    Io
 * @copyright  Copyright (c) 2005-2006 liu21st.com.  All rights reserved. 
 * @author     liu21st <liu21st@gmail.com>
 * @version    $Id: Dir.class.php 103 2006-11-14 10:17:28Z fcs $
 +------------------------------------------------------------------------------
 */

 /**
 +------------------------------------------------------------------------------
 * 目录操作类
 +------------------------------------------------------------------------------
 * @package   Io
 * @author    liu21st <liu21st@gmail.com>
 * @version   1.0.0
 +------------------------------------------------------------------------------
 */
class Dir extends Base
{//类定义开始

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
    function del($directory,$subdir=true)
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