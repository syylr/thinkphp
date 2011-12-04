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
 * 系统行为扩展 模板解析
 * 增加配置参数如下：
 +------------------------------------------------------------------------------
 */
class ParseTemplateBehavior {
    // 行为扩展的执行入口必须是run
    public function run($_data){
        $engine  = strtolower(C('TMPL_ENGINE_TYPE'));
        if('php'==$engine) {
            // 模板阵列变量分解成为独立变量
            extract($_data['var'], EXTR_OVERWRITE);
            // 直接载入PHP模板
            include $_data['file'];
        }elseif('think'==$engine && $this->checkCache($_data['file'])) {
            // 如果是Think模板引擎并且缓存有效 分解变量并载入模板缓存
            extract($_data['var'], EXTR_OVERWRITE);
            //载入模版缓存文件
            include C('CACHE_PATH').md5($_data['file']).C('TMPL_CACHFILE_SUFFIX');
        }else{
            // 模板文件需要重新编译 支持第三方模板引擎
            // 调用模板引擎解析和输出
            $className   = 'Template'.ucwords($engine);
            require_cache(LIBRARY_PATH.'Think/Util/Template/'.$className.'.class.php');
            $tpl   =  new $className;
            $tpl->fetch($_data['file'],$_data['var']);
        }
    }


    /**
     +----------------------------------------------------------
     * 检查缓存文件是否有效
     * 如果无效则需要重新编译
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $tmplTemplateFile  模板文件名
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
    protected function checkCache($tmplTemplateFile) {
        if (!C('TMPL_CACHE_ON')) // 优先对配置设定检测
            return false;
        $tmplCacheFile = C('CACHE_PATH').md5($tmplTemplateFile).C('TMPL_CACHFILE_SUFFIX');
        if(!is_file($tmplCacheFile)){
            return false;
        }elseif (filemtime($tmplTemplateFile) > filemtime($tmplCacheFile)) {
            // 模板文件如果有更新则缓存需要更新
            return false;
        }elseif (C('TMPL_CACHE_TIME') != -1 && time() > filemtime($tmplCacheFile)+C('TMPL_CACHE_TIME')) {
            // 缓存是否在有效期
            return false;
        }
        //缓存有效
        return true;
    }
}