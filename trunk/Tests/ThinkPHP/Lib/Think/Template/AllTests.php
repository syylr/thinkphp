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
require_once 'ThinkPHP/Lib/Think/Template/TagLib/AllTests.php';
require_once 'ThinkPHP/Lib/Think/Template/TagLibTest.php';
require_once 'ThinkPHP/Lib/Think/Template/ThinkTemplateTest.php';

class Lib_Think_Template_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('ThinkPHP_Lib_Think_Template');

        $suite->addTest(Lib_Think_Template_TagLib_AllTests::suite());
        $suite->addTestsuite('Lib_Think_Template_TagLibTest');
        $suite->addTestsuite('Lib_Think_Template_ThinkTemplateTest');

        return $suite;
    }
}
?>