<?php
/**
 * Created by PhpStorm.
 * User: paisit
 * Date: 15/2/2559
 * Time: 14:57
 */

function getCparams($param)
{
    $app        = JFactory::getApplication();
    $cparams    = $app->getParams('com_webportal');
    $result 	= htmlspecialchars($cparams->get($param));

    return $result;
}

//var Cparams
$property_type = getCparams('property_type');
$category_id =  getCparams('category_id');
$buy_rent = getCparams('buy_rent');
$show_featured = getCparams('show_featured');