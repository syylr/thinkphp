<?php
// +----------------------------------------------------------------------
// | ThinkPHP
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
    'Dispatcher'=>THINK_PATH.'/Mode/Compat/Dispatcher.class.php',
    'HtmlCache'=>THINK_PATH.'/Lib/Think/Util/HtmlCache.class.php',
    'Db'=>THINK_PATH.'/Mode/Compat/Db.class.php',
    'ResultIterator'=>THINK_PATH.'/Mode/Compat/ResultIterator.class.php',
    'ThinkTemplate'=>THINK_PATH.'/Lib/Think/Template/ThinkTemplate.class.php',
    'Template'=>THINK_PATH.'/Lib/Think/Util/Template.class.php',
    'TagLib'=>THINK_PATH.'/Lib/Think/Template/TagLib.class.php',
    'Cache'=>THINK_PATH.'/Lib/Think/Util/Cache.class.php',
    'Cookie'=>THINK_PATH.'/Lib/Think/Util/Cookie.class.php',
    'Session'=>THINK_PATH.'/Lib/Think/Util/Session.class.php',
    'Page'=>THINK_PATH.'/Lib/ORG/Util/Page.class.php',
    'Filter'=>THINK_PATH.'/Lib/Think/Util/Filter.class.php',
    'RBAC'=>THINK_PATH.'/Lib/ORG/RBAC/RBAC.class.php',
    'TagLibCx'=>THINK_PATH.'/Lib/Think/Template/TagLib/TagLibCx.class.php',
    )
);
?>