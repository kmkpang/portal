<?php

/**
 * Created by PhpStorm.
 * User: khan
 * Date: 6/27/14
 * Time: 4:39 PM
 */

require_once JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . 'facebook' . DS . 'FacebookSession.php';
require_once JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . 'facebook' . DS . 'FacebookRequest.php';
require_once JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . 'facebook' . DS . 'Entities' . DS . 'AccessToken.php';
require_once JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . 'facebook' . DS . 'HttpClients' . DS . 'FacebookHttpable.php';
require_once JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . 'facebook' . DS . 'HttpClients' . DS . 'FacebookCurlHttpClient.php';
require_once JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . 'facebook' . DS . 'HttpClients' . DS . 'FacebookCurl.php';
require_once JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . 'facebook' . DS . 'FacebookResponse.php';
require_once JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . 'facebook' . DS . 'GraphObject.php';

require_once JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . 'facebook' . DS . 'FacebookSDKException.php';
require_once JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . 'facebook' . DS . 'FacebookRequestException.php';
require_once JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . 'facebook' . DS . 'FacebookAuthorizationException.php';
require_once JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . 'facebook' . DS . 'FacebookOtherException.php';

use Facebook\FacebookSession;
use Facebook\FacebookRequest;

class FacebookService
{
    /**
     * @var \Facebook\FacebookSession
     */
    private $facebookSession;

    private $appToken;


    /**
     * @return \Facebook\FacebookSession
     */
    private function getFacebookSession()
    {
        if ($this->facebookSession === null) {
            $configArray = WFactory::getConfig()->getWebportalConfigurationArray();
            $this->appToken = $configArray["externalApis"]["facebookAppId"] . '|' . $configArray["externalApis"]["facebookAppKey"];
            FacebookSession::setDefaultApplication(
                $configArray["externalApis"]["facebookAppId"],
                $configArray["externalApis"]["facebookAppKey"]
            );


            $this->facebookSession = new FacebookSession($this->appToken);
        }

        return $this->facebookSession;
    }

    /**
     * @param $lat
     * @param $lang
     * @param $radius | Radius in Km
     * @param bool $isUnittest
     * @return array
     */
    public function searchPlaces($lat, $lang, $radius, $isUnittest = false)
    {

        //if($lat=== 0.000)

        $singleQuery = array();


        //  search?type=placetopic&topic_filter=all
        $singleQuery['type'] = 'place';
        $singleQuery['center'] = $lat . ',' . $lang;
        $singleQuery['distance'] = $radius * 1000; // km to meter

        $configArray = WFactory::getConfig()->getWebportalConfigurationArray();

        $queries = file_get_contents('/home/khan/www/softverk-webportal-remaxth/libraries/webportal/services/facebook/placetopic.json');// $configArray["externalApis"]['pointsOfInterests'];
        $queries = json_decode($queries);

        $results = array();


        foreach ($queries->data as $q) {
            $singleQuery['q'] = $q->name;

            /**
             * @var $request |Facebook|FacebookRequest
             */
            $request = new FacebookRequest(
                $this->getFacebookSession(),
                'GET',
                '/search',
                $singleQuery);


            WFactory::getLogger()->debug("Doing graph search for : " . json_encode($q) . " [$lat,$lang,$radius]");
            try {
                /**
                 * @var $result \Facebook\GraphObject
                 */
                $result = $request->execute()->getGraphObject();

                $result = $result->asArray();
                $tempArray = array();
                foreach ($result['data'] as $place) {
                    $placeModel = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PLACES)->getPlaceModel();

                    $placeModel->category = $q;
                    $placeModel->uniqueid = $place->id;
                    $placeModel->name = $place->name;
                    $placeModel->latitude = $place->location->latitude;
                    $placeModel->longitude = $place->location->longitude;
                    $placeModel->zip = intval($place->location->zip);

                    //now get the page..

                    $pageRequest = new FacebookRequest(
                        $this->getFacebookSession(),
                        'GET',
                        '/' . $place->id
                    );

                    $pageDetail = $pageRequest->execute()->getGraphObject();
                    $placeModel->detail = $pageDetail;

//                    $searchQuery = "{$place->location->street} {$place->location->zip} {$place->location->city} {$place->location->country} {$placeModel->category} {$placeModel->name}";
//                    $imageSearchQuery = "{$place->location->street}  {$place->location->zip} {$place->location->city} {$place->location->country} {$placeModel->category} {$placeModel->name}";

//                    $placeModel->link = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_GSEARCH)->searchUrl($searchQuery);
//                    $placeModel->image = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_GSEARCH)->searchImage($imageSearchQuery);

                    WFactory::getLogger()->info("For {$placeModel->name} --> {$placeModel->link} & {$placeModel->image}");


                    $tempArray[] = $placeModel;
                }


                $results[$q->name] = $tempArray;
            } catch (Exception $ex) {
                WFactory::getLogger()->error($ex->getMessage());
            }

//            if($isUnittest===true)
//                break;//break and return fast for unittest...!!!
        }


        WFactory::getLogger()->debug("----------------output start----------");
        var_dump($results);

        file_put_contents("/tmp/search_result", json_encode($results));

        WFactory::getLogger()->debug("----------------output end----------");

        return $results;

    }
}