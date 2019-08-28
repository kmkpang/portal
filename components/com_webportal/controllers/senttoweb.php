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
 * Controller for senttoweb
 */
class WebportalControllerSenttoweb extends WebportalController
{
    function __construct()
    {
        parent::__construct();
        WFactory::getLogger()->debug("Senttoweb service called");
    }

    function display()
    {
        return $this->service();
    }

    function service()
    {
        $task = JFactory::getApplication()->input->getCmd('task');
        $version = JFactory::getApplication()->input->getCmd('version', 'v1');

        ob_clean();
        $output = $this->$version($task);
        echo $output;
        ob_flush();
        exit();


    }



    private function v1($fullTask)
    {
        return WFactory::getServices()->getWebservice($fullTask,'websending');
    }


}