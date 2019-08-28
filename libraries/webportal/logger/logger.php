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
//debug 5 , info 4 , warn 3 , error 2, fatal 1 , Off 0

define('__LOG_LEVEL_DEBUG', "DEBUG");
define('__LOG_LEVEL_INFO', "INFO");
define('__LOG_LEVEL_WARN', "WARN");
define('__LOG_LEVEL_ERROR', "ERROR");
define('__LOG_LEVEL_FATAL', "FATAL");

/**
 * Class WebportalLogger
 * @codeCoverageIgnore
 */
class WebportalLogger
{
    /**
     * @var Logger
     */
    var $logger;
    var $logLevel;
    var $colorEnabled;

    static $colors = array(
        __LOG_LEVEL_DEBUG => null,
        __LOG_LEVEL_INFO => "green",
        __LOG_LEVEL_WARN => "yellow",
        __LOG_LEVEL_ERROR => "brown",
        __LOG_LEVEL_FATAL => "bold_red",
    );

    //debug 5 , info 4 , warn 3 , error 2, fatal 1 , Off 0


    protected static $instance = null;

    protected function __construct()
    {
        //Thou shalt not construct that which is unconstructable!

    }

    protected function __clone()
    {
        //Me not like clones! Me smash clones!
    }


    public static function getInstance()
    {
        if (!isset(static::$instance)) {
            static::$instance = new static;

        }
        return static::$instance;
    }

    function logEmail($type, $subject, $propertyId, $sender, $receiver, $message, $fullMail)
    {
        if (!is_string($fullMail))
            $fullMail = json_encode($fullMail);


        /**
         * @var $emailDbClass PortalPortalPropertyEmailLogSql
         */
        $emailDbClass = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_PROPERTY_EMAIL_LOG_SQL);
        $emailDbClass->__datetime = WFactory::getSqlService()->getMySqlDateTime();
        $emailDbClass->__fromip = $_SERVER['REMOTE_ADDR'];
        $emailDbClass->__type = $type;
        $emailDbClass->__subject = $subject;
        $emailDbClass->__property_id = $propertyId;
        $emailDbClass->__sender_email = $sender;
        $emailDbClass->__receiver_email = $receiver;
        $emailDbClass->__message = $message;
        $emailDbClass->__fullemail = $fullMail;

        return WFactory::getSqlService()->insert($emailDbClass);

    }

    function debug($msg, $line = null, $file = null, $auxData = null)
    {


        if ($this->logger == null)
            $this->initialize();

        if ($this->logLevel < 5)
            return;

        $this->logger->debug($this->prepareMsg($msg, $line, $file), $auxData);
    }

    function info($msg, $line = null, $file = null, $auxData = null)
    {


        if ($this->logger == null)
            $this->initialize();


        if ($this->logLevel < 4)
            return;

        $this->logger->info($this->prepareMsg($msg, $line, $file), $auxData);
    }

    function warn($msg, $line = null, $file = null, $auxData = null)
    {


        if ($this->logger == null)
            $this->initialize();

        if ($this->logLevel < 3)
            return;


        $this->logger->warn($this->prepareMsg($msg, $line, $file), $auxData);
    }

    function error($msg, $line = null, $file = null, $auxData = null)
    {


        if ($this->logger == null)
            $this->initialize();

        if ($this->logLevel < 2)
            return;

        $this->logger->error($this->prepareMsg($msg, $line, $file, true), $auxData);
    }

    /**
     * This also triggers a user error , so calling this will effectively close down the program!
     * @param $msg
     * @throws Exception
     */
    function fatal($msg, $line = null, $file = null)
    {


        if ($this->logger == null)
            $this->initialize();

        if ($this->logLevel < 1)
            return;

        $currentUrl = JUri::getInstance()->toString();
        $msg = "[$currentUrl] $msg ";
        $this->logger->fatal($this->prepareMsg($msg, $line, $file, true));

    }


    private function prepareMsg($msg, $line, $file, $fullStack = false)
    {
        $user =& JFactory::getUser();
        $client = $_SERVER['REMOTE_ADDR'];

        $e = new Exception();
        $trace = $e->getTrace();

        $this_one = $trace[0]; //prepareMsg
        $before_this = $trace[1]; //other methods in this class!

        $original_caller = $trace[2]; //the original caller in some other file / class
        $function = $original_caller["class"] . $original_caller["type"] . $original_caller["function"];

        if ($fullStack) {
            for ($i = count($trace) - 1; $i > 3; $i--) {
                $caller = $trace[$i];
                $f = $caller["class"] . $caller["type"] . $caller["function"];
                $function .= "\r\n$f";
            }
        }

        $lineFile = !empty($file) ? $file . "-" : "" . !empty($line) ? $line : "";
        if (!empty($lineFile)) {
            $lineFile = str_replace(JPATH_BASE, "", $lineFile);
            $lineFile = '[' . trim($lineFile) . ']';

        }

        $msg = "[$function][" . $user->username . "/" . $client . "]$lineFile " . $msg;
        return $msg;
    }

    /**
     * Initializes logger based on logging section in the /var/www/softverk-webportal/webportal.configuration.php file
     *
     */
    public function initialize()
    {

        register_shutdown_function(array($this, 'fatalErrorCapture'));

        //what logger is chosen?

        $logConfig = WFactory::getConfig()->getWebportalConfigurationArray();

        $logConfig = $logConfig["logging"];

        $selectedLogger = $logConfig["logger"];

        $this->logLevel = $logConfig["loglevel"];

        $this->colorEnabled = $logConfig["enableColor"];

        if ($this->colorEnabled) {
            require_once 'ecsapeColors.php';
        }

        if ($selectedLogger == "log4php") {
            require_once 'log4php' . DS . 'Logger.php';
            $configFile = $logConfig["log4php"]["configFile"];
            Logger::configure($configFile);
            $this->logger = Logger::getLogger("webportalLogger");
        }
        if ($selectedLogger == "jlog") {
            require_once 'JLog' . DS . 'Logger.php';
            $this->logger = new Logger();
        }


        return true;


    }

    /**
     * Called when a system shutdown is called
     */
    function fatalErrorCapture()
    {

        $error = error_get_last();

        if (!is_null($error)) {

            if ($error["type"] == E_ERROR
                || $error["type"] == E_PARSE
                || $error["type"] == E_CORE_ERROR
                || $error["type"] == E_COMPILE_ERROR
            ) {

                WFactory::getLogger()->fatal("{$error['file']}:{$error['line']} --> {$error['message']}");
            }

        }
    }

    public function cleanLogFiles()
    {
        //apache logs
        $logFolder = "/var/log/apache2";

        $command = "ls $logFolder | grep \"\\.log\\.\" ";
        $logFiles = shell_exec($command);
        $logFiles = explode("\n", $logFiles);

        $finalResult = true;
        foreach ($logFiles as $log) {
            $log = trim($log);
            if (!WFactory::getHelper()->isNullOrEmptyString($log)) {
                $file = "$logFolder/$log";
                $result = unlink($file);
                if ($result) {
                    $this->debug("[cleanLogFiles] deleted $file");
                } else
                    $this->warn("[cleanLogFiles] FAILED to delete $file");

                $finalResult &= $result;
            }
        }

        //joomla logs
        $logFolder = JPATH_BASE . DS . "logs";
        $command = "ls $logFolder | grep \"\\.log\\.\" ";
        $logFiles = shell_exec($command);
        $logFiles = explode("\n", $logFiles);


        foreach ($logFiles as $log) {
            $log = trim($log);
            if (!WFactory::getHelper()->isNullOrEmptyString($log) && strpos($log, ".php") === false) {
                $file = "$logFolder/$log";

                $result = unlink($file);
                if ($result) {
                    $this->debug("[cleanLogFiles] deleted $file");
                } else
                    $this->warn("[cleanLogFiles] FAILED to delete $file");

                $finalResult &= $result;
            }
        }


        return $finalResult;


    }

}


