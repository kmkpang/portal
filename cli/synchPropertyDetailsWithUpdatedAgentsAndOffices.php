<?php
/**
 * @package        Joomla.Site
 * @copyright    Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */
if (!defined('_JEXEC'))
    define('_JEXEC', 1);

define('__ISUNITTEST', false);

error_reporting(1);


define('DS', DIRECTORY_SEPARATOR);

ini_set('display_errors', '1'); // only for xampp , because it screws up the display
ini_set('max_execution_time', '0');
ini_set('memory_limit', '2048M'); // required only on linux and ubuntu !

// We are a valid entry point.
const _JEXEC = 1;

// Load system defines
if (file_exists(dirname(__DIR__) . '/defines.php')) {
    require_once dirname(__DIR__) . '/defines.php';
}

if (!defined('_JDEFINES')) {
    define('JPATH_BASE', dirname(__DIR__));
    require_once JPATH_BASE . '/includes/defines.php';
}

/**
 * Constant that is checked in included files to prevent direct access.
 * define() is used in the installation folder rather than "const" to not error for PHP 5.2 and lower
 */
define('_JEXEC', 1);

if (file_exists(__DIR__ . '/defines.php')) {
    include_once __DIR__ . '/defines.php';
}

if (!defined('_JDEFINES')) {
    define('JPATH_BASE', __DIR__);
    require_once JPATH_BASE . '/includes/defines.php';
}
// Get the framework.
require_once JPATH_LIBRARIES . '/import.legacy.php';

// Bootstrap the CMS libraries.
require_once JPATH_LIBRARIES . '/cms.php';
// Framework import
require_once JPATH_BASE . '/includes/framework.php';
require_once JPATH_BASE . '/cliParser.php';


// Instantiate the application.
$app = JFactory::getApplication('site');

ob_start(); // Start output buffering

// Execute the application.
$app->execute();

$list = ob_get_contents(); // Store buffer in variable

ob_end_clean(); // End buffering and clean up

/**
 * This script will fetch the update information for all extensions and store
 * them in the database, speeding up your administrator.
 *
 * @package  Joomla.Cli
 * @since    2.5
 */
class Synchpropertydetailswithupdatedagentsandoffices extends JApplicationCli
{
    /**
     * Entry point for the script
     *
     * @return  void
     *
     * @since   2.5
     */
    public function doExecute()
    {

        WFactory::getLogger()->debug("************* Synchpropertydetailswithupdatedagentsandoffices  start **********************");

        /**
         * @var $properties PortalPortalPropertiesSql
         *
         */
        $properties = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_PROPERTIES_SQL);
        $properties->__is_deleted = 0;

        $properties = $properties->loadDataFromDatabase(false);

        $total = count($properties);

        $agents = array();
        $offices = array();

        WFactory::getLogger()->info("Updating $total properties", __LINE__, __FILE__);


        foreach ($properties as $p) {


            /**
             * @var $p PortalPortalPropertiesSql
             * @var $agent AgentModel
             */

            if (!array_key_exists($p->__sale_id, $agents))
                $agents[$p->__sale_id] = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_AGENT)->getAgent($p->__sale_id);

            $agent = $agents[$p->__sale_id];

            if (!array_key_exists($p->__office_id, $offices))
                $offices[$p->__office_id] = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_OFFICE)->getOffice($p->__office_id);

            $office = $offices[$p->__office_id];

            $p->__sales_agent_image = $agent->image_file_path;
            $p->__sales_agent_email = $agent->email;
            $p->__sales_agent_full_name = str_replace("  ", "", "{$agent->first_name} {$agent->middle_name} {$agent->last_name}");
            $p->__sales_agent_mobile_phone = $agent->mobile;
            $p->__sales_agent_office_phone = $agent->phone;

            $p->__office_email = $office['email'];
            $p->__office_logo_path = $office['logo'];
            $p->__office_phone = $office['phone'];





            WFactory::getSqlService()->update($p);

            WFactory::getLogger()->debug("Updated property {$p->__id} with agent and office information");

        }

        WFactory::getLogger()->debug("************* Synchpropertydetailswithupdatedagentsandoffices  end **********************");

    }

    function simpleEcho($msg)
    {
        echo "$msg\r\n";
    }


}


JApplicationCli::getInstance('Synchpropertydetailswithupdatedagentsandoffices')->execute();
