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
require_once THINK_PATH . '/Lib/ORG/Util/Page.class.php';

class Lib_ORG_Util_PageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    Page
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
        C('PAGE_NUMBERS', 10);
        C('LIST_NUMBERS', 10);
        C('VAR_PAGE', 'page');
        $this->object = new Page(10, 10, 'q=test');
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
     * @todo Implement testSetConfig().
     */
    public function testSetConfig() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testShow().
     */
    public function testShow() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
?>