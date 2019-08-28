<?php

/** @var WebportalViewAddproperty $this */
if ($this->addPropertyModel !== null && $this->addPropertyModel->property_id)
    $this->_setUpStep(4, "property-id={$this->addPropertyModel->property_id}");
else
    $this->_setUpStep(4);

$editor = &JFactory::getEditor();

?>




<?php $this->_insertHead(); ?>


<div class="row">
    <div class="column small-6 text-center">
        <div class="circle-step active">1</div>
        <p><?php echo JText::_("STEP 1 PERSONAL AND CONTACT INFORMATION") ?></p>
    </div>

    <div class="column small-6 text-center">
        <div class="circle-step active">2</div>
        <p><?php echo JText::_("STEP 2 PROPERTY INFORMATION") ?></p>
    </div>

    <div class="column small-6 text-center">
        <div class="circle-step active">3</div>
        <p><?php echo JText::_("STEP 3 DESCRIPTION AND PHOTOS") ?></p>
    </div>
    <div class="column small-6 text-center">
        <div class="circle-step active">4</div>
        <p><?php echo JText::_("STEP 4 YOUR PROPERTY IS READY") ?></p>
    </div>
</div>

<hr />
<div class="row">
    <div class="small-24 large-24 columns ">
        <h2>Exclusive Text</h2>
    </div>
</div>

<div class="small-24 large-offset-19 large-5 columns" style="padding-right: 0">
    <br/>
    <input type="submit" class="input-submit secondary-medium" ng-click="changeExclusivity(true)" value="<?php echo JText::_("SIGN EXCLUSIVE") ?>">
</div>


<div class="row">
    <div class="small-24 large-24 columns ">
        <h2>Non-Exclusive Text</h2>
    </div>
</div>

<br/>
<div class="row">
    <div class="small-24 large-offset-14 large-5 columns" style="padding-right: 0">
        <a type="submit" class="input-submit secondary-medium" href="<?php echo $this->previous_step_link ?>"
           value="<?php echo JText::_("PREVIOUS STEP") ?>"><?php echo JText::_("PREVIOUS STEP") ?></a>
    </div>
    <div class="small-24 large-5 columns" style="padding-right: 0">
        <input type="submit" class="input-submit primary-medium" ng-click="changeExclusivity(false)"
               value="<?php echo JText::_("OPEN LISTING") ?>">
    </div>
</div>


<?php $this->_insertTail(); ?>








