<div class="small-24 search-filters--frontpage--column row">
    <div class="column large-24" ng-show="searchfilter.office_id || searchfilter.sale_id">
        <span class="search-filters__slider-label"><?php echo JText::_('SEARCH') ?> <span ng-show="searchfilter.office_id"><?php echo JText::_('from') . ' ' . JText::_('Office') ?> {{ searchfilter.office_name }}</span> <span ng-show="searchfilter.sale_id"><?php echo JText::_('from') . ' ' . JText::_('Agent') ?> {{ searchfilter.sale_name }}</span></span>
    </div>
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
            <div class="column large-16 small-24 search-filters--frontpage--row">

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

            <input type="checkbox" class="advanced_searchckbox" name="advanced_searchckbox"
                   id="advanced_searchckbox"
                   ng-model="showAdvancedSearch"/>


        </div>

        <!-- ------------------------ ROW 3 START  ---------------------------------------  -->
        <div class="search-filters--frontpage__advanced-search render--offcanvas"
             ng-class="showAdvancedSearch == false ? 'render--offcanvas' : ''">
            <?php //START ADVANCED SEARCH?>

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

            <?php if (getParam('showTransport') == 'true') { ?>
                <transport-select-top></transport-select-top>
            <?php } ?>

            <?php if (getParam('currency') == 'true') { ?>
                <div class="search-filters--frontpage--row row">
                    <div class="column small-24">
                        <?php require_once JPATH_BASE . DS . 'templates/webportal/ng_templates/search/_elements/currency.php' ?>
                    </div>
                </div>
            <?php } ?>

            <div class="search-filters--frontpage--row row">
                <div class="column small-24 medium-12">
                    <?php require_once JPATH_BASE . DS . 'templates/webportal/ng_templates/search/_elements/price_slider.html' ?>
                </div>
                <div class="column small-24 medium-12">
                    <?php require_once JPATH_BASE . DS . 'templates/webportal/ng_templates/search/_elements/rooms_slider.html' ?>
                </div>
                <div class="column small-24 medium-12">
                    <?php require_once JPATH_BASE . DS . 'templates/webportal/ng_templates/search/_elements/bedrooms_slider.html' ?>
                </div>
                <div class="column small-24 medium-12">
                    <?php require_once JPATH_BASE . DS . 'templates/webportal/ng_templates/search/_elements/bathrooms_slider.html' ?>
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
                <label class="search-filters__label--home"
                       ng-click="showAdvancedSearch"
                       for="advanced_searchckbox"><?php echo JText::_("ADVANCE_SEARCH") ?></label>
            </div>


            <div class="column cosmall-only-text-center medium-12 large-12 text-center no-pad-left">
                <a href="#" class="search-filters__label--home"
                   ng-click="resetfilters()"><?php echo JText::_("CLEAR") ?></a>
            </div>
        </div>


    </div>

    <?php //END ADVANCED SEARCH?>

</div>
