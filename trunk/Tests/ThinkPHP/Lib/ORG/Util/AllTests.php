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
require_once 'ThinkPHP/Lib/ORG/Util/AppCodingSwitchTest.php';
require_once 'ThinkPHP/Lib/ORG/Util/ArrayListTest.php';
require_once 'ThinkPHP/Lib/ORG/Util/HashMapTest.php';
require_once 'ThinkPHP/Lib/ORG/Util/ImageTest.php';
require_once 'ThinkPHP/Lib/ORG/Util/PageTest.php';
require_once 'ThinkPHP/Lib/ORG/Util/StackTest.php';

class Lib_ORG_Util_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('ThinkPHP_Lib_ORG_Util');

        $suite->addTestSuite('Lib_ORG_Util_AppCodingSwitchTest');
        $suite->addTestSuite('Lib_ORG_Util_ArrayListTest');
        $suite->addTestSuite('Lib_ORG_Util_HashMapTest');
        $suite->addTestSuite('Lib_ORG_Util_ImageTest');
        $suite->addTestSuite('Lib_ORG_Util_PageTest');
        $suite->addTestSuite('Lib_ORG_Util_StackTest');
 
        return $suite;
    }
}
?>