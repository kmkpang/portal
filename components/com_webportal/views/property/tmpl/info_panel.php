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

        <div class="columns small-12">
            <div class="property-details__buy-rent">
                <span class="text-uppercase"><?php echo JText::_(strtoupper($property->buy_rent."_PRICE")) ?></span>
            </div>
        </div>
        <div class="columns small-12 text-right">
                <span class="property-details__price"><?php echo $property->current_listing_price_formatted; ?></span>
        </div>
    </div>

    <div class="row collapse property-details__category">
        <div class="columns small-12">
            <span class="property-details__category-name"><?php echo $property->category_name; ?></span>
        </div>
        <?php if (__COUNTRY == "TH") { ?>
            <div class="columns small-12 text-right">
                <?php if ($property->total_area < 400) { ?>
                    <span class="property-details__area-name"><?php echo number_format((float)$property->total_area, 1, '.', ''); ?>
                        <span class="area--unit"><?php echo JText::_("SQM"); ?></span></span>
                <?php } else { ?>
                    <span class="property-details__area-name"><?php echo $property->total_area == 0 ? 0 : WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTY)->convertSizeUnit($property->total_area) ?></span>
                <?php } ?>
            </div>
        <?php } else { ?>
            <div class="columns small-12 text-right">
                <span class="property-details__area-name"><?php echo number_format((float)$property->total_area, 1, '.', ''); ?>
                    <span class="area--unit"><?php echo JText::_("SQM"); ?></span></span>
            </div>
        <?php } ?>
    </div>

    <div class="row collapse">

        <div class="property-details__property-table">
            <table>
                <?php if (__COUNTRY == "TH") { ?>
                <tr>
                    <th><?php echo JText::_("AREA") ?></th>
                    <td><span class="property-details__area-name"><?php echo $property->total_area == 0 ? 0 : WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTY)->convertSizeUnit($property->total_area) ?></span></td>
                </tr>
                    
                <tr>
                    <th><?php if($property->total_area >= 32000) {
                            echo JText::_("PRICE PER RAI");
                        } else if($property->category_id == 103 || $property->category_id == 105 || $property->category_id == 108 || $property->category_id == 114) {
                            echo JText::_("PRICE PER SQ METER");
                        } else {
                            echo JText::_("PRICE PER SQ WHA");
                        } ?>
                    </th>
                    <td><span class="property-details__area-name"><?php echo $property->total_area == 0 ? 0 : WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTY)->pricePerArea($property->current_listing_price , $property->total_area, $property->category_id) ?></span></td>
                </tr>
                <?php } ?>
                
                <tr>

                    <?php if ($property->residential_commercial == 'COMMERCIAL') { 
                        require "info_panel_commercial.php"; 
                    } else { 
                        require "info_panel_residential.php";
                    } ?>
                </tr>
                <?php if (!empty($property->number_of_floors) && ($property->number_of_floors > 0)) { ?>
                    <tr>
                        <th><?php echo JText::_("NUMBER_OF_FLOOR") ?></th>
                        <td><?php echo number_format((float)$property->number_of_floors, 1, '.', ''); ?></td>
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
