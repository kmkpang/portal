<?php
/**
 * Created by JetBrains PhpStorm.
 * User: user
 * Date: 8/7/12
 * Time: 10:58 PM
 * To change this template use File | Settings | File Templates.
 */
// no direct access
defined('_JEXEC') or die ("Restricted area");


define('__PORTAL_GEOGRAPHY_COUNTRIES_SQL','PortalGeographyCountriesSql') ;
define('__PORTAL_GEOGRAPHY_POSTAL_CODES_SQL','PortalGeographyPostalCodesSql') ;
define('__PORTAL_GEOGRAPHY_POSTAL_CODES_IS_SQL','PortalGeographyPostalCodesIsSql') ;
define('__PORTAL_GEOGRAPHY_POSTAL_CODES_PH_SQL','PortalGeographyPostalCodesPhSql') ;
define('__PORTAL_GEOGRAPHY_REGIONS_SQL','PortalGeographyRegionsSql') ;
define('__PORTAL_GEOGRAPHY_REGIONS_IS_SQL','PortalGeographyRegionsIsSql') ;
define('__PORTAL_GEOGRAPHY_REGIONS_PH_SQL','PortalGeographyRegionsPhSql') ;
define('__PORTAL_GEOGRAPHY_TOWNS_SQL','PortalGeographyTownsSql') ;
define('__PORTAL_GEOGRAPHY_TOWNS_IS_SQL','PortalGeographyTownsIsSql') ;
define('__PORTAL_GEOGRAPHY_TOWNS_PH_SQL','PortalGeographyTownsPhSql') ;
define('__PORTAL_PORTAL_ADDRESS_TYPES_SQL','PortalPortalAddressTypesSql') ;
define('__PORTAL_PORTAL_AREA_SQL','PortalPortalAreaSql') ;
define('__PORTAL_PORTAL_COMPANIES_SQL','PortalPortalCompaniesSql') ;
define('__PORTAL_PORTAL_CONTACTS_SQL','PortalPortalContactsSql') ;
define('__PORTAL_PORTAL_FEATURES_SQL','PortalPortalFeaturesSql') ;
define('__PORTAL_PORTAL_LOG_ACTIONS_SQL','PortalPortalLogActionsSql') ;
define('__PORTAL_PORTAL_MARKETING_INFO_SQL','PortalPortalMarketingInfoSql') ;
define('__PORTAL_PORTAL_MARKETING_INFO_TYPE_SQL','PortalPortalMarketingInfoTypeSql') ;
define('__PORTAL_PORTAL_OFFICES_SQL','PortalPortalOfficesSql') ;
define('__PORTAL_PORTAL_PLACES_SQL','PortalPortalPlacesSql') ;
define('__PORTAL_PORTAL_PROJECT_IMAGE_SQL','PortalPortalProjectImageSql') ;
define('__PORTAL_PORTAL_PROJECT_IMAGE_TYPE_SQL','PortalPortalProjectImageTypeSql') ;
define('__PORTAL_PORTAL_PROJECT_PLAN_SQL','PortalPortalProjectPlanSql') ;
define('__PORTAL_PORTAL_PROJECT_PLAN_TYPE_SQL','PortalPortalProjectPlanTypeSql') ;
define('__PORTAL_PORTAL_PROJECT_UNIT_SQL','PortalPortalProjectUnitSql') ;
define('__PORTAL_PORTAL_PROJECT_UNIT_TYPE_SQL','PortalPortalProjectUnitTypeSql') ;
define('__PORTAL_PORTAL_PROJECTS_SQL','PortalPortalProjectsSql') ;
define('__PORTAL_PORTAL_PROPERTIES_SQL','PortalPortalPropertiesSql') ;
define('__PORTAL_PORTAL_PROJECT_FEATURE_LIST_SQL','PortalPortalProjectFeatureListSql') ;
define('__PORTAL_PORTAL_PROPERTIES_PROMOTIONS_SQL','PortalPortalPropertiesPromotionsSql') ;
define('__PORTAL_PORTAL_PROPERTIES_USERS_SQL','PortalPortalPropertiesUsersSql') ;
define('__PORTAL_PORTAL_PROPERTY_ADDRESSES_SQL','PortalPortalPropertyAddressesSql') ;
define('__PORTAL_PORTAL_PROPERTY_ADDRESSES_BKUP_SQL','PortalPortalPropertyAddressesBkupSql') ;
define('__PORTAL_PORTAL_PROPERTY_CATEGORIES_SQL','PortalPortalPropertyCategoriesSql') ;
define('__PORTAL_PORTAL_PROJECT_UNIT_IMAGE_SQL','PortalPortalProjectUnitImageSql') ;
define('__PORTAL_PORTAL_PROPERTY_EMAIL_LOG_SQL','PortalPortalPropertyEmailLogSql') ;
define('__PORTAL_PORTAL_PROJECT_FEATURES_SQL','PortalPortalProjectFeaturesSql') ;
define('__PORTAL_PORTAL_PROPERTY_FEATURES_SQL','PortalPortalPropertyFeaturesSql') ;
define('__PORTAL_PORTAL_PROPERTY_FILES_SQL','PortalPortalPropertyFilesSql') ;
define('__PORTAL_PORTAL_PROPERTY_IMAGES_SQL','PortalPortalPropertyImagesSql') ;
define('__PORTAL_PORTAL_PROPERTY_MODES_SQL','PortalPortalPropertyModesSql') ;
define('__PORTAL_PORTAL_PROPERTY_TYPES_SQL','PortalPortalPropertyTypesSql') ;
define('__PORTAL_PORTAL_PROPERTY_VIDEOS_SQL','PortalPortalPropertyVideosSql') ;
define('__PORTAL_PORTAL_PROPERTY_VIEWINGS_USERS_SQL','PortalPortalPropertyViewingsUsersSql') ;
define('__PORTAL_PORTAL_REQUESTINFO_SQL','PortalPortalRequestinfoSql') ;
define('__PORTAL_PORTAL_SALES_SQL','PortalPortalSalesSql') ;
define('__PORTAL_PORTAL_SAVED_SEARCH_SQL','PortalPortalSavedSearchSql') ;
define('__PORTAL_PORTAL_SEARCHES_USERS_SQL','PortalPortalSearchesUsersSql') ;
define('__PORTAL_PORTAL_SENTTOWEB_LOG_SQL','PortalPortalSenttowebLogSql') ;
define('__PORTAL_PORTAL_SETTING_SQL','PortalPortalSettingSql') ;
define('__PORTAL_PORTAL_STREET_SQL','PortalPortalStreetSql') ;
define('__PORTAL_PORTAL_USERS_PROFILE_SQL','PortalPortalUsersProfileSql') ;
define('__PORTAL_USERS_SQL','PortalUsersSql') ;

define('__PROPPERTY_PORTAL_LOCALITY', 'LocalityService');
define('__PROPPERTY_PORTAL_MANDRILL', 'MandrillService');
define('__PROPPERTY_PORTAL_HTMLMAIL', 'HtmlmailService');
define('__PROPPERTY_PORTAL_OFFICE', 'OfficeService');
define('__PROPPERTY_PORTAL_PROPERTY', 'PropertyService');
define('__PROPPERTY_PORTAL_SAGAAPICORE', 'SagaapicoreService');
define('__PROPPERTY_PORTAL_PROJECT', 'ProjectService');
define('__PROPPERTY_PORTAL_MARKETINGINFO', 'MarketinginfoService');
define('__PROPPERTY_PORTAL_ADDRESS', 'AddressService');
define('__PROPPERTY_PORTAL_SAGA', 'SagaService');
define('__PROPPERTY_PORTAL_SEARCH', 'SearchService');
define('__PROPPERTY_PORTAL_SENTTOWEB', 'SenttowebService');
define('__PROPPERTY_PORTAL_PROPERTIES', 'PropertiesService');
define('__PROPPERTY_PORTAL_PROJECTS', 'ProjectsService');
define('__PROPPERTY_PORTAL_DEVELOPER', 'DeveloperService');
define('__PROPPERTY_PORTAL_IMAGE', 'ImageService');
define('__PROPPERTY_PORTAL_VIDEO', 'VideoService');
define('__PROPPERTY_PORTAL_COMPANY', 'CompanyService');
define('__PROPPERTY_PORTAL_AGENT', 'AgentService');
define('__PROPPERTY_PORTAL_AGENTS', 'AgentsService');
define('__PROPPERTY_PORTAL_CONTACTS', 'ContactsService');
define('__PROPPERTY_PORTAL_BEANSTALKD', 'BeanstalkdService');
define('__PROPPERTY_PORTAL_EXCEL', 'ExcelService');
define('__PROPPERTY_PORTAL_PLACES', 'PlacesService');
define('__PROPPERTY_PORTAL_GMAP', 'GmapService');
define('__PROPPERTY_PORTAL_FACEBOOK', 'FacebookService');
define('__PROPPERTY_PORTAL_FOURSQUARE', 'FoursquareService');
define('__PROPPERTY_PORTAL_GSEARCH', 'GsearchService');
define('__PROPPERTY_PORTAL_GANALTICS', 'GanalyticsService');
define('__PROPPERTY_PORTAL_GAPI', 'GoogleapiService');
define('__PROPPERTY_PORTAL_REQUESTINFO', 'RequestinfoService');
define('__PROPPERTY_PORTAL_SITEMAP', 'SitemapService');
define('__PROPPERTY_PORTAL_ARTICLES', 'ArticleService');
define('__PROPPERTY_PORTAL_TEMPLATE', 'TemplateService');
define('__PROPPERTY_PORTAL_USERS', 'UsersService');
define('__PROPPERTY_PORTAL_CURRENCY', 'CurrencyService');
define('__PROPPERTY_PORTAL_CONFIGURATION', 'ConfigurationService');


/**
 * Class WebportalSqlServiceProvider
 * @codeCoverageIgnore
 */
class WebportalSqlServiceProvider
{

    /**
     * @var JDatabase
     */
    private $db;

    private $serviceClassArray;

    protected static $instance = null;

    protected function __construct()
    {
        //Thou shalt not construct that which is unconstructable!
        $this->serviceClassArray = array();

    }

    protected function __clone()
    {
        //Me not like clones! Me smash clones!
    }

    /**
     * @return WebportalSqlServiceProvider
     */
    public static function getInstance()
    {
        if (!isset(static::$instance)) {
            static::$instance = new static;

        }
        return static::$instance;
    }

    public function getDbo()
    {
        if ($this->db == null) {
            $this->db = &JFactory::getDBO();
        }
        return $this->db;
    }


    /**
     * returns you one of the file in /var/www/softverk-webportal/libraries/webportal/services/dbclasses
     * @param $className
     * @return mixed
     */
    public function getDbClass($className)
    {
        $fileName = preg_replace('/Portal/', '', $className, 1);
        $fileName = str_replace('Sql', '', $fileName);
        $fileName = lcfirst($fileName);
        $fileName = "class.$fileName.php";
        ///var/www/softverk-webportal/libraries/webportal/services/dbclasses/class.geographyCountries.php
        $basePath = JPATH_ROOT . "/libraries/webportal/services/dbclasses/";
        $filePath = $basePath . $fileName;

        require_once $filePath;

        $result = new $className();

        return $result;
    }

    /**
     * @param $serviceName
     * @return mixed|ConfigurationService|SitemapService|GanalyticsService|RequestinfoService|CompanyService|GsearchService|FacebookService|GmapService|PlacesService|ExcelService|MarketinginfoService|AddressService|OfficeService|SearchServic|PropertiesService|PropertyService|AgentService|AgentsService|BeanstalkdService|ContactsService
     */
    public function getSqlServiceClass($serviceName)
    {
        if (array_key_exists($serviceName, $this->serviceClassArray))
            return $this->serviceClassArray[$serviceName];

        if (strpos($serviceName, "__") === 0) // somewhere along the way the variable become the define
        {
            $globalDefines = get_defined_constants(false);
            $serviceName = $globalDefines[$serviceName];
        }


        $folderName = WFactory::getHelper()->splitAtUpperCase($serviceName);
        $folderName = lcfirst($folderName[0]);
        $fileName = lcfirst($serviceName);
        $className = $serviceName;
        $basePath = JPATH_ROOT . "/libraries/webportal/services/$folderName/";

        $filePath = "$basePath$fileName.php";
        require_once $filePath;

        $result = new $className();
        $this->serviceClassArray[$serviceName] = $result;
        return $result;

    }


    /**
     * @return JDatabaseQuery
     */
    public function getQuery()
    {
        return $this->getDbo()->getQuery(true);
    }

    public function select($query)
    {
        if (!is_string($query)) //JDatabaseQuery type!
        {
            $query = (string)$query;
        }

        $db = $this->getDbo();
        try {
            $db->setQuery($query);
            $returnValue = $db->loadAssocList();
        } catch (RuntimeException $e) {

            WFactory::getLogger()->fatal("failed on select statement: \r\n$query\r\n" . $e->getMessage());
        }


        return $returnValue;
    }

    /**
     * @param $queryOrObject
     * @return bool|mixed
     */
    public function update($queryOrObject)
    {
        $logger = WFactory::getLogger();
        //$query = str_replace("\r\n"," ");
        $db = $this->getDbo();
        if (is_string($queryOrObject)) {

            try {
                $db->setQuery($queryOrObject);
                $returnValue = $db->execute();

            } catch (RuntimeException $e) {
                WFactory::getLogger()->fatal("failed on update statement: " . $e->getMessage());
            }


            if (strpos($queryOrObject, "INSERT") === 0) {
                $connection = $db->getConnection();
                $insertId = $connection->insert_id;
                $logger->debug("Insert id:$insertId");
                return $insertId;
            }

            return $returnValue;
        } else {


            try {

                $returnValue = $db->updateObject($queryOrObject->getTableName(), $this->getDbObjectVariables($queryOrObject), $queryOrObject->getKey());
                return $returnValue;

            } catch (RuntimeException $e) {
                WFactory::getLogger()->fatal("failed on update statement:" . $e->getMessage());
            }


        }


    }

    /**
     * @param $query
     * @return mixed
     */
    public function delete($query)
    {
        $db = $this->getDbo();

        try {
            $db->setQuery($query);
            $returnValue = $db->execute();
            return $returnValue;
        } catch (RuntimeException $e) {
            WFactory::getLogger()->fatal("failed on delete statement: \r\n$query\r\n " . $e->getMessage());
        }


    }

    public function insert($ObjectOfDbType)
    {
        if (is_string($ObjectOfDbType)) {
            return $this->update($ObjectOfDbType);
        } else {

            try {

                $result = $this->getDbo()->insertObject($ObjectOfDbType->getTableName(), $this->getDbObjectVariables($ObjectOfDbType));


                $connection = $this->getDbo()->getConnection();
                $insertId = $connection->insert_id;
                WFactory::getLogger()->debug("Inserted new record in {$ObjectOfDbType->getTableName()} id: $insertId");
                return $insertId;

            } catch (RuntimeException $e) {
                WFactory::getLogger()->fatal("failed on inserting record in {$ObjectOfDbType->getTableName()} : " .
                    /*--------------------*/
                    "message : " . $e->getMessage());
            } catch (Exception $e) {
                WFactory::getLogger()->fatal("failed on inserting record in {$ObjectOfDbType->getTableName()} : " .
                    /*--------------------*/
                    "message : " . $e->getMessage());
            }
        }

    }


    private function getDbObjectVariables($dbObject)
    {

        $variables = get_object_vars($dbObject);
        $dbObjectForInsertUpdate = new stdClass();

        $originalDBClass = $this->getDbClass(get_class($dbObject));
        $originalDBVariables = get_object_vars($originalDBClass);


        foreach ($variables as $variable => $value) {
            if (strpos($variable, "__") === 0 && array_key_exists($variable, $originalDBVariables)) // they start with __ and MUST be in the original db class. otherwise insert will fail with "unknown column" error
            {
                $dbvariableName = str_replace("__", "", $variable);
                $dbObjectForInsertUpdate->{$dbvariableName} = $dbObject->{$variable};
            }
        }
        return $dbObjectForInsertUpdate;
    }

    public function getMySqlDateTime($dateTime = null)
    {
        if ($dateTime == null) {
            $dateTime = time();
        } else
            $dateTime = strtotime($dateTime);
        $date = date("Y-m-d H:i:s", $dateTime);
        return $date;
    }

    public static function returnDeletedRecord()
    {
        $config = WFactory::getConfig()->getWebportalConfigurationArray();
        //the reason for this strange statement is returnDeletedRecord might not even be present :P
        //in whcih case return false
        return $config["returnDeletedRecord"] === true ? true : false;
    }

    public static function allowPortalV2CompatibleRouting()
    {
        $config = WFactory::getConfig()->getWebportalConfigurationArray();
        if (array_key_exists('portalV2Compatibility', $config)) {
            return $config["portalV2Compatibility"]['enableV2CompatibleRouting']=== true ? true : false;
        }
        return false;
    }

}
