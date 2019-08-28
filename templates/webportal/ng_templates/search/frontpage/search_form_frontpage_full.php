<?php
require_once(JPATH_BASE . DS . 'templates' . DS . 'generic' . DS . 'controllers' . DS . 'helpers.php');

$app = JFactory::getApplication();
$menu = $app->getMenu();
$menuMapItem = $menu->getItems( 'link', 'index.php?option=com_webportal&view=map', true );
$menuListItem = $menu->getItems( 'link', 'index.php?option=com_webportal&view=list', true );

$columns = array(getParam('loan80'), getParam('garage'), getParam('elevator'), getParam('newToday'), getParam('newWeek'));
?>

    <div class="small-24 search-filters--frontpage--column row">
        <!-- COLUMN 1 -->
        <div class="column large-18">

            <!-- ------------------------ ROW 1 START  --------------------------------------- -->
            <div class="row">

                <div class="column large-16 search-filters--frontpage--row clearfix">
                    <div class="input-textbox--wrapper">
                        <input type="text" ng-model="searchfilter.text"
                               placeholder="<?php echo JText::_('SEARCH HOMES') ?>"/>
                    </div>
                </div>

                <div
                    class="column large-8 search-filters--frontpage--row search-filters--frontpage--property-type clearfix">
                    <?php require_once JPATH_BASE . DS . 'templates/webportal/ng_templates/search/frontpage/property_category_select_frontpage.html' ?>
                </div>

            </div>

            <!-- ------------------------ ROW 2 START  --------------------------------------- -->
            <div class="row">

                <?php if (getParam('countryCode') == 'is') { ?>
                    <div class="columns large-16">
                        <?php if (getParam('loan80') == 1) { ?>
                            <span
                                class="search-home-filters__button small-24 medium-<?php if (getColumns($columns) == 5) {
                                    echo getColumns($columns) - 1;
                                } else {
                                    echo getColumns($columns);
                                } ?> column"
                                ng-class="searchfilter.loan80 ? 'active' : ''"
                                ng-click="searchfilter.loan80 = !searchfilter.loan80"><?php echo JText::_('LOAN80') ?>
                            </span>
                        <?php } ?>

                        <?php if (getParam('garage') == 1) { ?>
                            <span
                                class="search-home-filters__button small-24 medium-<?php echo getColumns($columns); ?> column"
                                ng-class="searchfilter.garage ? 'active' : ''"
                                ng-click="searchfilter.garage = !searchfilter.garage"><?php echo JText::_('GARAGE') ?>
                            </span>
                        <?php } ?>

                        <?php if (getParam('elevator') == 1) { ?>
                            <span
                                class="search-home-filters__button small-24 medium-<?php echo getColumns($columns); ?> column"
                                ng-class="searchfilter.elevator ? 'active' : ''"
                                ng-click="searchfilter.elevator = !searchfilter.elevator"><?php echo JText::_('ELEVATOR') ?>
                            </span>
                        <?php } ?>

                        <?php if (getParam('newToday') == 1) { ?>
                            <label for="new_today"
                                   class="search-home-filters__button small-24 medium-<?php echo getColumns($columns); ?> column"
                                   ng-class="searchfilter.new_today ? 'active' : ''"
                                   ng-click="searchfilter.new_today = !searchfilter.new_today ">
                                <?php echo JText::_('NEW_TODAY') ?>
                            </label>
                        <?php } ?>

                        <?php if (getParam('newWeek') == 1) { ?>
                            <input type="checkbox" id="new_this_week" name="new" class="hidden"/>
                            <label for="new_this_week"
                                   class="search-home-filters__button small-24 medium-<?php echo getColumns($columns); ?> column"
                                   ng-class="searchfilter.new_this_week ? 'active' : ''"
                                   ng-click="searchfilter.new_this_week = !searchfilter.new_this_week">
                                <?php echo JText::_('NEW_THIS_WEEK') ?>
                            </label>


                        <?php } ?>

                    </div>

                    <div class="columns large-8 search-filters--frontpage--row clearfix">
                        <div class="input-textbox--wrapper">
                            <multi-select
                                class="multiSelectClass"
                                input-model="postal_code_tree_multiselect"
                                output-model="postal_code_tree_multiselect_output"
                                on-item-click="geodata_filter_changed()"
                                on-select-none="geodata_filter_changed()"
                                on-reset="geodata_filter_changed()"
                                on-clear="geodata_filter_changed()"
                                on-open="bind_to_keypress_select()"
                                button-label="name"
                                default-label="<?php echo JText::_("SELECT LOCATION") ?>"
                                item-label="html"
                                tick-property="checked"
                                <?php // group-property="xx"  // the we DO NOT use grouping, because : 1. group header can not be selected , 2: 2.0.2 version seems buggy with nested groups!?>
                                selection-mode="multi"
                                helper-elements="reset filter"
                                max-labels="3"
                                max-height="400px"
                                >
                            </multi-select>
                        </div>
                    </div>


                <?php } ?>

                <?php if (getParam('countryCode') == 'th' || getParam('countryCode') == 'ph') { ?>

                    <div class="column large-16 small-24">

                        <div class="row collapse">
                            <div class="column small-24 medium-8">
                                <?php require_once JPATH_BASE . DS . 'templates/webportal/ng_templates/search/frontpage/postal_code_select_frontpage_province.php' ?>
                            </div>

                            <div class="column small-24 medium-8">
                                <?php require_once JPATH_BASE . DS . 'templates/webportal/ng_templates/search/frontpage/postal_code_select_frontpage_district.php' ?>
                            </div>

                            <div class="column small-24 medium-8">
                                <?php require_once JPATH_BASE . DS . 'templates/webportal/ng_templates/search/frontpage/postal_code_select_frontpage_postalcode.php' ?>
                            </div>
                        </div>

                    </div>

                    <div class="columns large-8 small-24">
                        <div class="search-filters--frontpage-wrapper">

                            <?php if (getParam('loan80') == 1) { ?>
                                <span
                                    class="search-home-filters__button small-24 medium-<?php if (getColumns($columns) == 5) {
                                        echo getColumns($columns) - 1;
                                    } else {
                                        echo getColumns($columns);
                                    } ?> column"
                                    ng-class="searchfilter.loan80 ? 'active' : ''"
                                    ng-click="searchfilter.loan80 = !searchfilter.loan80"><?php echo JText::_('LOAN80') ?>
                            </span>
                            <?php } ?>

                            <?php if (getParam('garage') == 1) { ?>
                                <span
                                    class="search-home-filters__button small-24 medium-<?php echo getColumns($columns); ?> column"
                                    ng-class="searchfilter.garage ? 'active' : ''"
                                    ng-click="searchfilter.garage = !searchfilter.garage"><?php echo JText::_('GARAGE') ?>
                            </span>
                            <?php } ?>

                            <?php if (getParam('elevator') == 1) { ?>
                                <span
                                    class="search-home-filters__button small-24 medium-<?php echo getColumns($columns); ?> column"
                                    ng-class="searchfilter.elevator ? 'active' : ''"
                                    ng-click="searchfilter.elevator = !searchfilter.elevator"><?php echo JText::_('ELEVATOR') ?>
                            </span>
                            <?php } ?>

                            <?php if (getParam('newToday') == 1) { ?>
                                <label for="new_today"
                                       class="search-home-filters__button small-24 medium-<?php echo getColumns($columns); ?> column"
                                       ng-class="searchfilter.new_today ? 'active' : ''"
                                       ng-click="searchfilter.new_today = !searchfilter.new_today ">
                                    <?php echo JText::_('NEW_TODAY') ?>
                                </label>
                            <?php } ?>

                            <?php if (getParam('newWeek') == 1) { ?>
                                <input type="checkbox" id="new_this_week" name="new" class="hidden"/>
                                <label for="new_this_week"
                                       class="search-home-filters__button small-24 medium-<?php echo getColumns($columns); ?> column"
                                       ng-class="searchfilter.new_this_week ? 'active' : ''"
                                       ng-click="searchfilter.new_this_week = !searchfilter.new_this_week">
                                    <?php echo JText::_('NEW_THIS_WEEK') ?>
                                </label>


                            <?php } ?>

                        </div>
                    </div>


                <?php } ?>

                <input type="checkbox" class="advanced_searchckbox" name="advanced_searchckbox"
                       id="advanced_searchckbox"
                       ng-model="showAdvancedSearch"/>


                <?php if ((getParam('countryCode') == 'th') && (getParam('showTransport') == 'true')) { ?>

                    <div id="search-filters--frontpage__bts-search">

                        <div id="bts_buttons" class="column">
                            <ul class="bts_button_tabs">

                                <li class="show_transports"><a
                                        ng-click="searchfilter.type_id = 0; searchfilter.search_bts = !searchfilter.search_bts;"
                                        ng-class="searchfilter.search_bts ? 'active' : ''"
                                        ><?php echo JText::_('PUBLIC TRANSPORTATION') ?></a></li>
                                <li class="show_stations" data-train-line="sukhumvit" ng-show="searchfilter.search_bts">
                                    <a href="#"><img src="<?php echo JURI::base(); ?>images/bts_icon.png" alt="bts"> BTS
                                        Sukhumvit</a></li>
                                <li class="show_stations" data-train-line="silom" ng-show="searchfilter.search_bts"><a
                                        href="#"><img src="<?php echo JURI::base(); ?>images/bts_icon.png" alt="bts">
                                        BTS Silom</a></li>
                                <li class="show_stations" data-train-line="mrt" ng-show="searchfilter.search_bts"><a
                                        href="#"><img src="<?php echo JURI::base(); ?>images/mrt_icon.png" alt="mrt">
                                        MRT</a></li>
                                <li class="show_stations" data-train-line="mrtp" ng-show="searchfilter.search_bts"><a
                                        href="#"><img src="<?php echo JURI::base(); ?>images/mrt_icon.png" alt="mrt_purple_line">
                                        MRT Purple Line</a></li>
                                <li class="show_stations" data-train-line="ap_link" ng-show="searchfilter.search_bts"><a
                                        href="#"><img src="<?php echo JURI::base(); ?>images/ap_icon.png" alt="airport">
                                        Airport Link</a></li>
                                <li ng-show="!searchfilter.search_bts"><?php echo JText::_('SHOW TRANSPORTATION') ?></li>

                            </ul>

                        </div>
                        <div id="bts_map" class="row row" ng-show="searchfilter.search_bts">
                            <!-- bts map goes here -->
                        </div>
                    </div>
                <?php } ?>


            </div>

            <!-- ------------------------ ROW 3 START  ---------------------------------------  -->
            <div class="search-filters--frontpage__advanced-search render--offcanvas"
                 ng-class="showAdvancedSearch == false ? 'render--offcanvas' : ''">
                <?php //START ADVANCED SEARCH?>

                <?php if (getParam('countryCode') == 'th') { ?>
                    <div class="search-filters--frontpage--row row collapse">
                        <div class="column small-24 medium-8">
                            <?php require_once JPATH_BASE . DS . 'templates/webportal/ng_templates/search/frontpage/postal_code_select_frontpage_province.php' ?>
                        </div>

                        <div class="column small-24 medium-8">
                            <?php require_once JPATH_BASE . DS . 'templates/webportal/ng_templates/search/frontpage/postal_code_select_frontpage_district.php' ?>
                        </div>

                        <div class="column small-24 medium-8">
                            <?php require_once JPATH_BASE . DS . 'templates/webportal/ng_templates/search/frontpage/postal_code_select_frontpage_postalcode.php' ?>
                        </div>
                    </div>
                <?php } ?>

                <?php if (getParam('currency') == 'true') { ?>
                    <div class="search-filters--frontpage--row">
                        <?php require_once JPATH_BASE . DS . 'templates/webportal/ng_templates/search/_elements/currency.php' ?>
                    </div>
                <?php } ?>

                <div class="search-filters--frontpage--row">
                    <div class="row collapse">
                        <div class="column small-24 medium-11">
                            <div class="gap-bottom">
                                <?php require_once JPATH_BASE . DS . 'templates/webportal/ng_templates/search/_elements/price_slider.html' ?>
                            </div>
                        </div>
                        <div class="column small-24 medium-11">
                            <div class="gap-bottom">
                                <?php require_once JPATH_BASE . DS . 'templates/webportal/ng_templates/search/_elements/rooms_slider.html' ?>
                            </div>
                        </div>
                    </div>
                    <div class="row collapse">
                        <div class="column small-24 medium-11">
                            <div class="gap-bottom">
                                <?php require_once JPATH_BASE . DS . 'templates/webportal/ng_templates/search/_elements/bedrooms_slider.html' ?>
                            </div>
                        </div>
                        <div class="column small-24 medium-11">
                            <div class="gap-bottom">
                                <?php require_once JPATH_BASE . DS . 'templates/webportal/ng_templates/search/_elements/bathrooms_slider.html' ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>


        <!-- COLUMN 2 -->
        <div class="column large-6 search-filters--frontpage--row">

            <!-- ------------------------ ROW 1 START  ---------------------------------------  -->

            <div class="row input-textbox--wrapper button--wrapper">

                <a class="input-submit" style="display: none"
                   ng-class="searchfilter.type_id == 2 && !searchfilter.search_bts ? 'active' : ''"
                   ng-click="searchfilter.search_bts = false; searchfilter.type_id = 2"
                   ng-init="searchfilter.type_id = (searchfilter.type_id == 0 ? 2 : searchfilter.type_id)">
                    <?php echo JText::_('SALE') ?>
                </a>

                <a class="input-submit" style="display: none"
                   ng-class="searchfilter.type_id == 3 ? 'active' : ''"
                   ng-click="searchfilter.search_bts = false; searchfilter.type_id = 3">
                    <?php echo JText::_('RENT') ?>
                </a>

                <input type="submit" value="<?php echo JText::_('MAP') ?>" class="input-submit full-width gray" formaction="<?php echo JRoute::_('index.php?Itemid='. $menuMapItem->id); ?>"/>

            </div>


            <!-- ------------------------ ROW 2 START  ---------------------------------------  -->

            <div class="row input-textbox--wrapper button--wrapper">

                <input type="submit" value="<?php echo JText::_("SEARCH SALE") ?>"
                       ng-click="searchfilter.type_id = 2" class="input-submit primary-medium half-width"/>
                <input type="submit" value="<?php echo JText::_("SEARCH RENT") ?>"
                       ng-click="searchfilter.type_id = 3" class="input-submit primary-dark half-width"/>

            </div>

            <!-- ------------------------ ROW 3 START  ---------------------------------------  -->

            <div class="row search-filters--frontpage__advance-filters--row">
                <div class="column small-24 small-only-text-center medium-12 large-12 text-center">
                    <?php
                    $advancedSearch = getParam('showAdvanceSearch');
                    if ($advancedSearch == 'true' || WFactory::getHelper()->isNullOrEmptyString($advancedSearch)) {
                        if (getParam('countryCode') == 'th') { ?>

                            <label class="search-filters__label--home"
                                   
                                   for="advanced_searchckbox"><?php echo JText::_("ADVANCE_SEARCH") ?></label>

                        <?php } ?>

                        <?php if (getParam('countryCode') == 'is' || getParam('countryCode') == 'ph') { ?>

                            <label class="search-filters__label--home"
                                   ng-click="showAdvancedSearch"
                                   for="advanced_searchckbox"><?php echo JText::_("ADVANCE_SEARCH") ?></label>

                        <?php }
                    } ?>

                </div>


                <div class="column cosmall-only-text-center medium-12 large-12 text-center">
                    <a href="#" class="search-filters__label--home"
                       ng-click="resetfilters()"><?php echo JText::_("CLEAR") ?></a>
                </div>
            </div>


        </div>

        <?php //END ADVANCED SEARCH?>

    </div>
