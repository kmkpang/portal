<?php

$jpath_base = (dirname(__FILE__)) . "/../../../";
$jpath_base = realpath($jpath_base);
require_once "$jpath_base/phpunitbootstrap.php";
//require_once 'PHPUnit/Framework/TestCase.php';
//require_once 'PHPUnit/Autoload.php';

/**
 * Created by PhpStorm.
 * User: khan
 * Date: 8/16/14
 * Time: 11:02 PM
 */
class GanalyticsServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var GanalyticsService
     */
    var $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_GANALTICS);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function testUpdateAllPropertyViewCount()
    {
        $this->assertTrue($this->object->updateAllPropertyViewCount());
    }

    public function testGetPageViewCount()
    {

        $result = $this->object->getPropertyViewCount();
        $this->assertNotEmpty($result);

        $result = $this->object->getPropertyViewCount(3164);
        $this->assertTrue(is_int($result) && $result > 0);


    }

}
 