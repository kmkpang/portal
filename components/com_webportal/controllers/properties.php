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
 * Controller for properties
 */
class WebportalControllerProperties extends WebportalController
{
    function __construct()
    {
        parent::__construct();
        WFactory::getLogger()->debug("Properties service called");
    }

    /**
     * For backward compatibility with saga
     * @deprecated
     */
    function getAllPropertiesList()
    {

        //now set up for api call
        JFactory::getApplication()->input->set('service', 'properties');
        JFactory::getApplication()->input->set('data', 'getPropertiesListForSaga');
        JFactory::getApplication()->input->set('version', 'v1');


        require_once 'api.php';

        $apiController = new WebportalControllerApi();
        $apiController->service();

    }


}