<?php


$jpath_base = (dirname(__FILE__)) . "/../../../";
$jpath_base = realpath($jpath_base);
require_once "$jpath_base/phpunitbootstrap.php";


//require_once 'PHPUnit/Autoload.php';

/**
 * Created by PhpStorm.
 * User: khan
 * Date: 6/25/14
 * Time: 3:36 PM
 */
class CurrencyServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var CurrencyService
     */
    var $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_CURRENCY);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function test__getCurrency()
    {
        $result = $this->object->getCurrency();

        $this->assertNotEmpty($result);
    }
}
 