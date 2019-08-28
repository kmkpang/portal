<?php
/**
 * @version     1.0.0
 * @package     com_fingi
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Shrouk Khan <shroukkhan@fingi.com> - http://www.fingi.com
 */

defined('_JEXEC') or die;

//do some definition
define('JPATH_COMPONENT_WEBPORTAL', JPATH_BASE . DS . 'components' . DS . 'com_webportal');
define('JPATH_COMPONENT_WEBPORTAL_CONTROLLERS', JPATH_COMPONENT_WEBPORTAL . DS . 'controllers');
define('JPATH_COMPONENT_WEBPORTAL_MODELS', JPATH_COMPONENT_WEBPORTAL . DS . 'models');

// Include dependancies
jimport('joomla.application.component.controller');

defined('_JEXEC') or die('Restricted access');

// Require the base controller
require_once(JPATH_COMPONENT . DS . 'controller.php');


//since i am too lazy to go into every portal and update it by hand !!!
WFactory::getHelper()->updateWebportalConfigurationJavascript();


//Require apecific controller if requested
if ($controller = JFactory::getApplication()->input->getWord('controller')) {
    $path = JPATH_COMPONENT_WEBPORTAL_CONTROLLERS . DS . $controller . '.php';
    if (file_exists($path)) {
        require_once $path;
    } else {
        $controller = '';
    }
}

//Create the controller
$classname = 'WebportalController' . ucfirst($controller);
$controller = new $classname(array('default_task' => 'display'));
$controller->execute(JFactory::getApplication()->input->getCmd('task'));
//Redirect if set by the controller
$controller->redirect();

