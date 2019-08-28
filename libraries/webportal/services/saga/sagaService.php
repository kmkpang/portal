<?php

/**
 * Created by PhpStorm.
 * User: shroukkhan
 * Date: 7/8/17
 * Time: 4:23 PM
 */
class SagaService
{


    function getSagaAgentsCheckinList()
    {
        //$fromMobile = $queryParams['fromMobile'];

        $result = $this->executeSagaCurl("/api/agents/getCheckedInAgents", "{}");
        $result = json_decode($result);

//        if ($fromMobile === 'true' && $result && count($result) > 0) {
//            //format result
//
//            $extractUid = function ($data) {
//                return $data->user_id;
//            };
//            $userId = array_map($extractUid, $result);
//
//            $query = "SELECT jos_portal_sales.unique_id,
//                           jos_portal_sales.id,
//                           jos_portal_sales.first_name,
//                           jos_portal_sales.middle_name,
//                           jos_portal_sales.last_name
//                      FROM `era-property-network-th`.jos_portal_sales jos_portal_sales
//                     WHERE (   (jos_portal_sales.unique_id = '111')
//                            OR (jos_portal_sales.unique_id = '1111'))";
//
//        }


        return $result;

    }


    function createRandomCheckinForDemo()
    {
        $result = $this->executeSagaCurl("/api/agents/createRandomCheckinForDemo", "{}", "GET");
        $result = json_decode($result);
        return $result;

    }


    function getPropertyDetails()
    {
        $result = $this->executeSagaCurl("/api/properties/getDetail?propertyId=2&lang=th", "{}");
        $result = json_decode($result);
        return $result;

    }


    function getToken()
    {
        $salt = 'rz8LuOtFBXphj9WQfvFh';
        $userName = __SAGA_API_USERNAME;
        $password = md5(__SAGA_API_PASSWORD);

        $key = base64_encode(hash_hmac('sha256', "$password:$salt", $salt, true));
        //---

        $time = time();
        $ticks = number_format(($time * 10000000) + 621355968000000000, 0, '.', '');

        $message = "$userName:$ticks";
        $hash = hash_hmac('sha256', $message, $key, true);

        $token = base64_encode($hash);
        $tokenStr = "$token:$userName:$ticks";
        $token = base64_encode($tokenStr);

        return $token;

    }

    function executeSagaCurl($url, $jsonPayload, $getPost = "POST")
    {


        $url = __SAGA_API_URL . $url;


        $jsonPayload = is_string($jsonPayload) ? $jsonPayload : json_encode($jsonPayload);
        $token = $this->getToken();
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_PORT => parse_url($url, PHP_URL_PORT),
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 50,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $getPost,
            CURLOPT_POSTFIELDS => $jsonPayload,
            CURLOPT_HTTPHEADER => array(
                "accept: application/json; charset=utf-8",
                "cache-control: no-cache",
                "content-type: application/json",
                "token: Bearer $token"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            WFactory::getLogger()->error("Failed to execute SAGA Curl to $url : $err", __LINE__, __FILE__);
            // echo "cURL Error #:" . $err;
        } else {
            return $response;
        }
    }

}