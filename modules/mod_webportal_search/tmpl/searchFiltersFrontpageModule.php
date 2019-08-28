
<div class="search--feature clearfix show-large-only">

    <div class="" ng-controller="SearchCtrl">
        <form id="front-page-search-form-module"
              action="<?php echo JRoute::_('index.php?Itemid='. $menuListItem->id); ?>" method="post"
              ng-submit="savefilter()">
            <search-filters-frontpage-module></search-filters-frontpage-module>
        </form>
    </div>

</div>