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
 * Controller for property
 */
class WebportalControllerProperty extends WebportalController
{
    function __construct()
    {
        parent::__construct();
        WFactory::getLogger()->debug("Property service called");
    }

    /**
     * For backward compatibility with saga
     * @deprecated
     */
    function getProperty()
    {

        //now set up for api call
        JFactory::getApplication()->input->set('service', 'property');
        JFactory::getApplication()->input->set('data', 'getDetailForSaga');
        JFactory::getApplication()->input->set('version', 'v1');


        require_once 'api.php';

        $apiController = new WebportalControllerApi();
        $apiController->service();

    }


}