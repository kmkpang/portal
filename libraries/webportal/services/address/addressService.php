<?php

/**
 * Created by JetBrains PhpStorm.
 * User: khan
 * Date: 3/12/13
 * Time: 2:42 PM
 * To change this template use File | Settings | File Templates.
 */
class AddressService
{

    var $countryId;
    var $countryCode;
    var $language;

    public function __construct($countryId = null, $countryCode = null)
    {
        if ($countryCode == null || $countryCode == null) {
            $configuration = WFactory::getConfig()->getWebportalConfigurationArray();
            $this->countryId = $configuration['countryId'];
            $this->countryCode = $configuration['countryCode'];
        } else {
            $this->countryId = $countryId;
            $this->countryCode = $countryCode;
        }

        $this->language = WFactory::getHelper()->getCurrentlySelectedLanguage();

        if (__COUNTRY == "TH" && $this->language == "th") {
            //oK!
        } else if (__COUNTRY == "IS" && $this->language == "is") {
            //oK!
        } else if (__COUNTRY == "PH" && $this->language == "tl") {
            //oK!
        } else {

            /**
             * @var $dbTable PortalGeographyPostalCodesSql
             */
            $dbTable = WFactory::getSqlService()->getDbClass(__PORTAL_GEOGRAPHY_POSTAL_CODES_SQL);

            if (!property_exists($dbTable, "__name_" . $this->language)) {
                WFactory::getLogger()->warn("Currently selected language {$this->language} does not exist in the PortalGeographyPostalCodesSql class..! add it or refractor this method,setting default language", __LINE__, __FILE__);
                $this->language = "en";
            }
        }
        WFactory::getLogger()->info("AddressService initialized with CountryID : {$this->countryId} and CountryCode : {$this->countryCode}");

    }

    /**
     * @return PropertyAddressModel
     */
    function getPropertyAddressModel()
    {
        require_once JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . "address" . DS . "propertyAddressModel.php";

        return new PropertyAddressModel();

    }

    function getStreetsTree()
    {
        $lang = $this->language;

        $query = "SELECT name_{$lang} as address from #__portal_street";

        $result = WFactory::getSqlService()->select($query);
        $finalResult = array();
        foreach ($result as $value) {

            if (!WFactory::getHelper()->isNullOrEmptyString($value['address'])) {
                $finalResult[$value['address']] = array("label" => $value['address'], "key" => $value['address']);
            }
        }
        return array_values($finalResult);

    }

    function getAreaGroupTree()
    {
        $lang = $this->language;

        $query = "SELECT name_{$lang} as area ,id as group_id from #__portal_area_group";

        $result = WFactory::getSqlService()->select($query);
        $finalResult = array();
        foreach ($result as $value) {

            if (!WFactory::getHelper()->isNullOrEmptyString($value['area'])) {
                $finalResult[$value['area']] = array("id" => $value['group_id'], "label" => $value['area']);
            }
        }

        return array_values($finalResult);
    }

    function getAreaTree()
    {
        $lang = $this->language;

        $query = "SELECT name_{$lang} as area, name_en as area_value ,id as area_id from #__portal_area";

        $result = WFactory::getSqlService()->select($query);
        $finalResult = array();
        foreach ($result as $value) {

            if (!WFactory::getHelper()->isNullOrEmptyString($value['area'])) {
                $finalResult[$value['area']] = array("id" => $value['area_id'], "label" => $value['area'], "value" => $value['area_value']);
            }
        }

        return array_values($finalResult);
    }

    function getTransportsTree($grouped)
    {
        if (is_object($grouped)) {
            $grouped = get_object_vars($grouped);
        }

        if (is_array($grouped)) {
            $grouped = $grouped['grouped'];
        }
        $grouped = false;
        if ($grouped === 'true')
            $grouped = true;

        //for now only th :)
        // C:\xampp\htdocs\softverk-webportal\templates\webportal\js\bts.json
        $data = file_get_contents(JPATH_ROOT . "/templates/webportal/js/bts.json");
        $data = json_decode($data);

        $lang = WFactory::getHelper()->getCurrentlySelectedLanguage(true);

        $langKey = "name_$lang";
        $langKeyBkup = "name_en";

        $result = array(
            "BTS" => array(),
            "MRT" => array(),
            "AIRPORT_LINK" => array(),
        );

        $parent = $data->sukhumvit;
        $parent = get_object_vars($parent);
        $parent = array_key_exists($langKey, $parent) ? $parent[$langKey] : $parent[$langKeyBkup];
        foreach ($data->sukhumvit->stations as $station) {
            $station = get_object_vars($station);
            $label = array_key_exists($langKey, $station) ? $station[$langKey] : $station[$langKeyBkup];
            $label = "$parent - $label";
            $key = "{$station['lat']},{$station['lng']}";
            $result['BTS'][] = array("label" => $label, "key" => $key);
        }
        $parent = $data->silom;
        $parent = get_object_vars($parent);
        $parent = array_key_exists($langKey, $parent) ? $parent[$langKey] : $parent[$langKeyBkup];
        foreach ($data->silom->stations as $station) {
            $station = get_object_vars($station);
            $label = array_key_exists($langKey, $station) ? $station[$langKey] : $station[$langKeyBkup];
            $label = "$parent - $label";
            $key = "{$station['lat']},{$station['lng']}";
            $result['BTS'][] = array("label" => $label, "key" => $key);
        }
        $parent = $data->mrt;
        $parent = get_object_vars($parent);
        $parent = array_key_exists($langKey, $parent) ? $parent[$langKey] : $parent[$langKeyBkup];
        foreach ($data->mrt->stations as $station) {
            $station = get_object_vars($station);
            $label = array_key_exists($langKey, $station) ? $station[$langKey] : $station[$langKeyBkup];
            $label = "$parent - $label";
            $key = "{$station['lat']},{$station['lng']}";
            $result['MRT'][] = array("label" => $label, "key" => $key);
        }
        $parent = $data->mrtp;
        $parent = get_object_vars($parent);
        $parent = array_key_exists($langKey, $parent) ? $parent[$langKey] : $parent[$langKeyBkup];
        foreach ($data->mrtp->stations as $station) {
            $station = get_object_vars($station);
            $label = array_key_exists($langKey, $station) ? $station[$langKey] : $station[$langKeyBkup];
            $label = "$parent - $label";
            $key = "{$station['lat']},{$station['lng']}";
            $result['MRT'][] = array("label" => $label, "key" => $key);
        }
        $parent = $data->ap_link;
        $parent = get_object_vars($parent);
        $parent = array_key_exists($langKey, $parent) ? $parent[$langKey] : $parent[$langKeyBkup];
        foreach ($data->ap_link->stations as $station) {
            $station = get_object_vars($station);
            $label = array_key_exists($langKey, $station) ? $station[$langKey] : $station[$langKeyBkup];
            $label = "$parent - $label";
            $key = "{$station['lat']},{$station['lng']}";
            $result['AIRPORT_LINK'][] = array("label" => $label, "key" => $key);
        }

        if (!$grouped) {//flatten
            $__result = array();
            foreach ($result as $stationType) {
                foreach ($stationType as $station)
                    $__result[] = $station;

            }
            $result = $__result;
        }


        return $result;
    }


    function getAddressTypeIdFromAddressType($addressType)
    {
        $query = "SELECT #__portal_address_types.id
                      FROM #__portal_address_types #__portal_address_types
                     WHERE (#__portal_address_types.description = '$addressType')";


        $result = WFactory::getServices()->getSqlService()->select($query);
        $result = $result[0]['id'];

        return $result;
    }

    function getAddressByPropertyId($propertyId)
    {
        $query = "SELECT #__portal_properties.address_id
                  FROM #__portal_properties #__portal_properties
                 WHERE (#__portal_properties.id = $propertyId)";

        $result = WFactory::getSqlService()->select($query);
        return $this->getAddress($result[0]['address_id']);
    }

    function getAddressByLanguage($addressId, $lang)
    {

        $query = "SELECT address
                  FROM #__portal_property_addresses
                 WHERE (id = $addressId)";

        $result = WFactory::getSqlService()->select($query);
        if (preg_match('/<' . $lang . '>/i', $result[0]['address'])) {
            $address = substr($result[0]['address'], strpos($result[0]['address'], '<' . $lang . '>') + 4);
            $address = substr($address, 0, strpos($address, '</' . $lang . '>'));
            $address_lang = $address;
        } //use for legacy version
        else {
            $address = substr($result[0]['address'], 0, strpos($result[0]['address'], '<'));
            $address_lang = $address;
        }

        if (!WFactory::getHelper()->isNullOrEmptyString($address_lang)) {
            return $address_lang;
        } else {
            return $result[0]['address'];
        }
    }

    /**
     * This function is language specific.So if currently selected language is Thai,It will return thai names of zones
     * @param $addressId
     * @param bool $languageIndependent
     * @return array|null
     */
    function getAddress($addressId, $languageIndependent = false)
    {

        if (intval($addressId) === 0)
            return null;

        //if $languageIndependent , then select en by default and then append country lang to it
        $language = $this->language;


        $secondLang = $this->countryCode;

        $query = WFactory::getServices()->getSqlService()->getQuery();
        $query->select('*')
            ->from("#__portal_property_addresses")
            ->where("id=$addressId");


        $result = WFactory::getServices()->getSqlService()->select($query);
        $address = $result[0];


        $postalCodeName = $languageIndependent ? " CONCAT(#__geography_postal_codes.name_{$language},' ',#__geography_postal_codes.name_{$secondLang})" : "#__geography_postal_codes.name_{$language}";
        $townName = $languageIndependent ? " CONCAT(#__geography_towns.name_{$language},' ',#__geography_towns.name_{$secondLang})" : "#__geography_towns.name_{$language}";
        $regionName = $languageIndependent ? " CONCAT(#__geography_regions.name_{$language},' ',#__geography_regions.name_{$secondLang})" : "#__geography_regions.name_{$language}";

        $query = "SELECT #__geography_postal_codes.id AS postal_code_id,
                         $postalCodeName AS postal_code,
                         #__geography_towns.id AS city_town_id,
                         $townName AS city_town_name,
                         #__geography_regions.id AS region_id,
                         $regionName AS region_name,
                         #__geography_countries.id as country_id,
                         #__geography_countries.country_code,
                         #__geography_countries.common_name as country_name
                  FROM #__geography_postal_codes #__geography_postal_codes
                       CROSS JOIN #__geography_towns #__geography_towns
                       CROSS JOIN #__geography_regions #__geography_regions
                       INNER JOIN #__geography_countries #__geography_countries
                          ON (#__geography_regions.parent_id = #__geography_countries.id)
                 WHERE     (#__geography_postal_codes.id = {$address["postal_code_id"]})
                       AND (#__geography_towns.id = {$address["town_id"]})
                       AND (#__geography_regions.id = {$address["region_id"]})";


        $result = WFactory::getServices()->getSqlService()->select($query);

        if (empty($result)) {

            WFactory::getLogger()->info("failed query --> $query");

            WFactory::getLogger()->warn("Address does not match with #__geography_* table!country incorrect?");

            if (__NEWCOUNTRYGEODATAIMPLMENETEDBYSAGAYET == true) {
                WFactory::throwPortalException("Address does not match with #__geography_* table!country incorrect?");
                return null;
            } else {
                //now try to retrive them manually..one by one..ugh

                $regionQuery = "SELECT #__geography_countries.id AS country_id,
                                   #__geography_countries.country_code,
                                   $regionName AS region_name,
                                   #__geography_regions.id AS region_id
                              FROM    #__geography_regions #__geography_regions
                                   INNER JOIN
                                      #__geography_countries #__geography_countries
                                   ON (#__geography_regions.parent_id = #__geography_countries.id)
                             WHERE (#__geography_regions.id = {$address["region_id"]})";

                $region = WFactory::getSqlService()->select($regionQuery);

                if (__COUNTRY == 'TH') {

                    $postalCodeQuery = "SELECT #__geography_postal_codes.id AS postal_code_id,
                                           $postalCodeName AS postal_code
                                      FROM #__geography_postal_codes #__geography_postal_codes
                                     WHERE (#__geography_postal_codes.name_en like '{$address["postal_code_id"]}%')";

                    $postalCode = WFactory::getSqlService()->select($postalCodeQuery);

//                    $pCode = $postalCode[0]['postal_code'];
//                    $pCode = explode('-', $pCode);
//                    $pCodeName = trim($pCode[1]);
//
//                    //do not show number..just show name!!
//                    $postalCode[0]['postal_code'] = $pCodeName;

                } else {
                    $postalCodeQuery = "SELECT #__geography_postal_codes.id AS postal_code_id,
                                           $postalCodeName AS postal_code
                                      FROM #__geography_postal_codes #__geography_postal_codes
                                     WHERE (#__geography_postal_codes.id = {$address["postal_code_id"]})";

                    $postalCode = WFactory::getSqlService()->select($postalCodeQuery);

                }


                $townQuery = "SELECT #__geography_towns.id AS city_town_id,
                                      $townName AS city_town_name
                                  FROM #__geography_towns #__geography_towns
                                 WHERE (#__geography_towns.id = {$address["town_id"]})";

                $town = WFactory::getSqlService()->select($townQuery);
                $result = array();
                if (!empty($region))
                    $result = array_merge($result, $region[0]);
                if (!empty($town))
                    $result = array_merge($result, $town[0]);
                if (!empty($postalCode))
                    $result = array_merge($result, $postalCode[0]);

                //
//                // $result = array_merge($region, $postalCode[0], $town[0]);
//
//                $fullResult = array();
//                foreach ($result as $r)
//                    $fullResult = array_merge($fullResult, $r);
//                $result = array($fullResult);

            }
        } else
            $result = $result[0];


        $address = array_merge($address, $result);

        //$postalCode =  //explode('-', $address["postal_code"]);
        $fullCode = $address['postal_code'];
        $address["postal_code"] = trim(substr($fullCode, 0, strpos($fullCode, '-')));
        $address["postal_code_name"] = trim(str_replace(range(0, 9), '', $fullCode));
        $address["postal_code_name"] = trim(str_replace('-', ' ', $address['postal_code_name']));

        $propertyAddress = $address["address"];
        if (preg_match('{(\d+)}', $propertyAddress) && __COUNTRY == "IS") /* address contains number */ {

            $arrayOfAddress = explode(' ', $propertyAddress);
            foreach ($arrayOfAddress as $anAddress) {
                if (preg_match('{(\d+)}', $anAddress)) {
                    $address["house_number"] = $address["house_number"] . ' ' . $anAddress;
                } else
                    $address["street_name"] = $address["street_name"] . ' ' . $anAddress;
            }
        } else {
            $address["street_name"] = $address["address"];
        }

        $address["street_name"] = trim($address["street_name"]);
        $address["house_number"] = trim($address["house_number"]);

        //Hack shit up...because during demo ALL kinds of silly address are sent to portal !!!

        if (!array_key_exists('city_town_id', $address)) {
            $address['city_town_id'] = $address['town_id'];
        }
        if (!array_key_exists('zip_code_id', $address)) {
            $address['zip_code_id'] = $address['postal_code_id'];
        }


        if (!array_key_exists('town_id', $address)) {
            $address['town_id'] = $address['city_town_id'];
        }
        if (!array_key_exists('postal_code_id', $address)) {
            $address['postal_code_id'] = $address['zip_code_id'];
        }

        if (__COUNTRY == "TH") {
            $address['property_region_town_zip_formatted'] = $address['city_town_name'] . ", " . $address['region_name'];
        } else if (__COUNTRY == "IS") {
            $address['property_region_town_zip_formatted'] = $address['zip_code'] . " " . $address['zip_code_name'];
        } else if (__COUNTRY == "PH") {
            $address['property_region_town_zip_formatted'] = $address['region_name'] . ", " . $address['city_town_name'];
        }


        return $address;
    }

    function getLatLangOfGeoLocationByName($locationSearchModel)
    {
        $type = $locationSearchModel->type;
        $id = $locationSearchModel->id;

        /**
         * @var $addressTable PortalGeographyRegionsSql
         */
        if ($type == 'regions')
            $addressTable = WFactory::getSqlService()->getDbClass(__PORTAL_GEOGRAPHY_REGIONS_SQL);
        if ($type == 'towns')
            $addressTable = WFactory::getSqlService()->getDbClass(__PORTAL_GEOGRAPHY_TOWNS_SQL);
        if ($type == 'postal_codes')
            $addressTable = WFactory::getSqlService()->getDbClass(__PORTAL_GEOGRAPHY_POSTAL_CODES_SQL);

        $addressTable->__id = $id;
        $addressTable = $addressTable->loadDataFromDatabase();

        $latLong = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_GMAP)->searchLocationByName($addressTable->__name_en);

        return $latLong;


    }

    /**
     *
     * This i use to get random address to fill in mock websending data
     * @return Array|bool
     */

    function getRandomAddress()
    {
        $tree = $this->postalCodeTree();
        $x = count($tree) - 1;
        $regionIndex = rand(0, $x);
        $regionId = $tree[$regionIndex]["id"];

        $y = count($tree[$regionIndex]["towns"]) - 1;
        $townIndex = rand(0, $y);
        $townId = $tree[$regionIndex]["towns"][$townIndex]["id"];

        $z = count($tree[$regionIndex]["towns"][$townIndex]["postal_codes"]) - 1;
        $postalIndex = rand(0, $z);
        $postalId = $tree[$regionIndex]["towns"][$townIndex]["postal_codes"][$postalIndex]["id"];


        $tree = array("region" => $regionId,
            "town" => $townId,
            "postal" => $postalId);


        $geoQuery = "SELECT #__geography_postal_codes.id AS __zip_code_id,
                           #__geography_postal_codes.name AS __zip_code_name,
                           #__geography_towns.id AS __city_town_id,
                           #__geography_towns.name AS __city_town_name,
                           #__geography_regions.id AS __region_id,
                           #__geography_regions.name AS __region_name
                      FROM    (   #__geography_towns #__geography_towns
                               INNER JOIN
                                  #__geography_regions #__geography_regions
                               ON (#__geography_towns.parent_id =
                                      #__geography_regions.id))
                           INNER JOIN
                              #__geography_postal_codes #__geography_postal_codes
                           ON (#__geography_postal_codes.parent_id =
                                  #__geography_towns.id)
                     WHERE (#__geography_postal_codes.id = {$tree["postal"]})";
        $result = WFactory::getServices()->getSqlService()->select($geoQuery);
        $result = $result[0];

        if ($result["__city_town_id"] != $tree["town"]) return false;
        if ($result["__region_id"] != $tree["region"]) return false;

        return $tree;

    }

    /**
     * @internal param int $country_id default Iceland
     * @return Array
     */
    function regions()
    {


        $country_id = $this->countryId;


        // country -> region -> town -> postal_code
        $query = WFactory::getServices()->getSqlService()->getQuery();
        $query->select('id,parent_id')
            ->select('name_' . $this->language . ' as name')
            //->from('#__geography_regions_is')
            ->from('#__geography_regions')
            ->where('parent_id = ' . $country_id);


        if (__COUNTRY == "IS") {
            $query->order('id');
        } else
            $query->order('name');


        $regions = WFactory::getServices()->getSqlService()->select($query);

        return $regions;
    }

    function region($region_id)
    {


        //  $country_id = $this->countryId;


        // country -> region -> town -> postal_code
        $query = WFactory::getServices()->getSqlService()->getQuery();
        $query->select('id,parent_id')
            ->select('name_' . $this->language . ' as name')
            //->from('#__geography_regions_is')
            ->from('#__geography_regions')
            ->where('id = ' . $region_id);


        if (__COUNTRY == "IS") {
            $query->order('id');
        } else
            $query->order('name');


        $regions = WFactory::getServices()->getSqlService()->select($query);

        return $regions[0];
    }

    function town($town_id)
    {
        // country -> region -> town -> postal_code
        $query = WFactory::getServices()->getSqlService()->getQuery();
        $query->select('id,parent_id')
            ->select('name_' . $this->language . ' as name')
            //->from('#__geography_towns_is')
            ->from('#__geography_towns')
            ->where('id = ' . $town_id);


        if (__COUNTRY == "IS") {
            $query->order('id');
        } else
            $query->order('name');

        $towns = WFactory::getServices()->getSqlService()->select($query);

        return $towns[0];
    }


    function towns($region_id)
    {
        // country -> region -> town -> postal_code
        $query = WFactory::getServices()->getSqlService()->getQuery();
        $query->select('id,parent_id')
            ->select('name_' . $this->language . ' as name')
            //->from('#__geography_towns_is')
            ->from('#__geography_towns')
            ->where('parent_id = ' . $region_id);


        if (__COUNTRY == "IS") {
            $query->order('id');
        } else
            $query->order('name');

        $towns = WFactory::getServices()->getSqlService()->select($query);

        return $towns;
    }

    function postal_codes($town_id)
    {

        // country -> region -> town -> postal_code
        $query = WFactory::getServices()->getSqlService()->getQuery();
        $query->select('id,parent_id')
            ->select('name_' . $this->language . ' as name')
            //->from('#__geography_postal_codes_is')
            ->from('#__geography_postal_codes')
            ->where('parent_id = ' . $town_id);


        if (__COUNTRY == "IS") {
            $query->order('id');
        } else
            $query->order('name');

        $codes = WFactory::getServices()->getSqlService()->select($query);

        return $codes;
    }

    function postal_code($postal_code_id)
    {

        // country -> region -> town -> postal_code
        $query = WFactory::getServices()->getSqlService()->getQuery();
        $query->select('id,parent_id')
            ->select('name_' . $this->language . ' as name')
            //->from('#__geography_postal_codes_is')
            ->from('#__geography_postal_codes')
            ->where('id = ' . $postal_code_id);


        if (__COUNTRY == "IS") {
            $query->order('id');
        } else
            $query->order('name');

        $codes = WFactory::getServices()->getSqlService()->select($query);

        return $codes[0];
    }

    /**
     * Gets a structured tree for postal codes.
     * Utilizes caching.
     * @return Array
     */
    function postalCodeTree()
    {
        /** @var JCacheController $cache */
        $cache = JFactory::getCache('addressService', '');

        $cache_id = 'postal_code_tree_' . $this->language;

        if (!$regions = $cache->get($cache_id)) {
            $regions = $this->regions();

            foreach ($regions as &$region) {
                $region['towns'] = $this->towns($region['id']);

                foreach ($region['towns'] as &$town) {
                    $town['postal_codes'] = $this->postal_codes($town['id']);
                }
            }

            $result = $cache->store($regions, $cache_id);
            if (!$result && !defined('KHAN_HOME'))
                WFactory::getLogger()->warn("Failed to save cache for $cache_id");
        }

        return $regions;
    }

    function getModes($allowedModes = array())
    {
        $query = WFactory::getServices()->getSqlService()->getQuery();
        $query->select('id')
            ->select('description')
            ->from('#__portal_property_modes')
            ->where('id > 1');

        if (!empty($allowedModes)) {
            $query->where('id in (' . implode(',', $allowedModes) . ')');
        }

        $modes = WFactory::getServices()->getSqlService()->select($query);

        foreach ($modes as &$m) {
            $m['description'] = JText::_(strtoupper($m['description']));
        }

        return $modes;
    }


    function mockGeoData()
    {
        $geoData = $this->postalCodeTree();

        //now mock it / break it
        $region = $geoData[0];
        $region['towns'] = $geoData[0]['towns'][0];


        $data = array();
        $data[] = $region;

        return $data;
    }


    function getPropCategories($mode_id)
    {
        $query = WFactory::getServices()->getSqlService()->getQuery();
        $query->select('id')
            ->select('description')
            ->select('mode_id')
            ->from('#__portal_property_categories')
            ->where('mode_id=' . $mode_id)
            ->order('id');


        $categories = WFactory::getServices()->getSqlService()->select($query);
        WFactory::getLogger()->debug("$mode_id -> ".json_encode($categories));
        return $categories;
    }

    function propCategoriesTree()
    {

        /** @var JCacheController $cache */
        $cache = JFactory::getCache('addressService', '');

        $cache_id = 'property_categories_' . $this->language;

        if (!$modes = $cache->get($cache_id)) {
            // mode -> category
            $allowedModes = array(2, 3);//all,
            $config = WFactory::getConfig()->getWebportalConfigurationArray();
            if (array_key_exists('allowedModes', $config)) {
                $allowedModes = $config['allowedModes'];
            }

            $modes = $this->getModes($allowedModes);

            foreach ($modes as &$mode) {

//                if(count($allowedModes)==1)//if ONLY one mode, no need to add description text
//                {
//                    $mode['description']='';
//                }

                $categories = $this->getPropCategories($mode['id']);

                foreach ($categories as &$c) {

                    if (defined('__TRANSLATE_CATEGORY_NAME') && __TRANSLATE_CATEGORY_NAME === true)
                        $c['description'] = JText::_(strtoupper($c['description']));
                    else
                        $c['description'] = $c['description'];
                    //$c['description'] = JText::_(strtoupper($c['description']));
                }

                if ($categories) {
                    $mode['categories'] = $categories;
                }
            }

            $result = $cache->store($modes, $cache_id);
            if (!$result && !defined('KHAN_HOME'))
                WFactory::getLogger()->warn("Failed to save cache for $cache_id");
        }

        return $modes;
    }
}
