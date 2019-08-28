<?php

//This is Dummy, created in order to generate a bacnet menu item.
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$doc = JFactory::getDocument();
$app = JFactory::getApplication();

require_once(JPATH_BASE . DS . 'templates' . DS . 'generic' . DS . 'controllers' . DS . 'helpers.php');
require_once(JPATH_BASE . DS . 'components' . DS . 'com_webportal' . DS . 'helper.php');

WFactory::getHelper()->setCurrentPage('list');
?>

<div ng-controller="SearchCtrl">
    <div class="search-filters small-24 clearfix">
        <div class="search-filters-listmap row">
            <search-filters-topbar></search-filters-topbar>
        </div>
    </div>

    <div class="row--property-list__controlpanel">
        <div class="row">
            <div class="small-24 columns">
                <sort-controls></sort-controls>
            </div>
        </div>
    </div>

    <!-- <div class="row collapse" ng-show="!listloading && items.length > 0"> -->
    <div class="row collapse">
        <div class="column small-24 row--pager">
            <?php require JPATH_BASE . "/templates/webportal/ng_templates/properties/controls.php"; ?>
        </div>
    </div>

    <div class="row row--property-list" id="search">

        <div class="small-24">
            <div class="property-list--wrapper">
                <properties-list></properties-list>
            </div>
        </div>

    </div>

    <!-- <div class="row row--pager-container row--pager-container--bottom" ng-show="!listloading && items.length > 0"> -->
    <div class="row row--pager-container row--pager-container--bottom">
        <div class="column small-24 row--pager">
            <?php require JPATH_BASE . "/templates/webportal/ng_templates/properties/controls.php";?>
        </div>
    </div>

</div>
