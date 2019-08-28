<?php /** @var WebportalViewAddproperty $this */ ?>
<div class="input-textbox--wrapper">
    <select ng-model="currentProperty.city_town_id" name="towns"
            ng-change="updateOfficeList();updateGoogleMapCenter('towns');"
            ng-options="town.id as town.name for town in postal_code_tree.towns | filter:filterTown" required>
        <option value="" selected default><?php echo JText::_('DISTRICT') ?></option>
    </select>
</div>