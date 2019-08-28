<?php

//This is Dummy, created in order to generate a bacnet menu item.
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$doc = JFactory::getDocument();
$doc->addScriptDeclaration('

    window.is_showing_mappage=true;

');

WFactory::getHelper()->setCurrentPage('map');
?>

<div ng-controller="SearchCtrl" ng-init="pager.setSize(-1);">
    <div class="search-filters small-24 clearfix">
        <div class="search-filters-listmap row">
            <search-filters-topbar></search-filters-topbar>
        </div>
    </div>
    <div>
        <sort-controls-map></sort-controls-map>
    </div>
    <div ng-controller="MapCtrl" class="ng-cloak">

        <span ng-show="listloading"><?php echo JText::_("COM_WEBPORTAL_FETCHING") ?></span>

        <div class="sort-control-map top row show-small-only">
            <sort-control-map></sort-control-map>
        </div>

        <div class="property-map--wrapper large-24">
            <div class="embed-container">
                <properties-map></properties-map>
            </div>
        </div>
        <?php //<label class="pull-left" ng-show="!listloading">{{mapstatus}}</label> ?>
    </div>

</div>

