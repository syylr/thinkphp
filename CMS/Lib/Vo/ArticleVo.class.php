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
// $Id: ArticleVo.class.php 9 2007-01-03 09:32:19Z liu21st $

/**
 +------------------------------------------------------------------------------
 * CMS 文章数据对象
 +------------------------------------------------------------------------------
 * @author liu21st <liu21st@gmail.com>
 * @version  $Id: ArticleVo.class.php 9 2007-01-03 09:32:19Z liu21st $
 +------------------------------------------------------------------------------
 */

class ArticleVo extends Vo
{//类定义开始

    //+----------------------------------------
    //| 数据模型 数据表字段名 
    //+----------------------------------------
    var $id;                     // ID
    var $name;                // Article url name
    var $userId;           // write author
    var $categoryId;
    var $title;                  // Article title
    var $content;             // Content
    var $password;          // access password
    var $cTime;               // create time
    var $aTime;              // last access time
    var $mTime;             // last modify time
    var $status;               // article status
    var $isRecommend;    // recommend
    var $isTop;                // top
    var $commentStatus;  // comment status
    var $guid;                  // access address
    var $readCount;         // read count
    var $commentCount;  // comment count
    var $type;
    //+----------------------------------------
    //|    其他业务字段
    //+----------------------------------------

}//类定义结束
?>