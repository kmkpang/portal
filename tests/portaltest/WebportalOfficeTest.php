<?php

$jpath_base = (dirname(__FILE__)) . "/../../";
$jpath_base = realpath($jpath_base);
require_once "$jpath_base/phpunitbootstrap.php";
//require_once 'PHPUnit/Framework/TestCase.php';
//require_once 'PHPUnit/Autoload.php';

/**
 * Created by JetBrains PhpStorm.
 * User: khan
 * Date: 7/15/13
 * Time: 3:44 PM
 * To change this template use File | Settings | File Templates.
 */

/**
 * @covers OfficeService
 * Class WebportalOfficeTest
 */
class WebportalOfficeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var OfficeService
     */
    var $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object =  $this->object = WFactory::getServices()->getSqlService()->getSqlServiceClass(__PROPPERTY_PORTAL_OFFICE);;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {

    }

    /**
     * @covers OfficeService::getOffice
     */
    public function testGetOffice()
    {
        $office = $this->object->getOffice(37);
        $this->assertNotEmpty($office);
    }


}
