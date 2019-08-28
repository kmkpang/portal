<!-- THIS IS USED IN LIST AND MAP PAGE -->
<?php
$app = JFactory::getApplication();
$menu = $app->getMenu();
$menuMapItem = $menu->getItems( 'link', 'index.php?option=com_webportal&view=map', true );
$menuListItem = $menu->getItems( 'link', 'index.php?option=com_webportal&view=list', true );
?>

<form ng-submit="filterSidebarModule()">

    <div ng-show="searchfilter.office_id">
        <span class="search-filters__slider-label"><?php echo JText::_('Office') ?>
            {{ searchfilter.office_name }}</span>
    </div>
    <div ng-show="searchfilter.sale_id">
        <span class="search-filters__slider-label"><?php echo JText::_('Agent') ?> {{ searchfilter.sale_name }}</span>
    </div>

    <!-- Top Search Block -->

    <div class="search-filters--listmap--row clearfix">


        <div class="column small-24 medium-24 large-12">
            <div class="input-textbox--wrapper gap-right">
                <input type="text" ng-model="searchfilter.text"
                       placeholder="<?php echo JText::_('SEARCH HOMES') ?>"/>
            </div>
        </div>

        <div class="search-home-filters__button--wrapper column small-24 medium-24 large-12">

            <span class="search-home-filters__button small-8 medium-4 column"
                  ng-class="searchfilter.loan80 ? 'active' : ''"
                  ng-click="searchfilter.loan80 = !searchfilter.loan80"><?php echo JText::_('LOAN80') ?>
            </span>

            <span class="search-home-filters__button small-8 medium-3 column"
                  ng-class="searchfilter.garage ? 'active' : ''"
                  ng-click="searchfilter.garage = !searchfilter.garage"><?php echo JText::_('GARAGE') ?>
            </span>

            <span class="search-home-filters__button small-8 medium-3 column"
                  ng-class="searchfilter.elevator ? 'active' : ''"
                  ng-click="searchfilter.elevator = !searchfilter.elevator"><?php echo JText::_('ELEVATOR') ?>
            </span>

            <input type="checkbox" id="new_today" name="new" class="hidden"/>
            <label for="new_today" class="search-home-filters__button small-12 medium-7 column"
                   ng-class="searchfilter.new_today ? 'active' : ''"
                   ng-click="searchfilter.new_today = !searchfilter.new_today ">
                <?php echo JText::_('NEW_TODAY') ?>
            </label>

            <input type="checkbox" id="new_this_week" name="new" class="hidden"/>
            <label for="new_this_week" class="search-home-filters__button small-12 medium-7 column"
                   ng-class="searchfilter.new_this_week ? 'active' : ''"
                   ng-click="searchfilter.new_this_week = !searchfilter.new_this_week">
                <?php echo JText::_('NEW_THIS_WEEK') ?>
            </label>

        </div>


    </div>


    <input type="checkbox" class="advanced_searchckbox" name="advanced_searchckbox" id="advanced_searchckbox"
           ng-model="showAdvancedSearch"/>

    <div class="search-filters--listmap__advanced-search render--offcanvas"
         ng-class="showAdvancedSearch == false ? 'render--offcanvas' : ''">

        <div class="search-filters--listmap--row">

            <div class="column small-24 large-12">
                <div class="gap-right">
                    <?php require_once JPATH_BASE . DS . 'templates/webportal/html/ng_templates/search/frontpage/property_category_select_frontpage.html' ?>
                </div>

            </div>
            <div class="column small-24 large-12">
                <?php require_once JPATH_BASE . DS . 'templates/webportal/html/ng_templates/search/_elements/currency.php' ?>
            </div>

        </div>

        <div class="search-filters--listmap--row">

            <div class="column small-24 large-8">
                <?php require_once JPATH_BASE . DS . 'templates/webportal/html/ng_templates/search/frontpage/postal_code_select_frontpage_province.php' ?>
            </div>
            <div class="column small-24 large-8">
                <?php require_once JPATH_BASE . DS . 'templates/webportal/html/ng_templates/search/frontpage/postal_code_select_frontpage_district.php' ?>
            </div>
            <div class="column small-24 large-8">
                <?php require_once JPATH_BASE . DS . 'templates/webportal/html/ng_templates/search/frontpage/postal_code_select_frontpage_postalcode.php' ?>
            </div>

        </div>

        <div class="search-filters--frontpage--row">

            <div class="column small-24 large-12">
                <div class="gap-right">
                    <?php require_once JPATH_BASE . DS . 'templates/webportal/html/ng_templates/search/_elements/price_slider.html' ?>
                </div>
            </div>

            <div class="column small-24 large-12">
                <?php require_once JPATH_BASE . DS . 'templates/webportal/html/ng_templates/search/_elements/rooms_slider.html' ?>
            </div>


        </div>

    </div>
    <!-- End advanced search -->


    <div class="search-filters--listmap--row">

        <div class="column small-24 medium-12 large-6">
            <div class="gap-right gap-bottom-medium">
                <a class="input-submit"
                   ng-class="searchfilter.type_id == 2 && !searchfilter.search_bts ? 'active' : ''"
                   ng-click="searchfilter.search_bts = false; searchfilter.type_id = 2"
                   ng-init="searchfilter.type_id = (searchfilter.type_id == 0 ? 2 : searchfilter.type_id)">
                    <?php echo JText::_('SALE') ?>
                </a>
            </div>
        </div>

        <div class="column small-24 medium-offset-1 medium-11 large-offset-0 large-6">
            <div class="gap-right gap-bottom-medium">
                <a class="input-submit"
                   ng-class="searchfilter.type_id == 3 ? 'active' : ''"
                   ng-click="searchfilter.search_bts = false; searchfilter.type_id = 3">
                    <?php echo JText::_('RENT') ?>
                </a>
            </div>
        </div>

        <div class="column small-24 medium-12 large-6">
            <div class="gap-right">
                <input type="submit" value="<?php echo JText::_("SEARCH") ?>" class="input-submit lightgreen"/>
            </div>
        </div>

        <div class="column small-24 medium-offset-1 medium-11 large-offset-0 large-6 gap-bottom gap-bottom-medium">
            <a class="input-submit mediumgray"
               href="<?php echo JRoute::_('index.php?Itemid='. $menuMapItem->id); ?>">
                <?php echo JText::_('MAP') ?>
            </a>
        </div>


    </div>

    <div class="search-filters--listmap--row">

        <div class="row collapse">
            <div class="column small-24 small-only-text-center medium-15 large-15">
                <label class="search-filters__label"
                       for="advanced_searchckbox"><?php echo JText::_("ADVANCE_SEARCH") ?></label>

            </div>

            <div class="column small-24 small-only-text-center medium-9 large-9 text-right">
                <a href="#" class="search-filters__label" ng-click="resetfilters()"><?php echo JText::_("CLEAR") ?></a>
            </div>
        </div>

    </div>


</form>