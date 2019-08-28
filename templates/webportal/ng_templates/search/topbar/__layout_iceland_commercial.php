
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

<div class="row">
    <div class="column small-24 medium-12 large-12">
        <div class="search-filters--frontpage--row row clearfix">
            <div class="column small-24 medium-12">
                    <?php require_once JPATH_BASE . DS . 'templates/webportal/ng_templates/search/_elements/price_slider.html' ?>
            </div>
            <div class="column small-24 medium-12">
                <?php require_once JPATH_BASE . DS . 'templates/webportal/ng_templates/search/_elements/size_slider.html' ?>
            </div>


        </div>
    </div>

    <div class="column small-24 medium-5">
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

    <div class="column small-24 medium-7">
        <div class="search-filters--listmap--row column small-24 medium-13 no-pad-left no-pad-right">
            <input type="submit" value="<?php echo JText::_("SEARCH") ?>" class="input-submit primary-medium"/>
        </div>

        <div class="search-filters--listmap--row column small-24 medium-10 no-pad-left no-pad-right">
            <input type="submit" value="<?php echo JText::_('MAP') ?>" class="input-submit full-width gray" formaction="<?php echo JRoute::_('index.php?Itemid='. $menuMapItem->id); ?>"/>
        </div>
    </div>
</div>

<div class="row search-filters--listmap--row clearfix">
    <div class="column  small-8 large-offset-16 text-right">
        <a href="#" class="search-filters__label border-submit" ng-click="resetfilters()"><?php echo JText::_("CLEAR") ?></a>
    </div>
</div>
