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

// Require test suites
require_once 'ThinkPHP/Lib/Think/Core/ActionTest.php';
require_once 'ThinkPHP/Lib/Think/Core/AppTest.php';
require_once 'ThinkPHP/Lib/Think/Core/BaseTest.php';
require_once 'ThinkPHP/Lib/Think/Core/ModelTest.php';
require_once 'ThinkPHP/Lib/Think/Core/ViewTest.php';

class Lib_Think_Core_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('ThinkPHP_Lib_Think_Core');

        $suite->addTestsuite('Lib_Think_Core_ActionTest');
        $suite->addTestsuite('Lib_Think_Core_AppTest');
        $suite->addTestsuite('Lib_Think_Core_BaseTest');
        $suite->addTestsuite('Lib_Think_Core_ModelTest');
        $suite->addTestsuite('Lib_Think_Core_ViewTest');

        return $suite;
    }
}
?>