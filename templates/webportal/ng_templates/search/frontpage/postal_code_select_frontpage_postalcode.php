<div class="input-textbox--wrapper">
    <select ng-model="searchfilter.zip_code_id"
            ng-change="updateOfficeList();updateGoogleMapCenter('postal_codes');"
            ng-options="postals.id as postals.name for postals in postal_code_tree.postals | filter:filterPostal">
        <option value="" selected default><?php echo JText::_('POSTCODE') ?></option>
    </select>
</div>

