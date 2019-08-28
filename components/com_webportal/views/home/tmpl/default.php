<?php

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
$doc = JFactory::getDocument();

$app = JFactory::getApplication();
$menu = $app->getMenu();
$menuMapItem = $menu->getItems('link', 'index.php?option=com_webportal&view=map', true);
$menuListItem = $menu->getItems('link', 'index.php?option=com_webportal&view=list', true);

$propertiesMenuItemId = WFactory::getConfig()->getWebportalConfigurationArray()['propertiesItemId'][JFactory::getLanguage()->getTag()];
$propertiesMenuItem = JFactory::getApplication()->getMenu()->getItem($propertiesMenuItemId);
$propertiesListRoute = JUri::base() . $propertiesMenuItem->route;

WFactory::getHelper()->setCurrentPage('home');

require_once(JPATH_BASE . DS . 'templates' . DS . 'generic' . DS . 'controllers' . DS . 'helpers.php');
?>

<?php if (getParam('searchFrontPage') == 'half') {
    require "__half.php";
} ?>

<?php if (getParam('searchFrontPage') == 'full') {
    require "__full.php";
} ?>

<?php if (getParam('searchFrontPage') == 'commercial') {
    require "__commercial.php";
} ?>

<?php /*
<!-- Begin Top Header -->
<div id="svTopHeader" class="dark shadow">
    <div class="row text--white">
        <div class="column large-8"><?php echo '<i class="fa fa-building-o"></i> Office Name'; ?></div>
        <div class="column large-8"><?php echo '<i class="fa fa-home"></i> Office Address'; ?></div>
        <div class="column large-8"><?php echo '<i class="fa fa-phone"></i> Office Contact'; ?></div>
    </div>
</div>
<!-- End Top Header -->
*/ ?>

<?php if (!empty(JModuleHelper::getModules('top-1'))) { ?>
    <!-- Begin Top 1 -->
    <div id="svTop1" class="post light shadow">
        <h1 class="modules__caption"><?php echo JText::_('COM_WEBPORTAL_FEATURE_PROPERTIES'); ?></h1>

        <?php foreach (JModuleHelper::getModules('top-1') as $module) {
            echo JModuleHelper::renderModule($module);
        } ?>

    </div>
    <!-- End Top 1 -->
<?php } ?>

<?php if (getParam('mapFrontPage') == 'true') { ?>
    <!-- Begin Map Ads -->
    <div id="svMapAds" class="post large-24">
        <div class="map-ads-frontpage--wrapper">
            <div class="row">
                <h1 class="modules-map__caption"><i class="fa fa-map-marker"></i></h1>
                <div class="contact__form--row small-14 large-8 large-centered">
                    <a class="input-submit primary-medium big-title"
                       href="<?php echo JUri::base() . "portal-map" ?>"><?php echo JText::_("MAP_SEARCH") ?></a>
                </div>
            </div>
        </div>
    </div>
    <!-- End Map Ads -->
<?php } ?>

<?php if (!empty(JModuleHelper::getModules('top-2'))) { ?>
    <!-- Begin Top 2 -->
    <div id="svTop2" class="post large-24">
        <div class="row">
            <h1 class="modules__caption"><?php echo JText::_('COM_WEBPORTAL_PROPERTIES_LIST'); ?></h1>
            <?php foreach (JModuleHelper::getModules('top-2') as $module) {
                echo JModuleHelper::renderModule($module);
            } ?>
        </div>
    </div>
    <!-- End Top 2 -->
<?php } ?>

<?php if (!empty(JModuleHelper::getModules('company'))) { ?>
    <!-- Begin Company -->
    <div id="svCompany" class="post large-24">
        <?php foreach (JModuleHelper::getModules('company') as $module) {
            echo JModuleHelper::renderModule($module);
        } ?>
    </div>
    <!-- End Company -->
<?php } ?>

<?php if (!empty(JModuleHelper::getModules('agents'))) { ?>
    <!-- Begin Agents -->
    <div id="svAgents" class="post large-24">
        <div class="row">
            <?php foreach (JModuleHelper::getModules('agents') as $module) {
                echo JModuleHelper::renderModule($module);
            } ?>
        </div>
    </div>
    <!-- End Agents -->
<?php } ?>

<?php if (!empty(JModuleHelper::getModules('map'))) { ?>
    <!-- Begin Map -->
    <div id="svMap" class="large-24">
        <?php foreach (JModuleHelper::getModules('map') as $module) {
            echo JModuleHelper::renderModule($module);
        } ?>
    </div>
    <!-- End Map -->
<?php } ?>

<?php if (!empty(JModuleHelper::getModules('address')) || !empty(JModuleHelper::getModules('contact'))) { ?>
    <!-- Begin Address -->
    <div id="svAddress" class="light large-24">
        <div class="row">
            <div class="column white shadow small-24 large-11 radius">
                <?php foreach (JModuleHelper::getModules('address') as $module) {
                    echo JModuleHelper::renderModule($module);
                } ?>
            </div>

            <div class="column white shadow large-11 radius">
                <?php foreach (JModuleHelper::getModules('contact') as $module) {
                    echo JModuleHelper::renderModule($module);
                } ?>
            </div>
        </div>
    </div>
    <!-- End Address -->
<?php } ?>

<?php if (!empty(JModuleHelper::getModules('contact-form'))) { ?>
    <!-- Begin Contact -->
    <div id="svContactForm" class="post large-24 large-centered">
        <div class="row">
            <h1 class="modules__caption">
                <?php echo JText::_('COM_WEBPORTAL_CONTACT_FORM'); ?><br/>
                <small><?php echo JText::_('COM_WEBPORTAL_CONTACT_FORM_DESCRIPTION'); ?></small>
            </h1>
            <?php foreach (JModuleHelper::getModules('contact-form') as $module) {
                echo JModuleHelper::renderModule($module);
            } ?>
        </div>
    </div>
    <!-- End Contact -->
<?php } ?>

<?php if (getParam('viewportLoad') == 'true') { ?>
    <script type="text/javascript">
        jQuery(document).ready(function () {
            jQuery('.post').addClass("opacity-none").viewportChecker({
                classToAdd: 'opacity-visible animated fadeIn',
                offset: 100
            });
        });
    </script>
<?php } ?>
