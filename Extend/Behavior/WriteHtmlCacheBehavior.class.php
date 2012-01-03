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
 * 系统行为扩展 静态缓存写入
 * 增加配置参数如下：
 +------------------------------------------------------------------------------
 */
class WriteHtmlCacheBehavior extends Behavior {
    protected $options   =  array(
            'HTML_CACHE_ON'=>true,
        );
    // 行为扩展的执行入口必须是run
    public function run(&$content){
        if(C('HTML_CACHE_ON'))  {
            import('ORG.Util.HtmlCache');
            HtmlCache::writeHtmlCache($content);
        }
    }

}