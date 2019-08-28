<?php

/**
 * Created by PhpStorm.
 * User: khan
 * Date: 5/10/14
 * Time: 3:21 PM
 */

require_once JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . 'modelbase.php';

define("RETURN_TYPE_LIST", "RETURN_TYPE_LIST");
define("RETURN_TYPE_DETAIL", "RETURN_TYPE_DETAIL");
define("RETURN_TYPE_MAP", "RETURN_TYPE_MAP");

class SearchModelUnused extends ModelBase
{


    var $returnType = RETURN_TYPE_LIST;

    //every search result is cached, for quick retrival . you can paginate using this key
    var $search_key;
    //for pagination, where should i send you result from
    var $limit_start;
    //for pagination, until which should i send you result
    var $limit_end;

    /**
     * if Array, will return all property
     * @var String | Array
     *
     */
    var $property_id;
    /**
     * full text search
     * @var $test String
     */
    var $text;
    /**
     * @var String | Array
     */
    var $reg_id;
    /**
     * @var String | Array
     */
    var $sale_id;
    /**
     * @var String | Array
     */
    var $office_id;
    /**
     * @var String | Array
     */
    var $category_id;
    /**
     * Possible values
     *  --- BUY = 2
     *  --- RENT = 3
     *  --- ALL  = 1 [ has the same effect of not setting this condition ]
     * @var int
     */
    var $type_id;
    /**
     * Possible values
     *  --- RESIDENTIAL
     *  --- COMMERCIAL
     * @var String
     */
    var $residential_commercial;
    /**
     * @var String | Array
     */
    var $region_id;
    /**
     * NOT IMPLEMENTED
     */
    var $region_name;
    /**
     * NOT IMPLEMENTED
     */
    var $state_province_id;
    /**
     * NOT IMPLEMENTED
     */
    var $sate_province_name;
    /**
     * NOT IMPLEMENTED
     */
    var $district_id;
    /**
     * NOT IMPLEMENTED
     */
    var $district_name;
    /**
     * NOT IMPLEMENTED
     */
    var $subdistrict_id;
    /**
     * NOT IMPLEMENTED
     */
    var $subdistrict_name;
    /**
     * @var String|Array
     */
    var $city_town_id;
    /**
     * NOT IMPLEMENTED
     */
    var $city_town_name;
    /**
     * @var String|Array
     */
    var $zip_code_id;
    /**
     * @var String|Array
     */
    var $zip_code;
    /**
     * NOT IMPLEMENTED
     */
    var $zip_code_name;
    /**
     * NOT IMPLEMENTED
     */
    var $street_name;
    /**
     * NOT IMPLEMENTED
     */
    var $house_number;
    /**
     * NOT IMPLEMENTED
     */
    var $address;
    var $property_type_id;
    var $property_type_name;
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





}