<?php
/**
 * Created by PhpStorm.
 * User: Lian
 * Date: 6/25/14
 * Time: 11:12 PM
 */

$jpath_base = (dirname(__FILE__)) . "/../../../";
$jpath_base = realpath($jpath_base);
require_once "$jpath_base/phpunitbootstrap.php";

//require_once 'PHPUnit/Framework/TestCase.php';

class AddressServiceTest extends PHPUnit_Framework_TestCase
{
    /** @var AddressService service */
    var $service;

    function setUp()
    {
        $this->service = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_ADDRESS);
    }

    function test_zones()
    {
        $this->assertNotEmpty($this->service);
        $regions = $this->service->regions();
        $this->assertNotEmpty($regions);
    }

    function test_towns()
    {
        $zones = $this->service->regions();
        $towns = $this->service->towns($zones[0]['id']);

        $this->assertNotEmpty($towns);
    }

    function test_postalcodes()
    {
        $zones = $this->service->regions();
        $towns = $this->service->towns($zones[0]['id']);
        $postals = $this->service->postal_codes($towns[0]['id']);

        $this->assertNotEmpty($postals);
    }

    function test_postal_codes_tree()
    {
        $tree = $this->service->postalCodeTree();

        $this->assertNotEmpty($tree);
    }

    function test_postal_codes_tree_thaiLang()
    {
        JFactory::getLanguage()->setLanguage('th-TH');
        $this->service->language = WFactory::getHelper()->getCurrentlySelectedLanguage();

        $tree = $this->service->postalCodeTree();

        $this->assertNotEmpty($tree);
    }

    function testGetAddres()
    {
        $query = "select * from #__portal_property_addresses";
        $result = WFactory::getSqlService()->select($query);

        if (!empty($result)) {
            $id = $result[0]["id"];
            $address = $this->service->getAddress($id);

            $this->assertNotEmpty($address);
        }
    }


    function testGetAddresLangIndependent()
    {
        $query = "select * from #__portal_property_addresses";

        $result = WFactory::getSqlService()->select($query);

        if (!empty($result)) {
            $id = $result[0]["id"];
            $address = $this->service->getAddress($id, true);

            $this->assertNotEmpty($address);
        }
    }


    function testGetAddresThai()
    {
        JFactory::getLanguage()->setLanguage('th-TH');
        $query = "select * from #__portal_property_addresses";
        $result = WFactory::getSqlService()->select($query);

        if (!empty($result)) {
            $id = $result[0]["id"];
            $address = $this->service->getAddress($id);

            $this->assertNotEmpty($address);
        }
    }

    function test_postalCodeTree()
    {
        $this->assertNotEmpty($this->service->postalCodeTree());
    }

    function test_modes()
    {
        $modes = $this->service->getModes();
        $this->assertNotEmpty($modes);
    }

    function test_categories()
    {
        $modes = $this->service->getModes();
        $cats = $this->service->getPropCategories($modes[0]['id']);
        $this->assertNotEmpty($cats);

        $this->assertNotEmpty($cats[0]['id']);
        $this->assertNotEmpty($cats[0]['description']);
    }

    function test_categories_tree()
    {
        $cat_tree = $this->service->propCategoriesTree();
        $this->assertNotEmpty($cat_tree);
    }

    function test__mockGeoData()
    {
        $data = $this->service->mockGeoData();
        $json = json_encode($data);
        print_r($json);
    }
}
 