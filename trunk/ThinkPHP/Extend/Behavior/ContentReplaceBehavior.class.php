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
 * 系统行为扩展 模板内容输出替换 包含令牌自动生成
 * 增加配置参数如下：
 +------------------------------------------------------------------------------
 */
class ContentReplaceBehavior {
    // 行为扩展的执行入口必须是run
    public function run(&$content){
        $content = $this->templateContentReplace($content);
    }

    /**
     +----------------------------------------------------------
     * 模板内容替换
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param string $content 模板内容
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    protected function templateContentReplace($content) {
        // 系统默认的特殊变量替换
        $replace =  array(
            '__TMPL__'      => APP_TMPL_PATH,  // 项目模板目录
            '__ROOT__'      => __ROOT__,       // 当前网站地址
            '__APP__'       => __APP__,        // 当前项目地址
            '__GROUP__'   =>   defined('GROUP_NAME')?__GROUP__:__APP__,
            '__ACTION__'    => __ACTION__,     // 当前操作地址
            '__SELF__'      => __SELF__,       // 当前页面地址
            '__URL__'       => __URL__,
            '__INFO__'      => __INFO__,
        );
        if(C('TOKEN_ON')) {
            if(strpos($content,'{__TOKEN__}')) {
                // 指定表单令牌隐藏域位置
                $replace['{__TOKEN__}'] =  $this->buildFormToken();
            }elseif(strpos($content,'{__NOTOKEN__}')){
                // 标记为不需要令牌验证
                $replace['{__NOTOKEN__}'] =  $this->buildFormToken();
            }elseif(preg_match('/<\/form(\s*)>/is',$content,$match)) {
                // 智能生成表单令牌隐藏域
                $replace[$match[0]] = $this->buildFormToken().$match[0];
            }
        }
        // 允许用户自定义模板的字符串替换
        if(is_array(C('TMPL_PARSE_STRING')) )
            $replace =  array_merge($replace,array_change_key_case(C('TMPL_PARSE_STRING'),CASE_UPPER));
        $content = str_replace(array_keys($replace),array_values($replace),$content);
        return $content;
    }

    /**
     +----------------------------------------------------------
     * 创建表单令牌隐藏域
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    private function buildFormToken() {
        $tokenName   = C('TOKEN_NAME');
        $tokenType = C('TOKEN_TYPE');
        if(!isset($_SESSION[$tokenName])) {
            $_SESSION[$tokenName]  = array();
        }
        // 标识当前页面唯一性
        $tokenKey  =  md5(__SELF__);
        if(isset($_SESSION[$tokenName][$tokenKey])) {// 相同页面不重复生成session
            $tokenValue = $_SESSION[$tokenName][$tokenKey];
        }else{
            $tokenValue = $tokenType(microtime(TRUE));
            $_SESSION[$tokenName][$tokenKey]   =  $tokenValue;
        }
        $token   =  '<input type="hidden" name="'.$tokenName.'" value="'.$tokenKey.'_'.$tokenValue.'" />';
        return $token;
    }
}