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