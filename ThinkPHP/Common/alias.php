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
return array(
    // 系统核心类
    'Model'         => THINK_PATH.'Lib/Think/Core/Model.class.php',
    'Dispatcher'    => THINK_PATH.'Lib/Think/Core/Dispatcher.class.php',
    'HtmlCache'     => THINK_PATH.'Lib/Think/Util/HtmlCache.class.php',
    'Db'            => THINK_PATH.'Lib/Think/Db/Db.class.php',
    'ThinkTemplate' => THINK_PATH.'Lib/Think/Template/ThinkTemplate.class.php',
    'Template'      => THINK_PATH.'Lib/Think/Util/Template.class.php',
    'TagLib'        => THINK_PATH.'Lib/Think/Template/TagLib.class.php',
    'Cache'         => THINK_PATH.'Lib/Think/Util/Cache.class.php',
    'Debug'         => THINK_PATH.'Lib/Think/Util/Debug.class.php',
    'Session'       => THINK_PATH.'Lib/Think/Util/Session.class.php',
    'TagLibCx'      => THINK_PATH.'Lib/Think/Template/TagLib/TagLibCx.class.php',
    'TagLibHtml'    => THINK_PATH.'Lib/Think/Template/TagLib/TagLibHtml.class.php',
    'ViewModel'     => THINK_PATH.'Lib/Think/Core/Model/ViewModel.class.php',
    'AdvModel'      => THINK_PATH.'Lib/Think/Core/Model/AdvModel.class.php',
    'RelationModel' => THINK_PATH.'Lib/Think/Core/Model/RelationModel.class.php',
    'MongoModel'  => THINK_PATH.'Lib/Think/Core/Model/MongoModel.class.php',
    // 系统行为扩展
    'CheckLangBehavior'=>EXTEND_PATH.'Behavior/CheckLangBehavior.class.php',
    'CheckTemplateBehavior'=>EXTEND_PATH.'Behavior/CheckTemplateBehavior.class.php',
    'ReadHTMLCacheBehavior'=>EXTEND_PATH.'Behavior/ReadHTMLCacheBehavior.class.php',
    'WriteHTMLCacheBehavior'=>EXTEND_PATH.'Behavior/WriteHTMLCacheBehavior.class.php',
    'ShowRuntimeBehavior'=>EXTEND_PATH.'Behavior/ShowRuntimeBehavior.class.php',
    'ShowPageTraceBehavior'=>EXTEND_PATH.'Behavior/ShowPageTraceBehavior.class.php',
    'ContentReplaceBehavior'=>EXTEND_PATH.'Behavior/ContentReplaceBehavior.class.php',
    'LocationTemplateBehavior'=>EXTEND_PATH.'Behavior/LocationTemplateBehavior.class.php',
    );