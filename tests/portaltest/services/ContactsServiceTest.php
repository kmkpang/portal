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
class ContactsServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ContactsService
     */
    var $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_CONTACTS);
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
        $contactModel = $this->object->getContactsModel();

        $contactModel->contact_email = "khan@gmail.com";
        $contactModel->contact_name = "Khan";
        $contactModel->contact_phone = "1234";
        $contactModel->message = "Yo!";
        $contactModel->contact_category = "CAT 1";
        $contactModel->contact_city = "y";

        $result = $this->object->saveContact($contactModel);

        $this->assertNotNull($result);
        $this->assertTrue(is_numeric($result));

    }

    public function test__sendPropertyMailToFriend()
    {
        $contactModel = $this->object->getContactsModel();

        $contactModel->from_email = "khan@gmail.com";
        $contactModel->to_email = "shroukkhan@gmail.com , shroukkhan@hotmail.com";
        $contactModel->property_id = "10393";
        $contactModel->message = "Yo!";
        $contactModel->contact_category = "MAIL TO FRIEND";

        $result = $this->object->sendPropertyMailToFriend($contactModel);

        $this->assertNotNull($result);
        $this->assertTrue(is_numeric($result));
    }

    public function test__sendPropertyMailToFriendMultipleProperties()
    {
        $contactModel = $this->object->getContactsModel();

        $contactModel->from_email = "shroukkhan@gmail.com";
        $contactModel->to_email = "shroukkhan@gmail.com";
        $contactModel->property_id = array("10538", "10543", "10544"
        , "10545", "10546", "10547", "10548", "10549", "10550", "10551", "10552", "10553", "10555", "10556", "10557", "10558"
        , "10559", "10560", "10561", "10562", "10563", "10564", "10565","10538", "10543", "10544"
        , "10545", "10546", "10547", "10548", "10549", "10550", "10551", "10552", "10553", "10555", "10556", "10557", "10558"
        , "10559", "10560", "10561", "10562", "10563", "10564", "10565");
        $contactModel->message = "Yo!";
        $contactModel->contact_category = "MAIL TO FRIEND";

        $result = $this->object->sendPropertyMailToFriend($contactModel);

        $this->assertNotNull($result);
        $this->assertTrue(is_numeric($result));
    }

    public function testSendMailToDefaultCompany()
    {
        $contactModel = $this->object->getContactsModel();

        $contactModel->contact_email = "khan@gmail.com";
        $contactModel->contact_name = "Khan";
        $contactModel->contact_phone = "1234";
        $contactModel->message = "Yo!";
        $contactModel->contact_category = "CAT 1";
        $contactModel->contact_city = "y";

        $result = $this->object->sendMailToDefaultCompany($contactModel);

        $this->assertNotNull($result);
        $this->assertTrue(is_numeric($result));
    }


    public function testGetContactsAsExcel()
    {
        logTestStart(__FUNCTION__);
        try {
            $filePath = $this->object->getContactsAsExcel();


            $x = file_exists($filePath);
            $this->assertTrue($x);

        } catch (Exception $e) {
            WFactory::getLogger()->error($e->getMessage());
            $this->assertTrue(false);
        }
    }

    public function testSendMailToAgent()
    {
        $contactModel = $this->object->getContactsModel();

        $contactModel->contact_name = "Khan";
        $contactModel->contact_phone = "1234";
        $contactModel->contact_email = "shroukkhan@gmail.com";
        $contactModel->message = "Yo!";

        $result = $this->object->sendMailToAgent($contactModel, "khan@fingi.com");

        $this->assertTrue($result);
    }

    /**
     * http://redmine.softverk.is/issues/1355
     */
    public function testSendMailToAgent_1355()
    {
        $jsonMsg = <<<EOD
{
    "{\"contact_name\":\"Shrouk Khan\",\"contact_phone\":\"0875049439\",\"contact_email\":\"shroukkhan@gmail.com\",\"message\":\"test mail from remax\",\"agent_message\":\", , My condo  -  PROPERTY ID: 550-005-55 - <a href": "'http:\/\/www.remax.co.th\/property\/4383-condo-apartment-my-condo-phra-khanong-10260'>http:\/\/www.remax.co.th\/property\/4383-condo-apartment-my-condo-phra-khanong-10260<\/a><br><br><span style",
    "nbsp;<\/strong><br style": "\"box-sizing: border-box; color: #3b444f; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; font-size: 12px; line-height: 19.2000007629395px;\" \/><strong style",
    "nbsp;Expressway 52, Shopping Center and resturrent, Well international school, hospital ( On Nut ": null,
    "nbsp;BTS Sky train Station ).": null,
    "nbsp;Fullfurnished": null,
    "nbsp;<br \/><\/strong><span style": "\"font-family: arial, helvetica, sans-serif; font-size: small;\">Price For Rent 22,000 Bath<\/span><br style",
    "nbsp;<br \/>Facilities.<br \/>-Fitness<br \/>-Security Card Access Control<br \/>-CCTV<br \/>-Car park<br \/>-Washing service room<br \/>-GYM and sauna<br \/>- 24 Security<\/strong>\",\"agent_email\":\"bell@remax.co.th\"}": null
}

EOD;

        $contactModel = json_decode($jsonMsg);
        $result = $this->object->sendMailToAgent($contactModel);

        $this->assertTrue($result);
    }
}
 