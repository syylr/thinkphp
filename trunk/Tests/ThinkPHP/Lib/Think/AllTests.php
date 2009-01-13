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
require_once 'ThinkPHP/Lib/Think/Core/AllTests.php';
require_once 'ThinkPHP/Lib/Think/Db/AllTests.php';
require_once 'ThinkPHP/Lib/Think/Exception/AllTests.php';
require_once 'ThinkPHP/Lib/Think/Template/AllTests.php';
require_once 'ThinkPHP/Lib/Think/Util/AllTests.php';

class Lib_Think_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('ThinkPHP_Lib_Think');

        $suite->addTest(Lib_Think_Core_AllTests::suite());
        $suite->addTest(Lib_Think_Db_AllTests::suite());
        $suite->addTest(Lib_Think_Exception_AllTests::suite());
        $suite->addTest(Lib_Think_Template_AllTests::suite());
        $suite->addTest(Lib_Think_Util_AllTests::suite());

        return $suite;
    }
}
?>