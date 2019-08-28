<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_webportal
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');//god damn windows

define('JPATH_COMPONENT_WEBPORTAL', JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_webportal');
define('JPATH_COMPONENT_WEBPORTAL_CONTROLLERS', JPATH_COMPONENT_WEBPORTAL . DS . 'controllers');
define('JPATH_COMPONENT_WEBPORTAL_MODELS', JPATH_COMPONENT_WEBPORTAL . DS . 'models');

//----------------- boot strap -------------------------
if (!class_exists('Wfactory')) {
    JLoader::import('webportal.factory');
}


///home/khan/www/softverk-webportal-remaxth/webportal.configuration.php
///home/khan/www/softverk-webportal-remaxth/templates/remax-th/controllers/temp_jscript.php
///home/khan/www/softverk-webportal-remaxth/administrator/components/com_webportal/temp_jscript_bkend.php
require_once JPATH_ROOT . "/webportal.configuration.php";
require_once JPATH_ROOT . "/templates/generic/controllers/temp_jscript.php";
require_once JPATH_ROOT . "/administrator/components/com_webportal/temp_jscript_bkend.php";

$doc =  & JFactory::getDocument();
$uri = JUri::getInstance();
$baseWithLang = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port', 'path'));


$doc->addScriptDeclaration("var documentRoot = \"" . $baseWithLang . "\";");
$doc->addScriptDeclaration("var documentRootRaw = \"" . JUri::root() . "\";");
//  $doc->addScriptDeclaration("var documentUrl = \"" . WFactory::getHelper()->getCurrentUrl() . "\";");
$doc->addScriptDeclaration("var lang = \"" . JFactory::getLanguage()->getTag() . "\";");
$doc->addScriptDeclaration("var langHalf = \"" . WFactory::getHelper()->getCurrentlySelectedLanguage() . "\";");

///home/khan/www/softverk-webportal-remaxth/assets/css/app.min.css
///home/khan/www/softverk-webportal-remaxth/assets/bower_components/jquery-ui/themes/base/jquery-ui.min.css
///home/khan/www/softverk-webportal-remaxth/assets/bower_components/ngDialog/css/ngDialog.min.css
$doc->addStyleSheet(JUri::root() . 'assets/bower_components/jquery-ui/themes/base/jquery-ui.min.css');
$doc->addStyleSheet(JUri::root() . 'assets/bower_components/ngDialog/css/ngDialog.min.css');
$doc->addStyleSheet(JUri::root() . 'administrator/components/com_webportal/assets/webporta.admin.css');
///home/khan/www/softverk-webportal-remaxth/assets/css/XMLDisplay.css
$doc->addStyleSheet(JUri::root() . 'assets/css/XMLDisplay.css');

$doc->addScriptDeclaration("
            angular.module('webportal').factory('uri', function() {
                return {
                    getBase: function() {
                        return '" . JUri::root() . "';
                    }
                }
            });
            ");


//----------------- boot strap -------------------------


// Set some global property
$document = JFactory::getDocument();
$document->addStyleDeclaration('.icon-webportal {background-image: url(../media/com_webportal/images/tux-16x16.png);}');

//// Access check: is this user allowed to access the backend of this component?
//if (!JFactory::getUser()->authorise('core.manage', 'com_webportal')) {
//    return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
//}

WFactory::getHelper()->isAdminOrExit();


if ($controller = JFactory::getApplication()->input->getWord('controller')) {
    $path = JPATH_COMPONENT_WEBPORTAL_CONTROLLERS . DS . $controller . '.php';
    if (file_exists($path)) {
        require_once $path;
    } else {
        $controller = '';
    }
}

if (!WFactory::getHelper()->isNullOrEmptyString($controller)) {
//Create the controller
    $classname = 'WebportalController' . ucfirst($controller);
    $controller = new $classname(array('default_task' => 'display'));
} else
    $controller = JControllerLegacy::getInstance('Webportal');
$controller->execute(JFactory::getApplication()->input->getCmd('task'));


/*------- remove things that were added by joomla regarding jquery -------*/
$doc = JFactory::getDocument();

$headData = $doc->getHeadData();
$scripts = $headData['scripts'];

//scripts to remove, customise as required

unset($scripts[JUri::root(true) . '/media/jui/js/jquery.min.js']);
unset($scripts[JUri::root(true) . '/media/jui/js/jquery-noconflict.js']);
unset($scripts[JUri::root(true) . '/media/jui/js/jquery-migrate.min.js']);


$headData['scripts'] = $scripts;
$doc->setHeadData($headData);


// Redirect if set by the controller
$controller->redirect();
