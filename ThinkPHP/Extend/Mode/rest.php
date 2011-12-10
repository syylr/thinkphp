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

// REST模式核心定义文件列表
return array(

    'core'         =>   array(
        THINK_PATH.'Common/functions.php',   // 系统函数库
        CORE_PATH.'Core/Log.class.php',// 日志处理
        MODE_PATH.'Core/App.class.php', // 应用程序类
        MODE_PATH.'Rest/Action.class.php',// 控制器类
    ),

    // 项目别名定义文件 [支持数组直接定义或者文件名定义]
    'alias'         =>    array(), 

    // 系统行为定义文件 [必须 支持数组直接定义或者文件名定义 ]
    'extends'    =>    MODE_PATH.'Rest/tags.php',

    // 项目应用行为定义文件 [支持数组直接定义或者文件名定义]
    'tags'         =>   array(), 

    // 项目公共文件
    'common'   =>    COMMON_PATH.'common.php', 

    // 模式配置文件  [支持数组直接定义或者文件名定义]（如有相同则覆盖项目配置文件中的配置）
    'config'   =>   MODE_PATH.'Rest/config.php',
);
?>