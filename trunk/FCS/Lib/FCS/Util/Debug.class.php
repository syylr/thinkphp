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
 * 系统调试类 
 +------------------------------------------------------------------------------
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id$
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