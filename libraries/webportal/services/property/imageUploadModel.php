<?php
/**
 * Created by PhpStorm.
 * User: khan
 * Date: 5/23/15
 * Time: 12:37 PM
 */

require_once JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . 'modelbase.php';

class ImageUploadModel extends ModelBase
{

    var $propertyId;
    var $officeId;
    var $s3Url;
    var $localUrl;

}