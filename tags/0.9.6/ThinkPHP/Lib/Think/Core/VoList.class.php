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
// $Id: VoList.class.php 33 2007-02-25 07:06:02Z liu21st $

import("Think.Util.ArrayList");

/**
 +------------------------------------------------------------------------------
 * 数据列表对象类 继承自ArrayList类
 * VoList->getIterator() 方法可以获得迭代子
 * VoList->size()方法获得列表长度
 * VoList->getRange() 获得列表对象的子集
 * VoList->get() 获得列表对象的某一行
 * VoList->set() 更改某一行数据的值
 * VoList->sortBy() 对列表排序
 +------------------------------------------------------------------------------
 * @package   core
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id: VoList.class.php 33 2007-02-25 07:06:02Z liu21st $
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
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function toJson($fields=array()) 
    {
        if(empty($this->json)) {
            $json = '';
            foreach ($this->getIterator() as $vo)
            {
                if(!empty($vo)){
                    $json .= $vo->toJson($fields).',';
                }
            }
            $this->json = '['.substr($json,0,-1).']';
        }
        return $this->json;
    }
    /*
    function toJson($fields=array()) 
    {
        if(empty($this->json)) {
            $this->json = json_encode($this->toResultSet);
        }
        return $this->json;
    }*/

    /**
     +----------------------------------------------------------
     * 转换为数据集
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws ThinkExecption
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
     * @throws ThinkExecption
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
     * @throws ThinkExecption
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
     * @throws ThinkExecption
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

    /**
     +----------------------------------------------------------
     * 列表对象排序
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function sortBy($field,$sort='desc') 
    {
        $resultSet = array();
        foreach($this->getIterator() as $key=>$vo) {
            $resultSet[$vo->$field] = $vo;
        }
        ($sort=='desc')? krsort($resultSet):ksort($resultSet);
        return new VoList($resultSet);
    }

    /**
     +----------------------------------------------------------
     * 获取Vo对象名称
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function getVoClass() 
    {
    	$vo  =  $this->get(0);
        return get_class($vo);
    }
}//类定义结束
?>