<?php


$jpath_base = (dirname(__FILE__)) . "/../../../";
$jpath_base = realpath($jpath_base);
require_once "$jpath_base/phpunitbootstrap.php";
//require_once 'PHPUnit/Framework/TestCase.php';
//require_once 'PHPUnit/Autoload.php';
/**
 * Created by PhpStorm.
 * User: khan
 * Date: 8/17/14
 * Time: 10:59 AM
 */
class GoogleapiServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var GoogleapiService
     */
    var $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_GAPI);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function testGetPageViewCount()
    {
        $this->assertNotEmpty($this->object->getPageViewCount('/property/\d*'));
    }

}
 