<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2008 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

// 导入别名定义
alias_import(array(
    'Dispatcher'         =>   MODE_PATH.'Lite/Dispatcher.class.php',
    'Model'         =>   MODE_PATH.'Lite/Model.class.php',
    'Db'                  =>    MODE_PATH.'Lite/Db.class.php',
    'Debug'              =>    THINK_PATH.'Lib/Think/Util/Debug.class.php',
    'Session'             =>   THINK_PATH.'Lib/Think/Util/Session.class.php',
    'ThinkTemplateLite'   =>    MODE_PATH.'Lite/ThinkTemplateLite.class.php',
    'ThinkTemplateCompiler'   =>    MODE_PATH.'Lite/ThinkTemplateCompiler.class.php',
    )
);
?>