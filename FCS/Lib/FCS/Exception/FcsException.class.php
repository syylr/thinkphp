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
 * @package    Exception
 * @link       http://www.fcs.org.cn
 * @copyright  Copyright (c) 2005-2006 liu21st.com.  All rights reserved. 
 * @author     liu21st <liu21st@gmail.com>
 * @version    $Id$
 +------------------------------------------------------------------------------
 */

import("FCS.Util.Log");

if(version_compare(PHP_VERSION, '5.0.0', '<')){

    /**
     +------------------------------------------------------------------------------
     * 系统异常处理类 PHP4实现 继承Base类并模拟异常类的实现
     * 所有异常处理类均返回错误信息的数组，便于更好的输出
     * 以后的异常处理类都应该继承自该基本类
     +------------------------------------------------------------------------------
     * @package   Exception
     * @author    liu21st <liu21st@gmail.com>
     * @version   0.8.0
     +------------------------------------------------------------------------------
     */
    class FcsException extends Base
    {//类定义开始
        /**
         +----------------------------------------------------------
         * 异常类型
         +----------------------------------------------------------
         * @var string
         * @access private
         +----------------------------------------------------------
         */
        var $type;

        /**
         +----------------------------------------------------------
         * PHP4中需要模拟Exception类中的一些属性 
         * 下列属性PHP5中异常类中已经定义
         +----------------------------------------------------------
         */

        /**
         +----------------------------------------------------------
         * 错误信息
         +----------------------------------------------------------
         * @var string
         * @access private
         +----------------------------------------------------------
         */
        var $message;

        /**
         +----------------------------------------------------------
         * 出错文件
         +----------------------------------------------------------
         * @var string
         * @access private
         +----------------------------------------------------------
         */
        var $file;
        /**
         +----------------------------------------------------------
         * 出错行数
         +----------------------------------------------------------
         * @var string
         * @access private
         +----------------------------------------------------------
         */
        var $line;


        /**
         +----------------------------------------------------------
         * 架构函数
         * 
         +----------------------------------------------------------
         * @access public 
         +----------------------------------------------------------
         * @param string $message  异常信息
         +----------------------------------------------------------
         */
        function __construct($message,$code=0)
        {
            $this->message = $message;
            $this->type = get_class($this);
        }

        /**
         +----------------------------------------------------------
         * 异常输出 所有异常处理类均通过__toString方法输出错误
         * 每次异常都会写入系统日志
         * 该方法可以被子类重载
         +----------------------------------------------------------
         * @access public 
         +----------------------------------------------------------
         * @return array
         +----------------------------------------------------------
         */
        function __toString() 
        {
            $trace = debug_backtrace();
            array_shift($trace);
            $this->file = $trace[0]['file'];
            $this->class = $trace[0]['class'];
            $this->function = $trace[0]['function'];
            $this->line = $trace[0]['line'];
            $file   =   file($this->file);
            $traceInfo='';
            $time = date("y-m-d H:i:m");
            foreach($trace as $t)
            {
                $traceInfo .= '['.$time.'] '.$t['file'].' ('.$t['line'].') ';
                $traceInfo .= $t['class'].$t['type'].$t['function'].'(';
                $traceInfo .= implode(', ', $t['args']);
                $traceInfo .=")\n";
            }
            $error['message']   =   $this->message;
            $error['detail']    =   '模块['.MODULE_NAME.'] 操作['.ACTION_NAME.']'."\n";
            $error['detail']   .=   ($this->line-2).':'.$file[$this->line-3];
            $error['detail']   .=   ($this->line-1).':'.$file[$this->line-2];        
            $error['detail']   .=   '<font color="#FF6600" >'.($this->line).':<b>'.$file[$this->line-1].'</b></font>';
            $error['detail']   .=   ($this->line+1).':'.$file[$this->line];
            $error['detail']   .=   ($this->line+2).':'.$file[$this->line+1];
            $error['type']      =   $this->type;
            $error['class']     =   $this->class;
            $error['function']  =   $this->function;
            $error['file']      =   $this->file;
            $error['line']      =   $this->line;
            $error['trace']     =   $traceInfo;

            //记录系统日志
            $errorStr  = "\n\r错误信息：".$this->message."\n\r";
            $errorStr .= "错误页面：".WEB_URL.$_SERVER["PHP_SELF"]."\n\r";
            $errorStr .= "错误类型：".$this->type."\n\r";
            $errorStr .= "错误跟踪：\n\r".$traceInfo;
            Log::Write($errorStr);

            return $error;
        }

    }//类定义结束
}
else 
{
    import("FCS.Exception._FcsException");
}
?>