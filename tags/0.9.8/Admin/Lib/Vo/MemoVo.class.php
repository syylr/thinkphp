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
// $Id: MemoVo.class.php 78 2007-04-01 04:29:15Z liu21st $

/**
 +------------------------------------------------------------------------------
 * 数据对象类
 +------------------------------------------------------------------------------
 * @package   Vo
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Ver$
 +------------------------------------------------------------------------------
 */
class MemoVo extends Vo
{//类定义开始

    //+----------------------------------------
    //| 数据模型 数据表字段名 
    //+----------------------------------------
    var $id;                     //ID
    var $label;                 //标签名称
    var $memo;                //SQL语句
    var $createTime;        //创建时间
    var $lastExecTime;     //最近执行时间
    var $userId;
    var $type;

    //+----------------------------------------
    //|	关联或者视图字段
    //+----------------------------------------

}//类定义结束
?>