<?php

defined('_JEXEC') or die('Restricted access');

$doc = JFactory::getDocument();
$app = JFactory::getApplication();
require_once(JPATH_BASE . DS . 'templates' . DS . 'generic' . DS . 'controllers' . DS . 'helpers.php');

$property_id = $this->propertyId;
$property = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTY)->getDetail($property_id);

$propertiesMenuItemId = WFactory::getConfig()->getWebportalConfigurationArray()['propertiesItemId'][JFactory::getLanguage()->getTag()];
$propertiesMenuItem = JFactory::getApplication()->getMenu()->getItem($propertiesMenuItemId);
$propertiesListRoute = JRoute::_("index.php?option=com_webportal&view=list" . $propertiesMenuItem->route);
$agentDetailsRoute = JRoute::_("index.php?option=com_webportal&view=agents&agent_id=" . $property->sale_id);
$officeDetailsRoute = JRoute::_("index.php?option=com_webportal&view=offices&office_id=" . $property->office_id);

JFactory::getDocument()->setTitle(
//JText::_("PROPERTY FOR " . strtoupper($property->buy_rent)) . ' - ' .  // this is due to : http://redmine.softverk.is/issues/1826
    $property->title
);

WFactory::getHelper()->setCurrentPage('property-details');

$shareUrl = sprintf("%s://%s%s", isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
    $_SERVER['SERVER_NAME'],
    $_SERVER['REQUEST_URI']
);

//add analytic helper..
WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_GANALTICS)->setAnalyticPage("/property/$property_id");
//WFactory::getSqlService()->getSqlServiceClass(__PROPPERTY_PORTAL_PROPERTY)->incrementViewCount($property_id);


//add meta tag for sharing image in facebook..
$doc->addCustomTag('<meta property="og:image" content="' . $property->images[0] . '" />');
$doc->addCustomTag('<meta property="og:description" content="' . $property->getShortDescription(250) . '...' . '" />');

JFactory::getDocument()->setMetaData('description', $property->getShortDescription(160));
JFactory::getDocument()->setMetaData('keywords', $property->metaKeyword);
//

$total_area = $property->total_area;

$start = $property->open_house_start;
$end = $property->open_house_end;


?>
    <script src="http://platform.twitter.com/widgets.js"></script>
    <script type="text/javascript">
        <?php


        $doc->addScriptDeclaration("var documentRoot = \"" . JURI::base() . "\";");
        $doc->addScriptDeclaration("var lang = \"" . JFactory::getLanguage()->getTag() . "\";");
        $doc->addScriptDeclaration("var propertesRoute = \"" . WFactory::getHelper()->buildUrl('/properties/list') . "\";");
        $doc->addScriptDeclaration("var propertyDetails = " . json_encode($property) . ";");

        ?>
    </script>
    <style>
        .property-details span[style],
        .property-details strong[style] {
            text-decoration: none !important;
        }
    </style>
    <div class="row row--property-details no-breadcrumbs"
         ng-init="loadDetails(<?php echo $this->propertyId ?>);"
         ng-controller="PropertyCtrl">
        
        <div class="property-details--wrapper small-24">
            <div class="property-details">

                <div ng-show="listloading"><?php WFactory::getHelper()->getLoadingIcon(); ?></div>
                <div ng-show="!listloading" class="ng-cloak">

                    <!-- PHOTO PLACEHOLDER -->
                    <div class="row property-details__image-wrapper">

                        <!-- SLIDER -->
                        <div class="property-details__slider-wrapper">

                            <div id="slider" class="flexslider">
                                <ul class="slides">
                                    <li data-thumb={{img.serverUrl}} ng-repeat="img in item.imagesV2 track by $index">
                                        <img
                                            alt="{{img.alt}} <?php echo $property->address . " " . $property->property_region_town_zip_formatted; ?>"
                                            ng-src={{img.serverUrl}}>

                                        <div ng-show="item.open_house" ng-class="{'opening': item.open_house_now}"
                                             class="property--openhouse">
                                        <span><i
                                                class="fa fa-calendar"></i> <?php echo JText::_("OPEN_HOUSE_START") . ' ' . $start . ' - ' . date("H:s", strtotime($end)); ?></span>
                                        </div>
                                    <span ng-show="img.description" class="property--img-description"><i
                                            class="fa fa-camera"></i> {{img.description}}</span>
                                    </li>
                                </ul>
                            </div>

                            <div id="carousel" class="flexslider flexslider--thumbnails">
                                <ul class="slides">
                                    <li data-thumb={{img.serverUrl}} ng-repeat="img in item.imagesV2 track by $index">
                                        <img
                                            alt="{{img.alt}} <?php echo $property->address . " " . $property->property_region_town_zip_formatted; ?>"
                                            ng-src={{img.serverUrl}}>
                                    </li>
                                </ul>
                            </div>
                        </div>


                    </div>
                    
                </div>

            </div>
        </div>
    </div>
