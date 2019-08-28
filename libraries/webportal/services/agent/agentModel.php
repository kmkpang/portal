<?php
/**
 * Created by PhpStorm.
 * User: khan
 * Date: 5/19/14
 * Time: 2:22 PM
 */
require_once JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . 'modelbase.php';

class AgentModel extends ModelBase
{
    var $id = '';
    var $unique_id = '';
    var $office_id = '';
    var $address_id = '';
    var $first_name = '';
    var $middle_name = '';
    var $last_name = '';
    var $email = '';
    var $fax = '';
    var $phone = '';
    var $mobile = '';
    var $url_to_private_page = '';
    var $gender = '';
    var $SIN = '';
    var $DOB = '';
    var $language_spoken1 = '';
    var $language_spoken2 = '';
    var $language_spoken3 = '';
    var $language_spoken4 = '';
    var $language_spoken5 = '';
    var $image_file_path = '';
    var $image_file_name = '';
    var $date_entered = '';
    var $date_modified = '';
    var $title = '';
    /**
     * @var PropertyAddressModel
     */
    var $address = array();
    /**
     * @var MarketingInfoModel
     */
    var $marketing_info = array();
    var $properties = array();
    var $office = array();
    var $office_name = '';
    var $show_on_web = '';
    var $is_deleted = '';
}