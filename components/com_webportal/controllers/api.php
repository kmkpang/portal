<?php
/**
 * Created by PhpStorm.
 * User: khan
 * Date: 5/10/14
 * Time: 12:19 PM
 */


jimport('joomla.application.component.controllerform');
jimport('joomla.error.error');

/**
 *
 */
require_once JPATH_COMPONENT_WEBPORTAL . DS . "controller.php";

/**
 * Controller for api
 */
class WebportalControllerApi extends WebportalController
{
    function __construct()
    {
        parent::__construct();
        WFactory::getLogger()->debug("Api service called");
    }


    function display()
    {
        return $this->service();
    }

    function service()
    {
        $task = JFactory::getApplication()->input->getCmd('service');
        $data = JFactory::getApplication()->input->getCmd('data');
        $version = JFactory::getApplication()->input->getCmd('version', 'v1');

        $fullTask = "{$task}.{$data}";

        WFactory::getLogger()->debug("[API-SERVICE]: " . JUri::current() . " called");

        ob_clean();
        $output = $this->$version($fullTask);

        if (!is_string($output)) {
            if (is_object($output))
                $output = get_object_vars($output);

            foreach ($output as &$o) {
                if (is_object($o))
                    $o = get_object_vars($o);
            }

            //$output = WFactory::getHelper()->safe_json_encode($output);
            //$output = iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($output));
            $output = json_encode($output);

        }

        echo $output;


        ob_flush();

        //WFactory::getLogger()->debug("[API-SERVICE]: " . JUri::current() . " returned:\r\n" . $output);

        exit();


    }


    private function v1($fullTask)
    {
        return WFactory::getServices()->getWebservice($fullTask);
    }


}