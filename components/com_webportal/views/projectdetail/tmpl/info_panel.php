<?php
/**
 * Created by PhpStorm.
 * User: khan
 * Date: 9/19/15
 * Time: 2:57 PM
 */
?>

<div class="property-details__sidebar-infopanel small-24">
    <div class="row collapse property-details__buy-rent__price">
        <h2 style="display: none" note="SEO Helper">
            <?php echo $property->buy_rent; ?>
        </h2>

        <h3 style="display: none" note="SEO Helper">
            <?php echo JText::_("PRICE") . ": " . $property->current_listing_price_formatted ?>
        </h3>

        <div class="columns small-12 medium-12 large-12">
            <div class="property-details__buy-rent">
                <span class="text-uppercase"><?php echo JText::_(strtoupper($property->buy_rent."_PRICE")) ?></span>
            </div>
        </div>
        <div class="columns small-12 medium-12 large-12">
                <span
                    class="property-details__price"><?php echo $property->current_listing_price_formatted; ?></span>
        </div>
    </div>

    <div class="row collapse property-details__category">
        <div class="columns small-12 large-12">
            <span class="property-details__category-name"><?php echo $property->category_name; ?></span>
        </div>

        <div class="columns small-12 large-12 text-right">
                    <span class="property-details__area-name"><?php echo number_format(round($property->total_area)) . ' ' ?>
                        <span class="area--unit"><?php echo JText::_("SQM") ?></span></span>
        </div>
    </div>

    <div class="row collapse">

        <div class="property-details__property-table">
            <table>
                <tr>

                    <?php if ($property->residential_commercial == 'COMMERCIAL') { ?>
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

                    <?php } else {?>
                        <th>
                            <div class="text-center">
                            <span
                                class="property-details__room-count"><?php echo $property->total_number_of_rooms; ?></span><br/>
                                <span class="property-details__label"><?php echo JText::_("ROOMS") ?></span>
                            </div>
                        </th>
                        <td>
                            <div>
                                <span
                                    class="property-details__bedroom-count"><?php echo $property->number_of_bedrooms; ?></span>
                                <span class="property-details__label"><?php echo JText::_("BEDROOMS") ?></span>
                            </div>
                            <div>
                                <span
                                    class="property-details__livingrooms-count"><?php echo $property->number_of_livingrooms; ?></span>
                                <span class="property-details__label"><?php echo JText::_("LIVINGROOMS") ?></span>
                            </div>
                            <div>
                                <span
                                    class="property-details__bathrooms-count"><?php echo $property->number_of_bathrooms; ?></span>
                                <span class="property-details__label"><?php echo JText::_("BATHROOMS") ?></span>
                            </div>
                        </td>
                    <?php } ?>
                </tr>
                <?php if (!empty($property->number_of_floors) && ($property->number_of_floors > 0)) { ?>
                    <tr>
                        <th><?php echo JText::_("NUMBER_OF_FLOOR") ?></th>
                        <td><?php echo number_format($property->number_of_floors); ?></td>
                    </tr>
                <?php } ?>
                <?php if (!empty($property->mortgage) && ($property->mortgage > 0)) { ?>
                <tr>
                    <th><?php echo JText::_("LOANS") ?></th>
                    <td><?php echo WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_CURRENCY)->formatCurrency($property->mortgage) ?></td>
                </tr>
                <?php } ?>
                <?php if (!empty($property->year_build) && ($property->year_build > 0)) { ?>
                <tr>
                    <th><?php echo JText::_("YEAR BUILT") ?></th>
                    <td><?php echo $property->year_build; ?></td>
                </tr>
                <?php } ?>
                <?php if (getParam('countryCode') == 'is') { ?>
                <tr>
                    <th><?php echo JText::_("ENTRANCE") ?></th>
                    <td><?php echo $property->entrance; ?></td>
                </tr>
                <?php } ?>
                <tr>
                    <th><?php echo JText::_("GARAGE") ?></th>
                    <td>

                        <?php echo $property->garage === "0" ? JText::_("_NO") : JText::_("_YES") ?>
                        <?php echo $property->garage === "1" ? " ( " . $property->garage_area . " " . JText::_("SQM") . " ) " : "" ?>

                    </td>
                </tr>
                <tr>
                    <th><?php echo JText::_("ELEVATOR") ?></th>
                    <td><?php echo $property->elevator === "0" ? JText::_("_NO") : JText::_("_YES") ?></td>
                </tr>
                <?php if (getParam('countryCode') == 'is') { ?>
                <tr>
                    <th><?php echo JText::_("PROPERTY APPRISAL") ?></th>
                    <td><?php echo WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_CURRENCY)->formatCurrency($property->property_appraisal); ?></td>
                </tr>
                <tr>
                    <th><?php echo JText::_("FIRE APPRISAL") ?></th>
                    <td><?php echo WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_CURRENCY)->formatCurrency($property->fire_appraisal); ?></td>
                </tr>
                <?php } ?>
            </table>

        </div>
    </div>
</div>
