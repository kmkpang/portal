<?php if ($property->category_id != 106 || $property->category_id != 131) { ?>
    <th>
        <?php if (!empty($property->total_number_of_rooms) && ($property->total_number_of_rooms > 0)) { ?>
            <div class="text-center">
                <span class="property-details__room-count"><?php echo $property->total_number_of_rooms; ?></span><br/>
                <span class="property-details__label"><?php echo JText::_("ROOMS") ?></span>
            </div>
        <?php } ?>
    </th>

    <td>
        <?php if (!empty($property->number_of_bedrooms) && ($property->number_of_bedrooms > 0)) { ?>
            <div>
                <span class="property-details__bedroom-count"><?php echo $property->number_of_bedrooms; ?></span>
                <span class="property-details__label"><?php echo JText::_("BEDROOMS") ?></span>
            </div>
        <?php } ?>

        <?php if (!empty($property->number_of_livingrooms) && ($property->number_of_livingrooms > 0)) { ?>
            <div>
                <span class="property-details__livingrooms-count"><?php echo $property->number_of_livingrooms; ?></span>
                <span class="property-details__label"><?php echo JText::_("LIVINGROOMS") ?></span>
            </div>
        <?php } ?>

        <?php if (!empty($property->number_of_bathrooms) && ($property->number_of_bathrooms > 0)) { ?>
            <div>
                <span class="property-details__bathrooms-count"><?php echo $property->number_of_bathrooms; ?></span>
                <span class="property-details__label"><?php echo JText::_("BATHROOMS") ?></span>
            </div>
        <?php } ?>
    </td>
    <?php ?>
<?php } ?>