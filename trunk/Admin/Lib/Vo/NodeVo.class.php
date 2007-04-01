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
class NodeVo extends Vo
{//类定义开始

    //+----------------------------------------
    //| 数据模型 数据表字段名 
    //+----------------------------------------
    var $id;                    //节点编号
    var $name;                  //节点名称
    var $title;                 //显示名称
    var $status;                //节点状态
    var $remark;                //节点描述
    var $seqNo;                 //节点排序（当前层次）
    var $access;                //访问权限
    var $pid;            //上级节点
    var $level;                 //节点层次
    var $type;
    //+----------------------------------------
    //|    关联或者视图字段
    //+----------------------------------------
    var $parentNode;
    var $subNodes;
    var $_link  = array(
        array(   'mapping_type'=>BELONGS_TO,
                    'class_name'=>'Node',
                    'foreign_key'=>'pid',
                    'mapping_name'=>'parentNode'),
        array(   'mapping_type'=>HAS_MANY,
                    'class_name'=>'Node',
                    'foreign_key'=>'pid',
                    'mapping_name'=>'subNodes')
    );
}//类定义结束
?>