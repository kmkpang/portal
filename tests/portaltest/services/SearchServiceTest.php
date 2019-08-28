<?php

$jpath_base = (dirname(__FILE__)) . "/../../../";
$jpath_base = realpath($jpath_base);
require_once "$jpath_base/phpunitbootstrap.php";
////require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Created by PhpStorm.
 * User: khan
 * Date: 5/10/14
 * Time: 3:43 PM
 */
class SearchServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var SearchService
     */
    var $object;


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_SEARCH);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }


    public function testSearch()
    {
        $this->markTestIncomplete("this test is not written yet");
    }

    /**
     * @covers SearchService getSearchModel
     */
    public function testGetSearchModel()
    {
        logTestStart(__FUNCTION__);
        $this->assertNotNull($this->object->getSearchModel());

        $json = $this->object->getSearchModel(true);
        WFactory::getLogger()->debug("$json");

    }

    /**
     * @covers SearchService buildFullTextSearchQuery
     */
    public function testBuildFullTextQuery()
    {
        logTestStart(__FUNCTION__);
        $result = $this->object->doFullTextSearch("'104 - Vogar', 'Höfuðborgarsvæðið',");
        $this->assertNotNull($result);
    }

    /**
     * @covers SearchService buildFullTextSearchQuery
     */
    public function testBuildFullTextQueryWithRegId()
    {
        logTestStart(__FUNCTION__);
        $result = $this->object->doFullTextSearch("920051002-");
        $this->assertNotNull($result);
    }


    public function test__generateSearchModelFromSearchHash()
    {
        // page=1&type=66,ALL&price=700000,8500000&text=holla molla sollaa&order=ORDER_BY_NEWEST_FIRST&bedrooms=0,3&town=77&region=8&zip=122&currency=AUD&loan80=YES&garage=YES&elevator=YES&new_today=YES&new_this_week=YES
        $urlModel = $this->object->getUrlSearchModel();
        $urlModel->agent = 1;
        $urlModel->page = 2;
        $urlModel->limit = 24;
        $urlModel->text = "someting wong";
        $urlModel->order = "ORDER_BY_NEWEST_FIRST,ORDER_BY_MOST_EXPENSIVE_FIRST";
        $urlModel->bedrooms = "0,3";
        $urlModel->region = 1;
        $urlModel->town = 77;
        $urlModel->zip = 122;
        $urlModel->currency = "ISK";
        $urlModel->loan80 = "80";

        $urlModel = $urlModel->toString();

        $searchModel = $this->object->generateSearchModelFromSearchHash($urlModel);

        $this->assertNotNull($searchModel);

    }

    public function testGenerateHash()
    {
        logTestStart(__FUNCTION__);
        $seachModel = $this->object->getSearchModel();

        // $hash1 = $this->object->generateSearchHash($seachModel);

        // $this->assertTrue(is_int($hash1));

        $seachModel->text = uniqid(uniqid());
        $seachModel->city_town_id = 0;
        $seachModel->is_deleted = 1;
        $seachModel->category_id = array(1, 2, 3);
        $seachModel->zip_code_id = "1";
        $seachModel->city_town_id = "1";
        $seachModel->region_id = "1";
        $seachModel->sale_id = "123";
        $seachModel->office_id = "123";

        $seachModel->current_listing_price = array(0, 1000);

        $seachModel->number_of_bedrooms = array(0, 5);
        $seachModel->total_number_of_rooms = array(0, 5);
        $seachModel->type_id = 2;
        $seachModel->latitude = "1.1";
        $seachModel->longitude = "2";

        $seachModel->property_id = array(1, 2, 3, 4);

        $hash2 = $this->object->generateSearchHash($seachModel);

        $this->assertNotNull($hash2);

        //  $this->assertTrue(is_int($hash2));

        //  $this->assertTrue($hash1 != $hash2);
    }

}
 