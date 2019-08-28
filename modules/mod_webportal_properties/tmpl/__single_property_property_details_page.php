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

<div class="webportalproperties-container--property item" >

    <?php if ($property !== null) { ?>
        <a href="<?php echo $property->url_to_direct_page ?>">
            <div class="webportalproperties--property <?php echo $style?>" >
                <div class="property__img">
                    <img src="<?php echo $image ?>">

                    <?php if (($property->is_new) && getParam('isNew') == 'true') { ?>
                        <div class="ribbon-wrapper-red">
                            <div class="ribbon-red"><?php echo JText::_("NEW") ?></div>
                        </div>
                    <?php } if (($property->is_recent) && getParam('isNew') == 'true') { ?>
                        <div class="ribbon-wrapper-orange">
                            <div class="ribbon-orange"><?php echo JText::_("RECENT") ?></div>
                        </div>
                    <?php } ?>

                    <?php if ($property->open_house) { ?>
                    <div class="property--openhouse thumb <?php echo $property->open_house_now?"opening":"" ?>"
                        <span>
                            <i class="fa fa-calendar"></i>
                            <?php echo JText::_("OPEN_HOUSE") . ' ' . $property->open_house_start . ' - ' . substr($property->open_house_end,-5); ?>
                        </span>
                    </div>
                    <?php } ?>

                </div>
                <div class="property__content">
                    <div class="property__name">
                        <h5><?php echo $property->category_name ?></h5>
                    </div>

                    <div class="property__address">
                         <?php if(getParam(propertyTitle) == 'true') { ?>
                             <?php echo $property->title; ?>
                         <?php } else { ?>
                             <span class="property-details__street">
                                <?php echo $address; ?> 
                             </span>
                             <span class="property-details__zipcode-name">
                                <?php echo $property->property_region_town_zip_formatted ?>&nbsp;
                             </span>
                         <?php } ?>
                    </div>
                    <div class="property__price">
                        <h4>
                            <span class="text-uppercase"><?php echo strtoupper(JText::_($property->buy_rent."_PRICE")) ?></span> <span class="price-color"><?php echo $property->current_listing_price_formatted ?></span>
                        </h4>
                    </div>
                    <div class="property__roomsize row">
                        <?php if (getParam('countryCode') == 'is') { ?>
                            <div class="column large-12 small-12 room">
                                <?php echo $property->total_number_of_rooms . ' ' . JText::_("ROOMS") ?>
                            </div>
                            <div class="column large-12 small-12 size">
                                <?php echo number_format(round($property->total_area)) . ' ' . JText::_("SQM") ?>
                            </div>
                        <?php } if (getParam('countryCode') == 'th') {
                        // House, etc...
                        if($property->category_id == 105 || $property->category_id == 108 || $property->category_id == 114) { ?>
                            <div class="property__roomsize">
                                <div class="column large-8 small-8 room">
                                    <?php echo JText::_("BEDROOMS") . '<br />' . $property->number_of_bedrooms == 0 ? JText::_("BEDROOMS") . '<br />' . JText::_("STUDIO") : $property->number_of_bedrooms ?>
                                </div>
                                <div class="column large-8 small-8 room">
                                    <?php echo JText::_("BATHROOMS") . '<br />' . $property->number_of_bathrooms ?>
                                </div>
                                <div class="column large-8 small-8 size">
                                    <?php echo JText::_("SIZE") . '<br />' . $property->total_area = 0 ? JText::_("SIZE") . '<br />' . 0 : WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTY)->convertSizeUnit($property->total_area) ?>
                                </div>
                            </div>
                        <?php }
                        // Condo
                        if($property->category_id == 103 || $property->category_id == 128) { ?>
                            <div class="property__roomsize">
                                <div class="column large-8 small-8 room">
                                    <?php echo JText::_("BEDROOMS") . '<br />' . $property->number_of_bedrooms == 0 ? JText::_("BEDROOMS") . '<br />' . JText::_("STUDIO") : $property->number_of_bedrooms ?>
                                </div>
                                <div class="column large-8 small-8 room">
                                    <?php echo JText::_("BATHROOMS") . '<br />' . $property->number_of_bathrooms ?>
                                </div>
                                <div class="column large-8 small-8 size">
                                    <?php echo JText::_("SIZE") . '<br />' . $property->total_area = 0 ? JText::_("SIZE") . '<br />' . 0 : number_format(round($property->total_area)) . ' ' . JText::_("SQM") ?>
                                </div>
                            </div>
                        <?php }
                        // Land
                        else if($property->category_id == 106) { ?>
                            <div class="property__roomsize">
                                <div class="column large-24 small-24 size">
                                    <?php echo JText::_("SIZE") . '<br />' . $property->total_area = 0 ? JText::_("SIZE") . '<br />' . 0 : WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTY)->convertSizeUnit($property->total_area) ?>
                                </div>
                            </div>
                        <?php }
                        // Apartment
                        else if($property->category_id == 130 || $property->category_id == 127) { ?>
                            <div class="property__roomsize">
                                <div class="column large-12 small-12 room">
                                    <?php echo JText::_("ROOMS") . '<br />' . $property->total_number_of_rooms ?>
                                </div>
                                <div class="column large-12 small-12 room">
                                    <?php echo JText::_("FLOOR") . '<br />' . $property->number_of_floors ?>
                                </div>
                            </div>
                        <?php }
                        // Others
                        else { ?>
                            <div class="property__roomsize">
                                <div class="column large-24 small-24 size">
                                    <?php echo JText::_("SIZE") . '<br />' . $property->total_area = 0 ? JText::_("SIZE") . '<br />' . 0 : WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTY)->convertSizeUnit($property->total_area) ?>
                                </div>
                            </div>
                        <?php }
                        } if (getParam('countryCode') == 'ph') { ?>
                            <div class="column large-12 small-12 room">
                                <?php echo $property->total_number_of_rooms . ' ' . JText::_("ROOMS") ?>
                            </div>
                            <div class="column large-12 small-12 size">
                                <?php echo number_format(round($property->total_area)) . ' ' . JText::_("SQM") ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </a>

    <?php } ?>
</div>