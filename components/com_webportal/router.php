<?php
/**
 * @version     1.0.0
 * @package     com_webportal
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Shrouk Khan <shroukkhan@softverk.is> - http://www.softverk.is
 */

// No direct access
defined('_JEXEC') or die;

/**
 * @param    array    A named array
 * @return    array
 */
function WebportalBuildRoute(&$query)
{
    $segments = array();
    $params = JComponentHelper::getParams('com_webportal');
    $advanced = $params->get('sef_advanced_link', 0);

    if (isset($query['task'])) {
        $segments[] = $query['task'];
        unset($query['task']);
    }
    if (isset($query['id'])) {
        $segments[] = $query['id'];
        unset($query['id']);
    }
    if (isset($query['controller'])) {
        $segments[] = $query['controller'];
        unset($query['controller']);
    }
    if (isset($query['view'])) {
        $segments[] = $query['view'];
        unset($query['view']);
    }
    if (isset($query['property-id'])) {
        $fullPropertyLink = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTY)->getJRouteFormattedPropertyRoute($query['property-id']);
        $segments[] = $fullPropertyLink;
        unset($query['property-id']);
    }
    if (isset($query['office_id'])) {

        $officeName = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_OFFICE)->getJRouteFormattedOfficeName($query['office_id']);
        $segments = array();
        $segments[] = $officeName;
        unset($query['office_id']);
        $query['Itemid'] = WFactory::getConfig()->getWebportalConfigurationArray()['officeHiddenItemId']; //JFactory::getApplication()->getMenu()->getDefault(JFactory::getLanguage()->getTag())->id; //make this home page!so that..you know it shows right after URL
    }
    if (isset($query['agent_id'])) {

        $agentName = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_AGENTS)->getJRouteFormattedAgentName($query['agent_id']);
        $segments = array();
        $segments[] = $agentName;
        unset($query['agent_id']);
        $query['Itemid'] = WFactory::getConfig()->getWebportalConfigurationArray()['agentHiddenItemId'];
        //JFactory::getApplication()->getMenu()->getDefault(JFactory::getLanguage()->getTag())->id; //make this home page!so that..you know it shows right after URL
    }

    if (isset($query['layout'])) {
        $segments[] = $query['layout'];
        unset($query['layout']);
    }

    return $segments;
}

/**
 * @param    array    A named array
 * @param    array
 *
 * Formats:
 *
 * /api/v1/properties/search/?x=data
 *
 * index.php?/controller/task/?x=data
 * @return array
 */
function WebportalParseRoute($segments)
{
    $vars = array();
    $params = JComponentHelper::getParams('com_webportal');
    $advanced = $params->get('sef_advanced_link', 0);
    /**
     *   NOTE: this following block is to redirect pages like:
     *         /var/www/remax-thailand/cli/en/property-area-guide to
     *         /property-area-guide
     */
    if (in_array('var', $segments) && in_array('www', $segments)) {

        $currentUrl = JUri::getInstance()->toString();
        WFactory::getLogger()->warn("Joomla route fuckup : " . $currentUrl);
        $allLangs = WFactory::getHelper()->getAllLang();

        foreach ($allLangs as $l) {
            $l = explode('-', $l);
            $l = $l[0];
            $tagToSearch = "/$l/";
            if (strpos($currentUrl, $tagToSearch) !== false) {
                $requestedPath = substr($currentUrl, strpos($currentUrl, $tagToSearch) + 1);
                $fullPath = JUri::base() . $requestedPath;
                WFactory::getLogger()->warn("Redirecting : $currentUrl -> $fullPath");
                JFactory::getApplication()->redirect($fullPath);
            }
        }

        // JFactory::getApplication()->redirect()
    }


    $count = count($segments);
    $requestedPath = str_replace(JUri::base(), "", JUri::current());
    $fullSegment = array_filter(explode('/', $requestedPath));
    $requestedView = $fullSegment[0];
    $lang = WFactory::getHelper()->getCurrentlySelectedLanguage(false);
    $langHalf = WFactory::getHelper()->getCurrentlySelectedLanguage(true);

//$lang = WFactory::getHelper()->getCurrentlySelectedLanguage();
//   // $loopStart = 0;
    if (array_key_exists($requestedView, WFactory::getHelper()->getAllLang())) {
        JFactory::getLanguage()->setLanguage(WFactory::getHelper()->getAllLang()[$requestedView]);
        $requestedView = $fullSegment[1];
        // $loopStart = 1;
    }


    for ($i = 0; $i < $count; $i++) {
        $item = trim($segments[$i]);
        if ($i == 0) {

            if ($item == 'addproperty') {
                $vars['view'] = 'addproperty';
                $vars["layout"] = $segments[$i + 1];
                break;
            }

            //preg_match('/^v[0-9]/', $item, $matches); //version
            if ($requestedView == "api") {
                $vars["controller"] = "api";
                $vars["version"] = $item;
                $vars["service"] = $segments[$i + 1];
                $vars["task"] = "service";
                $vars["data"] = $segments[$i + 2];

                WFactory::getHelper()->setCurrentPage('api');

                break;
            } else if ($requestedView == "property") {
                $item = explode(":", $item); // rest as address etc SEO crap
                if (is_numeric($item[0])) {
                    $vars["property-id"] = $item[0];
                } else {
                    //may be it is 32perty ?????
                    WFactory::getLogger()->info("Checking if property matches with {$segments[$count-1]}");
                    $propertyId = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTY)->checkIfRouteIsProperty($segments[$count - 1]);

                    if (intval($propertyId) == 0) {
                        WFactory::getHelper()->redirect404();
                    } else
                        $vars["property-id"] = $propertyId;

                }


                $vars["view"] = "property";

                $v2_v3_id = 0;
                WFactory::getHelper()->doSanityCheck(PROPERTY, $vars["property-id"], $v2_v3_id);
                if ($v2_v3_id > 0) {
                    $vars["property-id"] = $v2_v3_id;
                }

                WFactory::getHelper()->setCurrentPage('property');
                break;
            } else if ($requestedView == "property-print") {
                $item = explode(":", $item); // rest as address etc SEO crap
                if (is_numeric($item[0])) {
                    $vars["property-id"] = $item[0];
                } else {
                    //may be it is /property-print/property-print ?????
                    WFactory::getLogger()->info("Checking if property matches with {$segments[$count-1]}");
                    $propertyId = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTY)->checkIfRouteIsProperty($segments[$count - 1]);
                    if (intval($propertyId) == 0) {
                        WFactory::getHelper()->redirect404();
                    } else
                        $vars["property-id"] = $propertyId;

                }
                $vars["view"] = "property-print";

                WFactory::getHelper()->setCurrentPage('property-print');

                WFactory::getHelper()->doSanityCheck(PROPERTY, $item[0]);

                break;
            } else { //could be office name or agent name!
                WFactory::getLogger()->info("Checking if Office id matches with $item");
                $officeId = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_OFFICE)->checkIfRouteIsOffice($item);
                if ($officeId !== null) {
                    if ($requestedView === 'office') //a way to remove the menu item alias from the url
                    {

                        WFactory::getHelper()->setCurrentPage('office');

                        if ($lang == JFactory::getLanguage()->getDefault())
                            JFactory::getApplication()->redirect(JUri::base() . $item);
                        else
                            JFactory::getApplication()->redirect(JUri::base() . "$langHalf/$item");
                    }

                    WFactory::getLogger()->debug("Found $item matching office name $officeId, routing to office detail page...");
                    $vars["office_id"] = $officeId;
                    $vars["view"] = "offices";

                    //this following line allows you to acess office like this: site.name.com/officename
                    $vars["Itemid"] = WFactory::getConfig()->getWebportalConfigurationArray()['officeHiddenItemId'];

                    WFactory::getHelper()->doSanityCheck(OFFICE, $officeId);

                    break;
                }

                WFactory::getLogger()->info("Checking if Agent id matches with $item");
                $agentId = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_AGENTS)->checkIfRouteIsAgent($item);
                if ($agentId !== null) {
                    if ($requestedView === 'agent') {
                        if ($lang == JFactory::getLanguage()->getDefault())
                            JFactory::getApplication()->redirect(JUri::base() . $item);
                        else
                            JFactory::getApplication()->redirect(JUri::base() . "$langHalf/$item");
                    }
                    WFactory::getLogger()->debug("Found $item matching agent name $agentId, routing to agent detail page...");
                    $vars["agent_id"] = $agentId;
                    $vars["view"] = "agents";

                    WFactory::getHelper()->setCurrentPage('agent');

                    //this following line allows you to acess agent like this: site.name.com/agentname
                    $vars["Itemid"] = WFactory::getConfig()->getWebportalConfigurationArray()['agentHiddenItemId'];

                    WFactory::getHelper()->doSanityCheck(AGENT, $agentId);

                    break;
                }

                //if those didnt match..how about property id ??? huh huh huh...? :)

                WFactory::getLogger()->info("Checking if property matches with $item");
                $propertyId = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTY)->checkIfRouteIsProperty($item);

                WFactory::getHelper()->doSanityCheck(PROPERTY, $propertyId);

                if ($propertyId !== null) {

                    $vars["property-id"] = $propertyId;
                    $vars["view"] = "property";
                    $vars["Itemid"] = WFactory::getConfig()->getWebportalConfigurationArray()['propertiesItemId'];

                    WFactory::getHelper()->setCurrentPage('property');

                    //http://localhost/softverk-webportal-remaxth/th/blablahlblah-4652

//                    if ($lang == JFactory::getLanguage()->getDefault())
//                        JFactory::getApplication()->redirect(JUri::base() . "property/$propertyId");
//                    else
//                        JFactory::getApplication()->redirect(JUri::base() . "$langHalf/property/$propertyId");

                }


            }


        }

        //other stuffs...

    }


    return $vars;
}
