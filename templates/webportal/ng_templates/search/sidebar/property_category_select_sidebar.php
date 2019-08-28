

<!--<span class="search-filters__textbox-label">--><?php //echo JText::_('PROPERTY TYPE') ?><!--</span>-->
<multi-select
    id="property-categories"
    input-model="multiselectcats"

    button-label="description"
    item-label="description"
    default-label="<?php echo JText::_('PROPERTY TYPE') ?>"
    tick-property="checked"
    group-property="multiSelectGroup"
    selection-mode="single"
    on-item-click="category_filter_changed()"
    helper-elements=""
    max-labels="1"
    max-height="180px"
    >
</multi-select>