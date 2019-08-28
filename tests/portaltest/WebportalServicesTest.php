<?php

$jpath_base = (dirname(__FILE__)) . "/../../";
$jpath_base = realpath($jpath_base);
require_once "$jpath_base/phpunitbootstrap.php";
//require_once 'PHPUnit/Framework/TestCase.php';
//require_once 'PHPUnit/Autoload.php';

/**
 * Created by PhpStorm.
 * User: khan
 * Date: 5/10/14
 * Time: 5:49 PM
 */

class WebportalServicesTest extends PHPUnit_Framework_TestCase {
 /**
     * @var WebportalServices
     */
    var $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = WFactory::getServices();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers WebportalServices::getWebservice
     */
    function testGetWebervice()
    {
        $result = $this->object->getWebservice('properties.search');
        $this->assertFalse($result); // this will return false BECAUSE no search model is passed
    }
}
 