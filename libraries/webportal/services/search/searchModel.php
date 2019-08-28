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
define("ORDER_BY_SMALLEST_FIRST", "total_area asc");
define("ORDER_BY_LARGEST_FIRST", "total_area desc");
define("ORDER_BY_LEAST_EXPENSIVE_FIRST", "current_listing_price asc");
define("ORDER_BY_MOST_EXPENSIVE_FIRST", "current_listing_price desc");
define("ORDER_BY_NEWEST_FIRST", "created_date desc");
define("ORDER_BY_OLDEST_FIRST", "created_date asc");
define("ORDER_BY_SMALLEST_ZIP_FIRST", "zip_code asc");
define("ORDER_BY_NEAREST_FIRST", "distance asc");
define("ORDER_BY_OPENHOUSE_FIRST", "open_house_start asc");
define("ORDER_BY_SALES_COMMISSION", "sale_percent desc");
define("ORDER_BY_MOST_COMMISSION_FIRST", "sale_percent desc");
define("ORDER_BY_LESS_COMMISSION_FIRST", "sale_percent asc");

define("ORDER_BY_RANDOM", "RAND()");


class SearchModel extends ModelBase
{

    var $defaultOrderByArray = array(
        ORDER_BY_SMALLEST_FIRST, ORDER_BY_LARGEST_FIRST, ORDER_BY_LEAST_EXPENSIVE_FIRST, ORDER_BY_MOST_EXPENSIVE_FIRST, ORDER_BY_NEWEST_FIRST, ORDER_BY_SMALLEST_ZIP_FIRST, ORDER_BY_MOST_COMMISSION_FIRST, ORDER_BY_LESS_COMMISSION_FIRST
    );

    var $returnType = RETURN_TYPE_LIST;

    //every search result is cached, for quick retrival . you can paginate using this key
    //this key is a hash value of the model
    var $search_key;
    var $timespent;
    //for pagination, where should i send you result from
    // index based,so starts from 0
    var $limit_start;
    //for pagination, until which should i send you result
    // index based,so starts from 0
    var $limit_length;

    var $source="WEB_APP";

    /**
     * NOTE: if this is being set, i shall NOT search , but instead just generate the hash,set it in cookie named "PropertySearchKey" and redirect the page to this
     * URL. it is the responsibility of the redirected page to retrive the cookie and request search result and populate itself
     * @var string
     */
    var $redirectRef;

    function getCookieVariableName()
    {
        return "PropertySearchKey";
    }

    /**
     * if Array, will return all property
     * @var String | Array
     *
     */
    var $property_id;
    /**
     * full text search
     * @var $text String
     */
    var $text;
    /**
     * street search
     * @var $street String
     */
    var $street;

    /**
     * search by user id
     * @var $user_id Integer
     */
    var $user_id;

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
     * @var String|Array
     */
    var $city_town_id;

    /**
     * @var String|Array
     */
    var $zip_code_id;
    /**
     * @var String|Array
     */
    var $zip_code;
    /**
     * example: [from,to]
     *
     * [100,200] = price between 100 to 200 INCLUSIVE
     * [100,0  ] = price more than 100 [ 0 means ignore]
     * [0,200]   = price less than 200 [ 0 means ignore]
     * [100] = price EXACTLY 100
     * @var array
     */
    var $current_listing_price;
    /**
     * example: [from,to]
     *
     * [2,4] = floor between 2 to 4 INCLUSIVE
     * [2,0  ] = floor more than 2 [ 0 means ignore]
     * [0,4]   = floor less than 4 [ 0 means ignore]
     * [2] = floor EXACTLY 2
     * @var array
     */
    var $floor_level;
    /**
     * Mortgage Price
     * Check $current_listing_price or $floor_level for explanation
     * @var array
     */
    var $mortgage;
    /**
     * Check $current_listing_price or $floor_level for explanation
     * @var array
     */
    var $total_area;
    /**
     * Check $current_listing_price or $floor_level for explanation
     * @var array
     */
    var $living_area;
    /**
     * Check $current_listing_price or $floor_level for explanation
     * @var array
     */
    var $land_area;
    /**
     * Check $current_listing_price or $floor_level for explanation
     * @var array
     */
    var $cubic_volume;
    /**
     * Check $current_listing_price or $floor_level for explanation
     * @var array
     */
    var $total_number_of_rooms;
    /**
     * Check $current_listing_price or $floor_level for explanation
     * @var array
     */
    var $number_of_bathrooms;
    /**
     * Check $current_listing_price or $floor_level for explanation
     * @var array
     */
    var $number_of_toilet_rooms;
    /**
     * Check $current_listing_price or $floor_level for explanation
     * @var array
     */
    var $number_of_bedrooms;
    /**
     * Check $current_listing_price or $floor_level for explanation
     * @var array
     */
    var $number_of_livingrooms;
    /**
     * Check $current_listing_price or $floor_level for explanation
     * @var array
     */
    var $number_of_floors;
    /**
     * Check $current_listing_price or $floor_level for explanation
     * @var array
     */
    var $year_build;
    /**
     * Check $current_listing_price or $floor_level for explanation
     * @var array
     */

    /**
     * Check $current_listing_price or $floor_level for explanation
     * Allowed date format: 2014-05-20 13:57:46 ( Y-m-d H:i:s )
     *
     * @var array
     */
    var $open_house_start;
    /**
     * Check $current_listing_price or $floor_level for explanation
     * Allowed date format: 2014-05-20 13:57:46 ( Y-m-d H:i:s )
     *
     * @var array
     */
    var $open_house_end;
    /**
     * Check $current_listing_price or $floor_level for explanation
     * Allowed date format: 2014-05-20 13:57:46 ( Y-m-d H:i:s )
     * @var array
     */
    var $last_update;
    /**
     * Check $current_listing_price or $floor_level for explanation
     * Allowed date format: 2014-05-20 13:57:46 ( Y-m-d H:i:s )
     * @var array
     */
    var $created_date;
    /**
     * Check $current_listing_price or $floor_level for explanation
     * Allowed date format: 2014-05-20 13:57:46 ( Y-m-d H:i:s )
     * @var array
     */
    var $last_price_update_date;
    /**
     * Check $current_listing_price or $floor_level for explanation
     * Allowed date format: 2014-05-20 13:57:46 ( Y-m-d H:i:s )
     * @var array
     */
    var $last_price_reduction_date;

    /**
     * @var Int
     */
    var $garage;
    /**
     * Check $current_listing_price or $floor_level for explanation
     * @var array
     */
    var $garage_area;
    /**
     * @var Int
     */
    var $elevator;
    /**
     * @var Int
     */
    var $extra_flat;
    /**
     * @var Int
     */
    var $swapping;
    /**
     * @var Int
     */
    var $exclusive_entrance;
    /**
     * @var Int
     */
    var $is_deleted;
    /**
     * Check $current_listing_price or $floor_level for explanation
     * @var array
     */

    /**
     * @var text
     */
    var $project_name;

    /**
     * @var varchar
     */
    var $unit_code;

    /**
     * @var varchar
     */
    var $unit_type;

    /**
     * @var double
     */
    var $latitude;
    /**
     * @var double
     */
    var $longitude;
    /**
     * @var int | distance in KILO-meter.  if radius,lat and lang are ALL provided, it will search for properties within the radius with lat lang being the center
     */
    var $radius;

    /**
     * This one has NO effect in searching..used ONLY to restore the search panel
     * It is the search panels responsibility to translate a line to lat lang and send me
     * @var string
     */
    var $transport_line;
    /**
     * This one has NO effect in searching..used ONLY to restore the search panel
     * It is the search panels responsibility to translate a line to lat lang and send me
     * @var string
     */
    var $transport_station;

    /*
     * search property within bounds
east  100.59999782861325
north 13.750406801218707
south 13.70538223794761
west  100.44824917138669
     * */
    var $bounds=array();


    /**
     * list of predefined ordering:
     *      ORDER_BY_SMALLEST_FIRST
     *      ORDER_BY_LARGEST_FIRST
     *      ORDER_BY_LEAST_EXPENSIVE_FIRST
     *      ORDER_BY_MOST_EXPENSIVE_FIRST
     *      ORDER_BY_NEWEST_FIRST
     *      ORDER_BY_SMALLEST_ZIP_FIRST
     *
     *  do it like this:
     * [ORDER_BY_SMALLEST_FIRST,ORDER_BY_LEAST_EXPENSIVE_FIRST] will order by the smallest property which are least expensive
     * for undefined ones, you can use it like:
     *
     * ["id asc","office_Id asc" ] etc...
     * @var Array
     */
    var $order = array();

    var $preferred_currency = null;

    var $new_today;
    var $new_this_week;
    var $loan80;
    /**
     * List of IDs not to be included
     * @var Array
     */
    var $exclusion_list;

    var $is_next_previous = false;
    var $next_previous_center_property_id = 0;
    var $next_previous_max_length = 4;//4 before , 4 after

    /**
     *
     * //----------------------------------------------//
     * features: [],//used ONLY in add your property,  //
     * price: '',   //used ONLY in add your property,  //
     * size: '',    //used ONLY in add your property,  //
     * floor: '',   //used ONLY in add your property,  // <<-----------------
     * unit: '',    //used ONLY in add your property,  //
     * noof: '',    //used ONLY in add your property,  //
     * movein: ''   //used ONLY in add your property,  //
     * //----------------------------------------------//
     */

    var $features = array();
    var $price = null;
    var $size = null;
    var $floor = null;
    var $unit = null;
    var $noof = null;
    var $movein = null;
    var $address = null;

    var $return_properties_with_no_address = true;

    var $is_featured;
}
