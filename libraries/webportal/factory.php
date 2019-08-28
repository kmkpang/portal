<?php
/**
 * Created by PhpStorm.
 * User: khan
 * Date: 12/27/13
 * Time: 11:23 AM
 */
defined('JPATH_PLATFORM') or die;

define('JPATH_LIBRARY_WEBPORTAL', JPATH_LIBRARIES . DS . 'webportal');
define('JPATH_LIBRARY_WEBPORTAL_SERVICES', JPATH_LIBRARY_WEBPORTAL . DS . 'services');


/**
 * This class provides the access to many different necessary stuffs all around
 * This is loaded autometically by bootstrapper plugin ( look for it in system folder )
 * Class WFactory
 */
abstract class WFactory
{

    /**
     * @var WebportalLogger
     */
    private static $logger;
    private static $configuration;
    private static $publicip;
    private static $services;
    private static $helper;
    private static $fileManager;

    public static function getPublicIp()
    {
        if(defined('__ISUNITTEST') && __ISUNITTEST)
            return '';

        if (!self::$publicip) {
            $externalContent = file_get_contents('http://checkip.dyndns.com/');
            preg_match('/Current IP Address: ([\[\]:.[0-9a-fA-F]+)</', $externalContent, $m);
            self::$publicip = $m[1];
        }

        return self::$publicip;
    }


    /**
     * Gets the logger for the webportal
     * The log configuration is in  /var/www/privateportal/libraries/webportal/logger/log4php_configuration
     *
     * @return null|WebportalLogger
     */
    public static function getLogger()
    {
        if (!self::$logger) {
            JLoader::import('webportal.logger.logger');
            self::$logger = WebportalLogger::getInstance();
        }

        return self::$logger;
    }

    /**
     * Get the configuration stuff
     * @return null|WebportalConfiguration
     */
    public static function getConfig()
    {
        if (!self::$configuration) {
            JLoader::import('webportal.configuration.webportalconfig');
            self::$configuration = WebportalConfiguration::getInstance();
        }

        return self::$configuration;
    }




    /**
     * @return null|WebportalServices
     */
    public static function getServices()
    {
        if (!self::$services) {
            JLoader::import('webportal.services.webportalservices');
            self::$services = WebportalServices::getInstance();
        }

        return self::$services;
    }

    /**
     * Just a wrapper around WFactory::getServices()->getSqlService();
     *
     * @return WebportalSqlServiceProvider
     */
    public static function getSqlService()
    {
        return WFactory::getServices()->getSqlService();
    }


    /**
     * @return null|WebportalHelper
     */
    public static function getHelper()
    {
        if (!self::$helper) {
            JLoader::import('webportal.helper');
            self::$helper = WebportalHelper::getInstance();
        }

        return self::$helper;
    }

    /**
     * @param $msg
     * @throws PortalException
     */
    public static function throwPortalException($msg)
    {
        require_once JPATH_LIBRARY_WEBPORTAL . DS . "portalException.php";

        throw new PortalException($msg);

    }


    /**
     * @return null|WebportalFileManager
     */
    public static function getFileManager()
    {
        if (!self::$fileManager) {
            JLoader::import('webportal.fileManagement.filemanager');
            self::$fileManager = WebportalFileManager::getInstance();
        }

        return self::$fileManager;
    }



}