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
 * 系统行为扩展 模板检测
 * 增加配置参数如下：
 +------------------------------------------------------------------------------
 */
class CheckTemplateBehavior {
    // 行为扩展的执行入口必须是run
    public function run(){
        // 开启静态缓存
        $this->checkTemplate();
    }

    /**
     +----------------------------------------------------------
     * 模板检查，如果不存在使用默认
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    private function checkTemplate() {
        /* 获取模板主题名称 */
        $templateSet =  C('DEFAULT_THEME');
        if(C('TMPL_DETECT_THEME')) {// 自动侦测模板主题
            $t = C('VAR_TEMPLATE');
            if (isset($_GET[$t])){
                $templateSet = $_GET[$t];
            }elseif(cookie('think_template')){
                $templateSet = cookie('think_template');
            }
            // 主题不存在时仍改回使用默认主题
            if(!is_dir(TMPL_PATH.$templateSet))
                $templateSet = C('DEFAULT_THEME');
            cookie('think_template',$templateSet);
        }

        /* 模板相关目录常量 */
        define('THEME_NAME',   $templateSet);                  // 当前模板主题名称
        define('APP_TMPL_PATH',   __ROOT__.'/'.APP_NAME.(APP_NAME?'/':'').TMPL_DIR.'/'.THEME_NAME.(THEME_NAME?'/':''));// 当前项目模板目录
        define('TEMPLATE_PATH',   TMPL_PATH.THEME_NAME.(THEME_NAME?'/':''));       // 当前模版路径
        define('__CURRENT__',     APP_TMPL_PATH.MODULE_NAME);     // 当前默认模板目录

        if(defined('GROUP_NAME')) {
            C('TEMPLATE_NAME',TEMPLATE_PATH.GROUP_NAME.'/'.MODULE_NAME.C('TMPL_FILE_DEPR').ACTION_NAME.C('TMPL_TEMPLATE_SUFFIX'));
            C('CACHE_PATH',CACHE_PATH.GROUP_NAME.'/');
        }else{
            C('TEMPLATE_NAME',TEMPLATE_PATH.MODULE_NAME.'/'.ACTION_NAME.C('TMPL_TEMPLATE_SUFFIX'));
            C('CACHE_PATH',CACHE_PATH);
        }
        return ;
    }
}