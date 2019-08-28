<?php

$jpath_base = (dirname(__FILE__)) . "/../../../";
$jpath_base = realpath($jpath_base);
require_once "$jpath_base/phpunitbootstrap.php";


/**
 * Created by PhpStorm.
 * User: khan
 * Date: 5/10/14
 * Time: 4:14 PM
 */
class PropertiesServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PropertiesService
     */
    var $object;
    var $curl;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTIES);

        if (!__QUICKTEST) {

            $sqlFolder = JPATH_BASE . DS . "sql";
            $cli = JPATH_BASE . DS . "cli";
            $command = "php $cli/managePortalDb.php --function=restoredb --tarfile=$sqlFolder/jos_portal.sql.tar.gz";
            exec($command);
        }
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {

    }


    public function testFixAddressForWebsending()
    {
        fixWebsendingAddress();
    }

    /**
     * @covers PropertiesService::search
     */
    public function testSearchPropertiesDetailsById()
    {
        logTestStart(__FUNCTION__);
        $allPropertties = get20Properties();

        /**
         * @var $searchModel SearchModel
         */
        $searchModel = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_SEARCH)->getSearchModel();

        $singleId = $allPropertties[0]["id"];
        $searchModel->property_id = $singleId;
        $searchModel->returnType = RETURN_TYPE_DETAIL;


        $result = $this->object->search($searchModel);

        $json = json_encode($result);

        print_r($json);


        $this->assertNotEmpty($result);
    }


    /**
     * @covers PropertiesService::search
     */
    public function testSearchPropertiesDetailsByIdOverWebApi()
    {
        logTestStart(__FUNCTION__);
        $allPropertties = get20Properties();

        /**
         * @var $searchModel SearchModel
         */
        $searchModel = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_SEARCH)->getSearchModel();

        $singleId = $allPropertties[0]["id"];
        $searchModel->property_id = $singleId;
        $searchModel->returnType = RETURN_TYPE_DETAIL;


        $this->curl = getLocalApiCurl("th/api/v1/properties/search/");
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, json_encode($searchModel));
        $result = curl_exec($this->curl);

        $result = json_decode($result);
        $this->assertNotEmpty($result);
    }

    public function testSearchPropertyOrder()
    {
        /**
         * @var $searchModel SearchModel
         */
        $searchModel = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_SEARCH)->getSearchModel();

        $searchModel->order = array(ORDER_BY_NEWEST_FIRST, ORDER_BY_SMALLEST_FIRST, ORDER_BY_SMALLEST_ZIP_FIRST);

        $result = $this->object->search($searchModel);

        $this->assertNotEmpty($result);

    }

    public function testSearchPropertyOrderOverWebApi()
    {
        logTestStart(__FUNCTION__);
        $allPropertties = get20Properties();

        /**
         * @var $searchModel SearchModel
         */
        $searchModel = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_SEARCH)->getSearchModel();

        $searchModel->order = array('ORDER_BY_NEWEST_FIRST', 'ORDER_BY_SMALLEST_FIRST', 'ORDER_BY_SMALLEST_ZIP_FIRST');

        echo json_encode($searchModel);

        $this->curl = getLocalApiCurl("api/v1/properties/search/");
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, '{"text":"","type_id":0,"current_listing_price":[0,0],"rent_price":[0,0],"total_number_of_rooms":[0,0],"total_area":[0,0],"rent_total_area":[0,0],"region_id":"","city_town_id":"","zip_code_id":"","order":"ORDER_BY_NEWEST_FIRST","returnType":"RETURN_TYPE_LIST","limit_start":0,"limit_length":10}');
        $result = curl_exec($this->curl);

        $result = json_decode($result);
        $this->assertNotEmpty($result);
    }

    public function testSearchPropertyListByLang()
    {
        /**
         * @var $searchModel SearchModel
         */
        $searchModel = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_SEARCH)->getSearchModel();
        $searchModel->returnType = RETURN_TYPE_LIST;
        $searchModel->limit_start = 40;
        $searchModel->limit_length = 10;

        JFactory::getLanguage()->setLanguage('th-TH');

        $result = $this->object->getList($searchModel);


        $this->assertNotEmpty($result);


    }

    public function test__importPropertyFeaturesFromCsv()
    {
        $this->object->importPropertyFeaturesFromCsv();
    }

//
    /**
     * @covers PropertiesService::search
     */
    public function testSearchPropertiesDetailsByIds()
    {
        logTestStart(__FUNCTION__);
        $allPropertties = get20Properties();

        /**
         * @var $searchModel SearchModel
         */
        $searchModel = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_SEARCH)->getSearchModel();


        if (!empty($allPropertties)) {

            $multipleIds = array($allPropertties[0]["id"],
                $allPropertties[1]["id"],
                $allPropertties[2]["id"],
                $allPropertties[3]["id"]);
            $searchModel->property_id = $multipleIds;
            $searchModel->returnType = RETURN_TYPE_DETAIL;


            $result = $this->object->search($searchModel);
            $this->assertNotEmpty($result);
        } else {
            WFactory::getLogger()->error("Empty property table??? check jos_portal_properties");
            $this->markTestIncomplete("Empty property table, can not coninue");
        }

    }


    public function testSearchPropertiesInRectangle()
    {
        logTestStart(__FUNCTION__);

        // ALTER TABLE `jos_portal_properties` CHANGE `latitude` `latitude` DOUBLE NOT NULL;
        // ALTER TABLE `jos_portal_properties` CHANGE `longitude` `longitude` DOUBLE NOT NULL;
        /**
         * @var $searchModel SearchModel
         */
        $searchModel = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_SEARCH)->getSearchModel();

        $searchModel->bounds = array(
            "east" => 100.59999782861325,
            "north" => 13.750406801218707,
            "south" => 13.70538223794761,
            "west" => 100.44824917138669
        );

        $searchModel->returnType = RETURN_TYPE_LIST;

        $result = $this->object->search($searchModel);
        $this->assertNotEmpty($result);

    }

    public function testPropertiesSearchByStreet()
    {
        logTestStart(__FUNCTION__);

        // ALTER TABLE `jos_portal_properties` CHANGE `latitude` `latitude` DOUBLE NOT NULL;
        // ALTER TABLE `jos_portal_properties` CHANGE `longitude` `longitude` DOUBLE NOT NULL;
        /**
         * @var $searchModel SearchModel
         */
        $searchModel = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_SEARCH)->getSearchModel();

        $searchModel->street=' \u0e15\u0e34\u0e14\u0e16\u0e19\u0e19\u0e40\u0e17\u0e1e\u0e32\u0e23\u0e31\u0e01\u0e29\u0e4c \u0e01\u0e21.21';

        $searchModel->returnType = RETURN_TYPE_LIST;

        $result = $this->object->search($searchModel);
        $this->assertNotEmpty($result);

    }

    /**
     * @covers PropertiesService::search
     */
    public function testSearchPropertiesInRadius()
    {
        logTestStart(__FUNCTION__);
        $allPropertties = get20Properties();

        /**
         * @var $searchModel SearchModel
         */
        $searchModel = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_SEARCH)->getSearchModel();


        if (!empty($allPropertties)) {


            $searchModel->latitude = $allPropertties[0]["latitude"];
            $searchModel->longitude = $allPropertties[0]["longitude"];
            $searchModel->radius = 5; //2km! iceland is a big place
            $searchModel->returnType = RETURN_TYPE_LIST;
            $searchModel->order = ORDER_BY_NEAREST_FIRST;

            $result = $this->object->search($searchModel);
            $this->assertNotEmpty($result);
        } else {
            WFactory::getLogger()->error("Empty property table??? check jos_portal_properties");
            $this->markTestIncomplete("Empty property table, can not coninue");
        }

    }


    /**
     * @covers PropertiesService::search
     */
    public function testSearchPropertiesDetailsByIdsOverWebApi()
    {
        logTestStart(__FUNCTION__);
        $allPropertties = get20Properties();

        /**
         * @var $searchModel SearchModel
         */
        $searchModel = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_SEARCH)->getSearchModel();

        $multipleIds = array($allPropertties[0]["id"],
            $allPropertties[1]["id"],
            $allPropertties[2]["id"],
            $allPropertties[3]["id"]);
        $searchModel->property_id = $multipleIds;
        $searchModel->returnType = RETURN_TYPE_DETAIL;


        $this->curl = getLocalApiCurl("api/v1/properties/search/");


        curl_setopt($this->curl, CURLOPT_POSTFIELDS, json_encode($searchModel));
        $result = curl_exec($this->curl);

        $result = json_decode($result);
        $this->assertNotEmpty($result);
    }


    public function testSearchProperties1()
    {
        logTestStart(__FUNCTION__);
        /**
         * @var $searchModel SearchModel
         */
        $searchModel = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_SEARCH)->getSearchModel();


        $searchModel->returnType = RETURN_TYPE_LIST;
        //  $searchModel->text = "Svanur  Guðmundsson";
        //$searchModel->sale_id = array(40);

        $this->curl = getLocalApiCurl("api/v1/properties/search/");


        curl_setopt($this->curl, CURLOPT_POSTFIELDS, json_encode($searchModel));
        $result = curl_exec($this->curl);

        $result = json_decode($result);
        $this->assertNotEmpty($result);
    }

    public function testSearchProperties1OverApi()
    {
        logTestStart(__FUNCTION__);
        /**
         * @var $searchModel SearchModel
         */
        $searchModel = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_SEARCH)->getSearchModel();


        $searchModel->returnType = RETURN_TYPE_LIST;
        //$searchModel->text = "Svanur  Guðmundsson";

        $this->curl = getLocalApiCurl("api/v1/properties/search/");


        curl_setopt($this->curl, CURLOPT_POSTFIELDS, json_encode($searchModel));
        $result = curl_exec($this->curl);
        $this->assertNotNull($result);
    }

    public function testSearchPropertiesListOverApi()
    {
        logTestStart(__FUNCTION__);
        /**
         * @var $searchModel SearchModel
         */
        $searchModel = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_SEARCH)->getSearchModel();

        $this->curl = getLocalApiCurl("api/v1/properties/getList/");


        curl_setopt($this->curl, CURLOPT_POSTFIELDS, json_encode($searchModel));
        $result = curl_exec($this->curl);
        $this->assertNotNull($result);
    }

    public function testSearchPropertiesListOverApiGettingEmpty()
    {
        logTestStart(__FUNCTION__);
        /**
         * @var $searchModel SearchModel
         */
        $searchModel = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_SEARCH)->getSearchModel();
        $searchModel->returnType = RETURN_TYPE_LIST;
        $this->curl = getLocalApiCurl("api/v1/properties/getList/");


        curl_setopt($this->curl, CURLOPT_POSTFIELDS, json_encode($searchModel));
        $result = curl_exec($this->curl);
        $this->assertNotNull($result);
    }

    function testGetPropertiesListLimit10()
    {
        logTestStart(__FUNCTION__);
        $this->curl = getLocalApiCurl("api/v1/properties/getList/");

        curl_setopt($this->curl, CURLOPT_POSTFIELDS, '{"text":"a","returnType":"RETURN_TYPE_LIST","limit_start":0,"limit_length":10}');
        $result = curl_exec($this->curl);
        $this->assertNotNull($result);


    }

    public function testSearchMapModel()
    {
        logTestStart(__FUNCTION__);
        /**
         * @var $searchModel SearchModel
         */
        $searchModel = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_SEARCH)->getSearchModel();

        $searchModel->returnType = RETURN_TYPE_MAP;

        $start = time();
        $result = $this->object->search($searchModel);
        $this->assertNotNull($result);
        $end = time();


        WFactory::getLogger()->debug("TOOK " . ($end - $start) . " sec");
    }

    public function testSearchPropertiesOpenHouse()
    {
        logTestStart(__FUNCTION__);
        /**
         * @var $searchModel SearchModel
         */
        $searchModel = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_SEARCH)->getSearchModel();


        $searchModel->returnType = RETURN_TYPE_LIST;


        //Lets get all properties that have open house next week
        // start : 2014-06-15
        // end   : 2014-06-21

        // This means openhouse MUST NOT FINISH BEFORE next week
        //            openhouse MUST --finish AFTER -- next week
        //            end >= '2014-06-21'
        $searchModel->open_house_end = array(2014 - 06 - 21, 0);

        $result = $this->object->search($searchModel);
        $this->assertNotNull($result);
    }


    function testGetPropertiesByOffice()
    {
        logTestStart(__FUNCTION__);
        $this->assertNotNull($this->object->getPropertiesByOffice(23));


    }

    function testUpdatePropertyTableWithOfficeInformation()
    {
        logTestStart(__FUNCTION__);
        /**
         * @var $officeClass PortalPortalOfficesSql
         */
        $officeClass = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_OFFICES_SQL);
        $officeClass->__id = 53;
        $officeClass->loadDataFromDatabase();

        $this->assertTrue($this->object->updatePropertyTableWithOfficeInformation($officeClass));
    }


    function testSearchPropertyLocally()
    {
        logTestStart(__FUNCTION__);
        $searchModel = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_SEARCH)->getSearchModel();


        $searchModel->returnType = RETURN_TYPE_LIST;

        $result = $this->object->search($searchModel);

        $this->assertNotEmpty($result);
    }

    function testSearchGettingSlower()
    {
        logTestStart(__FUNCTION__);


        $this->curl = getLocalApiCurl("api/v1/properties/getList/");

        for ($i = 0; $i < 20; $i++) {
            $start = microtime();
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, '{"text":"","current_listing_price":[0,0],"total_number_of_rooms":[0,0],"total_area":[0,0],"returnType":"RETURN_TYPE_LIST","limit_start":0,"limit_length":10}');
            $result = curl_exec($this->curl);
            $result = json_decode($result);
            $this->assertNotNull($result);
            $end = microtime();

            WFactory::getLogger()->debug("search $i : time : " . ($end - $start), " sec");

        }


    }

    /**
     * @covers PropertiesService::processNewSaveSearch
     */
    function testBeanstalkdFunctionPropertiesSearch()
    {
        logTestStart(__FUNCTION__);

        $enabled = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_BEANSTALKD)->isBeanstalkdEnabled();

        $enabled = false;

        if (!$enabled) {
            // $this->markTestIncomplete("Beanstalk not enabled..skipping test " . __FUNCTION__);
            $this->markTestSkipped("This test : " . __FUNCTION__ . " is skipping. Because ..the methodology used here only was a prototype for full beanstalkd stack");

        } else {
            $searchModel = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_SEARCH)->getSearchModel();


            $searchModel->returnType = RETURN_TYPE_LIST;

            $result = $this->object->search($searchModel);

            $this->assertNotEmpty($result);

            // now get it from there!!

            $job = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_BEANSTALKD)->getFromSearchQueue();
            $data = $job->getData();
            $data = file_get_contents($data);
            $data = unserialize($data);

            $model = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_BEANSTALKD)->getBeanstalkdModel(null, null, null);
            $model->bindToDb($data);
            $result = WFactory::getServices()->getServiceClass($model->serviceProviderName)->{$model->functionName}($model->payLoad);

            if (is_numeric($result))
                $result = true;

            $this->assertTrue($result);

            if ($result === true) {
                WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_BEANSTALKD)->delete($job);
            } else {
                WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_BEANSTALKD)->release($job);
            }
        }

    }

    function testGetPropertiesListForSaga()
    {
        logTestStart(__FUNCTION__);


        $properties = get20Properties();
        $officeId = $properties[rand(0, 19)]["office_id"];
        $officeUniqueId = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_OFFICE)->getOffice($officeId)['unique_id'];

        $result = $this->object->getPropertiesListForSaga($officeUniqueId);

        $this->assertNotEmpty($result);

    }

    function testGetPropertiesListForSagaOverWebApi()
    {
        logTestStart(__FUNCTION__);


        $properties = get20Properties();
        $officeId = $properties[rand(0, 19)]["office_id"];
        $officeUniqueId = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_OFFICE)->getOffice($officeId)['unique_id'];

        $this->curl = getLocalApiCurl("index.php?option=com_webportal&controller=properties&task=getAllPropertiesList&format=raw&officeId=$officeUniqueId", true);


        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $officeUniqueId);
        $result = curl_exec($this->curl);

        $result = json_decode($result);
        $this->assertNotEmpty($result);

    }

    function testGetPropertiesMostExpensiveFirst()
    {
        logTestStart(__FUNCTION__);

//http://remax-staging.softverk.co.th/properties-search/list?option=com_webportal&controller=api&task=service&version=v1&format=raw&service=properties&data=search
        $this->curl = getLocalApiCurl("index.php?option=com_webportal&controller=api&task=service&version=v1&format=raw&service=properties&data=search", true);
        $searchModel = '{
                            "text": "",
                            "type_id": 2,
                            "mode_id": 2,
                            "current_listing_price": [
                                0,
                                0
                            ],
                            "rent_price": [
                                0,
                                0
                            ],
                            "total_number_of_rooms": [
                                0,
                                0
                            ],
                            "total_area": [
                                0,
                                0
                            ],
                            "rent_total_area": [
                                0,
                                0
                            ],
                            "region_id": "",
                            "city_town_id": "",
                            "zip_code_id": "",
                            "order": [
                                "ORDER_BY_MOST_EXPENSIVE_FIRST"
                            ],
                            "office_id": "",
                            "sale_id": "",
                            "office_name": "",
                            "sale_name": "",
                            "returnType": "RETURN_TYPE_LIST",
                            "limit_start": 0,
                            "limit_length": 10
                        }
                        ';
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $searchModel);
        $result = curl_exec($this->curl);
        $this->assertNotNull($result);

    }

}
 