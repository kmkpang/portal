<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_BASE . DS . 'templates' . DS . 'generic' . DS . 'controllers' . DS . 'helpers.php');

$property_id = $this->propertyId;
$property = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTY)->getDetail($property_id);
?>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <title><?php echo $property->title ?></title>
    <link rel="stylesheet" type="text/css" href="<?php echo getStyle(getParam('templateStyle')); ?>"/>
</head>

<body class="property-picture--wraper">
<div class="logo">
    <a href="<?php echo JURI::base(); ?>"><img src="<?php echo JURI::base() . getParam('logoFile') ?>"/></a>
</div>
<div class="more_photos_container">

    <?php
    foreach ($property->imagesV2 as $i => $picture) {

        ?>
        <div class="column large-24 small-24">
            <div class="more_photos_wrapper">
                <img class="more_photos" src="<?php echo $picture->serverUrl ?>" alt="<?php echo $picture->alt ?>"/>
                <?php if (!WFactory::getHelper()->isNullOrEmptyString($picture->description)) { ?>
                    <div class='more_photos_description'>
                        <!-- description content -->
                        <p class='description_content'><?php echo $picture->description ?></p>
                        <!-- end description content -->
                    </div>
                <?php } ?>
            </div>

        </div>
        <?php

    }
    ?>
</div>
</body>

<?php
WFactory::getLogger()->warn("Exiting in property picture ! ");
exit(1);
?>


