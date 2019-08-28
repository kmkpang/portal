<?php
/**
 * Created by PhpStorm.
 * User: khan
 * Date: 5/19/14
 * Time: 2:22 PM
 */
require_once JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . 'modelbase.php';

class ContactModel extends ModelBase
{
    var $id;

    var $contact_name;
    var $contact_phone;
    var $contact_email;
    var $contact_category;
    var $contact_city;
//    var $agent_id;
//    var $agent_email;

    var $message;
    var $agent_message;
    var $timestamp;

    //as used by send to friend ...

    var $from_email;
    var $from_name;
    var $to_email;
    var $property_id;


    public function __construct()
    {
        $this->timestamp = WFactory::getSqlService()->getMySqlDateTime();
    }
}