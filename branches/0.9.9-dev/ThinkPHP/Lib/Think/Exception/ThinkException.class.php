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

import("Think.Util.Log");
if(IS_PHP5){
    class ThinkException extends Exception
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
            parent::__construct($message,$code);
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
            $trace = $this->getTrace();
            array_shift($trace);
            $this->class = $trace[0]['class'];
            $this->function = $trace[0]['function'];
            $this->file = $trace[0]['file'];
            $this->line = $trace[0]['line'];
            $file   =   file($this->file);
            $traceInfo='';
            $time = date("y-m-d H:i:m");
            foreach($trace as $t) {
                $traceInfo .= '['.$time.'] '.$t['file'].' ('.$t['line'].') ';
                $traceInfo .= $t['class'].$t['type'].$t['function'].'(';
                $traceInfo .= implode(', ', $t['args']);
                $traceInfo .=")\n";
            }
            $error['message']   = $this->message;
            $error['type']      = $this->type;
            $error['detail']    = L('_MODULE_').'['.MODULE_NAME.'] '.L('_ACTION_').'['.ACTION_NAME.']'."\n";
            $error['detail']   .=   ($this->line-2).': '.$file[$this->line-3];
            $error['detail']   .=   ($this->line-1).': '.$file[$this->line-2];        
            $error['detail']   .=   '<font color="#FF6600" >'.($this->line).': <b>'.$file[$this->line-1].'</b></font>';
            $error['detail']   .=   ($this->line+1).': '.$file[$this->line];
            $error['detail']   .=   ($this->line+2).': '.$file[$this->line+1];
            $error['class']     =   $this->class;
            $error['function']  =   $this->function;
            $error['file']      = $this->file;
            $error['line']      = $this->line;
            $error['trace']     = $traceInfo;

            //记录系统日志            
            $errorStr   = "\n".L('_ERROR_INFO_')."[ ".APP_NAME.' '.MODULE_NAME.' '.ACTION_NAME.' ]'.$this->message."\n";
            $errorStr  .= L('_ERROR_URL_').WEB_URL.$_SERVER["PHP_SELF"]."\n";
            $errorStr  .= L('_ERROR_TYPE_').$this->type."\n";
            $errorStr  .= L('_ERROR_TRACE_')."\n".$traceInfo;
            Log::Write($errorStr);

            return $error ;
        }

    }//类定义结束
 }
else{

    /**
     +------------------------------------------------------------------------------
     * 系统异常处理类 PHP4实现 继承Base类并模拟异常类的实现
     * 所有异常处理类均返回错误信息的数组，便于更好的输出
     * 以后的异常处理类都应该继承自该基本类
     +------------------------------------------------------------------------------
     * @package   Exception
     * @author    liu21st <liu21st@gmail.com>
     * @version   $Id$
     +------------------------------------------------------------------------------
     */
    class ThinkException extends Base
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
            $error['detail']    =   L('_MODULE_').'['.MODULE_NAME.']'.L('_ACTION_').'['.ACTION_NAME.']'."\n";
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
            $errorStr  = "\n".L('_ERROR_INFO_').$this->message."\n";
            $errorStr .= L('_ERROR_URL_').WEB_URL.$_SERVER["PHP_SELF"]."\n";
            $errorStr .= L('_ERROR_TYPE_').$this->type."\n";
            $errorStr .= L('_ERROR_TRACE_')."\n".$traceInfo;
            Log::Write($errorStr);

            return $error;
        }
        
    }//类定义结束
}
?>