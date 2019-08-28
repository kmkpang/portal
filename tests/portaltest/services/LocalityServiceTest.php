<?php
/**
 * Created by PhpStorm.
 * User: khan
 * Date: 1/2/16
 * Time: 11:33 PM
 */

$jpath_base = (dirname(__FILE__)) . "/../../../";
$jpath_base = realpath($jpath_base);
require_once "$jpath_base/phpunitbootstrap.php";


class LocalityServiceTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var LocalityService
     */
    var $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_LOCALITY);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function test__getLocalAttractions(){


        $this->assertNotEmpty($this->object->getLocalAttractions(10439));




    }



}
