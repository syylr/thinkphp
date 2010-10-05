<?php 
// +----------------------------------------------------------------------+
// | ThinkCMS                                                             |
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
// $Id: CategoryVo.class.php 2 2007-01-03 07:52:09Z liu21st $

/**
 +------------------------------------------------------------------------------
 * CMS 类别数据对象
 +------------------------------------------------------------------------------
 * @author liu21st <liu21st@gmail.com>
 * @version  $Id: CategoryVo.class.php 2 2007-01-03 07:52:09Z liu21st $
 +------------------------------------------------------------------------------
 */

class CategoryVo extends Vo
{//类定义开始

    //+----------------------------------------
    //| 数据模型 数据表字段名 
    //+----------------------------------------
    var $id;                     // ID
    var $name;                //Category Name
    var $title;                  //Category Display Name
    var $status;               // status
    var $remark;             // Category remark
    var $seqNo;              // Category sort
    var $pid;                   // parent Id
    var $level;                 // category level
    //+----------------------------------------
    //|    其他业务字段
    //+----------------------------------------

}//类定义结束
?>