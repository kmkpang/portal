<?php /** @var WebportalViewAddproperty $this */ ?>
<?php //echo $provinceModel?>
<div class="input-textbox--wrapper">
    <select name="province" ng-model="<?php echo $provinceModel?>" id="province"
            ng-change="updateOfficeList();updateGoogleMapCenter('regions');"
            ng-options="region.id as region.name for region in postal_code_tree.regions" >
        <option value="" selected default><?php echo JText::_('PROVINCE') ?></option>
    </select>
</div>