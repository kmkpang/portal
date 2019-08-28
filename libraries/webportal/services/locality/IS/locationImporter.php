<?php

/**
 * Created by PhpStorm.
 * User: khan
 * Date: 1/3/16
 * Time: 6:52 AM
 */
class LocationImporter
{

    public function import()
    {
        /* cuz now i am on windows and this command does not work on windows..silly!
        $config = JFactory::getConfig();
        ///C:/xampp/htdocs/softverk-webportal/libraries/webportal/services/locality/tableQuery
        $command = "mysql -u" . $config->get("user") . " -p" . $config->get("password") . " " . $config->get("db") . " < " . JPATH_BASE . "/libraries/webportal/services/locality/tableQuery";

        WFactory::getLogger()->warn("Going to execute: $command");
        shell_exec($command);

        */
        //WFactory::getSqlService()->update($query);


        $query = "TRUNCATE jos_portal_locality";
        WFactory::getSqlService()->update($query);

        $this->importSchools();
        $this->importBustStand();



    }

    public function importBustStand()
    {

        ///home/khan/external/www/softverk-webportal-generic/libraries/webportal/services/locality/IS/busstand.xml
        $xmlFile = json_decode(json_encode(simplexml_load_file(JPATH_BASE . "/libraries/webportal/services/locality/IS/busstand.xml")));

        // $parsedData
        $localities = array();

        foreach ($xmlFile->stod as $xmlLine) {
            /**
             * @var $placesDbClass PortalPortalLocalitySql
             */
            $placesDbClass = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_LOCALITY_SQL);


            $xmlLine = $xmlLine->{'@attributes'};

            $placesDbClass->__latitude = $xmlLine->lat;
            $placesDbClass->__longitude = $xmlLine->lon;
            $placesDbClass->__name = $xmlLine->nafn;
            $placesDbClass->__type = "BUSSTAND";

            $localities[] = $placesDbClass;

        }

        foreach ($localities as $l) {
            WFactory::getSqlService()->insert($l);
        }

        return true;
    }

    public function importSchools()
    {


        ///home/khan/external/www/softverk-webportal-generic/libraries/webportal/services/locality/IS/busstand.xml
        $xmlFile = json_decode(json_encode(simplexml_load_file(JPATH_BASE . "/libraries/webportal/services/locality/IS/schools.xml")));

        // $parsedData
        $localities = array();

        $types = array("Skóli" => "SCHOOL",
            "Leikskóli" => "KINDERGARTEN",
            "Íþróttasvæði" => "SPORTSAREA",
            "Hjólabrettagarður" => "SKATEBOARDING",
            "Skíðalyfta" => "SKILIFT");

        $default = "AREAOFINTEREST";


        foreach ($xmlFile->Document->Folder as $xmlLine) {
            foreach ($xmlLine->Placemark as $place) {
                /**
                 * @var $placesDbClass PortalPortalLocalitySql
                 */
                $placesDbClass = WFactory::getServices()->getSqlService()->getDbClass(__PORTAL_PORTAL_LOCALITY_SQL);

                $schoolType = $place->Snippet;
                if (is_string($schoolType)) {
                    if (array_key_exists($schoolType, $types))
                        $placesDbClass->__type = $types[$schoolType];
                }
                if (WFactory::getHelper()->isNullOrEmptyString($placesDbClass->__type)) {
                    $placesDbClass->__type = $default;
                }

                $latitudeLongitude = explode(',', $place->Point->coordinates);

                $placesDbClass->__latitude = trim($latitudeLongitude[1]);
                $placesDbClass->__longitude = trim($latitudeLongitude[0]);
                $placesDbClass->__name = $place->name;
                //$placesDbClass->__type = "SCHOOL";

                $localities[] = $placesDbClass;
            }


        }

        WFactory::getLogger()->info(implode(" , ", $types) . "\r\n");

        foreach ($localities as $l) {
            WFactory::getSqlService()->insert($l);
        }

        return true;


    }


}