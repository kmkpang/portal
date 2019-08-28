<?php
/**
 * Created by PhpStorm.
 * User: khan
 * Date: 12/27/13
 * Time: 12:05 PM
 */

defined('_JEXEC') or die ("Restricted area");

require_once JPATH_ROOT . DS . "webportal.configuration.php";

class WebportalConfiguration
{

    var $config;

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
     * Gets full configuration
     * @return array
     */
    public function getWebportalConfigurationArray()
    {

        if (!$this->config) {
            global $webportalConfiguration;
            $this->config = $webportalConfiguration;
        }

        return $this->config;
    }




}