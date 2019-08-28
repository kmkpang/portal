<?php


$jpath_base = (dirname(__FILE__)) . "/../../../";
$jpath_base = realpath($jpath_base);
require_once "$jpath_base/phpunitbootstrap.php";
//require_once 'PHPUnit/Framework/TestCase.php';

//require_once 'PHPUnit/Autoload.php';

/**
 * Created by PhpStorm.
 * User: khan
 * Date: 6/25/14
 * Time: 3:36 PM
 */
class RequestinfoServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var RequestinfoService
     */
    var $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_REQUESTINFO);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function testSaveContactObject()
    {
        $requestInfoModel = $this->object->getRequestinfoModel();

        $requestInfoModel->contact_email = "khan@gmail.com";
        $requestInfoModel->contact_first_name = "Khan";
        $requestInfoModel->contact_last_name = "Khan2";
        $requestInfoModel->contact_phone = "1234";
        $requestInfoModel->message = "Yo!";
        $requestInfoModel->contact_province = "P";
        $requestInfoModel->contact_province_of_interest = "P2";
        $requestInfoModel->contact_province = "P3";
        $requestInfoModel->contact_district_of_interest = "D1";
        $requestInfoModel->previous_experience = "blaha";
        $requestInfoModel->interested_to_be = "agent";

        $result = $this->object->saveRequestinfo($requestInfoModel);

        $this->assertNotNull($result);
        $this->assertTrue(is_numeric($result));

    }

    public function testSendMailToDefaultCompany()
    {
        $requestInfoModel = $this->object->getRequestinfoModel();

        $requestInfoModel->contact_email = "khan@gmail.com";
        $requestInfoModel->contact_first_name = "Khan";
        $requestInfoModel->contact_last_name = "Khan2";
        $requestInfoModel->contact_phone = "1234";
        $requestInfoModel->message = "Yo!";
        $requestInfoModel->contact_province = "P";
        $requestInfoModel->contact_province_of_interest = "P2";
        $requestInfoModel->contact_province = "P3";
        $requestInfoModel->contact_district_of_interest = "D1";
        $requestInfoModel->previous_experience = "blaha";
        $requestInfoModel->interested_to_be = "agent";

        $result = $this->object->sendMailToDefaultCompany($requestInfoModel);

        $this->assertNotNull($result);
        $this->assertTrue(is_numeric($result));
    }


    public function testGetRequestinfoAsExcel()
    {
        logTestStart(__FUNCTION__);
        try {
            $filePath = $this->object->getRequestinfoAsExcel();


            $x = file_exists($filePath);
            $this->assertTrue($x);

        } catch (Exception $e) {
            WFactory::getLogger()->error($e->getMessage());
            $this->assertTrue(false);
        }
    }
}
 