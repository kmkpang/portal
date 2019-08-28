<?php
/**
 * Created by PhpStorm.
 * User: khan
 * Date: 8/21/15
 * Time: 1:23 PM
 */

$jpath_base = (dirname(__FILE__)) . "/../../../";
$jpath_base = realpath($jpath_base);
require_once "$jpath_base/phpunitbootstrap.php";

////require_once 'PHPUnit/Framework/TestCase.php';


class SenttowebServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var SenttowebService
     */
    var $object;


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_SENTTOWEB);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }


    public function test__searchSendToWebXmlByPropertyId()
    {

        $model = $this->object->getSenttowebModel();
        //$model->propertyId = 'C1PR2015081416384855CDB728A2304';
        $model->propertyUniqueId = 'C1PR20150926195326560695460EB81';


        $this->assertNotEmpty($this->object->searchSendToWebXml($model));


    }

    public function test__searchSendToWebXmlByAgentId()
    {

        $model = $this->object->getSenttowebModel();
        //$model->propertyId = 'C1PR2015081416384855CDB728A2304';
        $model->agentUniqueId = 'C1AG20140709094845';


        $this->assertNotEmpty($this->object->searchSendToWebXml($model));


    }

    public function test__searchSendToWebXmlByOfficeId()
    {

        $model = $this->object->getSenttowebModel();
        //$model->propertyId = 'C1PR2015081416384855CDB728A2304';
        $model->officeUniqueId = 'C1OF722014082803140653FE3C0E970A4';
        $model->type = SENTTOWEB_OFFICE;
        $model->direction = DIRECTION_INCOMING;
        $model->getAssociated = false;


        $this->assertNotEmpty($this->object->searchSendToWebXml($model));


    }


}
