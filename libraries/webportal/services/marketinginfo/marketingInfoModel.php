<?php
/**
 * Created by PhpStorm.
 * User: khan
 * Date: 8/5/15
 * Time: 9:45 PM
 */

require_once JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . 'modelbase.php';

class MarketingInfoModel extends ModelBase
{
    var $id;   // KEY ATTR. WITH AUTOINCREMENT

    var $marketing_info_type_id;   // (normal Attribute)
    var $reference_id;   // (normal Attribute)
    var $country_id;   // (normal Attribute)
    var $slogan;   // (normal Attribute)
    var $closer;   // (normal Attribute)
    var $bullet_point1;   // (normal Attribute)
    var $bullet_point2;   // (normal Attribute)
    var $bullet_point3;   // (normal Attribute)
    var $description;   // (normal Attribute)
}