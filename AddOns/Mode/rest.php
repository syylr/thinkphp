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
    THINK_PATH.'Common/functions.php',   // 系统函数库
    THINK_PATH.'Lib/Think/Core/Think.class.php',
    THINK_PATH.'Lib/Think/Exception/ThinkException.class.php',// 异常处理
    THINK_PATH.'Lib/Think/Core/Log.class.php',// 日志处理
    THINK_PATH.'Lib/Think/Core/App.class.php', // 应用程序类
    MODE_PATH.'Rest/Dispatcher.class.php',// URL路由
    MODE_PATH.'Rest/Action.class.php',// 控制器类
    MODE_PATH.'Rest/common.php', // 加载REST公共函数
);
?>