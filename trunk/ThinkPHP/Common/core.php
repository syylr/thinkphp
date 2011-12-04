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

// 系统默认的核心列表文件
return array(
    THINK_PATH.'Common/functions.php',   // 系统函数库
    CORE_PATH.'Think.class.php',
    CORE_PATH.'ThinkException.class.php',  // 异常处理类
    CORE_PATH.'Log.class.php',    // 日志处理类
    CORE_PATH.'Dispatcher.class.php', // URL调度和路由类
    CORE_PATH.'App.class.php',   // 应用程序类
    CORE_PATH.'Action.class.php', // 控制器类
    //CORE_PATH.'Model.class.php', // 模型类
    CORE_PATH.'View.class.php',  // 视图类
);