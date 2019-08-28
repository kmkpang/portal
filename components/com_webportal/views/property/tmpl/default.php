<?php

defined('_JEXEC') or die('Restricted access');

$doc = JFactory::getDocument();
$app = JFactory::getApplication();
$lang = WFactory::getHelper()->getCurrentlySelectedLanguage();
require_once(JPATH_BASE . DS . 'templates' . DS . 'generic' . DS . 'controllers' . DS . 'helpers.php');

$property_id = $this->propertyId;
$property = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTY)->getDetail($property_id);

$propertiesMenuItemId = WFactory::getConfig()->getWebportalConfigurationArray()['propertiesItemId'][JFactory::getLanguage()->getTag()];
$propertiesMenuItem = JFactory::getApplication()->getMenu()->getItem($propertiesMenuItemId);
$propertiesListRoute = JRoute::_("index.php?option=com_webportal&view=list" . $propertiesMenuItem->route);
$agentDetailsRoute = JRoute::_("index.php?option=com_webportal&view=agents&agent_id=" . $property->sale_id);
$officeDetailsRoute = JRoute::_("index.php?option=com_webportal&view=offices&office_id=" . $property->office_id);

$province = !WFactory::getHelper()->isNullOrEmptyString($property->property_region_town_zip_formatted) ? $property->property_region_town_zip_formatted : "";

//Set URL title or Address
$url = $property->address . $province;

if(getParam('titleUrl') == 'title') {
    //JText::_("PROPERTY FOR " . strtoupper($property->buy_rent)) . ' - ' .
    $url = $property->title;
} else if(getParam('titleUrl') == 'address'){
    $url = $property->address . $province;
} else if(getParam('titleUrl') == 'both'){
    $url = $property->title . $property->address . $province;
}

JFactory::getDocument()->setTitle($url);
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
    <div class="row row--property-details"
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

        <div class="column large-24">
            <div class="column small-24 medium-20 large-20">
                <h1>
                    <?php if(getParam(propertyTitle) == 'true') { ?>
                        <!-- TITLE -->
                        <div class="row property-details__address">
                            <?php echo $property->title ?>
                        </div>
                    <?php } else { ?>
                        <!-- ADDRESS -->
                        <div class="row property-details__address">
                                <span class="property-details__street">
                                    <?php echo $property->address; ?>
                                </span>
                                <span class="property-details__zipcode-name">
                                        <?php echo $province ?>
                                </span>
                        </div>
                    <?php } ?>
                </h1>
            </div>

            <div class="column small-24 medium-4 large-4 show-medium-up social-block">
                <div class="column small-4 large-4 small-offset-16 large-offset-16">
                    <div class="socialshare view" title="Total number of views">
                        <?php echo($property->viewcount) ?>
                        <br />
                        <small><?php echo JText::_("VIEWS"); ?></small>
                    </div>
                </div>
            </div>

        </div>
        <div class="property-details--wrapper small-24 large-16">
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
                                        <a target="_blank" href="<?php echo JUri::base() . "index.php?component=com_webportal&view=propertypictures&property-id=$property_id" ?>">
                                            <img alt="{{img.alt}} <?php echo $property->address . " " . $property->property_region_town_zip_formatted; ?>"
                                                ng-src={{img.serverUrl}}>
                                        </a>

                                        <div ng-show="item.open_house" ng-class="{'opening': item.open_house_now}"
                                             class="property--openhouse">
                                        <span><i
                                                class="fa fa-calendar"></i> <?php echo JText::_("OPEN_HOUSE_START") . ' ' . $property->open_house_start . ' - ' . date("H:s", strtotime($property->open_house_end)); ?></span>
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

                    <!-- PRINTING -->
                    <div class="property-details__sharing-wrapper">
                        <?php /* Temporary Disable
                        <div class="property-details__sharing-wrapper__allpics">
                        <span
                            ng-click="openMorePicturesPopup('<?php echo JUri::base() . "index.php?component=com_webportal&view=propertypictures&property-id=$property_id" ?>')"
                            >
                            <?php echo JText::_("SHOW_ALL_PICTURES") ?>
                            <i class="fa fa-eye"></i>
                        </span>
                        </div>
                        <div class="property-details__sharing-wrapper__print">
                            <a class="" target="_blank"
                               href="<?php echo WFactory::getHelper()->getCurrentlySelectedLanguage() . "/property-print/$property_id" ?>">
                                <?php echo JText::_("PRINTABLE DETAILS") ?>
                                <i class="fa fa-print"></i>
                            </a>
                        </div>
                        */ ?>
                    </div>

                    <!-- INFO , ONLY SHOWS IN MOBILE PHONES  -->
                    <div class="row show-small-only"> <!-- because the price type should show BEFORE the description -->
                        <?php require "info_panel.php" ?>
                    </div>

                    <!-- TAB GROUP -->
                    <div class="row tab-group">
                        <input class="tab-radio" value="" checked type="radio" name="tab-radio" id="tab1-radio">
                        <input class="tab-radio" value="" type="radio" name="tab-radio" id="tab3-radio">
                        <input class="tab-radio" value="" type="radio" name="tab-radio" id="tab4-radio">
                        <input class="tab-radio" value="" type="radio" name="tab-radio" id="tab5-radio">

                        <div class="tab-button-panel small-24">
                            <label for="tab1-radio" class="tab-button"><i class="fa fa-ellipsis-h show-small-only"></i><span class="show-medium-up"><?php echo JText::_("DESCRIPTION") ?></span></label>
                            <label for="tab3-radio" class="tab-button <?php if ($this->checkLocation($property->latitude, $property->longitude)) { echo 'ng-hide'; } ?>"
                                   ng-click="resizeMap()"><i class="fa fa-map-marker show-small-only"></i><span class="show-medium-up"><?php echo JText::_("MAP") ?></span></label>
                            <label for="tab4-radio" class="tab-button <?php if ($this->checkLocation($property->latitude, $property->longitude)) { echo 'ng-hide'; } ?>"
                                   ng-click="showPanorama()"><i class="fa fa-street-view show-small-only"></i><span class="show-medium-up"><?php echo JText::_("STREET_VIEW") ?></span></label>
                            <label for="tab5-radio" class="tab-button <?php if (empty($property->videos)) {
                                echo 'ng-hide';
                            } ?>"><i class="fa fa-youtube-play show-small-only"></i><span class="show-medium-up"><?php echo JText::_("VIDEO") ?></span></label>
                        </div>

                        <!-- TAB 1 DESCRIPTION -->
                        <div class="tab-panel clearfix" id="tab1">
                            <div class="property-details__property-info clearfix">
                                <p><?php echo $property->description_text; ?></p>
                            </div>

                            <div class="property-features__property-table">
                                <?php if (!empty($property->features)) { ?>
                                    <h3><?php echo JText::_("FEATURES") ?></h3>
                                <?php } ?>

                                <table>

                                    <?php
                                    $rows = array_chunk($property->features, 4);
                                    foreach ($rows as $property->features) {
                                        echo '<tr>';

                                        foreach ($property->features as $f) {
                                            if (!WFactory::getHelper()->isNullOrEmptyString($f['name'])) { ?>
                                                <td>
                                                    <i class="fa fa-check-circle"></i> <?php echo JText::_(strtoupper($f['name'])) ?>
                                                </td>
                                            <?php }
                                        } ?>


                                        <?php
                                        echo '</tr>';
                                    } ?>
                                </table>
                            </div>

                            <div class="property-details__social-horizontal">

                                <!-- Facebook -->
                                <a href="http://www.facebook.com/sharer.php?u=<?php echo $shareUrl ?>" target="_blank">
                                    <div class="socialshare-horizontal facebook">
                                        <i class="fa fa-facebook"></i>
                                    </div>
                                </a>

                                <!-- Twitter -->
                                <a href="https://twitter.com/share?url=<?php echo $shareUrl ?>" target="_blank">
                                    <div class="socialshare-horizontal twitter">
                                        <i class="fa fa-twitter"></i>
                                    </div>
                                </a>

                                <!-- Google+ -->
                                <a href="https://plus.google.com/share?url=<?php echo $shareUrl ?>" target="_blank">
                                    <div class="socialshare-horizontal google">
                                        <i class="fa fa-google"></i>
                                    </div>
                                </a>

                                <!-- Email -->
                                <div title="<?php echo JText::_("SHARE THIS PROPERTY WITH FRIEND") ?>">
                                    <span ng-click="showSendMailForm()">
                                        <div class="socialshare-horizontal mail"><i class="fa fa-envelope"
                                                                                    data-title="<?php echo JText::_("SHARE THIS PROPERTY WITH FRIEND") ?>"></i>

                                        </div>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 2 MAP -->
                        <div class="tab-panel clearfix" id="tab3">
                            <?php if (getParam('busFilter') == 'true') {
                                require_once "locality.php";
                            } ?>
                            <div class="embed-container">
                                <!-- <embed-map element-id="property_map" lat="{{item.latitude}}" lng="{{item.longitude}}"></embed-map> -->
                                <embed-map element-id="property_map"></embed-map>
                            </div>
                        </div>

                        <!-- TAB 3 STREET VIEW -->
                        <div class="tab-panel clearfix" id="tab4">
                            <div class="embed-container">
                                <div id="property_map_hidden" style="display:none"></div>
                                <span
                                    ng-show="!streetviewAvailable"><?php echo JText::_("STREETVIEW_NOT_AVAILABLE_AT_THIS_LOCATION") ?></span>

                                <div id="pano" style="min-height: 420px;position: relative">


                                </div>
                            </div>
                        </div>

                        <!-- TAB 4 VIDEO -->
                        <div class="tab-panel clearfix" id="tab5">
                            <!-- VIDEO Popup -->
                            <div class="property-video__wrapper">
                                <ul class="property-video--inner row">
                                    <?php foreach ($property->videos as $v) {
                                        /**
                                         * @var $v PropertyVideo
                                         */ ?>
                                        <li class="property-video--items column small-24 large-8"
                                            data-thumb="http://img.youtube.com/vi/<?php echo $v->providerVideoFileName; ?>/hqdefault.jpg">
                                            <img class="show-large-only"
                                                 alt="<?php echo $v->description; ?>"
                                                 src="http://img.youtube.com/vi/<?php echo $v->providerVideoFileName; ?>/hqdefault.jpg"
                                                 ng-click="showVideo('<?php echo $v->providerVideoFileName; ?>')">
                                            <iframe class="show-medium-down" src="https://www.youtube.com/embed/<?php echo $v->providerVideoFileName?>" frameborder="0" allowfullscreen></iframe>
                                            <span class="property-video--items--description"><?php echo $v->description?></span>
                                        </li>

                                    <?php }
                                    if (count($v) < ($v + 1)) {//If get even item, this can fix float right from foundation
                                        echo "<li class=\"property-video--items column small-24 large-8\">";
                                    } ?>
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>


                <div class="row property-details__other-properties">

                    <next-previous-properties
                        propertyid="<?php echo $property->property_id ?>">
                    </next-previous-properties>

                </div>


            </div>


        </div>


        <div class="sidebar small-24 large-8" ng-Controller="SearchCtrl">

            <div class="show-medium-up">
                <?php require "info_panel.php" ?>
            </div>

            <div class="show-medium-up">
                <?php require "info_panel_bottom.php" ?>
            </div>

            <div class="property-details__agentpanel" ng-controller="ContactCtrl"
                 ng-init="setAgentEmail('<?php echo $property->sales_agent_email ?>');">

                <div class="row collapse">
                    <div class="small-24 large-24 property-details__agentpanel__agentphoto text-center">
                        <div class="agent__img">
                            <a href="<?php echo $agentDetailsRoute ?>">
                                <img
                                    alt="<?php echo $property->sales_agent_full_name ?>"
                                    src="<?php echo $property->sales_agent_image; ?>"/>
                            </a>
                        </div>
                    </div>
                    <div class="small-24 large-24 text-center">
                        <div class="property-details__agentpanel__agentname">
                            <a href="<?php echo $agentDetailsRoute ?>">
                                <?php echo $property->sales_agent_full_name; ?>
                            </a>
                        </div>
                        <div class="property-details__agentpanel__officename">
                            <a href="<?php echo $officeDetailsRoute ?>">
                                <?php echo $property->office_name; ?>
                            </a>

                        </div>
                        <div class="agent-list__title">
                            <?php echo $agent->title ?>
                        </div>

                        <div class="property-details__agentpanel__agentrow">
                            <?php if (!empty(trim($property->sales_agent_mobile_phone)) || !isset($property->sales_agent_mobile_phone)) { ?>
                                <div class="property-details__agentpanel__agentphone">
                                    <span><?php echo JText::_('AGENT PHONE') ?>:</span>
                                    <span><?php echo $property->sales_agent_mobile_phone; ?></span>
                                </div>
                            <?php } ?>
                            <?php if (!empty(trim($property->sales_agent_office_phone)) || !isset($property->sales_agent_office_phone)) { ?>
                                <div class="property-details__agentpanel__officephone">
                                    <span><?php echo JText::_('OFFICE PHONE') ?>:</span>
                                    <span><?php echo $property->sales_agent_office_phone; ?></span>
                                </div>
                            <?php } ?>
                            <?php if (!empty(trim($property->sales_agent_email)) || !isset($property->sales_agent_email)) { ?>
                                <div class="property-details__agentpanel__agentemail">
                                    <span><?php echo JText::_('EMAIL') ?>:</span>
                                    <a href=mailto:<?php echo ($sendtoAgent ? $property->sales_agent_email : $property->office_email); ?>>
                                        <?php echo ($sendtoAgent ? $property->sales_agent_email : $property->office_email); ?>
                                    </a>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>


                <div class="property-details__agentpanel__emailform text-center">
                    <form method="post" ng-submit="submitContact()" class="ng-cloak">
                        <div ng-show="!sent">

                            <div class="property-details__agentpanel__emailform--label">
                                <span><?php echo JText::_('SEND AN EMAIL TO') ?></span>
                        <span
                            class="form-field--row property-details__agentpanel__agent--label"><?php echo $property->sales_agent_full_name; ?></span>
                            </div>

                            <div class="form-field--row">

                                <div class="input-textbox--wrapper">
                                    <input type="text" ng-model="name" required
                                           placeholder="<?php echo JText::_('Name') ?>"/>
                                </div>
                            </div>

                            <div class="form-field--row">

                                <div class="input-textbox--wrapper">
                                    <input type="email" ng-model="email" required
                                           placeholder="<?php echo JText::_('Email') ?>"/>
                                </div>
                            </div>

                            <div class="form-field--row">

                                <div class="input-textbox--wrapper">
                                    <input type="tel" ng-model="phone" required
                                           placeholder="<?php echo JText::_('PHONE') ?>"/>
                                </div>
                            </div>

                            <div class="form-field--row">

                                <div class="input-textbox--wrapper">
                                <textarea ng-model="message" required
                                          placeholder="<?php echo JText::_('COMMENT') ?>"></textarea>
                                </div>
                            </div>

                            <div ng-show="error.length > 0" class="form-field--row">
                                <span class="error">{{error}}</span>
                            </div>

                            <div class="large-18 large-centered large-offset-3">
                                <input class="input-submit primary-medium" ng-show="!sending" type="submit"
                                       value="<?php echo JText::_('SEND EMAIL') ?>"/>
                                <span ng-show="sending"><?php echo JText::_('SENDING') ?>...</span>
                            </div>
                        </div>

                        <div ng-show="sent">
                            <span><?php echo JText::_('COM_WEBPORTAL_CONTACT_FORM_THANKYOU') ?></span><br/>
                        </div>

                    </form>
                </div>

            </div>


        </div>

    </div>

<?php // require_once "send_mail_to_friend.php" ?>