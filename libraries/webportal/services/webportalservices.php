<?php
/**
 * Created by PhpStorm.
 * User: khan
 * Date: 4/24/14
 * Time: 5:37 PM
 */

defined('_JEXEC') or die ("Restricted area");

class WebportalServices
{


    /**
     * @var WebportalSqlServiceProvider
     */
    private $sqlService;


    /**
     * @var WebportalServices
     */
    protected static $instance = null;

    protected function __construct()
    {
        //Thou shalt not construct that which is unconstructable!
    }

    protected function __clone()
    {
        //Me not like clones! Me smash clones!
    }

    /**
     * @return null | WebportalConfiguration
     */
    public static function getInstance()
    {
        if (!isset(static::$instance)) {
            static::$instance = new static;
        }
        return static::$instance;
    }

    /**
     * @return WebportalSqlServiceProvider
     */
    public function getSqlService()
    {
        if ($this->sqlService == null) {
            ///var/www/softverk-webportal/libraries/webportal/services/sql/sqlServiceProvider.php
            JLoader::import('webportal.services.sql.sqlServiceProvider');
            $this->sqlService = WebportalSqlServiceProvider::getInstance();
        }

        return $this->sqlService;
    }

    /**
     * created in order to force an uniform return of php api functions across the service classes!
     * @param $success bool
     * @param $data array
     * @param $message string
     * @param bool $asJsonString
     * @return array|string
     */
    public static function getServiceResponse($success, $data, $message, $asJsonString = false)
    {
        $return = array("success" => $success, "data" => $data, "message" => $message);

        return $asJsonString ? json_encode($return) : $return;
    }

    /**
     * Wrapper around WebportalSqlServiceProvider::getSqlServiceClass
     * */
    /**
     * @param $serviceClassName
     * @return mixed|TemplateService|LocalityService|ConfigurationService| HtmlmailService|SenttowebService|CurrencyService|UsersService|SitemapService|ArticleService|GoogleapiService|GanalyticsService|MandrillService|RequestinfoService|CompanyService|PlacesService|GsearchService|FacebookService|GmapService|ExcelService|MarketinginfoService|AddressService|OfficeService|SearchService|PropertiesService|PropertyService|AgentService|AgentsService|BeanstalkdService|ContactsService
     */
    public static function getServiceClass($serviceClassName)
    {
        return WFactory::getServices()->getSqlService()->getSqlServiceClass($serviceClassName);
    }

    public function getWebservice($task, $serviceName = 'localservice')
    {
        $serviceName = strtolower($serviceName);


        JLoader::import("webportal.services.webservice.{$serviceName}.{$serviceName}factory");
        $className = "Webportal" . ucfirst($serviceName) . "Factory";

        /**
         * @var $factory WebportalWebsendingFactory|WebportalLocalserviceFactory
         */
        $factory = $className::getInstance();

        WFactory::getLogger()->debug("Executing service from $className");

        return $factory->execute($task);

    }


}