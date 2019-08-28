<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_webportal
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Webportal Controller
 *
 * @package     Joomla.Administrator
 * @subpackage  com_webportal
 * @since       0.0.9
 */
class WebportalControllerProperty extends JControllerForm
{
    /**
     * Implement to allowAdd or not
     *
     * Not used at this time (but you can look at how other components use it....)
     * Overwrites: JControllerForm::allowAdd
     *
     * @param array $data
     * @return bool
     */
    protected function allowAdd($data = array())
    {
        return parent::allowAdd($data);
    }

    /**
     * Implement to allow edit or not
     * Overwrites: JControllerForm::allowEdit
     *
     * @param array $data
     * @param string $key
     * @return bool
     */
    protected function allowEdit($data = array(), $key = 'id')
    {
        $id = isset($data[$key]) ? $data[$key] : 0;
        if (!empty($id)) {
            return JFactory::getUser()->authorise("core.edit", "com_webportal.message." . $id);
        }
    }

    /**
     * Autometically called by joomla framework
     */
    public function save()
    {
        $app = JFactory::getApplication();
        $input = $app->input;


        //save stuff

        //step 1 get data frm post

        $property_id = $input->getInt('property_id', 0);
        $unique_id = $input->getInt('unique_id', 0);
        $reg_id = $input->getInt('reg_id', 0);
        $sales_agent_full_name = $input->getString('sales_agent_full_name', null);
        $sales_agent_office_phone = $input->getString('sales_agent_office_phone', null);
        $sales_agent_mobile_phone = $input->getString('sales_agent_mobile_phone', null);
        $sales_agent_email = $input->getString('sales_agent_email', null);
        $sales_agent_image = $input->getString('sales_agent_image', null);
        $office_id = $input->getInt('office_id', 0);
        $sale_id = $input->getInt('sale_id', 0);
        $office_name = $input->getString('office_name', null);
        $office_phone = $input->getString('office_phone', null);
        $office_email = $input->getString('office_email', null);
        $office_logo_path = $input->getString('office_logo_path', null);
        $category_id = $input->getInt('category_id', 0);
        $category_name = $input->getString('category_name', null);
        $buy_rent = $input->getString('buy_rent', null);
        $residential_commercial = $input->getString('residential_commercial', null);
        $country_code = $input->getString('country_code', null);
        $country_name = $input->getString('country_name', null);
        $region_name = $input->getString('region_name', null);
        $sate_province_name = $input->getString('sate_province_name', null);
        $district_name = $input->getString('district_name', null);
        $subdistrict_name = $input->getString('subdistrict_name', null);
        $city_town_name = $input->getString('city_town_name', null);
        $zip_code = $input->getString('zip_code', null);
        $zip_code_name = $input->getString('zip_code_name', null);
        $street_name = $input->getString('street_name', null);
        $house_number = $input->getString('house_number', null);
        $address = $input->getString('address', null);
        $current_listing_price = $input->getString('current_listing_price', null);
        $current_listing_price_formatted = $input->getString('current_listing_price_formatted', null);
        $property_status = $input->getString('property_status', null);
        $floor_level = $input->getString('floor_level', null);
        $total_area = $input->getString('total_area', null);
        $living_area = $input->getString('living_area', null);
        $cubic_volume = $input->getString('cubic_volume', null);
        $total_number_of_rooms = $input->getString('total_number_of_rooms', null);
        $number_of_bathrooms = $input->getString('number_of_bathrooms', null);
        $number_of_toilet_rooms = $input->getString('number_of_toilet_rooms', null);
        $number_of_bedrooms = $input->getString('number_of_bedrooms', null);
        $number_of_livingrooms = $input->getString('number_of_livingrooms', null);
        $number_of_floors = $input->getString('number_of_floors', null);
        $year_build = $input->getString('year_build', null);
        $possession_date = $input->getString('possession_date', null);
        $availability_date = $input->getString('availability_date', null);
        $original_listing_date = $input->getString('original_listing_date', null);
        $expiry_date = $input->getString('expiry_date', null);
        $virtual_tour = $input->getString('virtual_tour', null);
        $description_text = $input->getString('description_text', null);
        $open_house_start = $input->getString('open_house_start', null);
        $open_house_end = $input->getString('open_house_end', null);
        $mortgage = $input->getString('mortgage', null);
        $entrance = $input->getString('entrance', null);
        $garage = $input->getString('garage', null);
        $garage_area = $input->getString('garage_area', null);
        $elevator = $input->getString('elevator', null);
        $extra_flat = $input->getString('extra_flat', null);
        $swapping = $input->getString('swapping', null);
        $exclusive_entrance = $input->getString('exclusive_entrance', null);
        $comm_number_of_offices = $input->getString('comm_number_of_offices', null);
        $comm_number_of_floors = $input->getString('comm_number_of_floors', null);
        $comm_total_number_of_rooms = $input->getString('comm_total_number_of_rooms', null);
        $comm_building_frontage = $input->getString('comm_building_frontage', null);
        $comm_clearance_height = $input->getString('comm_clearance_height', null);
        $comm_elevators = $input->getString('comm_elevators', null);
        $comm_lease_area = $input->getString('comm_lease_area', null);
        $comm_mannufacturing_space = $input->getString('comm_mannufacturing_space', null);
        $comm_office_space = $input->getString('comm_office_space', null);
        $comm_parking = $input->getString('comm_parking', null);
        $comm_possession_date = $input->getString('comm_possession_date', null);
        $comm_price_per_area = $input->getString('comm_price_per_area', null);
        $last_price_update_date = $input->getString('last_price_update_date', null);
        $last_price_reduction_date = $input->getString('last_price_reduction_date', null);
        $notes = $input->getString('notes', null);
        $google_viewcount = $input->getInt('google_viewcount', 0);
        $picture_count = $input->getInt('picture_count', 0);
        $latitude = $input->getString('latitude', null);
        $longitude = $input->getString('longitude', null);
        $title_en = $input->getString('title_en', null);
        $title_th = $input->getString('title_th', null);
        $title = $input->getString('title', null);
        $property_appraisal = $input->getString('property_appraisal', null);
        $fire_appraisal = $input->getString('fire_appraisal', null);

        $propertyId = $input->get('property_id',0);

        //first get the current office details

        $propertyService = WFactory::getServices()->getSqlService()->getSqlServiceClass(__PROPPERTY_PORTAL_PROPERTY);


        //step 2 : get current office
        /**
         * @$property OfficeModel
         */
        $property = $propertyService->getPropertyDetailsModel($propertyId,true);

        //step 3 : change office information

        $property->property_id = $property_id;
        $property->unique_id = $unique_id;
        $property->reg_id = $reg_id;
        $property->sales_agent_full_name = $sales_agent_full_name;
        $property->sales_agent_office_phone = $sales_agent_office_phone;
        $property->sales_agent_mobile_phone = $sales_agent_mobile_phone;
        $property->sales_agent_email = $sales_agent_email;
        $property->sales_agent_image = $sales_agent_image;
        $property->office_id = $office_id;
        $property->sale_id = $sale_id;
        $property->office_name = $office_name;
        $property->office_phone = $office_phone;
        $property->office_email = $office_email;
        $property->office_logo_path = $office_logo_path;
        $property->category_id = $category_id;
        $property->category_name = $category_name;
        $property->buy_rent = $buy_rent;
        $property->residential_commercial = $residential_commercial;
        $property->country_code = $country_code;
        $property->country_name = $country_name;
        $property->region_name = $region_name;
        $property->sate_province_name = $sate_province_name;
        $property->district_name = $district_name;
        $property->subdistrict_name = $subdistrict_name;
        $property->city_town_name = $city_town_name;
        $property->zip_code = $zip_code;
        $property->zip_code_name = $zip_code_name;
        $property->street_name = $street_name;
        $property->house_number = $house_number;
        $property->address = $address;
        $property->current_listing_price = $current_listing_price;
        $property->current_listing_price_formatted = $current_listing_price_formatted;
        $property->property_status = $property_status;
        $property->floor_level = $floor_level;
        $property->total_area = $total_area;
        $property->living_area = $living_area;
        $property->cubic_volume = $cubic_volume;
        $property->total_number_of_rooms = $total_number_of_rooms;
        $property->number_of_bathrooms = $number_of_bathrooms;
        $property->number_of_toilet_rooms = $number_of_toilet_rooms;
        $property->number_of_bedrooms = $number_of_bedrooms;
        $property->number_of_livingrooms = $number_of_livingrooms;
        $property->number_of_floors = $number_of_floors;
        $property->year_build = $year_build;
        $property->possession_date = $possession_date;
        $property->availability_date = $availability_date;
        $property->original_listing_date = $original_listing_date;
        $property->expiry_date = $expiry_date;
        $property->virtual_tour = $virtual_tour;
        $property->description_text = $description_text;
        $property->open_house_start = $open_house_start;
        $property->open_house_end = $open_house_end;
        $property->mortgage = $mortgage;
        $property->entrance = $entrance;
        $property->garage = $garage;
        $property->garage_area = $garage_area;
        $property->elevator = $elevator;
        $property->extra_flat = $extra_flat;
        $property->swapping = $swapping;
        $property->exclusive_entrance = $exclusive_entrance;
        $property->comm_number_of_offices = $comm_number_of_offices;
        $property->comm_number_of_floors = $comm_number_of_floors;
        $property->comm_total_number_of_rooms = $comm_total_number_of_rooms;
        $property->comm_building_frontage = $comm_building_frontage;
        $property->comm_clearance_height = $comm_clearance_height;
        $property->comm_elevators = $comm_elevators;
        $property->comm_lease_area = $comm_lease_area;
        $property->comm_mannufacturing_space = $comm_mannufacturing_space;
        $property->comm_office_space = $comm_office_space;
        $property->comm_parking = $comm_parking;
        $property->comm_possession_date = $comm_possession_date;
        $property->comm_price_per_area = $comm_price_per_area;
        $property->comm_retail_space = $comm_retail_space;
        $property->comm_total_area = $comm_total_area;
        $property->comm_useable_area = $comm_useable_area;
        $property->comm_warehouse_space = $comm_warehouse_space;
        $property->comm_year_built = $comm_year_built;
        $property->comm_zoning = $comm_zoning;
        $property->sent_to_web = $sent_to_web;
        $property->last_update = $last_update;
        $property->created_date = $created_date;
        $property->last_price_update_date = $last_price_update_date;
        $property->last_price_reduction_date = $last_price_reduction_date;
        $property->notes = $notes;
        $property->google_viewcount = $google_viewcount;
        $property->picture_count = $picture_count;
        $property->latitude = $latitude;
        $property->longitude = $longitude;
        $property->title_en = $title_en;
        $property->title_th = $title_th;
        $property->title = $title;
        $property->property_appraisal = $property_appraisal;
        $property->fire_appraisal = $fire_appraisal;

        //step 4 : update database
       $result =  $propertyService->updateProperty($property);

        if($result){
            //success
            $app->redirect(JUri::root()."administrator/index.php?option=com_webportal&view=properties",JText::_('COM_WEBPORTAL_SUCCESS'),'message');
        }else{
            //fail!
            $app->redirect(JUri::root()."administrator/index.php?option=com_webportal&view=property&layout=edit&property_id=$propertyId",JText::_('COM_WEBPORTAL_ERROR'),'message');
        }

    }

    public function apply()
    {
        //$this->edit();
        $app = JFactory::getApplication();
        $app->redirect(JUri::root()."administrator/index.php?option=com_webportal&view=property&layout=edit&property_id=$propertyId",JText::_('COM_WEBPORTAL_SUCCESS'),'message');
    }

    public function cancel() {
        JFactory::getApplication()->redirect(JUri::root()."administrator/index.php?option=com_webportal&view=properties");
    }

}
