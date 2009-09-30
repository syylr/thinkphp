<?php 
// +---------------------------------------------------------------------------+
// | FCS -- Fast,Compatible & Simple OOP PHP Framework                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2005-2006 liu21st.com.  All rights reserved.                |
// | Website: http://www.fcs.org.cn/                                           |
// | Author : Liu21st ���� <liu21st@gmail.com>                                 |
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
 * @version    $Id: ArrayObject.class.php 73 2006-11-08 10:08:01Z fcs $
 +------------------------------------------------------------------------------
 */

/**
 +------------------------------------------------------------------------------
 * ArrayObjectʵ���� PHP5����������ArrayObject��
 +------------------------------------------------------------------------------
 * @package   Util
 * @author    liu21st <liu21st@gmail.com>
 * @version   0.8.0
 +------------------------------------------------------------------------------
 */

if(!class_exists('ArrayObject')){//PHP5����������ArrayObject�࣬����Ҫ���¶���

    import("FCS.Util.ListIterator");

    class ArrayObject extends Base 
    {//�ඨ�忪ʼ

        /**
         +----------------------------------------------------------
         * �ܹ�����
         +----------------------------------------------------------
         * @access public 
         +----------------------------------------------------------
         * @param string $array  ��ʼ������Ԫ��
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
         * ׷�Ӷ���
         * 
         +----------------------------------------------------------
         * @access public 
         +----------------------------------------------------------
         * @param mixed $object  Ҫ���ӵĶ���
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
         * ͳ���б��ж�����Ŀ
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
         * ��õ�������
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
         * �Ƿ���ڶ�������
         * 
         +----------------------------------------------------------
         * @access public 
         +----------------------------------------------------------
         * @param mixed $index ����
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
         * ������������
         * 
         +----------------------------------------------------------
         * @access public 
         +----------------------------------------------------------
         * @param mixed $index ����
         * @param integer $object ����
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
         * ע������
         * 
         +----------------------------------------------------------
         * @access public 
         +----------------------------------------------------------
         * @param mixed $index ����
         +----------------------------------------------------------
         */
        function offsetUnset($index)
        {
            unset($this->$index);
        }

    }//�ඨ�����
}
?>