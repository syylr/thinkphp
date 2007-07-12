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

import("Think.Util.ArrayList");
/**
 +------------------------------------------------------------------------------
 * 数据集类 可以用size()方法获取数据集的行数
 +------------------------------------------------------------------------------
 * @package   Db
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class ResultSet extends ArrayList
{

    /**
     +----------------------------------------------------------
     * 架构函数
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $array  初始化数组元素
     +----------------------------------------------------------
     */
    function __construct($array=array())
    {
        parent::__construct($array);
    }

    /**
     +----------------------------------------------------------
     * 获取数据列表对象的子集
     * 用于列表分页显示
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param integer $offset 起始位置
     * @param integer $length 长度
     +----------------------------------------------------------
     * @return VoList
     +----------------------------------------------------------
     */
    function getRange($offset,$length=NULL)
    {
        return new ResultSet($this->range($offset,$length));
    }

    /**
     +----------------------------------------------------------
     * 取得某个字段的数据
	 * field参数支持数组和字符串（以,分割)
     * 通常可以用于volist的select输出
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param mixed $field vo字段名
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function getCol($field) 
    {
        if(is_string($field)) {
            $field	=	explode(',',$field);
        }        
        $array      =   array();
        foreach($this->toArray() as $key=>$val) {
            if(is_object($val)) {
            	$val  = get_object_vars($val);
            }
            if(!array_key_exists($field[0],$val)) {
                break;
            }
            if(count($field)>1) {
                $array[$val[$field[0]]] = '';
                $length	 = count($field);
                for($i=1; $i<$length; $i++) {
                    if(array_key_exists($field[$i],$val)) {
                        $array[$val[$field[0]]] .= $val[$field[$i]];
                    }
                }
            }else {
                $array[] = $val[$field[0]];
            }
        }
        return $array;
    }
};
?>