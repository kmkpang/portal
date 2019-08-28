<?php
/**
 * Created by PhpStorm.
 * User: khan
 * Date: 5/19/14
 * Time: 2:22 PM
 */
require_once JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . 'modelbase.php';

class CompanyModel extends ModelBase
{
    var $id = '';

    var $company_name = '';
    var $company_address = '';
    var $postal_address = '';
    var $telephone = '';
    var $fax = '';
    var $email = '';
    var $ssn = '';
    var $legal = '';
    var $description = '';
    var $image_file_path = '';
    var $image_file_name = '';
    var $date_entered = '';
    var $date_modified = '';


}