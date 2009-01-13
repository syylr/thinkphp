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
require_once 'ThinkPHP/Lib/Think/Template/TagLib/TagLibCxTest.php';
require_once 'ThinkPHP/Lib/Think/Template/TagLib/TagLibHtmlTest.php';

class Lib_Think_Template_TagLib_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('ThinkPHP_Lib_Think_Template_TagLib');

        $suite->addTestsuite('Lib_Think_Template_TagLib_TagLibCxTest');
        $suite->addTestsuite('Lib_Think_Template_TagLib_TagLibHtmlTest');

        return $suite;
    }
}
?>