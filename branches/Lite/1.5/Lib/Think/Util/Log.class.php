<?php
// +----------------------------------------------------------------------
// | ThinkPHP Lite
// +----------------------------------------------------------------------
// | Copyright (c) 2008 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

/**
 +------------------------------------------------------------------------------
 * 日志处理类
 +------------------------------------------------------------------------------
 * @category   Think
 * @package  Think
 * @subpackage  Util
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class Log extends Base
{//类定义开始

    // 日志级别
    const EMERG   = 'EMERG';  // Emergency: system is unusable
    const ALERT    = 'ALERT';  // Alert: action must be taken immediately
    const CRIT      = 'CRIT';  // Critical: critical conditions
    const ERR       = 'ERR';  // Error: error conditions
    const WARN    = 'WARN';  // Warning: warning conditions
    const NOTICE  = 'NOTIC';  // Notice: normal but significant condition
    const INFO     = 'INFO';  // Informational: informational messages
    const DEBUG   = 'DEBUG';  // Debug: debug messages
    const SQL       = 'SQL';  // SQL：sql messages

    // 日志记录方式
    const SYSTEM = 0;
    const MAIL      = 1;
    const TCP       = 2;
    const FILE       = 3;

    // 日志信息
    static $log =   array();

    // 日期格式
    static $format =  '[ c ]';

    /**
     +----------------------------------------------------------
     * 记录日志
     +----------------------------------------------------------
     * @static
     * @access public
     +----------------------------------------------------------
     * @param string $message 日志信息
     * @param string $level  日志级别
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    static function record($message,$level=self::ERR) {
        if(in_array($level,C('LOG_RECORD_LEVEL'))) {
            $now = date(self::$format);
            self::$log[] =   "{$now} {$level}: {$message}\r\n";
        }
    }

    /**
     +----------------------------------------------------------
     * 日志保存
     +----------------------------------------------------------
     * @static
     * @access public
     +----------------------------------------------------------
     * @param integer $type 日志记录方式
     * @param string $destination  写入目标
     * @param string $extra 额外参数
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    static function save($type=self::FILE,$destination='',$extra='')
    {
        if(empty($destination)) {
            $destination = LOG_PATH.date('y_m_d').".log";
        }
        if(self::FILE == $type) {
            if(!is_writable(LOG_PATH)){
                halt(L('_FILE_NOT_WRITEABLE_').':'.LOG_PATH);
            }
            //检测日志文件大小，超过配置大小则备份日志文件重新生成
            if(is_file($destination) && floor(C('LOG_FILE_SIZE')) <= filesize($destination) ){
                  rename($destination,dirname($destination).'/'.time().'-'.basename($destination));
            }
        }
        error_log(implode("",self::$log), $type,$destination ,$extra);
        //clearstatcache();
    }

    /**
     +----------------------------------------------------------
     * 日志直接写入
     +----------------------------------------------------------
     * @static
     * @access public
     +----------------------------------------------------------
     * @param string $message 日志信息
     * @param string $level  日志级别
     * @param integer $type 日志记录方式
     * @param string $destination  写入目标
     * @param string $extra 额外参数
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    static function write($message,$level=self::ERR,$type=self::FILE,$destination='',$extra='')
    {
        if(in_array($level,C('LOG_RECORD_LEVEL'))) {
            $now = date(self::$format);
            if(empty($destination)) {
                $destination = LOG_PATH.date('y_m_d').".log";
            }
            if(self::FILE == $type) {
                if(!is_writable(LOG_PATH)){
                    halt(L('_FILE_NOT_WRITEABLE_').':'.$destination);
                }
                //检测日志文件大小，超过配置大小则备份日志文件重新生成
                if(is_file($destination) && floor(C('LOG_FILE_SIZE')) <= filesize($destination) ){
                      rename($destination,dirname($destination).'/'.time().'-'.basename($destination));
                }
            }
            error_log("{$now} {$level}: {$message}\r\n", $type,$destination,$extra );
        }
        //clearstatcache();
    }

}//类定义结束
?>