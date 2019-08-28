<?php

defined('_JEXEC') or die('Restricted access');

$doc = JFactory::getDocument();
$app = JFactory::getApplication();
require_once(JPATH_BASE . DS . 'templates' . DS . 'generic' . DS . 'controllers' . DS . 'helpers.php');

//$property_id = JFactory::getApplication()->input->getCmd('propertyId');

$propertyId = $_GET['propertyId'];
$property = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTY)->getDetail($propertyId);

$videos = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_VIDEO)->getAllVideos(null);

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

        <div class="property-details__social">

            <!-- Facebook -->
            <a href="http://www.facebook.com/sharer.php?u=<?php echo $shareUrl ?>" target="_blank">
                <div class="socialshare facebook mb05">
                    <i class="fa fa-facebook"></i>
                </div>
            </a>

            <!-- Twitter -->
            <a href="https://twitter.com/share?url=<?php echo $shareUrl ?>" target="_blank">
                <div class="socialshare twitter mb05">
                    <i class="fa fa-twitter"></i>
                </div>
            </a>

            <!-- Google+ -->
            <a href="https://plus.google.com/share?url=<?php echo $shareUrl ?>" target="_blank">
                <div class="socialshare google mb05">
                    <i class="fa fa-google"></i>
                </div>
            </a>

            <!-- Email -->
            <div title="<?php echo JText::_("SHARE THIS PROPERTY WITH FRIEND") ?>">
                    <span ng-click="showSendMailForm()">
                        <div class="socialshare mail mb05"><i class="fa fa-envelope"
                                                              data-title="<?php echo JText::_("SHARE THIS PROPERTY WITH FRIEND") ?>"></i>
                        </div>
                    </span>
            </div>

            <!-- Print -->
            <a href="<?php echo WFactory::getHelper()->getCurrentlySelectedLanguage() . "/property-print/$property_id" ?>"
               target="_blank" title="<?php echo JText::_("PRINTABLE DETAILS") ?>">
                <div class="socialshare mail mb05">
                    <i class="fa fa-print"></i>
                </div>
            </a>

            <!-- All Photos -->
            <div title="<?php echo JText::_("SHOW_ALL_PICTURES") ?>">
                    <span
                        ng-click="openMorePicturesPopup('<?php echo JUri::base() . "index.php?component=com_webportal&view=propertypictures&property-id=$property_id" ?>')">
                        <div class="socialshare mail">
                            <i class="fa fa-file-image-o" data-title="<?php echo JText::_("SHOW_ALL_PICTURES") ?>"></i>
                        </div>
                    </span>
            </div>

        </div>

        <div class="property-details--wrapper small-24 large-16">
            <div class="property-details">

                <div ng-show="listloading"><?php WFactory::getHelper()->getLoadingIcon(); ?></div>
                <div ng-show="!listloading" class="ng-cloak">

                    <!-- PROPERTY VIDEO -->
                    <div class="row property-details__image-wrapper">
                        <div class="property-details__slider-wrapper">
                            <!-- Temp use array 0-->
                            <iframe width="660" height="370" src="https://www.youtube.com/embed/<?php echo $property->videos[0]->providerVideoFileName?>" frameborder="0" allowfullscreen></iframe>
                        </div>
                    </div>

                    <div class="row">

                        <!-- PROPERTY IMAGES -->
                        <div class="column property-details__image large-8">
                            <img
                                alt="<?php echo $property->address . " " . $property->property_region_town_zip_formatted; ?>"
                                src="<?php echo $property->list_page_thumb_path; ?>">

                        </div>

                        <!-- PROPERTY DETAIL -->
                        <div class="column property-details__description large-16">
                            <a href="<?php echo $property->url_to_direct_page; ?>">
                                <h1>
                                    <!-- ADDRESS -->
                                    <div class="row-item__address">
                                        <span class="row-item__street_name"><?php echo $property->address; ?>
                                            <?php echo !WFactory::getHelper()->isNullOrEmptyString($property->property_region_town_zip_formatted) ? "," : "" ?></span>
                                        <span class="row-item__code-name">
                                                <?php echo !WFactory::getHelper()->isNullOrEmptyString($property->property_region_town_zip_formatted) ? $property->property_region_town_zip_formatted : "" ?>
                                        </span>
                                    </div>
                                </h1>
                            </a>
                            <div class="property-details__buy-rent">
                                <i class="fa fa-tags"></i> <span class="text-uppercase"><?php echo JText::_(strtoupper($property->buy_rent."_PRICE")) ?></span> <span class="property-details__price price-color"><?php echo $property->current_listing_price_formatted; ?></span>
                            </div>

                            <div class="property-details__category">
                                <i class="fa fa-home" ng-show="item.residential_commercial == 'RESIDENTIAL'"></i><i class="fa fa-building" ng-show="item.residential_commercial == 'COMMERCIAL'"></i>
                                <span class="property-details__category-name"><?php echo $property->category_name; ?></span>
                            </div>

                            <div class="row property-details__rooms">

                                <div class="column small-8">
                                    <h5 class="row-item__rooms">{{item.total_number_of_rooms | number}}
                                        <span class="area--unit"><?php echo JText::_("ROOMS") ?></span>
                                    </h5>
                                </div>

                                <div class="column small-8">
                                    <h5 class="row-item__rooms">{{item.number_of_bedrooms | number}}
                                        <span class="area--unit"><?php echo JText::_("BEDROOMS") ?></span>
                                    </h5>
                                </div>
                                <div class="column small-8">
                                    <h5 class="row-item__rooms">{{item.number_of_bathrooms | number}}
                                        <span class="area--unit"><?php echo JText::_("BATHROOMS") ?></span>
                                    </h5>
                                </div>
                            </div>
                            <hr />
                            <div class="property-details__description">
                                <p><?php echo $property->description_text; ?></p>
                            </div>
                        </div>
                    </div>

                </div>

            </div>


        </div>


        <div class="sidebar small-24 large-8" ng-Controller="SearchCtrl">

            <div class="property-details__sidebar-infopanel small-24">
                <!-- SEQUENCE VIDEOS-->
                <div class="row property-video__wrapper">
                    <div class="row property-details__video__sidebar">
                        <span class="text-center"><?php echo JText::_('OTHERS VIDEOS');?></span>
                    </div>

                    <ul class="property-video--inner">
                        <?php foreach ($videos as $v) {
                            /**
                             * @var $v Videos
                             */ ?>
                            <li class="property-video--items"
                                data-thumb="http://img.youtube.com/vi/<?php echo $v->providerVideoFileName; ?>/hqdefault.jpg">
                                <img class="column small-10"
                                     alt="<?php echo $v->description; ?>"
                                     src="http://img.youtube.com/vi/<?php echo $v->providerVideoFileName; ?>/hqdefault.jpg"
                                     ng-click="showVideo('<?php echo $v->providerVideoFileName; ?>')">
                                <span class="property-video--items--description column small-14"><?php echo $v->description?></span>
                            </li>

                        <?php } ?>
                    </ul>
                </div>

                <hr />

                <!-- RELATED OTHER VIDEOS-->
                <div class="row property-video__wrapper">
                    <div class="row property-details__video__sidebar">
                        <span class="text-center"><?php echo JText::_('RELATED VIDEOS');?></span>
                    </div>

                    <ul class="property-video--inner">
                        <?php foreach ($videos as $v) {
                            /**
                             * @var $v Videos
                             */ ?>
                            <li class="property-video--items"
                                data-thumb="http://img.youtube.com/vi/<?php echo $v->providerVideoFileName; ?>/hqdefault.jpg">
                                <img class="column small-10"
                                     alt="<?php echo $v->description; ?>"
                                     src="http://img.youtube.com/vi/<?php echo $v->providerVideoFileName; ?>/hqdefault.jpg"
                                     ng-click="showVideo('<?php echo $v->providerVideoFileName; ?>')">
                                <span class="property-video--items--description column small-14"><?php echo $v->description?></span>
                            </li>

                        <?php } ?>
                    </ul>
                </div>
            </div>

        </div>

    </div>
