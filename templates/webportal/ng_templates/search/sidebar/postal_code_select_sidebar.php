<?php

require_once(JPATH_BASE . DS . 'templates' . DS . 'generic' . DS . 'controllers' . DS . 'helpers.php');
?>
<div class="search-filters--sidebar--row row">
    <div class="column large-8 small-24">
        <div class="input-textbox--wrapper">
            <select ng-model="searchfilter.region_id" ng-options="region.id as region.name for region in postal_code_tree.regions">
                <option value="" selected default><?php echo JText::_('PROVINCE') ?></option>
            </select>
        </div>
    </div>
    <div class="column large-8 small-24">
      <div class="input-textbox--wrapper">
        <select ng-model="searchfilter.city_town_id" ng-options="town.id as town.name for town in postal_code_tree.towns | filter:filterTown">
            <option value="" selected default><?php echo JText::_('DISTRICT') ?></option>
        </select>
      </div>
    </div>
    <?php if (getParam('countryCode') == 'is') { ?>
    <div class="column large-8 small-24 no-pad-left no-pad-right">
        <div class="input-textbox--wrapper column">
            <select ng-model="searchfilter.zip_code_id" ng-options="postals.id as postals.name for postals in postal_code_tree.postals | filter:filterPostal">
                <option value="" selected default><?php echo JText::_('POSTCODE') ?></option>
            </select>
        </div>
    </div>
    <?php } ?>

    </div>

