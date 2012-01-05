<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

/**
 +------------------------------------------------------------------------------
 * 系统行为扩展 静态缓存URL检测
 +------------------------------------------------------------------------------
 */
class HtmlCheckBehavior extends Behavior {
    protected $options   =  array(
            'HTML_CACHE_ON'    => false, // 开启静态缓存
            'HTML_FILE_SUFFIX' => '.shtml', // 静态URL后缀
            'HTML_CACHE_TIME' => 60,   // 静态缓存有效期 秒 仅在动态访问有效
        );
    public function run(&$params) {
        if(C('HTML_CACHE_ON') && $_SERVER['PATH_INFO']) {
            $part =  pathinfo($_SERVER['PATH_INFO']);
            if(isset($part['extension']) && $part['extension'] == ltrim(C('HTML_FILE_SUFFIX'),'.')) {
                $htmlFile = HTML_PATH.ltrim($_SERVER['PATH_INFO'],'/');
                if(is_file($htmlFile) && filemtime($htmlFile)<time()-C('HTML_CACHE_TIME')) {
                    readfile($htmlFile);
                    exit;
                }else{
                    $paths = explode('/',ltrim($_SERVER['PATH_INFO'],'/'));
                    if($paths[0]==basename(HTML_PATH)) {
                        // 开启静态缓存 则过滤URL地址中的静态目录名称
                        array_shift($paths);
                        $_SERVER['PATH_INFO'] =  implode('/',$paths);
                        define('__HTML__',$_SERVER['PATH_INFO']);
                    }
                }
            }
        }
    }
}