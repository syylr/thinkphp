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

// 导入别名定义
alias_import(array(
    'View' =>CORE_PATH.'Core/View.class.php',
    'Model'         => CORE_PATH.'Core/Model.class.php',
    'HtmlCache'     => CORE_PATH.'Util/HtmlCache.class.php',
    'Db'            => CORE_PATH.'Db/Db.class.php',
    'ThinkTemplate' => CORE_PATH.'Template/ThinkTemplate.class.php',
    'Template'      => CORE_PATH.'Util/Template.class.php',
    'TagLib'        => CORE_PATH.'Template/TagLib.class.php',
    'Cache'         => CORE_PATH.'Util/Cache.class.php',
    'Debug'         => CORE_PATH.'Util/Debug.class.php',
    'Session'       => CORE_PATH.'Util/Session.class.php',
    'TagLibCx'      => CORE_PATH.'Template/TagLib/TagLibCx.class.php',
    )
);