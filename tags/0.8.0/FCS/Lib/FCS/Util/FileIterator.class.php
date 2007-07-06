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

if(version_compare(PHP_VERSION, '5.0.0', '<')){
    
    import("FCS.Util.ListIterator");
    import("FCS.Util.ArrayObject");
    /**
     +------------------------------------------------------------------------------
     * 文件遍历类 PHP4实现
     +------------------------------------------------------------------------------
     * @package   Util
     * @author    liu21st <liu21st@gmail.com>
     * @version   0.8.0
     +------------------------------------------------------------------------------
     */
    class FileIterator extends ListIterator
    {//类定义开始

        /**
         +----------------------------------------------------------
         * 文件内容数组
         +----------------------------------------------------------
         * @var array
         * @access protected
         +----------------------------------------------------------
         */
        var $_line = array();

        /**
         +----------------------------------------------------------
         * 架构函数 可以继承ListIterator的所有方法
         * 
         +----------------------------------------------------------
         * @access public 
         +----------------------------------------------------------
         * @param string $filename  文件名
         * @param string $buffer  缓存读取大小
         +----------------------------------------------------------
         */
        function __construct($filename, $buffer = 1024) 
        {
            $this->readLine($filename,$buffer);
            parent::__construct($this->_line);
        }

        /**
         +----------------------------------------------------------
         * 读取文件内容
         * 
         +----------------------------------------------------------
         * @access public 
         +----------------------------------------------------------
         * @param mixed $filename 路径
         * @param mixed $buffer 缓存读取大小
         +----------------------------------------------------------
         */
        function readLine($filename,$buffer)
        {
            $i = 0;
            $line = array();
            $fp = fopen($filename, 'rb');
            while (!feof($fp)) {
                $line[$i] = fgets($fp, $buffer);
                $i++;
            }
            fclose($fp);
            $this->_line = $line;
        }

        /**
         +----------------------------------------------------------
         * 使用foreach遍历
         * 
         +----------------------------------------------------------
         * @access public 
         +----------------------------------------------------------
         * @param mixed $filename 路径
         * @param mixed $buffer 缓存读取大小
         +----------------------------------------------------------
         */
        function getIterator()
        {
             return new ArrayObject($this->_line);
        }

    }//类定义结束

}else {
    //引入PHP5支持的FileIterator类
	import("FCS.Util._FileIterator");
}
?>