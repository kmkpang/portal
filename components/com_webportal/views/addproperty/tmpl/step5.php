<?php

/** @var WebportalViewAddproperty $this */
if ($this->addPropertyModel !== null && $this->addPropertyModel->property_id)
    $this->_setUpStep(5, "property-id={$this->addPropertyModel->property_id}");
else
    $this->_setUpStep(5);

$editor = &JFactory::getEditor();

?>




<?php $this->_insertHead(); ?>


<h3 class="text-center"><?php echo JText::_("THANKYOU") ?></h3>


<div class="row">
    <div class="small-24 large-24 text-center columns">
        <?php echo JText::_("YOUR PROPERTY HAS BEEN REGISTERED WITH US") ?>
        <br/>
        <?php echo JText::_("WE SHALL GET BACK TO YOU SHORTLY") ?>
    </div>
</div>

<br/>
<br/>
<br/>


<br/>


<div class="row">
    <div class="small-24 large-offset-4 large-8 columns">
        <input type="submit" class="input-submit secondary-medium" ng-click="addAnother(true)"
               value="<?php echo JText::_("ADD ANOTHER PROPERTY") ?>">
    </div>
    <div class="small-24 large-8 columns">
        <input type="submit" class="input-submit secondary-medium" ng-click="addAnother(false)"
               value="<?php echo JText::_("GO TO HOME PAGE") ?>">
    </div>
    <div class="small-24 large-4 columns">
        &nbsp;
    </div>
</div>



<?php $this->_insertTail(); ?>








