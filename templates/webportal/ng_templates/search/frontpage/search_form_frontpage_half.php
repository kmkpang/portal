<?php
require_once(JPATH_BASE . DS . 'templates' . DS . 'generic' . DS . 'controllers' . DS . 'helpers.php');

$app = JFactory::getApplication();
$menu = $app->getMenu();
$menuMapItem = $menu->getItems( 'link', 'index.php?option=com_webportal&view=map', true );
$menuListItem = $menu->getItems( 'link', 'index.php?option=com_webportal&view=list', true );

$columns = array(getParam('loan80'), getParam('garage'), getParam('elevator'), getParam('newToday'), getParam('newWeek'));
?>

<div class="small-24 search-filters--frontpage--column">


    <div class="search-filters--frontpage--row clearfix">
        <div class="input-textbox--wrapper">
            <input type="text" ng-model="searchfilter.text"
                   placeholder="<?php echo JText::_('SEARCH HOMES') ?>"/>
        </div>
    </div>

    <div class="search-filters--frontpage--row clearfix show-medium-up">
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
                default-label="<?php echo JText::_("SELECT LOCATION")?>"
                item-label="html"
                tick-property="checked"
                <?php // group-property="xx"  // the we DO NOT use grouping, because : 1. group header can not be selected , 2: 2.0.2 version seems buggy with nested groups!?>
                selection-mode="multi"
                helper-elements="all none reset filter"
                max-labels="3"
                max-height="400px"
                >
            </multi-select>
        </div>
    </div>

    <div class="search-filters--frontpage--row clearfix">
        <div class="search-home-filters__button--wrapper">
            <?php if (getParam('loan80') == 1) { ?>
            <span class="search-home-filters__button small-24 medium-<?php if (getColumns($columns) == 5) {echo getColumns($columns)-1;} else {echo getColumns($columns);} ?> column"
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
            <label for="new_today" class="search-home-filters__button small-24 medium-<?php echo getColumns($columns); ?> column"
                   ng-class="searchfilter.new_today ? 'active' : ''"
                   ng-click="searchfilter.new_today = !searchfilter.new_today ">
                <?php echo JText::_('NEW_TODAY') ?>
            </label>
            <?php } ?>

            <?php if (getParam('newWeek') == 1) { ?>
            <input type="checkbox" id="new_this_week" name="new" class="hidden"/>
            <label for="new_this_week" class="search-home-filters__button small-24 medium-<?php echo getColumns($columns); ?> column"
                   ng-class="searchfilter.new_this_week ? 'active' : ''"
                   ng-click="searchfilter.new_this_week = !searchfilter.new_this_week">
                <?php echo JText::_('NEW_THIS_WEEK') ?>
            </label>
            <?php } ?>
        </div>
    </div>

    <input type="checkbox" class="advanced_searchckbox" name="advanced_searchckbox" id="advanced_searchckbox"
           ng-model="showAdvancedSearch"/>

    <div class="search-filters--frontpage__advanced-search render--offcanvas"
         ng-class="showAdvancedSearch == false ? 'render--offcanvas' : ''">

        <?php //START ADVANCED SEARCH?>

        <div class="search-filters--frontpage--row">
            <?php require_once JPATH_BASE . DS . 'templates/webportal/ng_templates/search/frontpage/property_category_select_frontpage.html' ?>
        </div>

        <div class="search-filters--frontpage--row row collapse show-small-only">
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

        <?php if (getParam('currency') == 'true') { ?>
        <div class="search-filters--frontpage--row">
            <?php require_once JPATH_BASE . DS . 'templates/webportal/ng_templates/search/_elements/currency.php' ?>
        </div>
        <?php } ?>

        <div class="search-filters--frontpage--row">
            <div class="row collapse">

                <div class="column large-15 medium-15">

                    <div class="gap-bottom">
                        <?php require_once JPATH_BASE . DS . 'templates/webportal/ng_templates/search/_elements/price_slider.html' ?>
                    </div>

                    <div class="gap-bttom-small-only">
                        <?php require_once JPATH_BASE . DS . 'templates/webportal/ng_templates/search/_elements/rooms_slider.html' ?>
                    </div>

                </div>

                <div class="column large-8 medium-8">

                    <div class="gap-bottom gap-bttom-small-only">

                        <a class="input-submit"
                           ng-class="searchfilter.type_id == 2 && !searchfilter.search_bts ? 'active' : ''"
                           ng-click="searchfilter.search_bts = false; searchfilter.type_id = 2"
                           ng-init="searchfilter.type_id = (searchfilter.type_id == 0 ? 2 : searchfilter.type_id)">
                            <?php echo JText::_('SALE') ?>
                        </a>

                    </div>

                    <div class="">
                        <a class="input-submit"
                           ng-class="searchfilter.type_id == 3 ? 'active' : ''"
                           ng-click="searchfilter.search_bts = false; searchfilter.type_id = 3">
                            <?php echo JText::_('RENT') ?>
                        </a>
                    </div>


                </div>
            </div>
        </div>

        <!-------------------------------------------------------------------------------------------------->

        <!--<div class="search-filters--frontpage--row">
            <?php /*require_once JPATH_BASE . DS . 'templates/webportal/ng_templates/search/_elements/price_slider.html' */ ?>
        </div>

        <div class="search-filters--frontpage--row">
            <?php /*require_once JPATH_BASE . DS . 'templates/webportal/ng_templates/search/_elements/rooms_slider.html' */ ?>
        </div>

        <div class="search-filters--frontpage--row">
            <div class="search-filters--frontpage--row">
                <a class="frontpage-search-select"
                   ng-class="searchfilter.type_id == 2 && !searchfilter.search_bts ? 'active' : ''"
                   ng-click="searchfilter.search_bts = false; searchfilter.type_id = 2"
                   ng-init="searchfilter.type_id = (searchfilter.type_id == 0 ? 2 : searchfilter.type_id)"><?php /*echo JText::_('SALE') */ ?></a>
            </div>

            <div class="search-filters--frontpage--row">
                <a class="frontpage-search-select"
                   ng-class="searchfilter.type_id == 3 ? 'active' : ''"
                   ng-click="searchfilter.search_bts = false; searchfilter.type_id = 3"><?php /*echo JText::_('RENT') */ ?></a>
            </div>
        </div>
-->
        <!-------------------------------------------------------------------------------------------------->

    </div>
    <?php //END ADVANCED SEARCH?>
</div>

<div class="search-filters--frontpage--row">
    <div class="row collapse">
        <div class="column large-15 medium-15 gap-bttom-small-only">
            <input type="submit" value="<?php echo JText::_("SEARCH") ?>" class="input-submit primary-medium"/>
        </div>

        <div class="column large-8 medium-8">
            <input type="submit" value="<?php echo JText::_('MAP') ?>" class="input-submit gray-medium" formaction="<?php echo JRoute::_('index.php?Itemid='. $menuMapItem->id); ?>"/>
        </div>
    </div>
</div>

<div class="search-filters--frontpage--row">
    <div class="row collapse">
        <?php if (getParam('showAdvanceSearch') == 'true') { ?>
            <div class="column small-24 small-only-text-center medium-15 large-15">
                <label class="search-filters__label"
                       for="advanced_searchckbox"><?php echo JText::_("ADVANCE_SEARCH") ?></label>
            </div>

            <div class="column small-24 small-only-text-center medium-9 large-9 text-right">
                <a href="#" class="search-filters__label" ng-click="resetfilters()"><?php echo JText::_("CLEAR") ?></a>
            </div>

        <?php } if (getParam('showAdvanceSearch') == 'false') { ?>
            <div class="column small-24 small-only-text-center text-right">
                <a href="#" class="search-filters__label" ng-click="resetfilters()"><?php echo JText::_("CLEAR") ?></a>
            </div>
        <?php } ?>
    </div>

</div>