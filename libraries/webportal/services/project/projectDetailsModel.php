<?php
/**
 * Created by PhpStorm.
 * User: khan
 * Date: 5/19/14
 * Time: 1:54 PM
 */


require_once JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . 'modelbase.php';


class ProjectDetailsModel extends ModelBase
{

    var $property_id;
    var $unique_id;
    var $reg_id;
    var $sales_agent_full_name;
    var $sales_agent_office_phone;
    var $sales_agent_mobile_phone;
    var $sales_agent_email;
    var $sales_agent_image;
    var $office_id;
    var $sale_id;
    var $office_name;
    var $office_phone;
    var $office_email;
    var $office_logo_path;
    var $category_id;
    var $category_name;
    var $buy_rent;
    var $residential_commercial;
    var $country_code;
    var $country_name;
    var $region_name;
    var $sate_province_name;
    var $district_name;
    var $subdistrict_name;
    var $city_town_name;
    var $zip_code;
    var $zip_code_name;
    var $street_name;
    var $house_number;
    var $address;
    var $current_listing_price;
    var $current_listing_price_formatted;
    var $property_status;
    var $floor_level;
    var $total_area;
    var $living_area;
    var $cubic_volume;
    var $total_number_of_rooms;
    var $number_of_bathrooms;
    var $number_of_toilet_rooms;
    var $number_of_bedrooms;
    var $number_of_livingrooms;
    var $number_of_floors;
    var $year_build;
    var $possession_date;
    var $availability_date;
    var $original_listing_date;
    var $expiry_date;
    var $virtual_tour;
    var $description_text;
    var $open_house_start;
    var $open_house_end;
    var $open_house = false;
    var $open_house_now = false;
    var $mortgage;
    var $entrance;
    var $garage;
    var $garage_area;
    var $elevator;
    var $extra_flat;
    var $swapping;
    var $exclusive_entrance;
    var $comm_number_of_offices;
    var $comm_number_of_floors;
    var $comm_total_number_of_rooms;
    var $comm_building_frontage;
    var $comm_clearance_height;
    var $comm_elevators;
    var $comm_lease_area;
    var $comm_mannufacturing_space;
    var $comm_office_space;
    var $comm_parking;
    var $comm_possession_date;
    var $comm_price_per_area;
    var $comm_retail_space;
    var $comm_total_area;
    var $comm_useable_area;
    var $comm_warehouse_space;
    var $comm_year_built;
    var $comm_zoning;
    var $sent_to_web;
    var $last_update;
    var $created_date;
    var $last_price_update_date;
    var $last_price_reduction_date;
    var $notes;
    var $google_viewcount;
    var $picture_count;
    var $latitude;
    var $longitude;
    var $title_en;
    var $title_th;
    var $title;
    var $property_appraisal;
    var $fire_appraisal;
    /**
     * @deprecated , use $imagesV2
     * @var array
     */
    var $images = array();
    /**
     * imagesV2 contains the serverurl,alt and description tag
     * @var array
     */
    var $imagesV2 = array();

    var $features = array();
    var $distance;
    var $metaKeyword;
    var $is_new = false;
    var $is_recent = false;
    var $url_to_direct_page = "";
    var $initial_picture_path = "";
    var $list_page_thumb_path = "";
    var $viewcount = 0;

    var $property_phone_link;
    var $property_registration_link;
    var $property_blueprint_link;
    var $property_region_town_zip_formatted = "";


    var $is_deleted = '';

    //var $search_key = "";
    var $videos = array();

    function bindToDb($array, $bindDescription = true)
    {
        parent::bindToDb($array);
        $this->property_id = $array["id"];
        $lang = WFactory::getHelper()->getCurrentlySelectedLanguage();
        //WFactory::getLogger()->debug("Currently selected language : ------------ >>>  $lang");

        $this->title = $this->{"title_" . $lang};
        if (WFactory::getHelper()->isNullOrEmptyString($this->title))
            $this->title = $this->title_en;

        if (!WFactory::getHelper()->isNullOrEmptyString($this->open_house_start) && //do NOT process unless they are NOT empty
            !WFactory::getHelper()->isNullOrEmptyString($this->open_house_end)
        ) {

            $oStart = new DateTime($this->open_house_start);
            $oEnd = new DateTime($this->open_house_end);
            $now = new DateTime();//going to assume server time zone is correct ! :)

            $this->open_house_start = WFactory::getHelper()->getFormattedDate($this->open_house_start, true);
            $this->open_house_end = WFactory::getHelper()->getFormattedDate($this->open_house_end, true);
            if ($now <= $oEnd) {
                $this->open_house = true;
                if ($now >= $oStart && $now <= $oEnd) {
                    $this->open_house_now = true;
                }

            }
        }


        if (!WFactory::getHelper()->isNullOrEmptyString($this->region_name)) {
            $english = trim(WFactory::getHelper()->extractEnglish($this->region_name));
            $english = trim(implode(' ', array_unique(explode(' ', $english))));
            if ($lang == "en" || $lang == "zh" || $lang == "is") { // TODO: TEMPORARY SOLUTION UNTIL WE FIX DATABASE
                $this->region_name = $english;
            } else {
                $this->region_name = trim(str_replace($english, "", $this->region_name));
            }
        }
        if (!WFactory::getHelper()->isNullOrEmptyString($this->city_town_name)) {
            $english = trim(WFactory::getHelper()->extractEnglish($this->city_town_name));
            $english = trim(implode(' ', array_unique(explode(' ', $english))));
            if ($lang == "en" || $lang == "zh" || $lang == "is") {
                $this->city_town_name = $english;
            } else {
                $this->city_town_name = trim(str_replace($english, "", $this->city_town_name));
            }
        }
        if (!WFactory::getHelper()->isNullOrEmptyString($this->zip_code_name)) {
            $english = trim(WFactory::getHelper()->extractEnglish($this->zip_code_name));
            $english = trim(implode(' ', array_unique(explode(' ', $english))));
            if ($lang == "en" || $lang == "zh" || $lang == "is") {
                $this->zip_code_name = $english;
            } else {
                $this->zip_code_name = trim(str_replace($english, "", $this->zip_code_name));
            }
        }

        if (__COUNTRY == "TH") {
            $this->property_region_town_zip_formatted = $this->city_town_name . ", " . $this->region_name;
        } else if (__COUNTRY == "IS") {
            $this->property_region_town_zip_formatted = $this->zip_code . " " . $this->zip_code_name;
        } else if (__COUNTRY == "PH") {
            $this->property_region_town_zip_formatted = $this->region_name;
            if (!WFactory::getHelper()->isNullOrEmptyString($this->city_town_name)) {
                $this->property_region_town_zip_formatted .= ", " . $this->city_town_name;
            }

            if ($this->property_region_town_zip_formatted == ", ") {
                $this->property_region_town_zip_formatted = "";
            }
        }

        $currentlySelevtedLangDescription = "description_text_" . $lang;

        if ($bindDescription) {
            if (WFactory::getHelper()->isNullOrEmptyString($array[$currentlySelevtedLangDescription])) {
                WFactory::getLogger()->warn("Description text for property {$this->property_id} is empty! selecting other languages");
                $otherLangs = WFactory::getHelper()->getAllLang(false);
                foreach ($otherLangs as $lang => $tag) {

                    //$lang = explode('-', $lang['tag'])[0];

                    $currentlySelevtedLangDescription = "description_text_" . $lang;
                    if (!WFactory::getHelper()->isNullOrEmptyString($array[$currentlySelevtedLangDescription])) {

                        WFactory::getLogger()->warn("Selecting $lang , instead of " . $lang);
                        break;
                    }

                }

            }
        }

        $this->current_listing_price_formatted = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_CURRENCY)->convertCurrency($this->current_listing_price);

        $this->description_text = $array[$currentlySelevtedLangDescription];
        if (WFactory::getHelper()->isNullOrEmptyString($this->description_text)) {
            $this->description_text = $array['description_text_en'];
        }

        if (defined('__TRANSLATE_CATEGORY_NAME') && __TRANSLATE_CATEGORY_NAME === true)
            $this->category_name = JText::_(strtoupper($this->category_name));
        else
            $this->category_name = $this->category_name;

        //$this->category_name = "xxxx";
        $this->metaKeyword = "{$this->buy_rent},{$this->title},{$this->category_name},{$this->region_name},{$this->city_town_name},{$this->zip_code_name},{$this->office_name},{$this->sales_agent_full_name}";

//for a start, take the creation date


        $now = new DateTime();
        $listingStarted = new DateTime($this->created_date);
        $diff = date_diff($listingStarted, $now, true);

        $this->is_new = false;
        $this->is_recent = false;
        if ($diff->days <= __IS_NEW ) {
            $this->is_new = true;
        } else if ($diff->days <= __IS_RECENT) {
            $this->is_recent = true;
        }
        
        global $currentPropertyModel;
        $currentPropertyModel = $this;
        $directPath = JRoute::_("index.php?option=com_webportal&view=property&property-id={$this->property_id}");
        //remove /api/v1/whatever!!
        $directPath = WFactory::getHelper()->getCurrentlySelectedLanguage() . substr($directPath, strpos($directPath, '/property/'));

        $directPath = (str_replace('/property/', '/', $directPath));

        $this->url_to_direct_page = JUri::base() . $directPath;


        if (__COUNTRY == "IS")
            $this->total_area = round($this->total_area);
        
        if ($this->swapping == 0) {
            $this->swapping = false;
        } else if ($this->swapping == 1) {
            $this->swapping = true;
        }
    }

    function getShortDescription($width = 160)
    {
        $description_text = preg_replace("/<br\W*?\/>/", " ", $this->description_text);
        $string = strip_tags($description_text);
        $string = str_replace('&nbsp;', '', $string);
        $string = str_replace('&amp;', '', $string);
        $string = str_replace('&copy;', '', $string);

        $your_desired_width = $width;
        $string = mb_substr($string, 0, $your_desired_width + 1);

//        if (strlen($string) > $your_desired_width) {
//            $string = wordwrap($string, $your_desired_width);
////            $i = strpos($string, "\n");
////            if ($i) {
////                $string = mb_substr($string, 0, $i);
////            }
//        }
        return $string;

    }

    function getAddress()
    {

        $model = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_ADDRESS)->getPropertyAddressModel();
        $model->bindToDb($this);


        return $model;
    }


}

//class PropertyImage
//{
//
//    var $serverUrl;
//    var $description;
//    var $alt;
//
//}
//
//class PropertyVideo
//{
//
//    var $serverUrl;
//    var $description;
//    var $alt;
//
//}
