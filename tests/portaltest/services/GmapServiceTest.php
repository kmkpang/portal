<?php

$jpath_base = (dirname(__FILE__)) . "/../../../";
$jpath_base = realpath($jpath_base);
require_once "$jpath_base/phpunitbootstrap.php";

//require_once 'PHPUnit/Autoload.php';

/**
 * Created by PhpStorm.
 * User: khan
 * Date: 5/8/14
 * Time: 12:26 AM
 */
class GmapServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var GmapService
     */
    var $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = WFactory::getServices()->getSqlService()->getSqlServiceClass(__PROPPERTY_PORTAL_GMAP);

    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {

    }

    public function testGetGmap()
    {
        logTestStart(__FUNCTION__);
        $this->assertNotEmpty($this->object->getGmap(53));
    }

    public function test_searchLocationByName()
    {


        $result = $this->object->searchLocationByName("Krunthep Kritha Baan Klang Muang");
        $this->assertNotEmpty($result);
    }

    public function test_cluster()
    {

        $propertiesQuery = "SELECT latitude,longitude,id FROM `jos_portal_properties`";
        $properties = WFactory::getSqlService()->select($propertiesQuery);

        $markers = array();
        foreach ($properties as $m) {
            $markers[] = array("lat" => $m['latitude'], "lon" => $m['longitude']);
        }
        $markerCount = count($markers);
        $result = $this->object->cluster($markers, 50, 1);
        $clusteredCount = count($result);

        $this->assertTrue($clusteredCount < $markerCount);
    }
}
 