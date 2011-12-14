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
 * 系统行为扩展 布局模板
 +------------------------------------------------------------------------------
 */
class LayoutTemplateBehavior extends Behavior {
    protected $options  =   array(
            'LAYOUT_ON'           => true, // 是否启用布局
            'LAYOUT_NAME'       => 'layout', // 当前布局名称 默认为layout
        );

    public function run(&$templateFile) {
        // 读取布局模板
        if(C('LAYOUT_ON')) {
            $layoutName  =  C('LAYOUT_NAME');
            $layoutFile  =  APP_TMPL_PATH.$layoutName.C('TMPL_TEMPLATE_SUFFIX');
            if(is_file($layoutFile)) {
                $layoutCacheFile   = C('CACHE_PATH').md5($templateFile).C('TMPL_TEMPLATE_SUFFIX');
                if(!is_file($layoutCacheFile) || filemtime($templateFile) > filemtime($layoutCacheFile)) {
                    // 检测缓存目录
                    if(!is_dir(C('CACHE_PATH')))   mk_dir(C('CACHE_PATH'));
                    // 写入布局缓存文件
                    $content = file_get_contents($templateFile);
                    if(false !== strpos($content,'{__NOLAYOUT__}')) { // 可以单独定义不使用布局
                        $content = str_replace('{__NOLAYOUT__}','',$content);
                    }else{ // 替换布局的主体内容
                        $content = str_replace('{__CONTENT__}',$content,file_get_contents($layoutFile));
                    }
                    file_put_contents($layoutCacheFile,trim($content));
                }
                $templateFile   = $layoutCacheFile;
            }
        }
    }
}