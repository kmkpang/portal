<?php
/**
 * Created by PhpStorm.
 * User: khan
 * Date: 5/19/14
 * Time: 2:22 PM
 */
require_once JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . 'modelbase.php';

class RequestinfoModel extends ModelBase
{
    public function __construct()
    {
        $this->timestamp = WFactory::getSqlService()->getMySqlDateTime();
    }


    var $id;

    var $contact_first_name;
    var $contact_last_name;
    var $contact_email;
    var $contact_province;
    var $contact_phone;
    var $contact_province_of_interest;
    var $contact_district_of_interest;
    var $how_do_you_know_us;
    var $interested_to_be;
    var $previous_realestate_experience;
    var $message;
    var $timestamp;
}