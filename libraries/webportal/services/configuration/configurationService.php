<?php

/**
 * Created by JetBrains PhpStorm.
 * User: khan
 * Date: 7/2/13
 * Time: 7:51 PM
 * To change this template use File | Settings | File Templates.
 */
class ConfigurationService
{

    /**
     * @var PortalPortalCompaniesSql
     */
    var $dbClass;

    public function __construct()
    {
        //Thou shalt not construct that which is unconstructable!
        $this->dbClass = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_COMPANIES_SQL);

    }


    public function updateLog4phpConfiguration($configurationModel)
    {

        $content = $configurationModel->config;
        $content = base64_decode($content);


        $path = JPATH_ROOT . "/log4phpConfiguration.xml";
        $result = file_put_contents($path, $content);

        if ($result) {
            $resultArray = array(
                "success" => true,
                "message" => "Update complete..(Make sure to adjust permission to readonly)"
            );
        } else
            $resultArray = array(
                "success" => false,
                "message" => "Update failed..! (Insufficient permission)"
            );

        echo json_encode($resultArray);
        exit(0);

    }

    public function updateJsConfiguration($configurationModel)
    {

        $content = $configurationModel->config;
        $content = base64_decode($content);


        $path = JPATH_ROOT . "/webportal.configuration.js";
        $result = file_put_contents($path, $content);

        if ($result) {
            $resultArray = array(
                "success" => true,
                "message" => "Update complete..(Make sure to adjust permission to readonly)"
            );
        } else
            $resultArray = array(
                "success" => false,
                "message" => "Update failed..! (Insufficient permission)"
            );

        echo json_encode($resultArray);
        exit(0);

    }

    public function updatePhpConfiguration($configurationModel)
    {

        $content = $configurationModel->config;
        $content = base64_decode($content);


        $path = JPATH_ROOT . "/webportal.configuration.php";
        $result = file_put_contents($path, $content);

        if ($result) {
            $resultArray = array(
                "success" => true,
                "message" => "Update complete.. (Make sure to adjust permission to readonly)"
            );
        } else
            $resultArray = array(
                "success" => false,
                "message" => "Update failed..! (Insufficient permission)"
            );

        echo json_encode($resultArray);
        exit(0);

    }

//    public function doHealthCheck(){
//
//
//        $message = array();
//        $result = WFactory::getHelper()->doHealthCheck($message);
//
//        $response = array("success"=>$result);
//
//    }

    //

}