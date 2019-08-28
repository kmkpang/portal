<?php

/**
 * Created by PhpStorm.
 * User: khan
 * Date: 5/10/14
 * Time: 3:21 PM
 */

require_once JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . 'search' . DS . 'searchModel.php';
require_once JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . 'search' . DS . 'urlSearchModel.php';

class SearchService
{

    function __construct()
    {
        JFactory::getSession();
    }

    function getSearchModel($asJson = false)
    {
        $model = new SearchModel();

        if ($asJson) {
            $model = json_encode(get_object_vars($model));
        }

        return $model;
    }

    /**
     * @return UrlSearchModel
     */
    function getUrlSearchModel()
    {
        return new UrlSearchModel();
    }

    function createLatLangSearchCondition($latitude, $longitude, $radius, &$selectClause, &$whereClause)
    {
        $selectClause = "(
                                6371 * acos(
                                                cos( radians({$latitude}) ) * cos( radians( `latitude` ) ) *
                                                cos( radians( `longitude` ) - radians({$longitude}) ) +
                                                sin( radians({$latitude}) ) * sin( radians( `latitude` )
                                                )
                                            )
                         ) AS distance";

        if (is_array($radius)) {
            $radius = $radius[0];
        }


        $whereClause = " distance <= {$radius}";


    }

    function doLatLangSearch($latitude, $longitude, $radius, $returnOnlyId = true)
    {

        if ($radius) {
            //http://stackoverflow.com/questions/8850336/radius-of-40-kilometers-using-latitude-and-longitude
            $query = "SELECT
                        #__portal_properties.id,
                        ( 6371 * acos( cos( radians({$latitude}) ) * cos( radians( `latitude` ) ) * cos( radians( `longitude` ) - radians({$longitude}) ) + sin( radians({$latitude}) ) * sin( radians( `latitude` ) ) ) ) AS distance
                    FROM #__portal_properties
                    HAVING distance <= {$radius} ORDER BY distance";

        } else {
            $query = "SELECT #__portal_properties.id
                          FROM  #__portal_properties
                         WHERE     (#__portal_properties.latitude = $latitude)
                               AND (#__portal_properties.longitude = $longitude)";
        }

        $return = WFactory::getSqlService()->select($query);

        if ($returnOnlyId) {
            $result = array();
            foreach ($return as $r) {
                $result[] = $r['id']; //benchmark shows foreach is faster than arraymap : http://stackoverflow.com/questions/18144782/performance-of-foreach-array-map-with-lambda-and-array-map-with-static-function
            }
            $return = $result;
        }

        if (empty($result))
            WFactory::getLogger()->warn("Places search with lat:$latitude, long:$longitude and radius: $radius returned empty! ");

        return $return;
    }

    //TODO: this is too Thailand specific. make it more international
    function doStreetSearch($street)
    {
        $street = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {
            return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
        }, $street);
        $street = trim($street);
        $street = mysqli_real_escape_string(WFactory::getSqlService()->getDbo()->getConnection(), $street);
        $query = "SELECT 
                   #__portal_properties.id
              FROM #__portal_properties #__portal_properties
                   INNER JOIN
                  #__portal_property_addresses
                   #__portal_property_addresses
                      ON (#__portal_properties.address_id =
                             #__portal_property_addresses.id)
             WHERE    (#__portal_property_addresses.address like '%$street%')
                   OR (#__portal_property_addresses.street like '%$street%')";

        $propertyId = WFactory::getSqlService()->select($query);

        $result = array();
        foreach ($propertyId as $i) {
            $result[] = $i['id'];
        }
        return $result;

    }

    function doFullTextSearch($keywordText, $returnOnlyId = true)
    {
        if (empty($keywordText))
            return "";




        $preg_split_patterm = '/[\s]*[,][\s]*/';
        $all_keywords = preg_split($preg_split_patterm, $keywordText);

        // if one of the word is 3 letter or less, it wont work. so assume they are one whole word


        $description_keywords = array();
        $zipcode_keyword = array();
        $category_region_town_keyword = array();
        $property_ids = array();
        $less_than_3_letter_texts = array();
        $pattern_for_matching_zipcodes = '/\d+\D\-\D+|\D+\-\D+\d+|\d+\-\D+|\D+\-\d+/';


        foreach ($all_keywords as $i => $val) {
            $val = trim($val);
            if (strlen($val) <= 0) continue;

            if (strpos($val, "'") === false) { // if its not wrapped like 'xxx' 'yyy' , its not e zipcode, town , region or category [the 4 we provide]!
                array_push($description_keywords, "+$val*");
                //                $descriptions = explode(" ", trim($val));
//                foreach ($descriptions as $j => $des) {
//
//                    //check if reg_id
//                    $propertyIdFromRegId = $this->checkIfRegId($des);
//                    if ($propertyIdFromRegId !== false) {
//                        $property_ids[] = $propertyIdFromRegId;
//                    } else {
//
//                        if (strlen($des) <= 3) {
//                            $less_than_3_letter_texts[] = $des;
//                        } else {
//                            $descriptions[$i] = '+' . $des . '*';
//                            array_push($description_keywords, $descriptions[$i]);
//                        }
//                    }
//                }
                continue;
            }

            $val = trim(substr($val, 1, -1)); /*remove the ' ' */
            $val = trim($val);
            if (preg_match($pattern_for_matching_zipcodes, $val) != 0) {
                $val = explode("-", $val);
                $v1 = trim($val[0]);
                $v2 = trim($val[1]);
                $zipNum = is_numeric($v1) ? $v1 : $v2;
                $zipName = is_numeric($v1) ? $v2 : $v1;
                $zipcode = "$zipNum $zipName";
                array_push($zipcode_keyword, '+"' . $zipcode . '"');

            } else if (array_key_exists($val, $category_region_town_keyword)) {
                array_push($category_region_town_keyword, '+"' . $val . '"');
            }
        }

        $keyword_array = array_merge($description_keywords, $zipcode_keyword, $category_region_town_keyword);

        // we can not do


        $keyword_query = implode(" ", $keyword_array);
        $selection = $returnOnlyId ? "id" : "*";
        $query = "SELECT $selection FROM #__portal_properties WHERE MATCH(full_text_search_helper) against ('$keyword_query' IN BOOLEAN MODE)";
        $return = WFactory::getSqlService()->select($query);



        if ($returnOnlyId) {
            $result = array();
            foreach ($return as $r) {
                $result[] = $r['id']; //benchmark shows foreach is faster than arraymap : http://stackoverflow.com/questions/18144782/performance-of-foreach-array-map-with-lambda-and-array-map-with-static-function
            }
            $return = $result;

        }
        $return = array_merge($return, $property_ids);

        if (empty($return)) {
            $return[] = '-1';//to make sure no property shows up
        }

        return $return;

    }


    function checkIfRegId($text)
    {
        $query = "SELECT #__portal_properties.id
                  FROM #__portal_properties where #__portal_properties.reg_id = '$text'";
        $result = WFactory::getSqlService()->select($query);
        if (empty($result))
            return false;
        return $result[0]['id'];
    }

    /**
     * builds condition like ( $tableName.$columnName = $idData[0] or $tableName.$columnName = $idData[1] )
     * @param $idData
     * @param $tableName
     * @param $columnName
     * @param $conditionArray
     */
    function buildIdSearchCondition($idData, $tableName, $columnName, &$conditionArray)
    {
        $idCondition = array();
        if (is_string($idData) || is_numeric($idData)) {
            $idCondition[] = "$tableName.$columnName = '$idData'";
        } else {
            foreach ($idData as $i) {
                if (!empty($i))
                    $idCondition[] = "$tableName.$columnName = '$i'";
            }
        }
        if (!empty($idCondition)) {
            $idCondition = implode(" or ", $idCondition);
            $conditionArray[] = " ( $idCondition ) ";
        }
    }


    /**
     * converts a search condition to simple string
     * @param $value
     * @param string $implodeWithGlue
     * @return string
     */
    function buildSearchCriteriaToSimpleString($value, $implodeWithGlue = ',')
    {
        if (is_string($value))
            return $value;
        if (is_object($value))
            $value = get_object_vars($value);
        if (is_array($value))
            $value = implode($implodeWithGlue, $value);

        return $value;
    }

    /**
     * Opposite of buildSearchCriteriaToSimpleString
     * @param $value
     * @param string $explodeWith
     * @return array|string
     */
    function buildSimpleStringToSearchCriteriaArray($value, $explodeWith = ',')
    {
        $value = trim($value);
        $value = explode($value, $explodeWith);
        foreach ($value as &$v)
            $v = trim($v);
        return $value;
    }

    /**
     * Builds a range condition like:
     *              ( $tableName.$columnName >= $idData[0] AND $tableName.$columnName <= $idData[1] )
     *     or       ( $tableName.$columnName <= $idData[1] )
     *     or       ( $tableName.$columnName >= $idData[0] )
     * @param $data
     * @param $tableName
     * @param $columnName
     * @param $conditionArray
     */
    function buildToFromSearchConditionNumeric($data, $tableName, $columnName, &$conditionArray)
    {
        $tempConditionArray = array();
        if (is_array($data)) {

            if (count($data) == 1) {
                $tempConditionArray[] = "$tableName.$columnName = {$data[0]}";
            } else {
                $from = intval($data[0]);
                $to = intval($data[1]);

                if ($from != 0) {
                    $tempConditionArray[] = "$tableName.$columnName >= $from";
                }
                if ($to != 0) {
                    $tempConditionArray[] = "$tableName.$columnName <= $to";
                }

            }

            if (!empty($tempConditionArray)) {

                $tempConditionArray = implode(" AND ", $tempConditionArray);
                $conditionArray[] = " ( $tempConditionArray ) ";
            }

        } else {
            WFactory::getLogger()->warn("$tableName.$columnName is NOT an array");
        }
    }

    /**
     * same as buildToFromSearchConditionNumeric , but with dates
     * @param $data
     * @param $tableName
     * @param $columnName
     * @param $conditionArray
     */
    function buildToFromSearchConditionDate($data, $tableName, $columnName, &$conditionArray)
    {
        $tempConditionArray = array();
        if (is_array($data)) {

            if (count($data) == 1) {
                $tempConditionArray[] = "$tableName.$columnName = '{$data[0]}'";
            } else {
                $from = intval($data[0]);
                $to = intval($data[1]);

                if ($from != 0) {
                    $tempConditionArray[] = "$tableName.$columnName >= '$from'";
                }
                if ($to != 0) {
                    $tempConditionArray[] = "$tableName.$columnName <='$to'";
                }

            }

            if (!empty($tempConditionArray)) {

                $tempConditionArray = implode(" AND ", $tempConditionArray);
                $conditionArray[] = " ( $tempConditionArray ) ";
            }

        } else {
            WFactory::getLogger()->warn("$tableName.$columnName is NOT an array");
        }
    }


    function getAllCategories()
    {
        $query = "SELECT #__portal_property_categories.id AS category_id,
                       #__portal_property_categories.description
                  FROM #__portal_property_categories #__portal_property_categories";
        $result = WFactory::getSqlService()->select($query);
        $return = array();
        foreach ($result as $val) {
            $return[trim($val['description'])] = trim($val['category_id']);
        }


        return $return;
    }

    function getAllZipCodes()
    {
        $query = "SELECT #__geography_postal_codes.id AS zip_code_id,
                       #__geography_postal_codes.name_en AS zip
                  FROM #__geography_postal_codes #__geography_postal_codes";

        $result = WFactory::getSqlService()->select($query);
        $return = array();
        foreach ($result as $val) {
            $return[trim($val['zip'])] = trim($val['zip_code_id']);
        }


        return $return;

    }

    /**
     * @param $hashes String
     * @return SearchModel
     */
    function generateSearchModelFromSearchHash($hashes)
    {
        $hashes = base64_decode($hashes);

        $searchModel = json_decode($hashes);

        return $searchModel;

    }

    /**
     * returns a crc32 calculated value of search model
     * @param $searchModel SearchModel
     * @return int
     */
    function generateSearchHash($searchModel)
    {

        $urlModel = $this->getUrlSearchModel();

        if (is_array($searchModel->category_id) && !empty($searchModel->category_id)) {
            $searchModel->category_id = implode(',', $searchModel->category_id);
            $urlModel->type = $searchModel->category_id;
        }

        if ($searchModel->type_id == 2)
            $urlModel->type .= ',SALE';
        else if ($searchModel->type_id == 3)
            $urlModel->type .= ',RENT';
        else
            $urlModel->type .= ',ALL';

        if ($searchModel->loan80) {
            $urlModel->loan80 = "YES";
        }
        if ($searchModel->garage) {
            $urlModel->garage = "YES";
        }
        if ($searchModel->elevator) {
            $urlModel->elevator = "YES";
        }
        if ($searchModel->new_today) {
            $urlModel->new_today = "YES";
        }
        if ($searchModel->new_this_week) {
            $urlModel->new_this_week = "YES";
        }
        if ($searchModel->swapping) {
            $urlModel->swapping = "YES";
        }
        if ($searchModel->is_featured) {
            $urlModel->featured = "YES";
        }
        if (!WFactory::getHelper()->isNullOrEmptyString($searchModel->text)) {
            $urlModel->text = trim($searchModel->text);
        }


        //--------------------------------------------------

        if (!empty($searchModel->sale_id))
            $urlModel->agent = $searchModel->sale_id;

        if (!empty($searchModel->office_id))
            $urlModel->office = $searchModel->office_id;

        if (!empty($searchModel->zip_code_id)) {
            $urlModel->zip = $this->buildSearchCriteriaToSimpleString($searchModel->zip_code_id);
        }

        if (!empty($searchModel->region_id)) {
            $urlModel->region = $this->buildSearchCriteriaToSimpleString($searchModel->region_id);
        }

        if (!empty($searchModel->city_town_id)) {
            $urlModel->town = $this->buildSearchCriteriaToSimpleString($searchModel->city_town_id);
        }

        if (!empty($searchModel->number_of_bedrooms)) {
            $urlModel->bedrooms = $this->buildSearchCriteriaToSimpleString($searchModel->number_of_bedrooms);
        }

        if (!empty($searchModel->number_of_bathrooms)) {
            $urlModel->bathrooms = $this->buildSearchCriteriaToSimpleString($searchModel->number_of_bathrooms);
        }

        if (!empty($searchModel->total_number_of_rooms)) {
            $urlModel->rooms = $this->buildSearchCriteriaToSimpleString($searchModel->total_number_of_rooms);
        }

        if (!empty($searchModel->order)) {
            $urlModel->order = $this->buildSearchCriteriaToSimpleString($searchModel->order);
        }

        if (!empty($searchModel->preferred_currency)) {
            $urlModel->currency = $this->buildSearchCriteriaToSimpleString($searchModel->preferred_currency);
        }

        if (!empty($searchModel->latitude)) {
            $urlModel->latitude = $this->buildSearchCriteriaToSimpleString($searchModel->latitude);
        }

        if (!empty($searchModel->longitude)) {
            $urlModel->longitude = $this->buildSearchCriteriaToSimpleString($searchModel->longitude);
        }

        if (!empty($searchModel->transport_line)) {
            $urlModel->line = $this->buildSearchCriteriaToSimpleString($searchModel->transport_line);
        }

        if (!empty($searchModel->transport_station)) {
            $urlModel->station = $this->buildSearchCriteriaToSimpleString($searchModel->transport_station);
        }

        if (!empty($searchModel->current_listing_price)) {
            $urlModel->price = $this->buildSearchCriteriaToSimpleString($searchModel->current_listing_price);
        }


        $resultString = $urlModel->toString();

        return $resultString;

    }

    function trimSearchModel($searchModel)
    {
        if (is_object($searchModel))
            $searchModel = get_object_vars($searchModel);

        unset($searchModel["search_key"]);
        unset($searchModel["limit_start"]);
        unset($searchModel["limit_length"]);
        unset($searchModel["timespent"]);
        unset($searchModel["redirectRef"]);

        $searchModel = array_filter($searchModel, array($this, "filterSearchModelForGeneratingHash"));

        $searchModel = json_encode($searchModel);

        return $searchModel;
    }

    function filterSearchModelForGeneratingHash($var)
    {
        if ($var === null)
            return false;

        return true;
    }

    function checkSearchCache($hash)
    {

        return false;
        $query = "SELECT #__portal_saved_search.id, #__portal_saved_search.hits,
                         #__portal_saved_search.search_result
                  FROM #__portal_saved_search #__portal_saved_search
                 WHERE (#__portal_saved_search.search_hash = $hash and #__portal_saved_search.is_valid = 1)";

        $result = WFactory::getSqlService()->select($query);

        if (empty($result))
            return false;

        $hits = intval($result[0]["hits"]) + 1;
        $id = $result[0]["id"];

        //using it like this seem to go faster!
        $query = "UPDATE #__portal_saved_search
                   SET hits = $hits
                 WHERE (#__portal_saved_search.id =$id );";

        WFactory::getSqlService()->update($query);

        WFactory::getLogger()->debug("Cache hit for $hash");


        return $result[0]["search_result"];

    }

    function saveSearchIntoCache($searchModel)
    {
        if (!JFactory::getConfig()->get('lian_db_test', false)) {
            if ($this->checkSearchCache($searchModel->__search_hash) === false) {
                return WFactory::getSqlService()->insert($searchModel);
            } else {
                WFactory::getLogger()->error("Multiple search models tried to save each other!! : new model : \r\n" . json_encode($searchModel, JSON_PRETTY_PRINT));
            }

        }

        return false;
    }

}
