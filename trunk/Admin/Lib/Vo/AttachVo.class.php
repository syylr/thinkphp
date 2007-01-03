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
 * @link       http://www.fcs.org.cn
 * @copyright  Copyright (c) 2005-2006 liu21st.com.  All rights reserved. 
 * @author     liu21st <liu21st@gmail.com>
 * @version    $Id$
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
class AttachVo extends Vo
{

    // +----------------------------------------
    // | 数据模型 数据表字段名 
    // +----------------------------------------
    var $id;                    //附件编号
    var $name;                  //原有名称
    var $type;                  //文件类型
    var $size;                  //文件大小
    var $extension;             //扩展名
    var $savepath;              //存放路径
    var $savename;              //保存名称
    var $module;                //模块名
    var $recordId;              //记录编号
    var $uploadTime;       //上传时间
    var $userId;               //上传用户id
    var $hash;                 //附件的Hash值

};
?>