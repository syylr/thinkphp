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

require_once THINK_PATH . '/Lib/Think/Core/Base.class.php';
require_once THINK_PATH . '/Lib/ORG/RBAC/ProviderManager.class.php';
require_once THINK_PATH . '/Lib/ORG/RBAC/Provider/DaoAuthentictionProvider.class.php';

class Lib_ORG_RBAC_Provider_DaoAuthentictionProviderTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * @var    DaoAuthentictionProvider
     * @access protected
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $this->object = new DaoAuthentictionProvider;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
    }

    /**
     * @todo Implement testAuthenticate().
     */
    public function testAuthenticate() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
?>