<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2012 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

defined('THINK_PATH') or exit();
/**
 +------------------------------------------------------------------------------
 * 系统行为扩展 页面Trace显示输出
 +------------------------------------------------------------------------------
 */
class ShowPageTraceBehavior extends Behavior {
    // 行为参数定义
    protected $options   =  array(
        'SHOW_PAGE_TRACE'        => false,   // 显示页面Trace信息
    );

    // 行为扩展的执行入口必须是run
    public function run(&$params){
        if(C('SHOW_PAGE_TRACE')) {
            echo $this->showTrace();
        }
    }

    /**
     +----------------------------------------------------------
     * 显示页面Trace信息
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     */
    private function showTrace() {
         // 系统默认显示信息
        $log  =   Log::$log;
        $files =  get_included_files();
        $info   =   array();
        foreach ($files as $key=>$file){
            $info[] = $file.' ( '.number_format(filesize($file)/1024,2).' KB )';
        }
        $trace  =   array();
        $base   =   array(
            '请求信息'=>  date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']).' '.$_SERVER['SERVER_PROTOCOL'].' '.$_SERVER['REQUEST_METHOD'].' : '.__SELF__,
            '运行时间'=> $this->showTime(),
            '内存开销'=> MEMORY_LIMIT_ON?number_format((memory_get_usage() - $GLOBALS['_startUseMems'])/1024,2).' kb':'不支持',
            '查询信息'=> N('db_query').' queries '.N('db_write').' writes ',
            '文件加载'=> count(get_included_files()),
            '缓存信息'=> N('cache_read').' gets '.N('cache_write').' writes ',
            '配置加载'=> count(c()),
            '函数调用'=> $this->showCall(),
            '会话信息'    =>  'SESSION_ID='.session_id(),
            );
        // 读取项目定义的Trace文件
        $traceFile  =   CONF_PATH.'trace.php';
        if(is_file($traceFile)) {
            $base    =   array_merge($base,include $traceFile);
        }
        $trace[L('_BASE_')] =   $base;
        $trace[L('_LOG_')]  =   implode('<br/>',$log);
        $trace[L('_FILE_')]  =   implode('<br/>',$info);
        unset($files,$info,$log,$base);
        $debug  =   trace();
        if($debug) {
            $trace[L('_DEBUG_')]  =   $debug;
        }
        // 调用Trace页面模板
        ob_start();
        include C('TMPL_TRACE_FILE')?C('TMPL_TRACE_FILE'):THINK_PATH.'Tpl/page_trace.tpl';
        return ob_get_clean();
    }

    /**
     +----------------------------------------------------------
     * 获取运行时间
     +----------------------------------------------------------
     */
    private function showTime() {
        // 显示运行时间
        G('beginTime',$GLOBALS['_beginTime']);
        G('viewEndTime');
        $showTime   =   G('beginTime','viewEndTime').'s ';
        // 显示详细运行时间
        $showTime .= '( Load:'.G('beginTime','loadTime').'s Init:'.G('loadTime','initTime').'s Exec:'.G('initTime','viewStartTime').'s Template:'.G('viewStartTime','viewEndTime').'s )';
        return $showTime;
    }

    /**
     +----------------------------------------------------------
     * 获取函数调用信息
     +----------------------------------------------------------
     */
    private function showCall() {
        // 显示函数调用次数 自定义函数,内置函数
        $fun  =  get_defined_functions();
        return (count($fun['user'])+count($fun['internal'])).' ( 用户:'.count($fun['user']).' , 内置:'.count($fun['internal']).' )';
    }

}