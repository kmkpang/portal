<?php

require_once(JPATH_BASE . DS . 'templates' . DS . 'generic' . DS . 'controllers' . DS . 'helpers.php');

$configuration = WFactory::getConfig()->getWebportalConfigurationArray();
$isCommercialSite = array_key_exists('isCommercialSite', $configuration) && $configuration['isCommercialSite'] === true;

$app = JFactory::getApplication();
$menu = $app->getMenu();
$menuMapItem = $menu->getItems( 'link', 'index.php?option=com_webportal&view=map', true );
$menuListItem = $menu->getItems( 'link', 'index.php?option=com_webportal&view=list', true );

$columns = array(getParam('loan80'), getParam('garage'), getParam('elevator'), getParam('newToday'), getParam('newWeek'));
?>

<!-- THIS IS USED IN LIST AND MAP PAGE -->
<form ng-submit="filterSidebarModule()">

    <!-- Top Search Block -->

    <?php
    if (getParam('countryCode') == 'is') {
        if ($isCommercialSite)
            require_once('__layout_iceland_commercial.php');
        else
            require_once('__layout_iceland.php');
    }

    if (getParam('countryCode') == 'th') {
        require_once('__layout_thailand.php');
    }

    if (getParam('countryCode') == 'ph') {
        require_once('__layout_philippine.php');
    } else {
    } ?>

</form>