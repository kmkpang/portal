<?php
/**
 * Created by PhpStorm.
 * User: khan
 * Date: 9/19/15
 * Time: 2:57 PM
 */
?>

<div class="property-details__sidebar-infopanel small-24">
    <div class="row collapse">
        <div class="property-details__property-table show-medium-up small">
            <table>
                <?php if (getParam('propertyID') == 'true') { ?>
                <tr>
                    <th><?php echo JText::_("PROPERTY ID") ?></th>
                    <td><?php echo $property->reg_id; ?></td>
                </tr>
                <?php } ?>
                <tr>
                    <th><?php echo JText::_("PROPERTY FIRST PUBLISHED") ?></th>
                    <td><?php echo WFactory::getHelper()->getFormattedDate($property->created_date) ?></td>
                </tr>
                <tr>
                    <th><?php echo JText::_("PROPERTY LAST UPDATED") ?></th>
                    <td><?php echo WFactory::getHelper()->getFormattedDate($property->last_update) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>

<?php if (getParam('countryCode') == 'is' && getParam('infoBottom') == 'true') { ?>
<div class="property-details__sidebar-infopanel small-24">
    <div class="row collapse">
        <div class="property-details__property-table show-medium-up small">
            <table>
                <tr>
                    <th style="vertical-align: top;" rowspan="3"><?php echo JText::_("PROPERTY LOOKUP") ?></th>
                    <td>
                        <?php if ($property->property_blueprint_link) { ?>
                            <a target="_blank" href="<?php echo $property->property_blueprint_link ?>">
                                <?php echo JText::_("BLUEPRINTS") ?>
                            </a>
                        <?php } else { ?>
                            <?php echo JText::_("BLUEPRINTS") ?>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a target="_blank" href="<?php echo $property->property_registration_link ?>">
                            <?php echo JText::_("REGISTER") ?>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a target="_blank" href="<?php echo $property->property_phone_link ?>">
                            <?php echo JText::_("TELEPHONE DIRECTORY") ?>
                        </a>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
<?php } ?>
