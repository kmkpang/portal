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
class OfficeServiceTest extends PHPUnit_Framework_TestCase
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
        $this->object = WFactory::getServices()->getSqlService()->getSqlServiceClass(__PROPPERTY_PORTAL_OFFICE);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {

    }

    public function test__deployFirstOffice()
    {
        $this->assertTrue($this->object->deployFirstOffice());
    }

    public function testGetOffice()
    {
        logTestStart(__FUNCTION__);

        $data = $this->object->getOffice(47);
        $json = json_encode($data);

        print_r($json);

        $this->assertNotEmpty($this->object->getOffice(53));
    }

    public function test_searchOfficeByLocation()
    {
        /**
         * @var $searchModel SearchModel
         */
        $searchModel = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_SEARCH)->getSearchModel();

        $searchModel->region_id = 9;
        $searchModel->city_town_id = 89;

//        $result = $this->object->searchOfficeByLocation($searchModel);
//        $this->assertNotEmpty($result);

        $result = $this->object->searchOfficeByLocation();
        $this->assertNotEmpty($result);
    }

    public function test__getOffices()
    {
        $result = $this->object->getOffices();
        $this->assertNotEmpty($result);
    }

    public function test__deletOffice()
    {
        $result = $this->object->deleteOffice(47);
        $this->assertNotEmpty($result);
    }

    public function  test__officeUpdate()
    {
        $office = $this->object->getOfficeModel();
        $office->show_on_web = 0;
        $office->id = 47;
        $result = $this->object->updateOffice($office);
        $this->assertNotEmpty($result);
    }

    public function  test__officeUpdateComplex()
    {
        $office = $this->object->getOffice(47, true);

        $result = $this->object->updateOffice($office);
        $this->assertNotEmpty($result);
    }

    public function test__GetOfficeAsObject()
    {
        logTestStart(__FUNCTION__);
        $this->assertObjectHasAttribute("id", $this->object->getOffice(47, true));
    }
}
 