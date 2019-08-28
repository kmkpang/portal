<?php
/**
 * Created by PhpStorm.
 * User: khan
 * Date: 1/3/16
 * Time: 7:06 AM
 */

$jpath_base = (dirname(__FILE__)) . "/../../../";
$jpath_base = realpath($jpath_base);
require_once "$jpath_base/phpunitbootstrap.php";


class LocationImporterTestIcelandic extends PHPUnit_Framework_TestCase
{
    /**
     * @var LocationImporter
     */
    var $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        require_once JPATH_BASE . "/libraries/webportal/services/locality/IS/locationImporter.php";
        $this->object = new LocationImporter();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function test__importBustStand()
    {
        $this->assertTrue($this->object->importBustStand());
    }

    public function test__importSchools()
    {
        $this->assertTrue($this->object->importSchools());
    }

    public function test__import()
    {
        $this->object->import();
    }


}
