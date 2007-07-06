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
 * @version    $Id: Debug.class.php 73 2006-11-08 10:08:01Z fcs $
 +------------------------------------------------------------------------------
 */

 /**
 +------------------------------------------------------------------------------
 * 系统调试类 
 +------------------------------------------------------------------------------
 * @package   Util
 * @author    liu21st <liu21st@gmail.com>
 * @version   1.0.0
 +------------------------------------------------------------------------------
 */
class Debug extends Base
{//类定义开始

    var $marker =  array();

    /**
     +----------------------------------------------------------
     * 架构函数
     * 
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     */
    function __construct()
    {

    }

    /**
     +----------------------------------------------------------
     * 标记调试位
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $name  要标记的位置名称
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    function mark($name) 
    {
    	$this->marker['time'][$name]  =  microtime();
        if(MEMORY_LIMIT_ON) {
        	 $this->marker['mem'][$name] = memory_get_usage();
        }
    }

    /**
     +----------------------------------------------------------
     * 区间使用时间查看
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $start  开始标记的名称
     * @param string $end  结束标记的名称
     * @param integer $decimals  时间的小数位
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    function useTime($start,$end,$decimals = 4) 
    {
		if ( ! isset($this->marker['time'][$start]))
		{
			return '';
		}
		
		if ( ! isset($this->marker['time'][$end]))
		{
			$this->marker['time'][$end] = microtime();
		}    	
        $startTime    =  array_sum(split(' ', $this->marker['time'][$start]));
        $endTime     =  array_sum(split(' ', $this->marker['time'][$end]));
        return number_format($endTime - $startTime, $decimals);
    }

    /**
     +----------------------------------------------------------
     * 区间使用内存查看
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param string $start  开始标记的名称
     * @param string $end  结束标记的名称
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    function useMemory($start,$end) 
    {
        if(!MEMORY_LIMIT_ON) {
        	return '';
        }
		if ( ! isset($this->marker['mem'][$start]))
		{
			return '';
		}
		
		if ( ! isset($this->marker['mem'][$end]))
		{
			$this->marker['mem'][$end] = memory_get_usage();
		}    	
        $startMem    =  array_sum(split(' ', $this->marker['mem'][$start]));
        $endMem     =  array_sum(split(' ', $this->marker['mem'][$end]));
        return number_format(($endMem - $startMem)/1024);
    }

}//类定义结束
?>