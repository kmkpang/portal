<?php
/**
 * Created by PhpStorm.
 * User: khan
 * Date: 9/28/15
 * Time: 11:27 PM
 */

$jpath_base = (dirname(__FILE__)) . "/../../../";
$jpath_base = realpath($jpath_base);
require_once "$jpath_base/phpunitbootstrap.php";


class HtmlmailServiceTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var HtmlmailService
     */
    var $object;
    /**
     * @var MandrillService
     */
    var $mandrillObject;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_HTMLMAIL);
        $this->mandrillObject = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_MANDRILL);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }


    public function test__getPropertyEmailTempalte()
    {


        $property = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTY)->getDetail(10393);

        $template = $this->object->getPropertyEmailTempalte('tempo', $property);

        //sendMandrillMail($subject, $body, $from, $to, $fromName = "", $isHtml = false)
        $this->assertNotEmpty($this->mandrillObject->sendMandrillMail("Subject",
            $template, //body
            "kaupa@kaupa.is", //from
            "shroukkhan@gmail.com",//to
            "KAUPAAA",//from name
            true));


    }
}
