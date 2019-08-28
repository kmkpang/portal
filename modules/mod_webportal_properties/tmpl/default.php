<?php

$doc = JFactory::getDocument();

/**
 * @var $params Joomla\Registry\Registry
 */
$rows = (int)$params->get('rows');
$columns = (int)$params->get('columns');
$template = $params->get('template');
$lang = WFactory::getHelper()->getCurrentlySelectedLanguage();

$app = JFactory::getApplication();
$menu = $app->getMenu();
$menuMapItem = $menu->getItems( 'link', 'index.php?option=com_webportal&view=map', true );
$menuListItem = $menu->getItems( 'link', 'index.php?option=com_webportal&view=list', true );

require_once(JPATH_BASE . DS . 'templates' . DS . 'generic' . DS . 'controllers' . DS . 'helpers.php');

if ($params->get('property_type') == 'next_previous') {
    $break = 1;
}

$properties = WpPropertiesModuleHelper::getProperties($params);
$property = $properties[0];
?>

<?php if ($template == 'featured_article') { ?>
    <div class="small-24 large-8 columns newsflash">
        <?php require "__single_property_box_featured_article.php"; ?>
    </div>
<?php } ?>


<?php if ($template == 'inline_article' || $template == 'newest_properties_frontpage' || $template == 'random_properties_frontpage') { ?>

    <div id="webportalproperties-container" class="webportalproperties-container large-24 small-24 row">
        <div class="webportalproperties-container--inner">

            <?php $currentIndex = 0;
            for ($row = 0; $row < $rows; $row++) { ?>
                <div class="large-24 small-24 row">
                    <?php for ($column = 0; $column < $columns; $column++) { ?>
                        <div
                            class="large-<?php echo 24 / $columns ?> medium-<?php echo 24 / $columns ?> small-24 column">

                            <?php $property = $properties[$currentIndex];
                            $currentIndex++;
                            if ($template == 'inline_article')
                                require "__single_property_box_inside_article.php";
                            if ($template == 'newest_properties_frontpage')
                                require "__newest_properites_frontpage.php";
                            if ($template == 'random_properties_frontpage')
                                require "__single_property_property_details_page.php";
                            ?>

                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>

<?php } ?>

<?php if ($template == 'carousel') {
    require "__carousel.php";
} ?>

<?php if ($template == 'carousel_property_detail_page') {
    require "__carousel_property_details.php";
} ?>
