<?php
/**
 * Created by PhpStorm.
 * User: khan
 * Date: 6/26/14
 * Time: 1:37 PM
 */

require_once JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . 'modelbase.php';

class PropertyAddressModel extends ModelBase
{


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

    var $id;   // KEY ATTR. WITH AUTOINCREMENT

    var $type_id;   // (normal Attribute)
    var $region_id;   // (normal Attribute)
    var $town_id;   // (normal Attribute)
    var $postal_code_id;   // (normal Attribute)
    var $street;   // (normal Attribute)
    var $latitude;   // (normal Attribute)
    var $longitude;   // (normal Attribute)

    function getJRouteFormattedAddress()
    {
       
        if (__COUNTRY === 'TH') {


            $address = array(
                //$this->house_number,
                //$this->street_name,
                $this->address,
//                $this->zip_code_name,
//                $this->zip_code,
                $this->city_town_name,
//                $this->subdistrict_name,
                $this->district_name,
                $this->sate_province_name,
                $this->region_name
            );


        } else if (__COUNTRY === 'IS') {


            $address = array(
                $this->address,
                $this->zip_code,
                $this->zip_code_name,
                //$this->region_name
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