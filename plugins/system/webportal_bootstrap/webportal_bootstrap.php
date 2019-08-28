<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.Debug
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

define('DS', DIRECTORY_SEPARATOR);


class plgSystemWebportal_Bootstrap extends JPlugin
{


    function onAfterInitialise()
    {
        //load the factory!
        JLoader::import('webportal.factory');

        WFactory::getLogger()->info("Bootstrap started");

        //$lang = JFactory::getLanguage()->getTag();

        //  $this->doRouting();


    }

    function onAfterRender()
    {
        if (JFactory::getApplication()->isAdmin()) {
            return;
        }

        $this->insertGoogleAnalyticCode();

    }

    function onExtensionAfterSave($moduleName, $params)
    {
        //$test = 1;

        //$x = $params->template == __TEMPLATE;
        if ($moduleName === "com_templates.style" && is_object($params)) {
            WFactory::getLogger()->info("Saving $moduleName [ {$params->template} ]");
            WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_TEMPLATE)->generateTemplateVariable($params->params,$params->template);
        }


    }

    function onContentPrepare($context, &$article, &$params, $page = 0)
    {
        if (JFactory::getApplication()->isAdmin()) {
            return;
        }

        $text = $this->applyDownloadAuthentication($article->text);


        $article->text = $text;

    }


    function applyDownloadAuthentication($buffer)
    {

        $regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
        if (preg_match_all("/$regexp/siU", $buffer, $matches)) {
            // $matches[2] = array of link addresses
            // $matches[3] = array of link text - including HTML code

            foreach ($matches[2] as $m) {
                if (strpos($m, 'images/agentsandbroker') !== false) {
                    //new changing !!!!!
                    $href = JUri::base() . 'api/v1/users/authenticateFile?&cache=' . uniqid() . '&file=' . urlencode(base64_encode($m));
                    $buffer = str_replace($m, $href, $buffer);
                }
            }

        }

        return $buffer;

    }


    function insertGoogleAnalyticCode()
    {
        if (defined('KHAN_HOME') && KHAN_HOME === true) // because google analytics does not work at my home..wtf!
            return true;


        $buffer = JResponse::getBody();


        /*
         *
           <script>
              (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
              (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
              m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
              })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

              ga('create', 'UA-53684118-2', 'auto');
              ga('send', 'pageview');

            </script>
         *
         * */


        $google_analytics_javascript = "";
        $google_analytics_javascript .= '<script type="text/javascript">';
        $google_analytics_javascript .= '
             (function(i,s,o,g,r,a,m){i[\'GoogleAnalyticsObject\']=r;i[r]=i[r]||function(){
              (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
              m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
              })(window,document,\'script\',\'//www.google-analytics.com/analytics.js\',\'ga\');
        ';

        $codes = WFactory::getConfig()->getWebportalConfigurationArray()['analytics'];

        foreach ($codes as $c) {
            $name = strtolower($c["name"]) == "default" ? "" : ", {'name': '{$c["name"]}'}";
            $google_analytics_javascript .= "
            ga('create', '{$c['code']}', 'auto'$name);";
        }

        foreach ($codes as $c) {
            $name = strtolower($c["name"]) == "default" ? "send" : "{$c["name"]}.send";

            $pageName = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_GANALTICS)->getAnalyticPage();
            if ($pageName === false) {
                $google_analytics_javascript .= "
                ga('$name', 'pageview');
                ";
            } else {
                $google_analytics_javascript .= "
                ga('$name', 'pageview','$pageName');
                ";
            }


        }

        $google_analytics_javascript .= '
        </script>';

        $buffer = str_replace("</head>", $google_analytics_javascript . "</head>", $buffer);


        $currentPage = WFactory::getHelper()->getCurrentPage();

        //<link href="http://localhost/softverk-webportal-remaxth/property/property/condo-apartment-grand-park-view-klong-toey-bangkok-8976" rel="canonical" />
        if ($currentPage === 'property') {

            //fix cannonical name

            $currentUrl = WFactory::getHelper()->getCurrentUrl();
            $propertyId = explode('/', $currentUrl);
            $propertyId = trim($propertyId[count($propertyId) - 1]);
            if (!is_numeric($propertyId)) {
                $propertyId = explode('-', $currentUrl);
                $propertyId = trim($propertyId[count($propertyId) - 1]);
            }
            $directUrl = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTY)->getUrlToDirectPage($propertyId);

            $app = JFactory::getApplication();
            $uri = JUri::getInstance();
            $router = $app::getRouter();
            $domain = $this->params->get('domain');

            if ($domain === null || $domain === '') {
                $domain = $uri->toString(array('scheme', 'host', 'port'));
            }

            $link = $domain . JRoute::_('index.php?' . http_build_query($router->getVars()), false);
            $buffer = str_replace($link, $directUrl, $buffer);


        }


        JResponse::setBody($buffer);

        return true;
    }
//
//    /**
//     * from : http://docs.joomla.org/Search_Engine_Friendly_URLs
//     *
//     * Joomla allows you to create your own routing mechanism. In order
//     * to create this mechanism you must have a plugin that overrides
//     * the JPlugin::onAfterInitialise() function.
//     * This function then parses the URL and creates the needed
//     * variables in $_GET before the standard Joomla routing is done.
//     *
//     */
//    function doRouting()
//    {
//        $app = JFactory::getApplication();
//
//
//        // Get the router
//        $router = $app->getRouter();
//
//        // Create a callback array to call the replaceRoute method of this object
//        $replaceRouteCallback = array($this, 'replaceRoute');
//
//        // Attach the callback to the router
//        $router->attachBuildRule($replaceRouteCallback);
//    }
//
//    /**
//     * @param   JRouterSite &$router The Joomla Site Router
//     * @param   JURI &$uri The URI to parse
//     *
//     * @return  array  The array of processed URI variables
//     */
//    public function replaceRoute($router, &$uri)
//    {
//
//
//        $app = JFactory::getApplication('site');
//        $vars = array();
//
//        // Get the true router
//        $siteRouter = $app->getRouter();
//
//        /**
//         * to avoid a recursion trap, we need to make sure that only
//         * the site router can call us!  We could have removed our own
//         * rule the myRouter...but that would only work
//         * inside our own method!  If someone else is also
//         * doing the same thing, we would have an ugly little
//         * recursion where they call parse which calls us
//         * and then we clone router and call parse which calls them
//         * back, and forth and back and forth
//         * friends don't let friends use recursion!
//         */
//
//        if (spl_object_hash($router) != spl_object_hash($siteRouter)) {
//            // Recursion detected -> abort!
//            return $vars;
//        }
//
//        /**
//         * we still want to clone the router passed to us, not the true router
//         * since the rules might be different
//         */
//
//        $myRouter = clone $router;
//
//        // Now use the power of Joomla! to parse this uri!
//        $vars = $myRouter->parse($uri);
//
//        if (isset($vars['office_id'])) {
//            unset($vars["Itemid"]);
//            //  $_GET["Itemid"] =  WFactory::getConfig()->getWebportalConfigurationArray()['officeHiddenItemId'];
//            //  $vars["Itemid"] = WFactory::getConfig()->getWebportalConfigurationArray()['officeHiddenItemId'];
//        }
//
////        // What is the menu id?  What is the airspeed velocity of an unladen swallow?
////        $menuId = isset($vars['itemId']) ? $vars['itemId'] : 0;
////
////        // Please be smarter than this.  Load the menu and check the config.  Do something
////        if ($menuId ==  WFactory::getConfig()->getWebportalConfigurationArray()['officeHiddenItemId']) {
////            // Change the option to be a different component.
////            $vars['option'] == 'com_my_custom_component';
////        }
//
//
//        // Return our custom variables
//        return $vars;
//    }


}
