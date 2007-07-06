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
 * @package    Util
 * @link       http://www.fcs.org.cn
 * @copyright  Copyright (c) 2005-2006 liu21st.com.  All rights reserved. 
 * @author     liu21st <liu21st@gmail.com>
 * @version    $Id$
 +------------------------------------------------------------------------------
 */

/**
 +------------------------------------------------------------------------------
 * ArrayObject实现类 PHP5以上内置了ArrayObject类
 +------------------------------------------------------------------------------
 * @package   Util
 * @author    liu21st <liu21st@gmail.com>
 * @version   0.8.0
 +------------------------------------------------------------------------------
 */

if(!class_exists('ArrayObject')){//PHP5以上内置了ArrayObject类，不需要重新定义

    import("FCS.Util.ListIterator");

    class ArrayObject extends Base 
    {//类定义开始

        /**
         +----------------------------------------------------------
         * 架构函数
         +----------------------------------------------------------
         * @access public 
         +----------------------------------------------------------
         * @param string $array  初始化数组元素
         +----------------------------------------------------------
         */
        function __construct($array)
        {
            foreach ($array as $key=>$val){
                $this->$key = $val;
            }
        }

        /**
         +----------------------------------------------------------
         * 追加对象
         * 
         +----------------------------------------------------------
         * @access public 
         +----------------------------------------------------------
         * @param mixed $object  要添加的对象
         +----------------------------------------------------------
         * @return boolen
         +----------------------------------------------------------
         */
        function append($object)
        {
            $index = $this->count();
            $this->$index = $object;
        }

        /**
         +----------------------------------------------------------
         * 统计列表中对象数目
         * 
         +----------------------------------------------------------
         * @access public 
         +----------------------------------------------------------
         * @return integer
         +----------------------------------------------------------
         */
        function count()
        {
            return count(get_object_vars($this));
        }

        /**
         +----------------------------------------------------------
         * 获得迭代因子
         * 
         +----------------------------------------------------------
         * @access public 
         +----------------------------------------------------------
         * @return ListIterator
         +----------------------------------------------------------
         */
        function getIterator()
        {
             return new ListIterator(get_object_vars($this));
        }


        /**
         +----------------------------------------------------------
         * 是否存在对象索引
         * 
         +----------------------------------------------------------
         * @access public 
         +----------------------------------------------------------
         * @param mixed $index 索引
         +----------------------------------------------------------
         * @return boolen
         +----------------------------------------------------------
         */
        function offsetExists($index)
        {
            return isset($this->$index);
        }

        /**
         +----------------------------------------------------------
         * 更新索引对象
         * 
         +----------------------------------------------------------
         * @access public 
         +----------------------------------------------------------
         * @param mixed $index 索引
         * @param integer $object 对象
         +----------------------------------------------------------
         * @return boolen
         +----------------------------------------------------------
         */
        function offsetSet($index,$object)
        {
            $this->$index = $object;
        }

        /**
         +----------------------------------------------------------
         * 注销对象
         * 
         +----------------------------------------------------------
         * @access public 
         +----------------------------------------------------------
         * @param mixed $index 索引
         +----------------------------------------------------------
         */
        function offsetUnset($index)
        {
            unset($this->$index);
        }

    }//类定义结束
}
?>