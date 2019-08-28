<?php

/**
 * Created by PhpStorm.
 * User: khan
 * Date: 12/20/14
 * Time: 6:23 PM
 */
class UrlSearchModel extends ModelBase
{

    /**
     * category_id,sale_rent,res/commercial
     * @var $type string
     */
    var $type;

    var $price;

    var $text;

    var $order;

    var $office;

    var $agent;

    var $bedrooms;

    var $transport;

    var $town;

    var $region;

    var $zip;

    var $lat;

    var $lan;

    var $rooms;

    var $line;

    var $station;

    var $currency;

    var $loan80;

    var $garage;

    var $elevator;

    var $new_today;

    var $new_this_week;

    var $page;

    var $limit;

    function toString()
    {
        $result = array();
        foreach ($this as $key => $value) {

            if ($value && $value !== "0,0" && $value !== "0") {
                $result[] = "$key=$value";
            }
        }

        return '&' . implode('&', $result);
    }


}