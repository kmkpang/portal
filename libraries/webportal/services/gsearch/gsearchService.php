<?php

/**
 * Created by PhpStorm.
 * User: khan
 * Date: 6/27/14
 * Time: 7:06 PM
 */
class GsearchService
{
    private $searchKey;
    private $placeKey;
    private $imageSearcher;

    function getSearchKey()
    {
        if ($this->searchKey === null) {
            $configArray = WFactory::getConfig()->getWebportalConfigurationArray();
            $this->searchKey = $configArray["externalApis"]["gsearchkey"];
        }
        return $this->searchKey;
    }

    function getPlaceKey()
    {
        if ($this->placeKey === null) {
            $configArray = WFactory::getConfig()->getWebportalConfigurationArray();
            $this->placeKey = $configArray["externalApis"]["gplacekey"];
        }
        return $this->placeKey;
    }

    /**
     * @return GoogleImages
     */
    private function getImageSearcher()
    {
        if ($this->imageSearcher === null) {
            require_once JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . 'gsearch' . DS . 'googleImageDownloader.php';

            //create class instance
            $this->imageSearcher = new GoogleImages($this->getSearchKey());
        }
        return $this->imageSearcher;
    }

    function searchImage($key = 'random logo')
    {
        $images = $this->getImageSearcher()->get_images($key);
        return $images[0]["url"];

    }

    function searchUrl($key)
    {
        $query = urlencode($key);
        $url = "http://ajax.googleapis.com/ajax/services/search/web?v=1.0&q=" . $query . '&key=' . $this->getSearchKey();

        $body = file_get_contents($url);
        $json = json_decode($body);

        if ($json == null) {
            WFactory::getLogger()->error("Failed to do google search for $key");
        }

        $result = $json->responseData->results[0]->url;

        if ($result == null) {
            WFactory::getLogger()->warn("google search null for $key");
        }

        WFactory::getLogger()->debug("Google searched for --->'$key'\r\nreturned url ----> $result");


        return $result;
    }

    /**
     * Dont use..dosent return good result for thailand
     * @param $lat
     * @param $lang
     * @param $radius
     * @internal param \PlaceSearchModel $searchModel
     * @return mixed
     */
    function searchPlaces($lat, $lang, $radius)
    {
        $key = $this->getPlaceKey();

        $language = JFactory::getLanguage()->getTag();
        $language = explode('-', trim($language));
        $language = $language[0];

        // $url = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=-33.8670522,151.1957362&radius=500&types=food&name=cruise&key=" . 'AIzaSyBzrYLSsoW3zvw2BTv50sSPqTrCTYpMIEI';
        //https://developers.google.com/places/documentation/search
        $url = "https://maps.googleapis.com/maps/api/place/textsearch/json?";

        $url .= "&location=$lat,$lang";
        $url .= "&radius=$radius";
        $url .= "&rankBy=distance";
       // $url .= "&types=bus-stand";
        $url .= "&query=shopping-mall";
        $url .= "&language=$language";
        $url .= "&key=$key";

        $curl = WFactory::getHelper()->getCurl($url);

        WFactory::getLogger()->info("Executing curl to : $url");

        $result = curl_exec($curl);

        if ($result === false) {
            WFactory::getLogger()->error("Failed to retrive google mal location. Error: " . curl_error($curl));
        }

        curl_close($curl);

        if ($result !== false) {

            $result = json_decode($result);

        }


        return $result;
    }

}