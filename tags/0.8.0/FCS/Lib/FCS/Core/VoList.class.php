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
 * @package    Core
 * @link       http://www.fcs.org.cn
 * @copyright  Copyright (c) 2005-2006 liu21st.com.  All rights reserved. 
 * @author     liu21st <liu21st@gmail.com>
 * @version    $Id$
 +------------------------------------------------------------------------------
 */

import("FCS.Util.ArrayList");

/**
 +------------------------------------------------------------------------------
 * 数据列表对象类 继承自ArrayList类
 * 用VoList->getIterator() 方法可以获得迭代子
 * 用VoList->size()方法获得列表长度
 * 用VoList->getRange() 获得列表对象的子集
 * 用VoList->get() 获得列表对象的某一行
 * 用VoList->set() 更改某一行数据的值
 +------------------------------------------------------------------------------
 * @package   core
 * @author    liu21st <liu21st@gmail.com>
 * @version   0.8.0
 +------------------------------------------------------------------------------
 */
class VoList extends ArrayList
{

    /**
     +----------------------------------------------------------
     * 原始数据集
     +----------------------------------------------------------
     * @var array
     * @access protected
     +----------------------------------------------------------
     */
    var $resultSet  = array();

    /**
     +----------------------------------------------------------
     * 原始数据集
     +----------------------------------------------------------
     * @var array
     * @access protected
     +----------------------------------------------------------
     */
    var $rowNums  = 0;


    /**
     +----------------------------------------------------------
     * Json 输出字符串
     +----------------------------------------------------------
     * @var string
     * @access protected
     +----------------------------------------------------------
     */
    var $json = '';

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
        return new VoList($this->range($offset,$length));
    }

    /**
     +----------------------------------------------------------
     * 转换为Json
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function toJson() 
    {
        if(empty($this->json)) {
            $json = '';
            foreach ($this->getIterator() as $vo)
            {
                if(!empty($vo)){
                    $json .= $vo->toJson().',';
                }
            }
            $this->json = '['.substr($json,0,-1).']';
        }
        return $this->json;
    }

    /**
     +----------------------------------------------------------
     * 转换为数据集
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function toResultSet() 
    {
        if(empty($this->resultSet)) {
            $resultSet = array();
            foreach($this->getIterator() as $key=>$vo) {
                $result = get_object_vars($vo);
                $resultSet[$key] = $result;
            }
            $this->resultSet = $resultSet;
        }
        return $this->resultSet;
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
     * @throws FcsException
     +----------------------------------------------------------
     */
    function getCol($field,$sepa='') 
    {
        if(is_string($field)) {
            $field	=	explode(',',$field);
        }        
        $resultSet  = $this->toResultSet();
        $array      =   array();
        foreach($resultSet as $key=>$val) {
            if(!array_key_exists($field[0],$val)) {
                break;
            }
            if(count($field)>1) {
                $array[$val[$field[0]]] = '';
                $length	 = count($field);
                for($i=1; $i<$length; $i++) {
                    if(array_key_exists($field[$i],$val)) {
                        $array[$val[$field[0]]] .= $val[$field[$i]].$sepa;
                    }
                }
            }else {
                $array[] = $val[$field[0]];
            }
        }
        return $array;
    }

    /**
     +----------------------------------------------------------
     * 从数据集中随机取number个Vo
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param Integer $number 随机个数
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function getRand($number=1) 
    {
        $resultSet = $this->toArray();
        $list   =   array_rand($resultSet,$number);
        if($number===1) {
            $list   =   $resultSet[$list];
        }
        return $list;
    }


    /**
     +----------------------------------------------------------
     * 转换为字符串
     * 格式为CSV格式
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function toString() 
    {
        $resultSet = $this->toResultSet();
        $str = '';
        foreach($resultSet as $key=>$val) {
            $str .= implode(',',$val)."\n";
        }
        return $str;
    }
}//类定义结束
?>