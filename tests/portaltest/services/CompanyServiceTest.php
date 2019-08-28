<?php

$jpath_base = (dirname(__FILE__)) . "/../../../";
$jpath_base = realpath($jpath_base);
require_once "$jpath_base/phpunitbootstrap.php";
//require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Created by PhpStorm.
 * User: khan
 * Date: 7/16/14
 * Time: 3:49 PM
 */
class CompanyServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var CompanyService
     */
    var $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_COMPANY);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function testGetCompany()
    {
        $company = $this->object->getCompany();

        $this->assertNotEmpty($company);
    }
}
 