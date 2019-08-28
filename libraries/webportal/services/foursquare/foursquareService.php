<?php

/**
 * Created by PhpStorm.
 * User: khan
 * Date: 6/27/14
 * Time: 4:39 PM
 */

require_once "FoursquareApi.php";

class FoursquareService
{

    public function get4sqApi()
    {

    }

    public function testGetLocation()
    {
        $latitude = 13.751151; //$properties[0]['latitude'];
        $longitude = 100.658723;//$properties[0]['longitude'];

        $foursquare = new FoursquareApi("RTMZZVYDELHJXGY5XSDXR0TVCKVKB1MDEYXY3UP2UVT2E4IM", "TPWBFG5IEABXKDMLDPW2IZHEGRTKG5Y2MV40VVRHZSJL34PW");

//        $endpoint = "venues/categories";
//        $params = array();
//
//        $cats = json_decode($foursquare->GetPublic($endpoint, $params));
//        $cats = $cats->response->categories;
//        $cats_json = json_encode($cats);
//
//        file_put_contents("/home/khan/www/softverk-webportal-remaxth/libraries/webportal/services/foursquare/categoryList.json", $cats_json);


        $endpoint = "venues/search";
        $params = array("ll" => "$latitude,$longitude",
            "intent" => "browse",
            "radius" => 2000,
            "limit" => 1000, // really limited to 50 !!!!
             "categoryId" => "4d4b7105d754a06372d81259",
        );
        $response = $foursquare->GetPublic($endpoint, $params);
        $response = json_decode($response);
        $response = $response->response;


        return $response;

    }

}