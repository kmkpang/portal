<?php /** @var WebportalViewAddproperty $this */ ?>

<div class="input-textbox--wrapper gap-right">
    <select name="province" ng-model="searchfilter.region_id" id="province"
            ng-change="updateOfficeList();updateGoogleMapCenter('regions');"
            ng-options="region.id as region.name for region in postal_code_tree.regions">
        <option value="" selected default><?php echo JText::_('PROVINCE') ?></option>
    </select>
</div>