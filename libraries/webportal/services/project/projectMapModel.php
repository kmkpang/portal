<?php
/**
 * Created by PhpStorm.
 * User: khan
 * Date: 5/19/14
 * Time: 1:54 PM
 */


require_once JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . 'modelbase.php';
require_once JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . 'property' . DS . 'propertyDetailsModel.php';

class ProjectMapModel extends PropertyDetailsModel
{

    var $property_id;
    var $unique_id;
    var $reg_id;
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
    var $is_deleted;
    var $last_update;
    var $created_date;
    var $last_price_update_date;
    var $last_price_reduction_date;
    var $google_viewcount;
    var $picture_count;
    var $list_page_thumb_path;
    var $url_to_direct_page;
    var $latitude;
    var $longitude;


    public function bindToDb($array)
    {


        parent::bindToDb($array, false);

        $this->description_text = mb_substr(strip_tags($this->description_text), 0, 195) . "...";

        $this->property_id = $array["id"];


        $url = JUri::base() . $this->getJRouteFormattedAddress() . "-{$this->property_id}";
        $this->url_to_direct_page = $url;
    }

    function getJRouteFormattedAddress()
    {
        if (__CUSTOMER_ID === 'REMAX_THAILAND') { //remax specific http://redmine.softverk.is/issues/1498


            $address = array(
                $this->house_number,
                $this->street_name,
//                $this->zip_code_name,
//                $this->zip_code,
                $this->city_town_name,
//                $this->subdistrict_name,
                $this->district_name,
                $this->sate_province_name,
                $this->region_name
            );


        } else {
            $address = array(
                $this->house_number,
                $this->street_name,
                $this->zip_code_name,
                $this->zip_code,
                $this->city_town_name,
                $this->subdistrict_name,
                $this->district_name,
                $this->sate_province_name,
                $this->region_name
            );
        }

        $address = array_filter($address);

        $address = implode("-", $address);

        return $address;

    }


}