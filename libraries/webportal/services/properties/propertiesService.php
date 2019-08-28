<?php

/**
 * Created by PhpStorm.
 * User: khan
 * Date: 5/10/14
 * Time: 4:00 PM
 */
class PropertiesService
{

    function getPropertiesListForSaga($officeUniqueId)
    {
        if (is_object($officeUniqueId))
            $officeUniqueId = get_object_vars($officeUniqueId);
        if (is_array($officeUniqueId))
            $officeUniqueId = $officeUniqueId['officeId'];


        $query = "SELECT

                    jos_portal_properties.unique_id AS propertyId,
                    jos_portal_offices.unique_id AS officeId,
                    jos_portal_sales.unique_id AS agentId,
                    jos_portal_properties.id as id,
                    jos_portal_properties.google_viewcount AS viewcount,
                    jos_portal_properties.viewcount AS propertyViewed

                    FROM jos_portal_properties
                    INNER JOIN jos_portal_offices ON jos_portal_properties.office_id = jos_portal_offices.id
                    INNER JOIN jos_portal_sales ON jos_portal_properties.sale_id = jos_portal_sales.id

                    AND jos_portal_offices.unique_id = '$officeUniqueId'
                    WHERE jos_portal_properties.is_deleted = 0";

        $properties = WFactory::getServices()->getSqlService()->select($query);
        return $properties;

    }

    /**
     * @param $searchModel SearchModel
     * @return bool|mixed|string
     */
    function getList($searchModel = null)
    {
        if ($searchModel !== null) {
            $searchModel->returnType = RETURN_TYPE_LIST;
        }

        return $this->search($searchModel);
    }


    function getFakePropertiesToClaim($latLng)
    {

        $latitude = $latLng->latitude;
        $longitude = $latLng->longitude;

        $data = $this->getRandomProperties(15, RETURN_TYPE_MAP);
        /**
         * @var $d PropertyListModel
         */
        foreach ($data as &$d) {


            $randomLocation = WFactory::getHelper()->getRandomLatitudeLongitude([$latitude, $longitude], 15);
            $d->latitude=$randomLocation[0];
            $d->longitude=$randomLocation[1];
        }

        return $data;


    }


    /**
     * @param $searchModel SearchModel
     * @return bool|mixed|string
     *
     */

    function search($searchModel = null)
    {
        ini_set('memory_limit', '5120M');//!!!big
        $start = microtime();
        $lang = WFactory::getHelper()->getCurrentlySelectedLanguage();

        /**
         * @var $searchService SearchService
         */
        $searchService = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_SEARCH);

        if (empty($searchModel) || $searchModel === null) {
            WFactory::getLogger()->warn("Empty Search model received");
            return false;
        } else {
            $defaultModel = $searchService->getSearchModel();
            $searchModel = (object)array_merge((array)$defaultModel, (array)$searchModel);
        }


        $searchModel->search_key = $searchService->generateSearchHash($searchModel);


        $cacheResult = $searchService->checkSearchCache($searchModel->search_key);

        if ($cacheResult !== false) {
            $cacheResult = json_decode($cacheResult);

            $end = microtime();
            $spent = $end - $start;
            WFactory::getLogger()->debug("[Search][CacheHit]: $spent seconds ");

            $searchModel->timespent = $spent;

            return $this->applyLimitsAppeadSearchModel($searchModel, $cacheResult, true);
        }


        $selectStatement = array();
        $selectStatement[] = "#__portal_properties.*";
        $conditions = array();
        $havingCondition = array();

        if (!WFactory::getHelper()->isNullOrEmptyString($searchModel->preferred_currency)) {
            WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_CURRENCY)->setPreferredCurrency($searchModel->preferred_currency);
        }

        if (!WFactory::getHelper()->isNullOrEmptyString($searchModel->text)
            || !WFactory::getHelper()->isNullOrEmptyString($searchModel->street)
        ) {
            $idFulLText = array();
            if (!WFactory::getHelper()->isNullOrEmptyString($searchModel->text)) {
                $idFulLText = $searchService->doFullTextSearch($searchModel->text);
            }
            $idsStreet = array();
            if (!WFactory::getHelper()->isNullOrEmptyString($searchModel->street)) {
                $idsStreet = $searchService->doStreetSearch($searchModel->street);
            }
            $ids = array_merge($idFulLText, $idsStreet);
            if (empty($ids)) {
                $ids = ["-1"];
            }
            $searchService->buildIdSearchCondition($ids, "#__portal_properties", "id", $conditions);

        }

        if (!empty($searchModel->property_id)) {
            $searchService->buildIdSearchCondition($searchModel->property_id, "#__portal_properties", "id", $conditions);
        }
        if (intval($searchModel->user_id) > 0) {

            $propertiesByUserQuery = "select property_id from #__portal_properties_users where user_id = " . $searchModel->user_id;
            $tempPropertiesResult = WFactory::getSqlService()->select($propertiesByUserQuery);
            $__properties = array("-1");
            foreach ($tempPropertiesResult as $t) {
                $__properties[] = $t['property_id'];
            }


            $searchService->buildIdSearchCondition($__properties, "#__portal_properties", "id", $conditions);

        }

        if (!empty($searchModel->reg_id)) {
            $searchService->buildIdSearchCondition($searchModel->reg_id, "#__portal_properties", "reg_id", $conditions);
        }

        if (!empty($searchModel->sale_id)) {
            $searchService->buildIdSearchCondition($searchModel->sale_id, "#__portal_properties", "sale_id", $conditions);
        }
        if (!empty($searchModel->office_id)) {
            $searchService->buildIdSearchCondition($searchModel->office_id, "#__portal_properties", "office_id", $conditions);
        }
        if (!empty($searchModel->category_id)) {

            /**
             * Special customer requirement: http://redmine.softverk.is/issues/1385
             * for REMAX_THAILAND
             */

            if (__CUSTOMER_ID === "REMAX_THAILAND") {
                $this->resolveIssue1385($searchModel);
            }

            if (strpos($searchModel->category_id, ',') !== false) {
                $searchModel->category_id = explode(',', $searchModel->category_id);
            }
            $searchService->buildIdSearchCondition($searchModel->category_id, "#__portal_properties", "category_id", $conditions);
        }

        if (!empty($searchModel->type_id)) {

            if (intval($searchModel->type_id) !== 1) // 1 = ALL, does NOT apply filter
                $searchService->buildIdSearchCondition($searchModel->type_id, "#__portal_properties", "type_id", $conditions);
        }

        if (!empty($searchModel->residential_commercial)) {
            $residential_commercial = trim($searchModel->residential_commercial);
            $conditions[] = " ( #__portal_properties.residential_commercial = '$residential_commercial' )";
        }


        if (!$searchModel->return_properties_with_no_address) {
            $conditions[] = " ( #__portal_properties.region_id > 0 &&   #__portal_properties.city_town_id > 0  )";
        }

        if (!empty($searchModel->region_id)) {
            $searchService->buildIdSearchCondition($searchModel->region_id, "#__portal_properties", "region_id", $conditions);
        }

        if (!empty($searchModel->city_town_id)) {
            $searchService->buildIdSearchCondition($searchModel->city_town_id, "#__portal_properties", "city_town_id", $conditions);
        }

        if (!empty($searchModel->zip_code_id)) {
            $searchService->buildIdSearchCondition($searchModel->zip_code_id, "#__portal_properties", "zip_code_id", $conditions);
        }

        if (!empty($searchModel->zip_code)) {
            $searchService->buildIdSearchCondition($searchModel->zip_code, "#__portal_properties", "zip_code", $conditions);
        }

        if (in_array(ORDER_BY_OPENHOUSE_FIRST, $searchModel->order)) {
            $conditions[] = " ( NOW() < #__portal_properties.open_house_end ) ";
        }

        if (!empty($searchModel->exclusion_list)) {
            $conditions_temp = array();
            foreach ($searchModel->exclusion_list as $exclude) {
                $conditions_temp[] = "#__portal_properties.id != $exclude";
            }
            $conditions_temp = implode(" AND ", $conditions_temp);
            $conditions[] = " ( $conditions_temp ) ";
        }

        if (!empty($searchModel->project_name)) {
            $conditions[] = " ( #__portal_properties.project_name = '$searchModel->project_name' )";
        }

        if (!empty($searchModel->unit_code)) {
            $conditions[] = " ( #__portal_properties.unit_code = '$searchModel->unit_code' )";
        }

        if (!empty($searchModel->unit_type)) {
            $conditions[] = " ( #__portal_properties.unit_type = '$searchModel->unit_type' )";
        }

        if (!empty($searchModel->current_listing_price)) {
            $searchService->buildToFromSearchConditionNumeric($searchModel->current_listing_price,
                "#__portal_properties",
                "current_listing_price",
                $conditions);
        }
        if (!empty($searchModel->mortgage)) {
            $searchService->buildToFromSearchConditionNumeric($searchModel->mortgage,
                "#__portal_properties",
                "mortgage",
                $conditions);
        }

        if (!empty($searchModel->floor_level)) {
            $searchService->buildToFromSearchConditionNumeric($searchModel->floor_level,
                "#__portal_properties",
                "floor_level",
                $conditions);

        }
        if (!empty($searchModel->total_area)) {
            $searchService->buildToFromSearchConditionNumeric($searchModel->total_area,
                "#__portal_properties",
                "total_area",
                $conditions);

        }
        if (!empty($searchModel->living_area)) {
            $searchService->buildToFromSearchConditionNumeric($searchModel->living_area,
                "#__portal_properties",
                "living_area",
                $conditions);
        }
        if (!empty($searchModel->living_area)) {
            $searchService->buildToFromSearchConditionNumeric($searchModel->land_area,
                "#__portal_properties",
                "land_area",
                $conditions);
        }
        if (!empty($searchModel->cubic_volume)) {
            $searchService->buildToFromSearchConditionNumeric($searchModel->cubic_volume,
                "#__portal_properties",
                "cubic_volume",
                $conditions);

        }
        if (!empty($searchModel->total_number_of_rooms)) {
            $searchService->buildToFromSearchConditionNumeric($searchModel->total_number_of_rooms,
                "#__portal_properties",
                "total_number_of_rooms",
                $conditions);

        }
        if (!empty($searchModel->number_of_bathrooms)) {
            $searchService->buildToFromSearchConditionNumeric($searchModel->number_of_bathrooms,
                "#__portal_properties",
                "number_of_bathrooms",
                $conditions);

        }
        if (!empty($searchModel->number_of_toilet_rooms)) {
            $searchService->buildToFromSearchConditionNumeric($searchModel->number_of_toilet_rooms,
                "#__portal_properties",
                "number_of_toilet_rooms",
                $conditions);

        }
        if (!empty($searchModel->number_of_bedrooms)) {
            $searchService->buildToFromSearchConditionNumeric($searchModel->number_of_bedrooms,
                "#__portal_properties",
                "number_of_bedrooms",
                $conditions);

        }
        if (!empty($searchModel->number_of_livingrooms)) {
            $searchService->buildToFromSearchConditionNumeric($searchModel->number_of_livingrooms,
                "#__portal_properties",
                "number_of_livingrooms",
                $conditions);

        }
        if (!empty($searchModel->number_of_floors)) {
            $searchService->buildToFromSearchConditionNumeric($searchModel->number_of_floors,
                "#__portal_properties",
                "number_of_floors",
                $conditions);

        }
        if (!empty($searchModel->year_build)) {
            $searchService->buildToFromSearchConditionNumeric($searchModel->year_build,
                "#__portal_properties",
                "year_build",
                $conditions);

        }
        if (!empty($searchModel->open_house_start)) {
            $searchService->buildToFromSearchConditionDate($searchModel->open_house_start,
                "#__portal_properties",
                "open_house_start",
                $conditions);
        }
        if (!empty($searchModel->open_house_end)) {
            $searchService->buildToFromSearchConditionDate($searchModel->open_house_end,
                "#__portal_properties",
                "open_house_end",
                $conditions);
        }

        if (!empty($searchModel->last_update)) {
            $searchService->buildToFromSearchConditionDate($searchModel->last_update,
                "#__portal_properties",
                "last_update",
                $conditions);
        }
        if (!empty($searchModel->created_date)) {
            $searchService->buildToFromSearchConditionDate($searchModel->created_date,
                "#__portal_properties",
                "created_date",
                $conditions);
        }
        if (!empty($searchModel->last_price_update_date)) {
            $searchService->buildToFromSearchConditionDate($searchModel->last_price_update_date,
                "#__portal_properties",
                "last_price_update_date",
                $conditions);
        }
        if (!empty($searchModel->last_price_reduction_date)) {
            $searchService->buildToFromSearchConditionDate($searchModel->last_price_reduction_date,
                "#__portal_properties",
                "last_price_reduction_date",
                $conditions);
        }

        if (!empty($searchModel->zone_id)) {
            $conditions[] = " ( #__portal_properties.zone_Id = {$searchModel->zone_id} )";
        }

        if (!empty($searchModel->exclusive_entrance)) {
            $conditions[] = " ( #__portal_properties.exclusive_entrance = {$searchModel->exclusive_entrance} )";
        }

        if (!empty($searchModel->exclusive_entrance)) {
            $conditions[] = " ( #__portal_properties.exclusive_entrance = {$searchModel->exclusive_entrance} )";
        }
        if (!empty($searchModel->swapping)) {
            $conditions[] = " ( #__portal_properties.swapping = {$searchModel->swapping} )";
        }
        if (!empty($searchModel->is_featured)) {
            $conditions[] = " ( #__portal_properties.is_featured = {$searchModel->is_featured} )";
        }
        if (!empty($searchModel->extra_flat)) {
            $conditions[] = " ( #__portal_properties.extra_flat = {$searchModel->extra_flat} )";
        }
        if (!empty($searchModel->elevator)) {
            $conditions[] = " ( #__portal_properties.elevator = {$searchModel->elevator} )";
        }
        if (!empty($searchModel->garage)) {
            $conditions[] = " ( #__portal_properties.garage = {$searchModel->garage} )";
        }
        if (!empty($searchModel->garage_area)) {
            $searchService->buildToFromSearchConditionNumeric($searchModel->garage_area,
                "#__portal_properties",
                "garage_area",
                $conditions);
        }
        if (!empty($searchModel->new_today)) {
            $conditions[] = " ( #__portal_properties.created_date >= CURDATE() )";
        }
        if (!empty($searchModel->new_this_week)) {
            $conditions[] = " ( DATEDIFF( NOW(), #__portal_properties.created_date ) < 7) ";
        }
        if (!empty($searchModel->loan80)) {
            $conditions[] = " ( #__portal_properties.mortgage >= (#__portal_properties.current_listing_price * 0.8 ) )";
        }

        if (!WFactory::getSqlService()->returnDeletedRecord()) {
            $conditions[] = " ( #__portal_properties.sent_to_web = 1 )";
        }

        if (!WFactory::getSqlService()->returnDeletedRecord()) {
            $conditions[] = " ( #__portal_properties.is_deleted = 0 )";
        }

        if (!empty((array)$searchModel->bounds)) {
            if (is_object($searchModel->bounds))
                $searchModel->bounds = get_object_vars($searchModel->bounds);
            $conditions[] = "(( #__portal_properties.longitude between {$searchModel->bounds['west']} and {$searchModel->bounds['east']}) AND
                              ( #__portal_properties.latitude between {$searchModel->bounds['south']} and {$searchModel->bounds['north']}) ) ";
        }


        if (!WFactory::getHelper()->isNullOrEmptyString($searchModel->latitude) &&
            !WFactory::getHelper()->isNullOrEmptyString($searchModel->longitude)
        ) {

            if ($searchModel->source !== "WEB_APP") {
                $searchModel->radius = 150; // some special stuff for mobile phones...
                $searchModel->limit_start = 0;
                $searchModel->limit_length = 100;
            }


            //$searchModel->limit=700;
            $whereClause = "";
            $selectClause = "";
            $searchService->createLatLangSearchCondition(
                $searchModel->latitude,
                $searchModel->longitude,
                $searchModel->radius,
                $selectClause,
                $whereClause);
            $havingCondition[] = $whereClause;
            $selectStatement[] = $selectClause;

            if (is_array($searchModel->order)) {
                $index = array_search("ORDER_BY_NEAREST_FIRST", $searchModel->order);
                if ($index !== false) {
                    unset($searchModel->order[$index]);
                }
                array_shift($searchModel->order, "ORDER_BY_NEAREST_FIRST");
            } else if (is_string($searchModel->order)) {
                $searchModel->order = array("ORDER_BY_NEAREST_FIRST", $searchModel->order);
            }


        }

        if (!empty($selectStatement)) {
            $selectStatement = "SELECT " . implode(" , ", $selectStatement);
        }


        if (!empty($conditions)) {

            $conditions = array_filter($conditions, array($this, "filterConditionAndRemoveEmpty"));
            $conditions = implode(" and ", $conditions);
            $conditions = "where $conditions";
        }

        if (!empty($havingCondition)) {

            $havingCondition = array_filter($havingCondition, array($this, "filterConditionAndRemoveEmpty"));
            $havingCondition = implode(" and ", $havingCondition);
            $conditions = $conditions . " HAVING $havingCondition";
        }


        if (!empty($searchModel->order)) {
            $definedGlobalVariables = get_defined_constants(false);


            if (is_string($searchModel->order))
                $searchModel->order = array($searchModel->order);
            $orders = array();

            foreach ($searchModel->order as $o) {
                if (array_key_exists($o, $searchModel->defaultOrderByArray)) {
                    $orders[] = $searchModel->defaultOrderByArray[$o];
                } else if (array_key_exists($o, $definedGlobalVariables)) {
                    $orders[] = $definedGlobalVariables[$o];
                } else
                    $orders[] = $o;
            }

            $orders = implode(", ", $orders);

            $orders = "ORDER BY $orders";
        } else {
            $orders = "ORDER BY created_date desc";
        }


        $fullQuery = "$selectStatement from #__portal_properties $conditions  $orders ";


        // if(WFactory::getHelper()->isUnitTest()){
        //   $fullQuery = "$fullQuery LIMIT 100";
        // }

        //$fullQuery = "$fullQuery LIMIT 100";

        //if (__ISUNITTEST) {
        WFactory::getLogger()->debug("selecting with: --> : \n\t $fullQuery");
        //}

        //exit(1);


        $result = WFactory::getSqlService()->select($fullQuery);

        $originalNumberOfResult = count($result);

        WFactory::getLogger()->debug("Total result of query is:" . $originalNumberOfResult);

//        if ($searchModel->returnType !== RETURN_TYPE_DETAIL) {
//            //save the thing in the list.....
//            //$this->saveSearchResult($result);
//
//        }


        $result = $this->applyLimits($searchModel, $result, true);
        $result = $this->formatResultBasedOnReturnType($result, $searchModel->returnType, null, $searchModel->search_key);


        if ($beanStalkdClass = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_BEANSTALKD)->isBeanstalkdEnabled()) {

            $this->registerForNewSaveSearch($searchModel, $result);
        } else {
            WFactory::getLogger()->warn("Beanstalkd disabled in configuration!Not registerForNewSaveSearch-ing");
        }

        $end = microtime();
        $spent = $end - $start;
        WFactory::getLogger()->debug("[Search][CacheMiss]: $spent seconds ");

        $searchModel->timespent = $spent;


        $result = $this->appendSearchModelApplyHint($searchModel, $result, $originalNumberOfResult);

        // sleep(10);//induce delay
        return $result;

    }

    /**
     * @param $seachModel SearchModel
     * @param $result
     * @param bool|false $last_page_if_empty
     * @return array
     */
    function applyLimits($seachModel, $result, $last_page_if_empty = false)
    {
        $finalValue = $result;
        if ($seachModel->is_next_previous) {

            $centerProperty = $seachModel->next_previous_center_property_id;

            foreach ($result as $i => $r) {
                if (intval($r['id']) === $centerProperty) {
                    //before

                    $start = $i - $seachModel->next_previous_max_length;
                    if ($start < 0)
                        $start = 0;
                    $end = $i + $seachModel->next_previous_max_length;
                    $finalValue = array_slice($result, $start, $end - $start + 1);
                    return $finalValue;
                }
            }


        }


        WFactory::getLogger()->debug("Applying limit ---->>   start: {$seachModel->limit_start} , length : {$seachModel->limit_length} , result size: " . count($result));

        if (!is_null($start = (int)$seachModel->limit_start)
            && !is_null($length = (int)$seachModel->limit_length)
        ) {

            // If we are paginating out of bounds, and $last_page_if_empty is true, then get the last page
            if ($start >= count($result) && $last_page_if_empty)
                $start = $seachModel->limit_start = count($result) - (count($result) % $length);


            if ($start >= 0 && $length > 0)
                $finalValue = array_slice($result, $seachModel->limit_start, $length);


            if (empty($finalValue) && !empty($result)) {
                WFactory::getLogger()->warn("Due to applying limit, 0 properties will be returned. Because limit start is {$seachModel->limit_start}
                and search result size is : " . count($result));
            }

        }

        return $finalValue;
    }

    /**
     * @param $searchModel SearchModel
     * @param $searchResult
     * @param $originalNumberOfResults
     * @return array
     */
    function appendSearchModelApplyHint($searchModel, $searchResult, $originalNumberOfResults)
    {


        // give a hint of how many pages there should be
        if ($originalNumberOfResults) {
            if (is_object($searchResult[0]))
                $searchResult[0]->pagination_total_results = $originalNumberOfResults;
            else if (is_array($searchResult[0]))
                $searchResult[0]['pagination_total_results'] = $originalNumberOfResults;
        }


        //search key to be added
        if (is_object($searchResult[0]))
            $searchResult[0]->search_key = $searchModel->search_key;
        else if (is_array($searchResult[0]))
            $searchResult[0]['search_key'] = $searchModel->search_key;
        else if (empty($searchResult)) {
            $searchResult[0] = array('search_key_only' => $searchModel->search_key);
        }

        // WFactory::getLogger()->debug("Final value contains " . count($finalValue) . " results", __LINE__, __FILE__);


        return $searchResult;
    }


    function getPropertiesAgentsOfficesFromSearchResult($search_result, &$properties, &$agents, &$offices)
    {
        foreach ($search_result as $s) {
            /**
             * @var $s PropertyListModel
             */
            $properties[] = $s->property_id;
            $agents[] = $s->sale_id;
            $offices[] = $s->office_id;
        }

        $properties = array_unique($properties);
        $agents = array_unique($agents);
        $offices = array_unique($offices);


    }

    function filterConditionAndRemoveEmpty($var)
    {
        if (WFactory::getHelper()->isNullOrEmptyString($var))
            return false;
        if (str_replace(" ", "", $var) == "()")
            return false;
        return true;
    }

    function formatResultBasedOnReturnType($result, $returnType, $distanceArray = null, $searchKey)
    {

        $returnArray = array();

        $totalMemory = WFactory::getHelper()->getMemoryLimit();

        $available = $totalMemory - memory_get_usage();

        WFactory::getLogger()->info("Available memory \t:$available");


        if ($returnType == RETURN_TYPE_LIST || $returnType == null) {

            foreach ($result as $r) {
                $available = $totalMemory - memory_get_usage();
                WFactory::getLogger()->debug("Available memory \t:$available");
                $model = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTY)->getPropertyListModel();

                $model->bindToDb($r);
                //$model->search_key = $searchKey;

                $returnArray[] = $model;
            }
        }
        if ($returnType == RETURN_TYPE_MAP) {

            // WFactory::getLogger()->warn("Return type map is NOT defined yet..so just returning list type for now");

            foreach ($result as $r) {
                $available = $totalMemory - memory_get_usage();
                WFactory::getLogger()->debug("Available memory \t:$available");
                $model = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTY)->getPropertyMapModel();

                $model->bindToDb($r);
                //$model->search_key = $searchKey;
                $returnArray[] = $model;
            }
        }
        if ($returnType == RETURN_TYPE_DETAIL) {
            foreach ($result as $r) {
                $available = $totalMemory - memory_get_usage();
                WFactory::getLogger()->debug("Available memory \t:$available");
                $model = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTY)->getPropertyDetailsModel();

                $model->bindToDb($r);
                //$model->search_key = $searchKey;

                $returnArray[] = $model;
            }
        }

        return $returnArray;
    }


    /**
     * @param $office PortalPortalOfficesSql
     * @return bool
     */
    function updatePropertyTableWithOfficeInformation($office)
    {
        $officeId = $office->__id;
        $properties = $this->getPropertiesByOffice($officeId);
        $returnResult = true;
        foreach ($properties as $property) {

            /**
             * @var $property PropertyListModel
             * @var $sqlPropertyClass PortalPortalPropertiesSql
             */
            $sqlPropertyClass = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_PROPERTIES_SQL);
            $sqlPropertyClass->__id = $property->property_id;
            $sqlPropertyClass->loadDataFromDatabase();

            $sqlPropertyClass->__office_name = $office->__office_name;
            $sqlPropertyClass->__office_id = $office->__id;
            $sqlPropertyClass->__office_logo_path = $office->__logo;
            $sqlPropertyClass->__office_email = $office->__email;
            $sqlPropertyClass->__office_phone = $office->__phone;


            $result = WFactory::getSqlService()->update($sqlPropertyClass);
            $returnResult = $returnResult && $result;
        }


        return $returnResult;
    }


    function getPropertiesByAgent($agentId)
    {
        $seachModel = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_SEARCH)->getSearchModel();;
        $seachModel->sale_id = $agentId;
        $seachModel->limit_start = 0;
        $seachModel->limit_length = 15;

        $properties = $this->search($seachModel);

        return $properties;
    }

    function getPropertiesByOffice($officeId)
    {
        $seachModel = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_SEARCH)->getSearchModel();;
        $seachModel->office_id = $officeId;
        $seachModel->limit_start = 0;
        $seachModel->limit_length = 15;

        $properties = $this->search($seachModel);

        return $properties;
    }


    function getAllFeatures($selectedFeaturesId = array(), &$mergedFeatures = array(), $categorised = false)
    {
        $query = "SELECT #__portal_features.*
                  FROM #__portal_features jos_portal_features
                  ORDER BY name";

        $result = WFactory::getSqlService()->select($query);

        $return = array();

        foreach ($result as $r) {
            $r['type'] = JText::_(strtoupper($r["type"]));

            if ($categorised) {
                if (!array_key_exists($r['type'], $return))
                    $return[$r['type']] = array();

                $return[$r['type']][] = array('name' => JText::_(strtoupper($r["name"])), 'id' => $r['id']);
            } else {
                $return[$r["id"]] = $r["name"];
            }


            if (in_array($r["id"], $selectedFeaturesId)) {
                $mergedFeatures[$r["id"]] = true;
            } else
                $mergedFeatures[$r["id"]] = false;
        }

        return $return;
    }


    /**
     * registers a beanstalkd job in order to process it later
     * @param $searchModel
     * @param $searchResult
     */
    function registerForNewSaveSearch($searchModel, $searchResult)
    {
        if (is_object($searchModel))
            $searchModel = get_object_vars($searchModel);
        if (is_object($searchResult))
            $searchResult = get_object_vars($searchResult);


        $payload = array("searchModel" => $searchModel, "searchResult" => $searchResult);

        $beanStalkdClass = WFactory::getServices()
            ->getServiceClass(__PROPPERTY_PORTAL_BEANSTALKD)
            ->getBeanstalkdModel(__PROPPERTY_PORTAL_PROPERTIES, "processNewSaveSearch", $payload);


        $result = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_BEANSTALKD)->putSearchQueue($beanStalkdClass);


        if ($result === null) {
            WFactory::getLogger()->warn("Failed to put beanstalkd job..is beanstalkd configured / running ?");
        } else {
            WFactory::getLogger()->debug("Beanstalkd job registered for registerForNewSaveSearch");
        }
    }

    /**
     * @param $queryModel SearchModel
     * @return bool|mixed|string
     *
     */

    function query($queryModel)
    {

        //$x = $queryModel;
        $sqlService = WFactory::getServices()->getSqlService();
        /**
         * @var $senToWebLog PortalPortalSenttowebLogSql
         */
        $senToWebLog = $sqlService->getDbClass(__PORTAL_PORTAL_SENTTOWEB_LOG_SQL);
        $senToWebLog->__command = "PROPERTY_QUERY";
        $senToWebLog->__data = json_encode($queryModel);
        $senToWebLog->__direction = "INCOMING";
        $senToWebLog->__fromip = $_SERVER['REMOTE_ADDR'];
        $senToWebLog->__toip = "127.0.0.1";
        $senToWebLog->__type = "Property";
        $senToWebLog->__associated_id = 0;
        $senToWebLog->__realted_senttoweb_id = 0;
        $senToWebLog->__date = $sqlService->getMySqlDateTime();


        $id = $sqlService->insert($senToWebLog);


        $companyMail = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_COMPANY)->getCompanyEmail();
        /**
         * @var $addressService AddressService
         */
        $addressService = WFactory::getServices()->getSqlService()->getSqlServiceClass(__PROPPERTY_PORTAL_ADDRESS);
        $queryPrefix = "[PROPERTY QUERY]";


        $subject = "$queryPrefix New incoming property query";

        $message = "---User information  -----<br/>";
        $message .= "<b>Name:</b> {$queryModel->userInfo->name} <br/>";
        $message .= "<b>Phone:</b> {$queryModel->userInfo->phoneNumber} <br/>";
        $message .= "<b>Email:</b> {$queryModel->userInfo->email} <br/>";
        $message .= "<br/>";
        $message .= "---Query Detail  -----<br/>";

        $region = $addressService->region($queryModel->region_id);
        $message .= "<b>Province:</b> {$region['name']} <br/>";

        $town = $addressService->town($queryModel->city_town_id);
        $message .= "<b>District:</b> {$town['name']} <br/>";

        $postalCode = $addressService->postal_code($queryModel->zip_code_id);
        $message .= "<b>Area:</b> {$postalCode['name']} <br/>";


        $message .= "<b>Street:</b> {$queryModel->street}<br/>";
        $message .= "<b>Transport</b> {$queryModel->transport_line} , {$queryModel->transport_station} <br/>";


        $message .= "<b>Comment:</b> {$queryModel->text} <br/>";

        $type = intval($queryModel->type_id);
        if ($type == 1) {
            $type = "Both sale / rent";
        } else if ($type == 2) {
            $type = "Sale";
        } else if ($type == 3) {
            $type = "Rental";
        }
        $message .= "<b>Type:</b> {$queryModel->category_name} ({$queryModel->residential_commercial_type})<br/>";
        $message .= "<b>Sale/Rent:</b>$type<br/>";


//        if ($debugMode) {
        $tos = array("shroukkhan@gmail.com");
        if (__CUSTOMER_ID == "ERA_TH")
            $tos[] = "eracall@gmail.com";
//        } else {
//            $officeEmail = $office['email'];
//
//            if (WFactory::getHelper()->isNullOrEmptyString($officeEmail))
//                $tos = array($companyMail, "shroukkhan@gmail.com");
//            else
//                $tos = array($officeEmail, $companyMail, "shroukkhan@gmail.com");
//        }

//        if (!WFactory::getHelper()->isNullOrEmptyString($officeName)) {
//            $subject .= "[ $officeName ]";
//        }

        WFactory::getLogger()->info("Sending property details mail to company owner");
        foreach ($tos as $to) {
            $response = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_MANDRILL)->sendMandrillMail(
                $subject,
                $message,
                $companyMail,
                $to,
                "",
                true

            );

            WFactory::getLogger()->info("Sending property details mail to company owner response: $response");

            WFactory::getLogger()->logEmail("QUERY_ADDED", $subject, null, $companyMail, $to, $message, $response);
        }

    }


    function getProperties($limit = 10, $returnType = RETURN_TYPE_LIST, $orderBy = ORDER_BY_NEWEST_FIRST, $searchModel = null)
    {
        if ($searchModel === null) {
            /**
             * @var $searchModel SearchModel
             */
            $searchModel = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_SEARCH)->getSearchModel();

            $searchModel->order = array($orderBy);
            $searchModel->limit_start = 0;
            $searchModel->limit_length = $limit;
            $searchModel->returnType = RETURN_TYPE_LIST;
            $searchModel->return_properties_with_no_address = true;
        }

        $result = $this->search($searchModel);

        /**
         * @var $r PropertyListModel
         */
        foreach ($result as &$r) {
            $images = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTY)->getAllPropertyImages($r->property_id);
            $r->images = $images;
        }


        return $result;
    }

    function getNewestProperties($limit = 10, $returnType = RETURN_TYPE_LIST)
    {
        return $this->getProperties($limit, $returnType, ORDER_BY_NEWEST_FIRST);
    }

    function getRandomProperties($limit = 10, $returnType = RETURN_TYPE_LIST)
    {
        return $this->getProperties($limit, $returnType, ORDER_BY_RANDOM);
    }

    /**
     * This will search for property categories and if any type of land is searched,it will add ALL types of lands
     * http://redmine.softverk.is/issues/1385
     * @param $searchModel SearchModel
     */
    function resolveIssue1385(&$searchModel)
    {
        $categories = $searchModel->category_id;
        // land = 106 [ residential ]
        // land/farm = 116 [ commercial ]
        if (__CUSTOMER_ID === "REMAX_THAILAND") {

            if (is_string($categories) || is_numeric($categories)) {

                if ($categories == "106" || $categories == "116") {
                    $categories = array("106", "116");
                    $searchModel->category_id = $categories;

                }

            } else if (is_array($categories)) {
                if (in_array("106", $categories) || in_array("116", $categories)) {
                    $categories[] = "106";
                    $categories[] = "116";
                    $categories = array_unique($categories);
                    $searchModel->category_id = $categories;
                }
            }


        }

    }


    /**
     * Called from beanstalk in order to save a search result into database
     *
     * @param $payload
     * @return bool|mixed
     */
    function processNewSaveSearch($payload)
    {

        WFactory::getLogger()->debug("Performing : " . __FUNCTION__);

        $searchModel = $payload['searchModel'];
        $result = $payload['result'];
        $searchService = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_SEARCH);
        //save it for future retrival
        /**
         * @var $savedSearchModel PortalPortalSavedSearchSql
         */
        $savedSearchModel = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_SAVED_SEARCH_SQL);
        $savedSearchModel->__search_hash = $searchService->generateSearchHash($searchModel);
        $properties = $agents = $offices = array();
        $this->getPropertiesAgentsOfficesFromSearchResult($result, $properties, $agents, $offices);
        $savedSearchModel->__properties = implode("|", $properties);
        $savedSearchModel->__agents = implode("|", $agents);
        $savedSearchModel->__offices = implode("|", $offices);
        $savedSearchModel->__created = WFactory::getSqlService()->getMySqlDateTime();
        $savedSearchModel->__hits = 1;
        $savedSearchModel->__search_result = json_encode($result);
        $savedSearchModel->__search_model = $searchService->trimSearchModel($searchModel);
        $savedSearchModel->__updated = $savedSearchModel->__created;
        $savedSearchModel->__is_valid = 1;
        $result = $searchService->saveSearchIntoCache($savedSearchModel);

        WFactory::getLogger()->debug("Save search result : " . $result);

        return $result;
    }

    function downloadAddPropertyLog($password)
    {

        if ($password['password'] !== 'SoFTVERkPOrTAL1986')
            return;

        $query = "SELECT *  FROM `#__portal_property_email_log` WHERE `type` LIKE '%ADD_PROPERTY%'";

        $addedProperties = WFactory::getSqlService()->select($query);

        try {

            $objPHPExcel = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_EXCEL)->getPhpExcel();


            $date = WFactory::getSqlService()->getMySqlDateTime();
            // Set document properties
            $objPHPExcel->getProperties()->setCreator("Softverk Webportal")
                ->setLastModifiedBy("Shrouk Khan")
                ->setTitle("Contacts [" . $date . "]")
                ->setSubject("Webportal add property log")
                ->setDescription("This document contains all the properties that were added by user until $date")
                ->setKeywords("office 2007 webportal add property")
                ->setCategory("Contacts");


            $workSheet = $objPHPExcel->setActiveSheetIndex(0);

            $yindex = 1;
            foreach ($addedProperties as $aPropertyLine) {
                $xindex = "A";
                if ($yindex == 1) //header
                {

                    foreach ($aPropertyLine as $key => $value) {

                        // WFactory::getLogger()->debug("Saving header $key to $xindex$yindex");

                        $workSheet->setCellValue("$xindex$yindex", $key);
                        $xindex++;
                    }

                    $workSheet->getStyle('A1:' . $xindex . $yindex)->getFont()->setBold(true);

                } else {
                    $xindex = "A";
                    foreach ($aPropertyLine as $key => $value) {
                        //WFactory::getLogger()->debug("Saving row $value to $xindex$yindex");
                        $workSheet->setCellValue("$xindex$yindex", $value);
                        $xindex++;
                    }
                }
                $yindex++;
            }

            $objPHPExcel->getActiveSheet()->setTitle('Add Property Log');


            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $fileName = uniqid() . ".xls";

            $filePath = JPATH_BASE . DS . "tmp" . DS . $fileName;
            $objWriter->save($filePath);

            WFactory::getLogger()->debug("Add Property log list generated and saved in $filePath");

            if (WFactory::getHelper()->isUnitTest())
                return $filePath;

            JFactory::getApplication()->redirect(JUri::base() . "tmp/$fileName");
            //return JUri::base() . "tmp/$fileName";
        } catch (Exception $e) {
            $msg = "Excel generation error! msg : " . $e->getMessage();
            WFactory::getLogger()->fatal($msg);
            WFactory::throwPortalException($msg);
        }


    }


    function importPropertyFeaturesFromCsv()
    {
        $csvLocation = JPATH_BASE . "/tests/portaltest/services/features.csv";
        $csvContent = file_get_contents($csvLocation);
        $csvContent = explode("\n", $csvContent);


        $query = "truncate table #__portal_features";
        $result = WFactory::getSqlService()->update($query);


        //id;type;en_type;feature;en_feature

        foreach ($csvContent as $i => $c) {
            $c = explode(';', $c);
            if ($i > 0) {//first line is header
                /**
                 * @var $featuresTable PortalPortalFeaturesSql
                 */
                $featuresTable = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_FEATURES_SQL);
                $featuresTable->__name = str_replace('"', '', $c[4]);
                $featuresTable->__id = str_replace('"', '', $c[0]);
                $featuresTable->__type = str_replace('"', '', $c[2]);

                WFactory::getSqlService()->insert($featuresTable);


            }

        }
        echo "------------ \n";
        foreach ($csvContent as $i => $c) {
            $c = explode(';', $c);
            if ($i > 0) {//first line is header
                /**
                 * @var $featuresTable PortalPortalFeaturesSql
                 */
                $featuresTable = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_FEATURES_SQL);
                $featuresTable->__name = str_replace('"', '', $c[4]);
                $featuresTable->__id = str_replace('"', '', $c[0]);
                $featuresTable->__type = str_replace('"', '', $c[2]);

                echo strtoupper($featuresTable->__name) . "\t=\t" . str_replace('"', '', $c[3]) . "\n";

            }

        }

        echo "------------ \n";
        foreach ($csvContent as $i => $c) {
            $c = explode(';', $c);
            if ($i > 0) {//first line is header
                /**
                 * @var $featuresTable PortalPortalFeaturesSql
                 */
                $featuresTable = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_FEATURES_SQL);
                $featuresTable->__name = str_replace('"', '', $c[4]);
                $featuresTable->__id = str_replace('"', '', $c[0]);
                $featuresTable->__type = str_replace('"', '', $c[2]);


                echo strtoupper($featuresTable->__name) . "\t=\t" . str_replace('"', '', $c[4]) . "\n";

            }

        }

        //id;type;en_type;feature;en_feature
        echo "--------++++++++++---- \n";

        $arrayTypeEn = array();
        $arrayTypeTh = array();

        foreach ($csvContent as $i => $c) {
            $c = explode(';', $c);
            if ($i > 0) {//first line is header
                /**
                 * @var $featuresTable PortalPortalFeaturesSql
                 */
                $featuresTable = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_FEATURES_SQL);
                $featuresTable->__name = str_replace('"', '', $c[4]);
                $featuresTable->__id = str_replace('"', '', $c[0]);
                $featuresTable->__type = str_replace('"', '', $c[2]);


                if (!in_array($featuresTable->__type, $arrayTypeEn)) {
                    $arrayTypeEn[] = $featuresTable->__type;
                    echo strtoupper($featuresTable->__type) . "\t=\t" . str_replace('"', '', $c[2]) . "\n";
                }

            }

        }
        echo "--------++++++++++---- \n";
        foreach ($csvContent as $i => $c) {
            $c = explode(';', $c);
            if ($i > 0) {//first line is header
                /**
                 * @var $featuresTable PortalPortalFeaturesSql
                 */
                $featuresTable = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_FEATURES_SQL);
                $featuresTable->__name = str_replace('"', '', $c[4]);
                $featuresTable->__id = str_replace('"', '', $c[0]);
                $featuresTable->__type = str_replace('"', '', $c[2]);


                if (!in_array($featuresTable->__type, $arrayTypeTh)) {
                    $arrayTypeTh[] = $featuresTable->__type;
                    echo strtoupper($featuresTable->__type) . "\t=\t" . str_replace('"', '', $c[1]) . "\n";
                }

            }

        }


    }


}
