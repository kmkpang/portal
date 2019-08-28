<?php
$id = $office['id'];
$route = JRoute::_("index.php?option=com_webportal&view=offices&office_id=$id&lang=" . WFactory::getHelper()->getCurrentlySelectedLanguage());
?>

<div class="columns large-8 small-24">
    <div class="office-list__item clearfix">

        <a href="<?php echo $route ?>">
            <div class="office-list__image-wrapper ">
                <img alt="<?php echo $office['office_name']; ?>" src="<?php echo $office['image_file_path']; ?>"
                     class="office-list__image"/>
            </div>
        </a>

        <div class="row office-list__column">
            <div class="office-list__info column large-12 small-12">
                <a href="<?php echo $route ?>">
                    <h2 class="office-list__name"><?php echo $office['office_name']; ?></h2>
                </a>
                                <span class="office-list__address">

                                    <?php
                                    $officeAddress = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_ADDRESS)->getAddress($office['address_id']);
                                    echo $officeAddress["address"] . "<br/>" .
                                        "{$officeAddress["postal_code"]} - {$officeAddress["postal_code_name"]}" . "<br/>";
                                    ?>

                                </span>
            </div>

            <div class="office-list__contact-info column large-12 small-12">
                <h2 class="office-list__contact-label"><?php echo JText::_("CONTACT") ?>:</h2>

                <div class="office-list__email">
                    <strong><?php echo JText::_("EMAIL") ?>:</strong> <a
                        ><?php echo $office['email']; ?></a>
                </div>
                <div class="office-list__phone">
                    <strong><?php echo JText::_("PHONE") ?>:</strong> <?php echo $office['phone']; ?>
                </div>
                <div class="office-list__propertiescount">
                    <strong><?php echo JText::_("PROPERTIES LISTED") ?>
                        :</strong> <?php echo $office['properties']; ?>
                </div>
            </div>
        </div>
    </div>
</div>
