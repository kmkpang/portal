<?php
/**
 * Created by PhpStorm.
 * User: khan
 * Date: 10/4/15
 * Time: 2:53 PM
 */

$params = array(
    "property_type=next_previous",
    "template=carousel_property_detail_page",
    "category_id=0",
    "office_id=0",
    "agent_id=0",
    "region_id=0",
    "city_town_id=0",
    "zip_code_id=0",
    "rows=3",
    "columns=3",
    "moduleclass_sfx=",
    "module_tag=div",
    "bootstrap_size=0",
    "header_tag=h3",
    "header_class=",
    "style=0"
);

ob_clean();

echo WFactory::getHelper()->getModule('mod_webportal_properties', $params);
ob_flush();
exit(0);


?>

