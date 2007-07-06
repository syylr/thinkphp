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
 * @package    Vo
 * @link       http://www.fcs.org.cn
 * @copyright  Copyright (c) 2005-2006 liu21st.com.  All rights reserved. 
 * @author     liu21st <liu21st@gmail.com>
 * @version    $Id: FileVo.class.php 9 2007-01-03 09:32:19Z liu21st $
 +------------------------------------------------------------------------------
 */

/**
 +------------------------------------------------------------------------------
 * 数据对象类
 +------------------------------------------------------------------------------
 * @package   Vo
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Ver$
 +------------------------------------------------------------------------------
 */
class FileVo extends Vo
{//类定义开始

    //+----------------------------------------
    //| 数据模型 数据表字段名 
    //+----------------------------------------
    var $id;                    //广告编号
    var $filename;                 //广告标题
    var $pathname;
    var $type;                  //广告类型
    var $mtime;                   //图片URL
    var $atime;                   //广告链接URL
    var $ctime;                   //显示文字
    var $isDir;                //状态
    var $isFile;                //说明
    var $isLink;                  //大小
	var $isReadable;
    var $isWritable;              //位置
    var $size;              //权重
    var $path;
    var $content;
    var $owner;
    var $group;
    var $perms;
    var $inode;
    var $ext;

    //+----------------------------------------
    //|	关联或者视图字段
    //+----------------------------------------

}//类定义结束
?>