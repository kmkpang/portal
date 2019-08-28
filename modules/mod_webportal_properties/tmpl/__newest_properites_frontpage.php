<?php
/**
 * Created by PhpStorm.
 * User: khan
 * Date: 1/11/15
 * Time: 3:30 PM
 */
/**
 * @var $property PropertyListModel
 * @var $image PortalPortalPropertyImagesSql
 * */
//$images = $property->images;
$image = $property->list_page_thumb_path;
$address = WFactory::getSqlService()->getSqlServiceClass(__PROPPERTY_PORTAL_ADDRESS)->getAddressByLanguage($property_id,$lang);
?>

<div class="webportalproperties-container--property shadow">

    <?php if ($property !== null) { ?>
        <a href="<?php echo $property->url_to_direct_page ?>">
            <div class="webportalproperties-container--property">

                <?php if (($property->is_new) && getParam('isNew') == 'true') { ?>
                    <div class="ribbon-wrapper-red">
                        <div class="ribbon-red"><?php echo JText::_("NEW") ?></div>
                    </div>
                <?php } if (($property->is_recent) && getParam('isNew') == 'true') { ?>
                    <div class="ribbon-wrapper-orange">
                        <div class="ribbon-orange"><?php echo JText::_("RECENT") ?></div>
                    </div>
                <?php } ?>

                <div class="property__img">
                    <img src="<?php echo $image ?>">
                </div>

                <div class="property__content">
                    <div class="property__name">
                        <h5><?php echo $property->category_name ?></h5>
                    </div>
                    <div class="property__address">
                        <?php if (getParam('countryCode') == 'is') { ?>
                            <span class="property-details__street"><?php echo $address; ?>,</span>
                            <span
                                class="property-details__zipcode-name"><?php echo $property->zip_code . " " . $property->zip_code_name ?> </span>

                        <?php } if ((getParam('countryCode') == 'th') && (getParam('propertyTitle') == 'false')) { ?>
                            <span class="property-details__street"><?php echo $address; ?> </span>
                            <span
                                class="property-details__zipcode-name"><?php echo $property->zip_code_name . " " . $property->zip_code ?> </span>
                        <?php } if ((getParam('countryCode') == 'th') && (getParam('propertyTitle') == 'true')) { ?>
                            <span class="property-details__street"><?php echo $property->title; ?></span>

                        <?php } if (getParam('countryCode') == 'ph') { ?>
                            <span class="property-details__street"><?php echo $address; ?> </span>
                            <span
                                class="property-details__zipcode-name"><?php echo $property->zip_code_name; ?> </span>
                        <?php } ?>
                    </div>
                    <div class="property__description">
                        <h4><small class="text-uppercase"><?php echo strtoupper(JText::_($property->buy_rent)."_PRICE") . "</small> <span class='price-color'>" . $property->current_listing_price_formatted . "</span>" ?></h4>
                    </div>
                    <div class="property__roomsize row">
                        <?php if (getParam('countryCode') == 'is') { ?>
                            <div class="column large-12 small-12 room">
                                <?php echo $property->total_number_of_rooms . ' ' . JText::_("ROOMS") ?>
                            </div>
                            <div class="column large-12 small-12 size">
                                <?php echo round($property->total_area) . ' ' . JText::_("SQM") ?>
                            </div>
                        <?php } if (getParam('countryCode') == 'th') { ?>
                            <div class="column large-8 small-8 room">
                                <?php echo $property->number_of_bedrooms . ' ' . JText::_("BEDROOMS") ?>
                            </div>
                            <div class="column large-8 small-8 room">
                                <?php echo $property->number_of_bathrooms . ' ' . JText::_("BATHROOMS") ?>
                            </div>
                            <div class="column large-8 small-8 size">
                                <?php echo round($property->total_area) . ' ' . JText::_("SQM") ?>
                            </div>
                        <?php } if (getParam('countryCode') == 'ph') { ?>
                            <div class="column large-12 small-12 room">
                                <?php echo $property->total_number_of_rooms . ' ' . JText::_("ROOMS") ?>
                            </div>
                            <div class="column large-12 small-12 size">
                                <?php echo round($property->total_area) . ' ' . JText::_("SQM") ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </a>

    <?php } ?>
</div>