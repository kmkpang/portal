<?php
/**
 * Created by JetBrains PhpStorm.
 * User: khan
 * Date: 7/2/13
 * Time: 11:09 PM
 * To change this template use File | Settings | File Templates.
 */

$jpath_base = (dirname(__FILE__)) . "/../../../";
$jpath_base = realpath($jpath_base);
require_once "$jpath_base/phpunitbootstrap.php";
//require_once 'PHPUnit/Framework/TestCase.php';
//require_once 'PHPUnit/Autoload.php';


///var/www/softverk-webportal/libraries/webportal/services/webservice/websending/office.php
///var/www/softverk-webportal/libraries/webportal/services/webservice/websending/office.php
require_once JPATH_ROOT . "/libraries/webportal/services/webservice/websending/office.php";

class OfficeUpdateToWebTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var OfficeSentToWeb
     */
    var $object;
    /**
     * @var OfficeSentToWeb
     */
    var $createClass;

    var $xmlCreate = "";
    var $xml = "";
    var $curl;
    var $xmlString;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->xmlCreate = JPATH_ROOT . "/tests/portaltest/testXml/officeCreate.xml.xsl";
        $this->xml = JPATH_ROOT . "/tests/portaltest/testXml/officeUpdate.xml.xsl";

        $xml = trim(file_get_contents($this->xml));
        $this->object = new OfficeSentToWeb($xml);

        $xmlCreate = trim(file_get_contents($this->xmlCreate));
        $this->xmlString = $xmlCreate;
        $this->createClass = new OfficeSentToWeb($xmlCreate);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }


    public function testUpdate()
    {



        //README: if doing full test, toggle this button
        $isDoingFullText = !__QUICKTEST;
        if ($isDoingFullText) {
            // ------- fist create the office ------
            $result = ($this->createClass->create());
            $this->assertNotEmpty($result);

            $xml = simplexml_load_string($result);

            $xmlData = json_decode(json_encode($xml), true);

            $this->assertTrue($xmlData["Response"]["Number"] == "02311");

            // -------- now try to update it -----------

            $uniqueId = $xmlData["Response"]["Message"]["UniqueID"];
            $publicKey = $xmlData["Response"]["Message"]["PublicKey"];
        } else {
            $uniqueId = 'C1OF3720140427182811'; //value from db
            $publicKey = 'C1OF3720140427182811535CE9CBA7BE6'; //
        }
        $this->object->publicKey = $this->object->dbClass->__public_key = $publicKey;
        $this->object->uniqueId = $this->object->dbClass->__unique_id = $uniqueId;



        $result = ($this->object->update());
        $this->assertNotEmpty($result);
        $xml = simplexml_load_string($result);

        $xmlData = json_decode(json_encode($xml), true);


        $this->assertTrue($xmlData["Response"]["Number"] == "02321");
    }


}
