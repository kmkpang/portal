<?php

/**
 * Created by JetBrains PhpStorm.
 * User: khan
 * Date: 3/12/13
 * Time: 2:42 PM
 * To change this template use File | Settings | File Templates.
 */
class SagaapicoreService
{
    private $__configuration;

    private function getConfiguration()
    {
        if (!$this->__configuration) {
            $configuration = WFactory::getConfig()->getWebportalConfigurationArray();
            $this->__configuration = $configuration['sagaApi'];
        }
        return $this->__configuration;
    }


    public function syncPropertyCategories()
    {


        $propertyTypeUrl = $this->getConfiguration()['apiEndpoint'] .
            '/v1/Properties/GetPropertyTypes?branchId=' .
            $this->getConfiguration()['sagaOfficeId'];

        $propertyTypesInSaga = $this->executeCurl($propertyTypeUrl);

        $cats = $propertyTypesInSaga;
        if ($cats && count($cats) > 0) {
            $truncateSql = "TRUNCATE table jos_portal_property_categories";
            WFactory::getServices()->getSqlService()->update($truncateSql);

            // INSERT INTO `jos_portal_property_categories` (`id`, `description`, `mode_id`) VALUES ('1', 'des', '2');

            foreach ($cats as $c) {
                $x = $c;
                $mode = $c->name === 'Commercial Property Type' ? 2 : 3;
                $sql = "INSERT INTO `jos_portal_property_categories` (`id`, `description`, `mode_id`) VALUES ('{$c->id}', '{$c->enDetail}', '$mode')";
                WFactory::getSqlService()->update($sql);

            }


        }

        //now sync existing properties

        $properties = WFactory::getSqlService()->select('Select id,category_id,category_name from jos_portal_properties where is_deleted = 0');
        foreach ($properties as $property) {

            $id = $property['id'];

            //sync category id
            $category = WFactory::getSqlService()->select("Select * from jos_portal_property_categories where id = {$property['category_id']}");
            if (intval($property['category_id']) > 1000) {
                $break = 1;
            }
            if (count($category) > 0) {
                $updatePropertyQuery = "Update jos_portal_properties set category_name = '{$category[0]['description']}' where id = $id";
                $updated = WFactory::getSqlService()->update($updatePropertyQuery);

                WFactory::getLogger()->debug(' -> Updated property id ' . $id . ' with category name : ' . $category[0]['description']);

            }


        }


    }


    private function executeCurl($url, $post = 0, $postData = null)
    {
        $authToken = $this->loginToSagaApi();
        $curl = WFactory::getHelper()->getCurl($url, $post);
        $otherCurlConfig = array(
            CURLOPT_PORT => $this->getConfiguration()['apiEndpointPort'],
            CURLOPT_HTTPHEADER => array(
                "Authorization : Bearer $authToken",
                "Cache-Control: no-cache",
                "Content-Type: application/json",
            ),
        );
        if ($postData && $post) {
            $otherCurlConfig["CURLOPT_POSTFIELDS"] = $postData;
        }
        curl_setopt_array($curl, $otherCurlConfig);


        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            throw new Exception('Failed to execute to SagaApiCore : ' . $err);
        } else {
            $response = json_decode($response);
            return $response->result;

        }
    }

    private function loginToSagaApi()
    {

        $loginUrl = $this->getConfiguration()['apiEndpoint'] . '/v1/Account/Login';
        $curl = WFactory::getHelper()->getCurl($loginUrl, 1);
        $otherCurlConfig = array(
            CURLOPT_PORT => $this->getConfiguration()['apiEndpointPort'],
            CURLOPT_POSTFIELDS => json_encode(array(
                "Username" => $this->getConfiguration()['username'],
                "Password" => $this->getConfiguration()['password'],
                "OfficeId" => $this->getConfiguration()['sagaOfficeId'],
            )),
            CURLOPT_HTTPHEADER => array(
                "Cache-Control: no-cache",
                "Content-Type: application/json",
            ),
        );
        curl_setopt_array($curl, $otherCurlConfig);


        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            throw new Exception('Failed to login to SagaApiCore : ' . $err);
        } else {
            $response = json_decode($response);
            return $response->result;

        }

    }

}
