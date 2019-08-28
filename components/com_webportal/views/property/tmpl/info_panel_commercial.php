<?php if (!empty($property->total_number_of_rooms) && ($property->total_number_of_rooms > 0)) { ?>
    <tr>
        <th><?php echo JText::_("ROOMS") ?></th>
        <td><?php echo number_format($property->total_number_of_rooms); ?></td>
    </tr>
<?php } ?>

<?php if (!empty($property->comm_number_of_offices) && ($property->comm_number_of_offices > 0)) { ?>
    <tr>
        <th><?php echo JText::_("NUMBER_OF_OFFICES") ?></th>
        <td><?php echo number_format($property->comm_number_of_offices); ?></td>
    </tr>
<?php } ?>

<?php if (!empty($property->comm_office_space) && ($property->comm_office_space > 0)) { ?>
    <tr>
        <th><?php echo JText::_("OFFICE_SPACE") ?></th>
        <td><?php echo number_format($property->comm_office_space); ?></td>
    </tr>
<?php } ?>

<?php if (!empty($property->comm_mannufacturing_space) && ($property->comm_mannufacturing_space > 0)) { ?>
    <tr>
        <th><?php echo JText::_("MANUFACTURING_SPACE") ?></th>
        <td><?php echo number_format($property->comm_mannufacturing_space); ?></td>
    </tr>
<?php } ?>

<?php if (!empty($property->comm_warehouse_space) && ($property->comm_warehouse_space > 0)) { ?>
    <tr>
        <th><?php echo JText::_("WAREHOUSE_SPACE") ?></th>
        <td><?php echo number_format($property->comm_warehouse_space); ?></td>
    </tr>
<?php } ?>