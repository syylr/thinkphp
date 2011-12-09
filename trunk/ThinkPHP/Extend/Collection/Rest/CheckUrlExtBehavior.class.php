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
 * 系统行为扩展 
 * 增加配置参数如下：
 +------------------------------------------------------------------------------
 */
class CheckUrlExtBehavior {
    // 行为扩展的执行入口必须是run
    public function run(){
        // 获取资源类型
        if(!empty($_SERVER['PATH_INFO'])) {
            $part =  pathinfo($_SERVER['PATH_INFO']);
            if(isset($part['extension'])) { // 判断扩展名
                define('__EXT__', strtolower($part['extension']));
                $_SERVER['PATH_INFO']   =   preg_replace('/.'.__EXT__.'$/','',$_SERVER['PATH_INFO']);
            }else{
                define('__EXT__', '');
            }
        }
    }

}