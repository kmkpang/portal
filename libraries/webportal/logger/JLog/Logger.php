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


/**
 * Class Logger
 * @codeCoverageIgnore
 */
class Logger
{
    function  __construct()
    {
        jimport('joomla.log.log');
        JLog::addLogger(
            array(
                // Sets file name
                'text_file' => 'portal_error_jlog.log',
                // Sets the format of each line
                'text_entry_format' => '{PRIORITY} {MESSAGE}'
            ),
            // Sets critical and emergency log level messages to be sent to the file
            JLog::CRITICAL + JLog::EMERGENCY + JLog::ERROR,
            // The log category which should be recorded in this file
            array('softverk_webportal')
        );
        JLog::addLogger(
            array(
                // Sets file name
                'text_file' => 'portal_log_jlog.log',
                // Sets the format of each line
                'text_entry_format' => '{PRIORITY} {MESSAGE}'
            ),
            // Sets critical and emergency log level messages to be sent to the file
            JLog::ALL,
            // The log category which should be recorded in this file
            array('softverk_webportal')
        );


    }

    function debug($msg, $auxData)
    {
        JLog::add($msg, JLog::DEBUG, $auxData);
    }

    function info($msg, $auxData)
    {
        if($auxData==null)
            $auxData='';
        JLog::add($msg, JLog::INFO, $auxData);
    }

    function error($msg, $auxData)
    {
        JLog::add($msg, JLog::ERROR, $auxData);
    }

    function fatal($msg, $auxData)
    {
        JLog::add($msg, JLog::CRITICAL, $auxData);
    }

    function warn($msg, $auxData)
    {
        JLog::add($msg, JLog::WARNING, $auxData);
    }
}