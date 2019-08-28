<div class="input-textbox--wrapper gap-right">
    <select ng-model="searchfilter.city_town_id"
            ng-change="updateOfficeList();updateGoogleMapCenter('towns');"
            ng-options="town.id as town.name for town in postal_code_tree.towns | filter:filterTown">
        <option value="" selected default><?php echo JText::_('DISTRICT') ?></option>
    </select>
</div>