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
 * @version    $Id: Log.class.php 90 2006-11-11 08:26:44Z fcs $
 +------------------------------------------------------------------------------
 */

/**
 +------------------------------------------------------------------------------
 * 日志处理类 在日志处理类中不抛出异常，而使用halt方法
 * 因为异常处理类中包含了日志记录 会导致循环错误
 +------------------------------------------------------------------------------
 * @package   Util
 * @author    liu21st <liu21st@gmail.com>
 * @version   0.8.0
 +------------------------------------------------------------------------------
 */

class Log extends Base
{//类定义开始

    /**
     +----------------------------------------------------------
     * 架构函数
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     */
    function __construct()
    {
    }

    /**
     +----------------------------------------------------------
     * 日志写入
     * 
     +----------------------------------------------------------
     * @static
     * @access public 
     +----------------------------------------------------------
     * @param string $message 日志信息
     * @param string $type  日志类型
     * WEB_LOG_DEBUG 调试信息
     * WEB_LOG_ERROR 错误信息
     * @param string $file  写入文件 默认取定义日志文件
     * WEB_LOG_DEBUG类型取系统日志目录下面的 systemOut.log
     * WEB_LOG_ERROR类型取系统日志目录下面的 systemErr.log
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function write($message,$type=WEB_LOG_ERROR,$file='')
    {
        $now = date('[ y-m-d H:i:s ]');
        switch($type){
            case WEB_LOG_DEBUG:
                $logType ='[调试]';
                $destination = $file == ''? LOG_PATH.date('y_m_d')."_systemOut.log" : $file;
                break;
            default :
                $logType ='[错误]';
                $destination = $file == ''? LOG_PATH.date('y_m_d')."_systemErr.log" : $file;
        }
        if(!is_writable(LOG_PATH)){
            halt(_FILE_NOT_WRITEABLE_.':'.$destination);
        }
        //检测日志文件大小，超过配置大小则备份日志文件重新生成
        if(file_exists($destination)) {
            if( defined('LOG_FILE_SIZE')  && floor(LOG_FILE_SIZE) <= filesize($destination) ){
                  rename($destination,dirname($destination).'/'.time().'-'.basename($destination));
            }        	
        }
        error_log("$now\n$message\n", FILE_LOG,$destination );

    }

}//类定义结束
?>