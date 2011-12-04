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
 * 系统行为扩展 语言检测 并自动加载语言包
 * 增加配置参数如下：
 *  DEFAULT_LANG 
 *  LANG_SWITCH_ON LANG_AUTO_DETECT
 +------------------------------------------------------------------------------
 */
class CheckLangBehavior {
    // 行为扩展的执行入口必须是run
    public function run(){
        // 开启静态缓存
        $this->checkLanguage();
    }

    /**
     +----------------------------------------------------------
     * 语言检查
     * 检查浏览器支持语言，并自动加载语言包
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    private function checkLanguage() {
        $langSet = C('DEFAULT_LANG');
        // 不开启语言包功能，仅仅加载框架语言文件直接返回
        if (!C('LANG_SWITCH_ON')){
            L(include EXTEND_PATH.'Lang/'.$langSet.'.php');
            return;
        }
        // 启用了语言包功能
        // 根据是否启用自动侦测设置获取语言选择
        if (C('LANG_AUTO_DETECT')){
            if(isset($_GET[C('VAR_LANGUAGE')])){
                $langSet = $_GET[C('VAR_LANGUAGE')];// url中设置了语言变量
                cookie('think_language',$langSet,3600);
            }elseif(cookie('think_language')){// 获取上次用户的选择
                $langSet = cookie('think_language');
            }elseif(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])){// 自动侦测浏览器语言
                preg_match('/^([a-z\-]+)/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches);
                $langSet = $matches[1];
                cookie('think_language',$langSet,3600);
            }
            if(false === stripos(C('LANG_LIST'),$langSet)) { // 非法语言参数
                $langSet = C('DEFAULT_LANG');
            }
        }
        // 定义当前语言
        define('LANG_SET',strtolower($langSet));
        // 加载框架语言包
        if(is_file(EXTEND_PATH.'Lang/'.LANG_SET.'.php'))
            L(include EXTEND_PATH.'Lang/'.LANG_SET.'.php');
        // 读取项目公共语言包
        if (is_file(LANG_PATH.LANG_SET.'/common.php'))
            L(include LANG_PATH.LANG_SET.'/common.php');
        $group = '';
        // 读取当前分组公共语言包
        if (defined('GROUP_NAME')){
            $group = GROUP_NAME.C('TMPL_FILE_DEPR');
            if (is_file(LANG_PATH.LANG_SET.'/'.$group.'lang.php'))
                L(include LANG_PATH.LANG_SET.'/'.$group.'lang.php');
        }
        // 读取当前模块语言包
        if (is_file(LANG_PATH.LANG_SET.'/'.$group.strtolower(MODULE_NAME).'.php'))
            L(include LANG_PATH.LANG_SET.'/'.$group.strtolower(MODULE_NAME).'.php');
    }
}