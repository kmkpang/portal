<?php

/**
 * Created by PhpStorm.
 * User: khan
 * Date: 6/26/14
 * Time: 10:05 PM
 */

define('OFFSET', 268435456); // It is half of the earth circumference in pixels at zoom level 21
define('RADIUS', 85445659.4471); /* $offset / pi() */

class GmapService
{


    function getKey()
    {
        $configArray = WFactory::getConfig()->getWebportalConfigurationArray();
        return $configArray["externalApis"]["gmapkey"];
    }

    function searchLocationByName($location)
    {
        $address = urlencode($location);
        $url = "https://maps.google.com/maps/api/geocode/json?key=AIzaSyBceY08Txx4Cu7k_oL19x_Q-FTj5kqJp1g&address=$address&sensor=false&region=" . WFactory::getHelper()->getCountryFullName();

        $ch = WFactory::getHelper()->getCurl($url);

        $response = curl_exec($ch);
        curl_close($ch);
        $response_a = json_decode($response);
        $lat = $response_a->results[0]->geometry->location->lat;
        $long = $response_a->results[0]->geometry->location->lng;

        return array('lat' => $lat, 'long' => $long);

    }

    //---------------------------------------

    function lonToX($lon)
    {
        return round(OFFSET + RADIUS * $lon * pi() / 180);
    }

    function latToY($lat)
    {
        return round(OFFSET - RADIUS *
            log((1 + sin($lat * pi() / 180)) /
                (1 - sin($lat * pi() / 180))) / 2);
    }

    function pixelDistance($lat1, $lon1, $lat2, $lon2, $zoom)
    {
        $x1 = $this->lonToX($lon1);
        $y1 = $this->latToY($lat1);

        $x2 = $this->lonToX($lon2);
        $y2 = $this->latToY($lat2);

        return sqrt(pow(($x1 - $x2), 2) + pow(($y1 - $y2), 2)) >> (21 - $zoom);
    }
    // based on this : http://www.appelsiini.net/2008/introduction-to-marker-clustering-with-google-maps
    function cluster($markers, $distance, $zoom)
    {
        $clustered = array();
        /* Loop until all markers have been compared. */
        while (count($markers)) {
            $marker = array_pop($markers);
            $cluster = array();
            /* Compare against all markers which are left. */
            foreach ($markers as $key => $target) {
                $pixels = $this->pixelDistance($marker['lat'], $marker['lon'],
                    $target['lat'], $target['lon'],
                    $zoom);
                /* If two markers are closer than given distance remove */
                /* target marker from array and add it to cluster.      */
                if ($distance > $pixels) {
                    printf("Distance between %s,%s and %s,%s is %d pixels.\n",
                        $marker['lat'], $marker['lon'],
                        $target['lat'], $target['lon'],
                        $pixels);
                    unset($markers[$key]);
                    $cluster[] = $target;
                }
            }

            /* If a marker has been added to cluster, add also the one  */
            /* we were comparing to and remove the original from array. */
            if (count($cluster) > 0) {
                $cluster[] = $marker;
                $clustered[] = $cluster;
            } else {
                $clustered[] = $marker;
            }
        }
        return $clustered;
    }


}