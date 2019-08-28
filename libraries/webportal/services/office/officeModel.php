<?php
/**
 * Created by PhpStorm.
 * User: khan
 * Date: 8/5/15
 * Time: 5:55 PM
 */

require_once JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . 'modelbase.php';

class OfficeModel extends ModelBase
{
    var $id;
    var $public_key;
    var $unique_id;
    var $company_id;
    var $address_id;
    var $country_id;
    var $office_name;
    var $email;
    var $fax;
    var $phone;
    var $url_to_private_page;
    var $image_file_path;
    var $image_file_name;
    var $date_entered;
    var $date_modified;
    var $manager_id;
    var $certified_agent_id;
    var $logo;
    var $show_on_web;

    /**
     * @var PropertyAddressModel
     */
    var $address;
    /**
     * @var MarketingInfoModel
     */
    var $marketingInfo;

    /**
     * @var Array
     */
    var $agents;

}