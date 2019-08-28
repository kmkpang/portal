<div class="post front-page--wrapper">
    <!-- cas -->
    <?php foreach (JModuleHelper::getModules('carousel') as $module) {
        echo JModuleHelper::renderModule($module);
    } ?>
    
    <div class="row">
        <div class="search--feature half--width clearfix" ng-cloak >
            <!-- <h1 class="search__caption"><?php echo JText::_('COM_WEBPORTAL_PROPERTY_SEARCH'); ?></h1> -->

            <div class="" ng-controller="SearchCtrl">
                <form id="front-page-search-form-list" class="small-24 large-24"
                      action="<?php echo JRoute::_('index.php?Itemid='. $menuListItem->id); ?>" method="post"
                      ng-submit="savefilter()">

                    <search-filters-frontpage-half></search-filters-frontpage-half>

                </form>
            </div>
        </div>
    </div>

</div>