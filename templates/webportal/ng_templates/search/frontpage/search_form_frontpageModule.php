<form id="areaguide-page-search-form">
    <div class="large-24 small-24 column">
        <!------------------- FRONT PAGE SEARCH MODULE IN ----->
        <!--- templates/webportal/ng_templates/search/frontpage/search_form_frontpageModule.php ->
        <!------------------- ROW 1 ------------------>

        <div class="search-filters--frontpage--row row collapse">

            <!------------------- Column 1 ------------------>
            <div class="large-5 column">
                <i class="icon-chevron-right"></i>
                <a class="frontpage-search-select"
                   ng-class="searchfilter.type_id == 2 && !searchfilter.search_bts ? 'active' : ''"
                   ng-click="searchfilter.search_bts = false; searchfilter.type_id = 2"
                   ng-init="searchfilter.type_id = (searchfilter.type_id == 0 ? 2 : searchfilter.type_id)"><?php echo JText::_('SALE') ?></a>

            </div>
            <!------------------- Column 2 ------------------>
            <div class="large-1 column"></br></div>
            <div class="large-10 column">

                <div class="input-textbox--wrapper">
                    <input type="text" ng-model="searchfilter.text"
                           placeholder="<?php echo JText::_('SEARCH HOMES') ?>"/>
                    <input type="submit" class="input-textbox--wrapper--search__button"/>
                </div>

            </div>
            <!------------------- Column 3 ------------------>
            <div class="large-8 column">

                <div class="margin-left-15">

                    <?php require_once JPATH_BASE . DS . 'templates/webportal/ng_templates/search/_elements/price_slider.html' ?>
                </div>


            </div>


        </div>

        <!------------------- ROW 2 ------------------>

        <div class="search-filters--frontpage--row row collapse">

            <!------------------- Column 1 ------------------>
            <div class="large-5 column">

                <a class="frontpage-search-select"
                   ng-class="searchfilter.type_id == 3 ? 'active' : ''"
                   ng-click="searchfilter.search_bts = false; searchfilter.type_id = 3"><?php echo JText::_('RENT') ?></a>

            </div>
            <!------------------- Column 2 ------------------>
            <div class="large-1 column"></br></div>
            <div class="large-10 column">

                <?php require_once JPATH_BASE . DS . 'templates/webportal/ng_templates/search/frontpage/postal_code_select_frontpage_province.php' ?>

            </div>
            <!------------------- Column 3 ------------------>
            <div class="large-8 column">

                <div class="margin-left-15">
                    <?php require_once JPATH_BASE . DS . 'templates/webportal/ng_templates/search/_elements/rooms_slider.html' ?>
                </div>

            </div>

        </div>

        <!------------------- ROW 3 ------------------>

        <div class="search-filters--frontpage--row row collapse">

            <!------------------- Column 1 ------------------>
            <div class="large-5 column">

                <a class="frontpage-search-select"
                   href="<?php echo WFactory::getHelper()->buildUrl('/properties-search/map') ?>">
                    <?php echo JText::_('Map') ?></a>

            </div>
            <!------------------- Column 2 ------------------>
            <div class="large-1 column"></br></div>
            <div class="large-10 column">

                <?php require_once JPATH_BASE . DS . 'templates/webportal/ng_templates/search/frontpage/postal_code_select_frontpage_district.php' ?>

            </div>
            <!------------------- Column 3 ------------------>
            <div class="large-8 column">

            </div>

        </div>

        <!------------------- ROW 4 ------------------>

        <div class="search-filters--frontpage--row row collapse">

            <!------------------- Column 1 ------------------>
            <div class="large-5 column">

                <br/>

            </div>
            <!------------------- Column 2 ------------------>
            <div class="large-1 column"></br></div>
            <div class="large-10 column">

                <?php require_once JPATH_BASE . DS . 'templates/webportal/ng_templates/search/frontpage/property_category_select_frontpage.html' ?>

            </div>


            <!------------------- Column 3 ------------------>
            <div class="large-8 column">

                <div class="margin-left-15 ">
                    <div class="row collapse">
                        <div class="column large-15">
                            <input type="submit" value="<?php echo JText::_("SEARCH") ?>" class="input-submit red"/>
                        </div>
                        <div class="column large-8">
                            <input type="button" value="<?php echo JText::_("CLEAR") ?>" ng-click="resetfilters()"
                                   class="input-submit"/>
                        </div>
                    </div>
                </div>

            </div>

        </div>


        <!------------------- ROW 5 ------------------>

        <div class="search-filters--frontpage--row row collapse">

            <!------------------- Column 1 ------------------>
            <div class="large-5 column">

                <br/>

            </div>
            <!------------------- Column 2 ------------------>
            <div class="large-1 column"></br></div>
            <div class="large-10 column">
                <filter-currency></filter-currency>
            </div>


            <!------------------- Column 3 ------------------>
            <div class="large-8 column">
                <br/>
            </div>

        </div>


    </div>
</form>
