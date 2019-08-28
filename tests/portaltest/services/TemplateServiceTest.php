<?php


$jpath_base = (dirname(__FILE__)) . "/../../../";
$jpath_base = realpath($jpath_base);
require_once "$jpath_base/phpunitbootstrap.php";


/**
 * Created by PhpStorm.
 * User: KHAN
 * Date: 1/29/2016
 * Time: 6:53 PM
 */
class TemplateServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var TemplateService
     */
    var $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_TEMPLATE);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers SitemapService::generateArticleLists
     */
    public function test_generateTemplateVariable()
    {
        $param = '{"countryCode":"is","sitetitle":"Softverk Webportal","sitealt":"Softverk Webportal","sitedescription":"Softverk Webportal Development Version","logoFile":"images\/webportal_logo.png","logoPrint":"images\/print_logo.png","languageEnable":"true","languageCode":["en-GB"],"dateFormat":"d.m.Y","selectTemplate":"t1","$template-path":"..\/..\/templates\/generic_b\/","$generic-primary-color-dark":"#293642","$generic-primary-color-medium":"#415569","$generic-primary-color-light":"#81A7CF","$generic-grey-color-dark":"","$generic-grey-color-medium":"","$generic-grey-color-light":"","$generic-grey-color-light-75percent":"","searchFrontPage":"full","mapFrontPage":"true","agentBlock":"a2","agentBlockWidth":"350px","agentBlockHeight":"350px","agentBlockColumns":"4","propertyID":"false","propertyTitle":"false","busFilter":"true","isNew":"true","viewportLoad":"true"}';

        $this->assertTrue($this->object->generateTemplateVariable($param));
    }
}
