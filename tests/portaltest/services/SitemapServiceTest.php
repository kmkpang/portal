<?php

$jpath_base = (dirname(__FILE__)) . "/../../../";
$jpath_base = realpath($jpath_base);
require_once "$jpath_base/phpunitbootstrap.php";
////require_once 'PHPUnit/Framework/TestCase.php';
//require_once 'PHPUnit/Autoload.php';

/**
 * Created by PhpStorm.
 * User: khan
 * Date: 10/7/14
 * Time: 2:47 PM
 */
class SitemapServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var SitemapService
     */
    var $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_SITEMAP);
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
    public function testGetArticles()
    {
        $this->assertNotEmpty($this->object->generateArticleLists());
    }

    public function testGenerateOfficeList()
    {
        $this->assertNotEmpty($this->object->generateOfficeList());
    }

    public function testGenerateAgentList()
    {
        $this->assertNotEmpty($this->object->generateAgentList());
    }

    public function testGeneratePropertyList()
    {
        $this->assertNotEmpty($this->object->generatePropertyList());
    }


    public function testGenerateSiteMap()
    {
        $this->assertNotEmpty($this->object->generateSitemapText());
    }

    public function testGenerateGoogleSiteMap()
    {
        $this->assertNotEmpty($this->object->generateGoogleSiteMap());

    }

}
 