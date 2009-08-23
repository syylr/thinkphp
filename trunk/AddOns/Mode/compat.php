<?php
// +----------------------------------------------------------------------
// | ThinkPHP
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

// 兼容模式核心文件列表
return array(
    THINK_PATH.'/Lib/Think/Exception/ThinkException.class.php',
    THINK_PATH.'/Mode/Compat/Log.class.php',
    THINK_PATH.'/Mode/Compat/App.class.php',
    THINK_PATH.'/Mode/Compat/Action.class.php',
    THINK_PATH.'/Mode/Compat/Model.class.php',
    THINK_PATH.'/Mode/Compat/View.class.php',
    THINK_PATH.'/Mode/Compat/compat.php',
    THINK_PATH.'/Mode/Compat/alias.php',  // 加载别名
);
?>