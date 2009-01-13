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
require_once 'ThinkPHP/Lib/ORG/RBAC/Provider/AllTests.php';
require_once 'ThinkPHP/Lib/ORG/RBAC/AccessDecisionManagerTest.php';
require_once 'ThinkPHP/Lib/ORG/RBAC/ProviderManagerTest.php';
require_once 'ThinkPHP/Lib/ORG/RBAC/RBACTest.php';

class Lib_ORG_RBAC_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('ThinkPHP_Lib_ORG_RBAC');

        $suite->addTest(Lib_ORG_RBAC_Provider_AllTests::suite());
        $suite->addTestSuite('Lib_ORG_RBAC_AccessDecisionManagerTest');
        $suite->addTestSuite('Lib_ORG_RBAC_ProviderManagerTest');
        $suite->addTestSuite('Lib_ORG_RBAC_RBACTest');

        return $suite;
    }
}
?>