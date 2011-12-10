<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2010 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

/**
 +------------------------------------------------------------------------------
 * 系统行为扩展 页面Trace显示输出
 +------------------------------------------------------------------------------
 */

C(array(
    'SHOW_PAGE_TRACE'		=> false,   // 显示页面Trace信息 由Trace文件定义和Action操作赋值
    'TMPL_TRACE_FILE'       => THINK_PATH.'Common/Tpl/page_trace.tpl',     // 页面Trace的模板文件
));
class ShowPageTraceBehavior {
    // 行为扩展的执行入口必须是run
    public function run(){
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
        // 显示页面Trace信息 读取Trace定义文件
        $traceFile  =   CONFIG_PATH.'trace.php';
        if(is_file($traceFile)) {
            // 定义格式 return array('当前页面'=>$_SERVER['PHP_SELF'],'通信协议'=>$_SERVER['SERVER_PROTOCOL'],...);
            $_trace  =  include $traceFile;
        }else{
             // 系统默认显示信息
            $log  =   Log::$log;
            $files =  get_included_files();
            $_trace   =  array(
                '当前页面'=>__SELF__,
                '请求方法'=>$_SERVER['REQUEST_METHOD'],
                '通信协议'=>$_SERVER['SERVER_PROTOCOL'],
                '请求时间'=>date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']),
                '会话ID'=>session_id(),
                '日志记录'=>count($log)?count($log).'条日志<br/>'.implode('<br/>',$log):'无日志记录',
                '加载文件'=>count($files).str_replace("\n",'<br/>',substr(substr(print_r($files,true),7),0,-2)),
                );
        }
        // 调用Trace页面模板
        ob_start();
        include C('TMPL_TRACE_FILE');
        return ob_get_clean();
    }
}