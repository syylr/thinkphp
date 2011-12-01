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

// 系统默认的核心行为扩展列表文件
// CheckLang行为 对应 Think/Extend/Behavior/CheckLangBehavior.class.php 

return array(
    // 陆续添加
    'app_init'=>array(),
    'app_begin'=>array('CheckLang', // 语言检测
        'CheckTemplate', // 模板检测
    ),
    'app_end'=>array(),
    'action_begin'=>array(),
    'action_end'=>array(),
    'view_begin'=>array(
        'LocationTemplate', // 自动定位模板文件
    ),
    'view_parse'=>array(
        'ParseTemplate', // 模板解析 支持PHP、内置模板引擎和第三方模板引擎
    ),
    'view_filter'=>array(
        'ContentReplace', // 模板输出替换
        'ShowRuntime', // 运行时间显示
        'ShowPageTrace', // 页面Trace显示
    ),
    'view_end'=>array(
    ),
);
?>