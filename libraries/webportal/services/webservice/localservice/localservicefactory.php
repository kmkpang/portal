<?php

/**
 * Created by PhpStorm.
 * User: khan
 * Date: 5/10/14
 * Time: 12:43 PM
 */
class WebportalLocalserviceFactory
{

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
     * @return WebportalLocalserviceFactory
     */
    public static function getInstance()
    {
        if (!isset(static::$instance)) {
            static::$instance = new static;

        }
        return static::$instance;
    }


    public function execute($task, &$originalParams)
    {



        $task = explode('.', $task);
        $class = $task[0];
        $task = $task[1];

        $classPath = JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . $class . DS . "{$class}Service.php";
        $className = ucfirst($class) . "Service";

        require_once $classPath;
        $class = new $className();


        $param = file_get_contents("php://input");

        $param = stripslashes($param);

        $source = $_SERVER['REMOTE_ADDR'];

        if(strpos($task,"search") !== false)
        {
            $x = $y;
        }

        $temp = $param;
        if (!empty($param)) {

            if (WFactory::getHelper()->isJson($param)) {
                $param = json_decode($param);

                if ($param === null) {
                    WFactory::getLogger()->error("Invalid json [ $temp  ] supplied for $className::$task() from $source");
                }


            } else if (strpos($param, '&') !== false && strpos($param, '=') !== false) {
                $param = explode('&', $param);

                $t = array();
                foreach ($param as $p) {
                    $p = explode('=', $p);
                    $t[$p[0]] = $p[1];
                }

                $param = $t;
                $temp = $param;
            }

            if (is_array($param)) {

                $param = new stdClass();
                foreach ($temp as $key => $value) {
                    $param->$key = $value;
                }
            }

        } else {
            WFactory::getLogger()->warn("Empty json param received from remote location $source , trying with joomla input now");
            $param = JFactory::getApplication()->input->getArray();


        }

        WFactory::getLogger()->debug("Calling $className::$task() with \r\n " . json_encode($param, JSON_PRETTY_PRINT));


        $originalParams = $param;
        return $class->$task($param);

    }
}