<?php

$jpath_base = (dirname(__FILE__)) . "/../../../";
$jpath_base = realpath($jpath_base);
require_once "$jpath_base/phpunitbootstrap.php";
//require_once 'PHPUnit/Framework/TestCase.php';
//require_once 'PHPUnit/Autoload.php';

/**
 * Created by PhpStorm.
 * User: khan
 * Date: 6/27/14
 * Time: 7:09 PM
 */
class GsearchServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var GsearchService
     */
    var $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_GSEARCH);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function testDownloadGoogleImageSingle()
    {
        $imageLink = $this->object->searchImage('softverk logo');
        WFactory::getLogger()->debug("got image : $imageLink");
        $this->assertNotEmpty($imageLink);
    }

    public function testSearchWeb()
    {
        $result = $this->object->searchUrl('ramkhamhaeng hospital');

        $this->assertNotNull($result);
    }

    public function testSearchPlaces()
    {
        //search places around BTS nana
        $result = $this->object->searchPlaces("13.7422401","100.5545422","2000");
    }


}
 