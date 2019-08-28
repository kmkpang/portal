<?php

/**
 * Created by PhpStorm.
 * User: khan
 * Date: 8/17/14
 * Time: 10:58 AM
 */
set_include_path(get_include_path() . PATH_SEPARATOR . JPATH_LIBRARY_WEBPORTAL_SERVICES . '/googleapi');
require_once 'Google/Client.php';
require_once 'Google/Auth/AssertionCredentials.php';


class GoogleapiService
{

    public function getPageViewCount($pagePathRegex)
    {

        WFactory::getLogger()->debug("Getting google page view count for $pagePathRegex");

############## ------------------------------ for reference purpose ------------------
        /*
            $client_id = '180067270462-59em2ce3bpat2pogc6irudgo680i0pti.apps.googleusercontent.com';
            $client_secret = 'a1o1pJn77eIlh7ddTlEjkpmY';
            $redirect_uri = 'urn:ietf:wg:oauth:2.0:oob';

            $client = new Google_Client();
            $client->setApplicationName('Softverk Webportal');
            $client->setClientId($client_id);
            $client->setClientSecret($client_secret);
            $client->setRedirectUri($redirect_uri);
            $client->setAccessType('offline');
            $client->setApprovalPrompt('auto');
            $client->setScopes(array('https://www.googleapis.com/auth/analytics.readonly'));

            $authUrl = $client->createAuthUrl();

            WFactory::getLogger()->info("Visit : $authUrl");

            $result = Wfa

            $token = $client->getAccessToken();



            //4/3sN_j00VFp168RivpXEdTz8iz4u6.0lxRgx6WXYMaBrG_bnfDxpIQHDjFjwI

            // $result = $client->authenticate('4/3sN_j00VFp168RivpXEdTz8iz4u6.0lxRgx6WXYMaBrG_bnfDxpIQHDjFjwI');

            // {"access_token":"ya29.ZADjv5fzPwWd9iEAAAAZC-UynTOmJV5vH-jgac10lwUPW00MRy28lrs0XUsDBpbY6GqnJ0UI4y6TnJbESJ4","token_type":"Bearer","expires_in":3600,"created":1408256109}

            // exit();


            //$client->authenticate();

            exit();

        */
        /*
            $client = new Google_Client();
            $client->setApplicationName('Softverk Webportal'); // name of your app

    // set assertion credentials
            $client->setAssertionCredentials(
                new Google_Auth_AssertionCredentials(
                    '180067270462-7ft51rkkdf7itruatua4ooj1rhcsbujo@developer.gserviceaccount.com', // email you added to GA
                    array('https://www.googleapis.com/auth/analytics.readonly'),
                    file_get_contents('/var/www/softverk-webportal-remaxth/libraries/webportal/services/googleapi/API Project-030c7f3a8c98.p12') // keyfile you downloaded
                )
            );

    // other settings
            $client->setClientId('180067270462-7ft51rkkdf7itruatua4ooj1rhcsbujo.apps.googleusercontent.com'); // from API console
            $client->setAccessType('offline_access'); // this may be unnecessary?

    // create service and get data
            $service = new Google_Service_Analytics($client);
            $profileId = $service->management_accounts->listManagementAccounts();
            var_dump($profileId);

            exit(0);

            */
########## end for reference purpose -------------------------------------------------------


        require_once 'Google/Service/Analytics.php';

        $client = $this->getClient('https://www.googleapis.com/auth/analytics.readonly');
        $service = new Google_Service_Analytics($client);

        //   For the NEW VERSION analytic page it is the number at the end of the ERL starting with p
        //https://www.google.com/analytics/web/#home/a11345062w43527078pXXXXXXXX/
        $managementAccount = $service->management_accounts->listManagementAccounts();
        $accountId = $managementAccount->getItems()[0]['id'];
        $profiles = $service->management_profiles->listManagementProfiles($accountId, '~all')->getItems();
        $defaultAnalytics = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_GANALTICS)->getDefaultAnalyticId();

        $profileId = "";
        foreach ($profiles as $p) {
            if ($p["webPropertyId"] == $defaultAnalytics) {
                $profileId = $p["id"];
                break;
            }

        }

        WFactory::getLogger()->debug("Obtaining page view count using analytic id: $defaultAnalytics & profileID : $profileId");
        /*
         * start-index=10
            Optional.
         If not supplied, the starting index is 1. (Result indexes are 1-based.
         That is, the first row is row 1, not row 0.) Use this parameter as a
         pagination mechanism along with the max-results parameter for situations
         when totalResults exceeds 10,000 and you want to retrieve rows indexed at
         10,001 and beyond.
         * */
        $index = 1;
        $gaMaxResult = 1000;
        $items = array();
        do {
            $OBJresult = $service->data_ga->get(
                'ga:' . $profileId,
                '2014-08-01', //static..cuz..well started then!
                date("Y-m-d"), //until today
                'ga:visits',
                array(
                    'filters' => 'ga:pagePath=~' . $pagePathRegex,

                    'dimensions' => 'ga:pagePath',
                    'metrics' => 'ga:pageviews',
                    'start-index' => $index,
                    'max-results' => $gaMaxResult,

                ));

            $results = $OBJresult->getRows();

            $resultReturned = count($results);
            $index = $index + $gaMaxResult;

            $items = array_merge($items, $results);



        } while ($gaMaxResult == $resultReturned);

        WFactory::getLogger()->debug("Total page view result array size is : " . count($items));

        return $items;


    }

    /**
     * @param $scopeUrl
     * @return Google_Client
     */
    private function getClient($scopeUrl)
    {

        $currentFolder = (dirname(__FILE__));
        $currentFolder = realpath($currentFolder);

        $auth = json_decode(file_get_contents($currentFolder . "/Auth.json"));

        $client_id = $auth->client_id; //'180067270462-7ft51rkkdf7itruatua4ooj1rhcsbujo.apps.googleusercontent.com'; //Client ID
        $service_account_name = $auth->client_email; //'180067270462-7ft51rkkdf7itruatua4ooj1rhcsbujo@developer.gserviceaccount.com'; //Email Address
        $key_file_location = $currentFolder . '/key.p12'; //key.p12

        WFactory::getLogger()->info("Loading google api client using:
                        clientId : $client_id,
                        serviceAccountMailAddress : $service_account_name
                        keyfile from : $key_file_location
        ");


        $client = new Google_Client();
        $client->setApplicationName('Softverk Webportal');
        $client->setClientId($client_id);
        $key = file_get_contents($key_file_location);
        $cred = new Google_Auth_AssertionCredentials(
            $service_account_name,
            array($scopeUrl),
            $key
        );

        $client->setAccessType('offline');
        $client->setAssertionCredentials($cred);
        if ($client->getAuth()->isAccessTokenExpired()) {
            $client->getAuth()->refreshTokenWithAssertion($cred);
        }

        WFactory::getLogger()->info("Got auth token : " . $client->getAccessToken());

        return $client;

    }


}