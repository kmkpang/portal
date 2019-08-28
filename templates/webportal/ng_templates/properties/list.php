<?php
require_once(JPATH_BASE . DS . 'templates' . DS . 'generic' . DS . 'controllers' . DS . 'helpers.php');

?>

<div ng-init="filter()" ng-switch on="selection">

    <div ng-show="listloading" class=""><?php WFactory::getHelper()->getLoadingIcon(); ?></div>
    <div ng-show="!listloading && items.length == 0" class="ng-cloak"><?php echo JText::_("COM_WEBPORTAL_PROPERTIES_NONE")?></div>

    <div ng-show="!listloading;" class="ng-cloak" ng-switch-when="list">

        <div ng-repeat="item in items" class="property-list__item">

            <article class="row-item clearfix">

                <div class="row collapse">

                    <!------------------- Column 1 ------------------>

                    <div class="small-24 medium-6 column">

                        <div class="row-item__thumbnail-wrapper">
                            <?php if (WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_TEMPLATE)->getParam('isBatch') == 'true') { ?>
                                <div class="row-item__send-email checkbox-one" ng-hide="!sendEmail">
                                    <!-- DO NOT FUCKING CHANGE THIS -->
                                    <input type="checkbox" id="{{item.property_id}}"
                                           ng-change="updatePropertiesForEmail()"
                                           ng-model="propertiesForEmail[item.property_id]" />
                                    <label for="{{item.property_id}}"></label>
                                </div>
                            <?php } ?>
                            
                            <a href="{{ item.url_to_direct_page }}">
                                <img ng-src={{item.list_page_thumb_path}} class="row-item__thumbnail" alt="{{item.address}} {{item.property_region_town_zip_formatted}}"/>
                            </a>

                            <?php if (getParam('isNew') == 'true') { ?>
                                <div class="ribbon-wrapper-red" ng-show="item.is_new">
                                    <div class="ribbon-red"><?php echo JText::_("NEW") ?></div>
                                </div>
                                <div class="ribbon-wrapper-orange" ng-show="item.is_recent">
                                    <div class="ribbon-orange"><?php echo JText::_("RECENT") ?></div>
                                </div>
                            <?php } ?>

                            <?php if (getParam('isFeatured') == 'true') { ?>
                                <div class="ribbon-wrapper-red" ng-show="item.is_featured">
                                    <div class="ribbon-red"><?php echo JText::_("FEATURED") ?></div>
                                </div>
                            <?php } ?>
                            
                            <div ng-show="item.open_house" ng-class="{'opening': item.open_house_now}" class="property--openhouse list">
                                <span><i class="fa fa-calendar"></i> <?php echo JText::_("OPEN_HOUSE_START") . ' ' . '{{item.open_house_start}}' . ' - ' . '{{item.open_house_end | limitTo: -5}}' ; ?></span>
                            </div>
                        </div>

                    </div>
                    <!------------------- Column 2 ------------------>
                    <div class="small-24 medium-18 column" ng-click="onPropertyRowClicked('{{ item.url_to_direct_page }}')">

                        <div class="row-item__information-wrapper">

                            <div class="row row-item__header-wrapper collapse">
                                <h2 class="row-item__address column medium-16 small-24">
                                    <?php if(getParam(propertyTitle) == 'true') { ?>
                                        {{item.title}}
                                    <?php } else { ?>
                                        <span class="row-item__street_name">{{item.address}}, </span>
                                        <span class="row-item__code_name">{{item.property_region_town_zip_formatted}}</span>
                                    <?php } ?>
                                </h2>
                                <h2 class="row-item__price medium-8 small-24 column">
                                    <i class="fa fa-tags"></i>
                                    <span class="text-uppercase" ng-show="item.buy_rent == 'SALE'"><?php echo strtoupper(JText::_("SALE_PRICE")); ?></span>
                                    <span class="text-uppercase" ng-show="item.buy_rent == 'RENT'"><?php echo strtoupper(JText::_("RENT_PRICE")); ?></span>
                                    <span class="price-color">{{item.current_listing_price_formatted}}</span>
                                </h2>
                            </div>

                            <?php if (getParam('countryCode') == 'is') { ?>
                                <div class="row-item__details-wrapper clearfix">

                                    <div class="row-item__property-type column medium-8 small-12 no-pad-left">
                                        <h3 class="row-item__property-type medium-21 small-21"><i class="fa fa-home" ng-show="item.residential_commercial == 'RESIDENTIAL'"></i><i class="fa fa-building" ng-show="item.residential_commercial == 'COMMERCIAL'"></i> {{item.category_name}}</h3>
                                    </div>
                                    <div class="column medium-4 small-6">
                                        <h3 class="row-item__rooms">{{item.total_number_of_rooms | number}}
                                            <span class="area--unit"><?php echo JText::_("ROOMS") ?></span></h3>
                                    </div>

                                    <div class="column medium-4 small-6">
                                        <h3 class="row-item__area">{{item.total_area | number}} <span class="area--unit"><?php echo JText::_("SQM")?></span>
                                        </h3>
                                    </div>
                                    <h3 class="column medium-8 small-24 no-pad-right">

                                    </h3>
                                </div>
                            <?php } if (getParam('countryCode') == 'th') { ?>
                                <div class="row-item__details-wrapper clearfix">

                                    <div class="row-item__property-type column medium-8 small-12 no-pad-left">
                                        <h3 class="row-item__property-type medium-21 small-21"><i class="fa fa-home" ng-show="item.residential_commercial == 'RESIDENTIAL'"></i><i class="fa fa-building" ng-show="item.residential_commercial == 'COMMERCIAL'"></i> {{item.category_name}}</h3>
                                    </div>

                                    <div class="column medium-4 small-12"
                                         ng-if="item.total_number_of_rooms > 0"
                                         ng-show="item.residential_commercial == 'COMMERCIAL' &&
                                              item.category_id !== '106' ||
                                              item.category_id == '105' ||
                                              item.category_id == '127' ||
                                              item.category_id == '130'">
                                        <h3 class="row-item__rooms">{{item.total_number_of_rooms | number}}
                                            <span class="area--unit"><?php echo JText::_("ROOMS") ?></span></h3>
                                    </div>

                                    <div class="column medium-4 small-6"
                                         ng-if="item.number_of_bedrooms > 0"
                                         ng-show="item.residential_commercial == 'RESIDENTIAL' &&
                                              item.category_id == '101' ||
                                              item.category_id == '102' ||
                                              item.category_id == '103' ||
                                              item.category_id == '118' ||
                                              item.category_id == '128'">
                                        <h3 class="row-item__rooms">{{item.number_of_bedrooms | number}}
                                            <span class="area--unit"><?php echo JText::_("BEDROOMS") ?></span></h3>
                                    </div>
                                    <div class="column medium-4 small-6"
                                         ng-if="item.number_of_bathrooms > 0"
                                         ng-show="item.residential_commercial == 'RESIDENTIAL' &&
                                              item.category_id == '101' ||
                                              item.category_id == '102' ||
                                              item.category_id == '103' ||
                                              item.category_id == '118' ||
                                              item.category_id == '128'">
                                        <h3 class="row-item__rooms">{{item.number_of_bathrooms | number}}
                                            <span class="area--unit"><?php echo JText::_("BATHROOMS") ?></span></h3>
                                    </div>

                                    <div class="column medium-4 small-12"
                                         ng-if="item.category_id == '130' &&
                                            item.number_of_floors">
                                        <h3 class="row-item__area">
                                            {{item.number_of_floors | number}} <span class="area--unit"><?php echo JText::_("NUMBER_OF_FLOOR")?></span>
                                        </h3>
                                    </div>

                                    <div class="column medium-4 small-12"
                                         ng-if="item.current_listing_price > 0"
                                         ng-show="item.residential_commercial == 'RESIDENTIAL' &&
                                              item.category_id == '103' ||
                                              item.category_id == '128'">
                                        <h3 class="row-item__area">
                                            {{item.current_listing_price / item.total_area | number:0}} <span class="area--unit"><?php echo JText::_("PRICE PER SQ METER")?></span>
                                        </h3>
                                    </div>

                                    <div class="column medium-4 small-12"
                                         ng-if="item.total_area >= 32000 &&
                                            item.category_id !== '130' &&
                                            item.current_listing_price > 0">
                                        <h3 class="row-item__area">
                                            {{(item.current_listing_price / (item.total_area / 1600)) | number:0}} <span class="area--unit"><?php echo JText::_("PRICE PER RAI")?></span>
                                        </h3>
                                    </div>

                                    <div class="column medium-4 small-12"
                                         ng-if="item.total_area <= 31999 &&
                                            item.category_id !== '103' &&
                                            item.category_id !== '128' &&
                                            item.category_id !== '130' &&
                                            item.current_listing_price > 0">
                                        <h3 class="row-item__area">
                                            {{(item.current_listing_price / (item.total_area / 4)) | number:0}} <span class="area--unit"><?php echo JText::_("PRICE PER SQ WHA")?></span>
                                        </h3>
                                    </div>

                                    <div class="column medium-4 small-12"
                                         ng-if="item.living_area > 0">
                                        <h3 class="row-item__area">
                                            <span class="area--unit"><?php echo JText::_("LIVING AREA")?></span> {{item.living_area | number}} <span class="area--unit"><?php echo JText::_("SQM")?></span>
                                        </h3>
                                    </div>

                                    <div class="column medium-4 small-12 no-pad-right"
                                         ng-if="item.residential_commercial == 'RESIDENTIAL' &&
                                              item.category_id == '103' ||
                                              item.category_id == '128' ">
                                        <h3 class="row-item__area">
                                            {{item.total_area | number}} <span class="area--unit"><?php echo JText::_("SQM")?></span>
                                        </h3>
                                    </div>

                                    <div class="column medium-4 small-12 no-pad-right"
                                         ng-if="item.total_area >= 1600">
                                        <h3 class="row-item__area">
                                            {{(item.total_area / 1600) | round:1:'down'}} <span class="area--unit"><?php echo JText::_("RAI")?></span>
                                            <span ng-show="(item.total_area % 1600 / 4) | round:1:'down' != 0">{{(item.total_area % 1600 / 4) | round:1:'down'}} <span class="area--unit"><?php echo JText::_("SQW")?></span></span>
                                        </h3>
                                    </div>

                                    <div class="column medium-4 small-12 no-pad-right"
                                         ng-if="item.total_area <= 1599 &&
                                            item.category_id !== '103' &&
                                            item.category_id !== '128'">
                                        <h3 class="row-item__area">
                                            {{(item.total_area % 1600 / 4) | round:1:'down'}} <span class="area--unit"><?php echo JText::_("SQW")?></span>
                                        </h3>
                                    </div>
                                </div>

                            <?php } if (getParam('countryCode') == 'ph') { ?>
                                <div class="row-item__details-wrapper clearfix">

                                    <div class="row-item__property-type column medium-8 small-12 no-pad-left">
                                        <h3 class="row-item__property-type medium-21 small-21"><i class="fa fa-home" ng-show="item.residential_commercial == 'RESIDENTIAL'"></i><i class="fa fa-building" ng-show="item.residential_commercial == 'COMMERCIAL'"></i> {{item.category_name}}</h3>
                                    </div>

                                    <div class="column medium-4 small-6" ng-show="item.residential_commercial == 'COMMERCIAL'">
                                        <h3 class="row-item__rooms">{{item.total_number_of_rooms | number}}
                                            <span class="area--unit"><?php echo JText::_("ROOMS") ?></span></h3>
                                    </div>

                                    <div class="column medium-4 small-6" ng-show="item.residential_commercial == 'RESIDENTIAL'">
                                        <h3 class="row-item__rooms">{{item.number_of_bedrooms | number}}
                                            <span class="area--unit"><?php echo JText::_("BEDROOMS") ?></span></h3>
                                    </div>
                                    <div class="column medium-4 small-6" ng-show="item.residential_commercial == 'RESIDENTIAL'">
                                        <h3 class="row-item__rooms">{{item.number_of_bathrooms | number}}
                                            <span class="area--unit"><?php echo JText::_("BATHROOMS") ?></span></h3>
                                    </div>

                                    <h3 class="column medium-8 small-24 no-pad-right">

                                    </h3>
                                </div>
                            <?php } ?>

                            <div class="medium-24">
                                <div class="row-item__description" ng-bind-html="item.description_text">
                                    <b><?php echo JText::_("COM_WEBPORTAL_PROPERTIES_NO_DETAIL")?></b>
                                </div>
                                <div class="row-item__distance"
                                     ng-show="searchfilter.latitude_display && searchfilter.longitude_display"
                                    >
                                    <?php echo JText::_("DISTANCE FROM")?> {{searchfilter.transport_name_display}} : {{item.distance | number:2 }} km
                                </div>

                                <?php if (getParam('propertyID') == 'true') { ?>
                                    <div class="row-item__regid">
                                        <span class="row-item__reg-id ng-binding"><?php echo JText::_("ID"); ?> : {{item.reg_id}}</span>
                                    </div>
                                <?php } ?>
                            </div>

                        </div>

                    </div>

                </div>

            </article>

        </div>

    </div>

    <div ng-show="!listloading;" class="ng-cloak grid row" ng-switch-when="grid">
        <ul class="property-grid__item">
            <li ng-repeat="item in items" class="property-grid__item column medium-6">

                <article class="row-item clearfix" ng-click="onPropertyRowClicked('{{ item.url_to_direct_page }}')">

                    <div class="row collapse">

                        <!------------------- Thumbnail ------------------>

                        <div class="small-24">

                            <div class="row-item__thumbnail-wrapper">
                                <?php if (WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_TEMPLATE)->getParam('isBatch') == 'true') { ?>
                                    <div class="row-item__send-email checkbox-one" ng-hide="!sendEmail">
                                        <!-- DO NOT FUCKING CHANGE THIS -->
                                        <input type="checkbox" id="{{item.property_id}}"
                                        ng-change="updatePropertiesForEmail()"
                                        ng-model="propertiesForEmail[item.property_id]" />
                                        <label for="{{item.property_id}}"></label>
                                    </div>

                                <?php } ?>

                                <a href="{{ item.url_to_direct_page }}">
                                    <img ng-src={{item.list_page_thumb_path}} class="row-item__thumbnail"/>
                                </a>

                                <?php if (getParam('isNew') == 'true') { ?>
                                    <div class="ribbon-wrapper-red" ng-show="item.is_new">
                                        <div class="ribbon-red"><?php echo JText::_("NEW") ?></div>
                                    </div>
                                    <div class="ribbon-wrapper-orange" ng-show="item.is_recent">
                                        <div class="ribbon-orange"><?php echo JText::_("RECENT") ?></div>
                                    </div>
                                <?php } ?>

                                <div ng-show="item.open_house" ng-class="{'opening': item.open_house_now}" class="property--openhouse list">
                                    <span><i class="fa fa-calendar"></i> <?php echo JText::_("OPEN_HOUSE_START") . ' ' . '{{item.open_house_start}}' . ' - ' . '{{item.open_house_end | limitTo: -5}}' ; ?></span>
                                </div>
                            </div>

                        </div>
                        <!------------------- Description ------------------>
                        <div class="small-24">

                            <div class="row-item__information-wrapper">

                                <div class="row row-item__header-wrapper">

                                    <div class="row-item__property-type medium-24 small-24">
                                        <h3 class="row-item__property-type medium-24 small-24"><i class="fa fa-home"></i> {{item.category_name}}</h3>
                                    </div>

                                    <h2 class="row-item__address small-24">
                                        <?php if (getParam('countryCode') == 'is') { ?>
                                            <span class="row-item__street_name">{{item.address}}, </span>
                                            <span class="row-item__code_name">{{item.zip_code}} {{item.zip_code_name}}</span>
                                        <?php } if ((getParam('countryCode') == 'th') && (getParam('propertyTitle') == 'false')) { ?>
                                            <span class="row-item__street_name">{{item.address}} </span>
                                            <span class="row-item__code_name">{{item.zip_code_name}} {{item.zip_code}}</span>
                                        <?php } if ((getParam('countryCode') == 'th') && (getParam('propertyTitle') == 'true')) { ?>
                                            <span class="row-item__street_name">{{item.title}}</span>
                                        <?php } if (getParam('countryCode') == 'ph') { ?>
                                            <span class="row-item__street_name">{{item.address}} </span>
                                            <span class="row-item__code_name">{{item.zip_code_name}}</span>
                                        <?php } ?>
                                    </h2>

                                    <h2 class="row-item__price small-24 medium-24">
                                        <i class="fa fa-tags"></i>
                                        <span ng-show="item.buy_rent == 'SALE'"><?php echo strtoupper(JText::_("SALE")); ?></span>
                                        <span ng-show="item.buy_rent == 'RENT'"><?php echo strtoupper(JText::_("RENT")); ?></span>
                                        <span class="price-color">{{item.current_listing_price_formatted}}</span>
                                    </h2>
                                </div>

                                <?php if (getParam('countryCode') == 'is') { ?>
                                    <div class="row-item__details-wrapper clearfix">

                                        <div class="column medium-12 small-12">
                                            <h3 class="row-item__rooms">{{item.total_number_of_rooms}}
                                                <span class="area--unit"><?php echo JText::_("ROOMS") ?></span></h3>
                                        </div>

                                        <div class="column medium-12 small-12">
                                            <h3 class="row-item__area">{{item.total_area}} <span class="area--unit"><?php echo JText::_("SQM")?></span>
                                            </h3>
                                        </div>

                                    </div>
                                <?php } if (getParam('countryCode') == 'th') { ?>
                                    <div class="row-item__details-wrapper clearfix">
                                        <div class="column medium-12 small-12"
                                             ng-if="item.total_number_of_rooms > 0"
                                             ng-show="item.residential_commercial == 'COMMERCIAL' &&
                                                  item.category_id !== '106' ||
                                                  item.category_id == '105' ||
                                                  item.category_id == '127' ||
                                                  item.category_id == '130'">
                                            <h3 class="row-item__rooms">{{item.total_number_of_rooms | number}}
                                                <span class="area--unit"><?php echo JText::_("ROOMS") ?></span></h3>
                                        </div>

                                        <div class="column medium-12 small-12"
                                             ng-if="item.number_of_bedrooms > 0"
                                             ng-show="item.residential_commercial == 'RESIDENTIAL' &&
                                                  item.category_id == '101' ||
                                                  item.category_id == '102' ||
                                                  item.category_id == '103' ||
                                                  item.category_id == '118' ||
                                                  item.category_id == '128'">
                                            <h3 class="row-item__rooms">{{item.number_of_bedrooms | number}}
                                                <span class="area--unit"><?php echo JText::_("BEDROOMS") ?></span></h3>
                                        </div>
                                        <div class="column medium-12 small-12"
                                             ng-if="item.number_of_bathrooms > 0"
                                             ng-show="item.residential_commercial == 'RESIDENTIAL' &&
                                                  item.category_id == '101' ||
                                                  item.category_id == '102' ||
                                                  item.category_id == '103' ||
                                                  item.category_id == '118' ||
                                                  item.category_id == '128'">
                                            <h3 class="row-item__rooms">{{item.number_of_bathrooms | number}}
                                                <span class="area--unit"><?php echo JText::_("BATHROOMS") ?></span></h3>
                                        </div>

                                        <div class="column medium-12 small-12"
                                             ng-if="item.category_id == '130' &&
                                                item.number_of_floors">
                                            <h3 class="row-item__area">
                                                {{item.number_of_floors | number}} <span class="area--unit"><?php echo JText::_("NUMBER_OF_FLOOR")?></span>
                                            </h3>
                                        </div>

                                        <div class="column medium-12 small-12"
                                             ng-if="item.current_listing_price > 0"
                                             ng-show="item.residential_commercial == 'RESIDENTIAL' &&
                                                  item.category_id == '103' ||
                                                  item.category_id == '128'">
                                            <h3 class="row-item__area">
                                                {{item.current_listing_price / item.total_area | number:0}} <span class="area--unit"><?php echo JText::_("PRICE PER SQ METER")?></span>
                                            </h3>
                                        </div>

                                        <div class="column medium-12 small-12"
                                             ng-if="item.total_area >= 32000 &&
                                                item.category_id !== '130' &&
                                                item.current_listing_price > 0">
                                            <h3 class="row-item__area">
                                                {{(item.current_listing_price / (item.total_area / 1600)) | number:0}} <span class="area--unit"><?php echo JText::_("PRICE PER RAI")?></span>
                                            </h3>
                                        </div>

                                        <div class="column medium-12 small-12"
                                             ng-if="item.total_area <= 31999 &&
                                                item.category_id !== '103' &&
                                                item.category_id !== '128' &&
                                                item.category_id !== '130' &&
                                                item.current_listing_price > 0">
                                            <h3 class="row-item__area">
                                                {{(item.current_listing_price / (item.total_area / 4)) | number:0}} <span class="area--unit"><?php echo JText::_("PRICE PER SQ WHA")?></span>
                                            </h3>
                                        </div>

                                        <div class="column medium-12 small-12"
                                             ng-if="item.living_area > 0">
                                            <h3 class="row-item__area">
                                                <span class="area--unit"><?php echo JText::_("LIVING AREA")?></span> {{item.living_area | number}} <span class="area--unit"><?php echo JText::_("SQM")?></span>
                                            </h3>
                                        </div>

                                        <div class="column medium-12 small-12"
                                             ng-if="item.residential_commercial == 'RESIDENTIAL' &&
                                                  item.category_id == '103' ||
                                                  item.category_id == '128' ">
                                            <h3 class="row-item__area">
                                                {{item.total_area | number}} <span class="area--unit"><?php echo JText::_("SQM")?></span>
                                            </h3>
                                        </div>

                                        <div class="column medium-12 small-12"
                                             ng-if="item.total_area >= 1600">
                                            <h3 class="row-item__area">
                                                {{(item.total_area / 1600) | round:1:'down'}} <span class="area--unit"><?php echo JText::_("RAI")?></span>
                                                <span ng-show="(item.total_area % 1600 / 4) | round:1:'down' != 0">{{(item.total_area % 1600 / 4) | round:1:'down'}} <span class="area--unit"><?php echo JText::_("SQW")?></span></span>
                                            </h3>
                                        </div>

                                        <div class="column medium-12 small-12"
                                             ng-if="item.total_area <= 1599 &&
                                                item.category_id !== '103' &&
                                                item.category_id !== '128'">
                                            <h3 class="row-item__area">
                                                {{(item.total_area % 1600 / 4) | round:1:'down'}} <span class="area--unit"><?php echo JText::_("SQW")?></span>
                                            </h3>
                                        </div>
                                    </div>

                                <?php } if (getParam('countryCode') == 'ph') { ?>
                                    <div class="row-item__details-wrapper clearfix">

                                        <div class="column medium-12 small-12">
                                            <h3 class="row-item__rooms">{{item.total_number_of_rooms}}
                                                <span class="area--unit"><?php echo JText::_("ROOMS") ?></span></h3>
                                        </div>

                                        <div class="column medium-12 small-12">
                                            <h3 class="row-item__area">{{item.total_area}} <span class="area--unit"><?php echo JText::_("SQM")?></span>
                                            </h3>
                                        </div>

                                    </div>
                                <?php } ?>

                                <?php /*
                            <div class="medium-24">

                                <div class="row-item__description" ng-bind-html="item.description_text">
                                    <b><?php echo JText::_("COM_WEBPORTAL_PROPERTIES_NO_DETAIL")?></b>
                                </div>

                                <div class="row-item__distance"
                                     ng-show="searchfilter.latitude_display && searchfilter.longitude_display"
                                    >
                                    <?php echo JText::_("DISTANCE FROM")?> {{searchfilter.transport_name_display}} : {{item.distance | number:2 }} km
                                </div>

                                <?php if (getParam('propertyID') == 'true') { ?>
                                    <div class="row-item__regid">
                                        <span class="row-item__reg-id ng-binding"><?php echo JText::_("ID"); ?> : {{item.property_id}}</span>
                                    </div>
                                <?php } ?>
                            </div>
                            */?>
                            </div>

                        </div>

                    </div>

                </article>

            </li>
        </ul>
    </div>
</div>