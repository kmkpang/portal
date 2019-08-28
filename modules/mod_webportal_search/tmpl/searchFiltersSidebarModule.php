 
<div class="search--feature-sidebar clearfix show-large-only">

    <div class="" ng-controller="SearchCtrl">
        <form id="sidebar-search-form-module"
              action="<?php echo JRoute::_('index.php?Itemid='. $menuListItem->id); ?>" method="post"
              ng-submit="savefilter()">
            <search-filters-sidebar-module></search-filters-sidebar-module>
        </form>
    </div>

</div>