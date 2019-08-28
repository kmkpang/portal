<div class="input-textbox--wrapper">
    <select id="offices" ng-model="currentProperty.office_id"
            ng-init="updateOfficeList()";
            ng-options="office.id as office.office_name for office in office_list">
        <option value="" selected default><?php echo JText::_('SELECT OFFICE') ?></option>
    </select>
</div>