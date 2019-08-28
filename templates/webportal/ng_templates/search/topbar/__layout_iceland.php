
<div class="search-filters--listmap--row row">

    <div class="column small-24 medium-9 large-9">
        <div class="input-textbox--wrapper">
            <span class="search-filters__slider-label"><?php echo JText::_('SEARCH') ?> <span ng-show="searchfilter.office_id"><?php echo JText::_('from') . ' ' . JText::_('Office') ?> {{ searchfilter.office_name }}</span> <span ng-show="searchfilter.sale_id"><?php echo JText::_('from') . ' ' . JText::_('Agent') ?> {{ searchfilter.sale_name }}</span></span>
            <input type="text" ng-model="searchfilter.text"
                   placeholder="<?php echo JText::_('SEARCH HOMES') ?>"/>
        </div>
    </div>

    <div class="column small-24 medium-8 large-8">
        <span class="search-filters__slider-label"><?php echo JText::_('PROPERTY LOCATION'); ?></span>
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
            default-label="<?php echo JText::_('SELECT LOCATION'); ?>"
            item-label="html"
            tick-property="checked"
            <?php // group-property="xx"  // the we DO NOT use grouping, because : 1. group header can not be selected , 2: 2.0.2 version seems buggy with nested groups!?>
            selection-mode="multi"
            helper-elements="reset filter"
            max-labels="2"
            max-height="400px"
            >
        </multi-select>
    </div>

    <div class="column small-24 medium-7 large-7">
        <span class="search-filters__slider-label"><?php echo JText::_('PROPERTY TYPE'); ?></span>
        <?php require_once JPATH_BASE . DS . 'templates/webportal/ng_templates/search/frontpage/property_category_select_frontpage.html' ?>
    </div>

</div>

<input type="checkbox" class="advanced_searchckbox" name="advanced_searchckbox" id="advanced_searchckbox"
       ng-model="showAdvancedSearch"/>

<div class="search-filters--listmap__advanced-search render--offcanvas"
     ng-class="showAdvancedSearch == false ? 'render--offcanvas' : ''">

    <div class="search-filters--frontpage--row row clearfix">

        <div class="column small-24 medium-12">
            <div class="">
                <?php require_once JPATH_BASE . DS . 'templates/webportal/ng_templates/search/_elements/price_slider.html' ?>
            </div>
        </div>

        <div class="column small-24 medium-12">
            <?php require_once JPATH_BASE . DS . 'templates/webportal/ng_templates/search/_elements/rooms_slider.html' ?>
        </div>


    </div>

</div>
<!-- End advanced search -->

<div class="row">
    <div class="search-filters--listmap--row column small-24 medium-12 large-12">
        <div class="search-home-filters__button--wrapper">

            <?php if (getParam('loan80') == 1) { ?>
                <span class="search-home-filters__button small-24 medium-<?php if (getColumns($columns) == 5) {
                    echo getColumns($columns) - 1;
                } else {
                    echo getColumns($columns);
                } ?> column"
                      ng-class="searchfilter.loan80 ? 'active' : ''"
                      ng-click="searchfilter.loan80 = !searchfilter.loan80"><?php echo JText::_('LOAN80') ?>
                                </span>
            <?php } ?>

            <?php if (getParam('garage') == 1) { ?>
                <span class="search-home-filters__button small-24 medium-<?php echo getColumns($columns); ?> column"
                      ng-class="searchfilter.garage ? 'active' : ''"
                      ng-click="searchfilter.garage = !searchfilter.garage"><?php echo JText::_('GARAGE') ?>
                                </span>
            <?php } ?>

            <?php if (getParam('elevator') == 1) { ?>
                <span class="search-home-filters__button small-24 medium-<?php echo getColumns($columns); ?> column"
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

    <div class="column small-24 medium-5 search-filters--listmap--row">
        <div class="column small-12 no-pad-left no-pad-right">
            <a class="input-submit sale-submit"
               ng-class="searchfilter.type_id == 2 && !searchfilter.search_bts ? 'active' : ''"
               ng-click="searchfilter.search_bts = false; searchfilter.type_id = 2"
               ng-init="searchfilter.type_id = (searchfilter.type_id == 0 ? 2 : searchfilter.type_id)">
                <?php echo JText::_('SALE') ?>
            </a>
        </div>
        <div class="column small-12 no-pad-left no-pad-right">
            <a class="input-submit rent-submit"
               ng-class="searchfilter.type_id == 3 ? 'active' : ''"
               ng-click="searchfilter.search_bts = false; searchfilter.type_id = 3">
                <?php echo JText::_('RENT') ?>
            </a>
        </div>
    </div>

    <div class="search-filters--listmap--row column small-24 medium-7">
        <div class="search-filters--listmap--row column small-24 medium-13 no-pad-left no-pad-right">
            <input type="submit" value="<?php echo JText::_("SEARCH") ?>" class="input-submit primary-medium"/>
        </div>

        <div class="search-filters--listmap--row column small-24 medium-10 no-pad-left no-pad-right">
            <input type="submit" value="<?php echo JText::_('MAP') ?>" class="input-submit full-width gray" formaction="<?php echo JRoute::_('index.php?Itemid='. $menuMapItem->id); ?>"/>
        </div>
    </div>
</div>

<div class="row search-filters--listmap--row clearfix">

    <?php
    if (getParam('showAdvanceSearch') == 'true') { ?>
        <div class="column small-15">
            <label class="search-filters__label border-submit"
                   for="advanced_searchckbox"><?php echo JText::_("ADVANCE_SEARCH") ?></label>
        </div>

        <div class="column small-8 text-right">
            <a href="#" class="search-filters__label border-submit" ng-click="resetfilters()"><?php echo JText::_("CLEAR") ?></a>
        </div>

    <?php } else if (getParam('showAdvanceSearch') == 'false') { ?>

        <div class="text-center">
            <a href="#" class="search-filters__label border-submit" ng-click="resetfilters()"><?php echo JText::_("CLEAR") ?></a>
        </div>

    <?php } ?>
</div>
