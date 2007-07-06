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
// $Id: PageVo.class.php 9 2007-01-03 09:32:19Z liu21st $

/**
 +------------------------------------------------------------------------------
 * CMS 页面数据对象
 +------------------------------------------------------------------------------
 * @author liu21st <liu21st@gmail.com>
 * @version  $Id: PageVo.class.php 9 2007-01-03 09:32:19Z liu21st $
 +------------------------------------------------------------------------------
 */

class PageVo extends Vo
{//类定义开始

    //+----------------------------------------
    //| 数据模型 数据表字段名 
    //+----------------------------------------
    var $id;                     // ID
    var $name;                //page Name
    var $title;                  //Category Display Name
    var $status;               // status
    var $content;             // Category remark
    var $seqNo;              // Category sort
    //+----------------------------------------
    //|    其他业务字段
    //+----------------------------------------

}//类定义结束
?>