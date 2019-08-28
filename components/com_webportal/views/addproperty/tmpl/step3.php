<?php

/** @var WebportalViewAddproperty $this */
if ($this->addPropertyModel !== null && intval($this->addPropertyModel->property_id) > 0)
    $this->_setUpStep(3, "property-id={$this->addPropertyModel->property_id}");
else
    $this->_setUpStep(3);

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
        <div class="circle-step">4</div>
        <p><?php echo JText::_("STEP 4 YOUR PROPERTY IS READY") ?></p>
    </div>
</div>
<hr />
<div class="row">
    <div class="small-24 large-24 columns ">
        <h2><?php echo JText::_("ADD DESCRIPTION IN ENGLISH") ?></h2>
        <?php
        echo $editor->display(
            'desc_english', //name
            $this->addPropertyModel->desc_english,
            '100%', //width
            '150px',//height <---fucking useless! change it from plugin-->tinyMCE-->editor panel
            '0', //col
            '0',//row
            false, //buttons
            'desc_english',//id
            null,//assets
            null,//author
            null); ?>
    </div>
</div>

<div class="row">
    <div class="small-24 large-24 columns ">
        <h2><?php echo JText::_("ADD DESCRIPTION IN THAI") ?></h2>
        <?php
        echo $editor->display(
            'desc_thai', //name
            $this->addPropertyModel->desc_thai,
            '100%', //width
            '150px',//height <---fucking useless! change it from plugin-->tinyMCE-->editor panel
            '0', //col
            '0',//row
            false, //buttons
            'desc_thai',//id
            null,//assets
            null,//author
            null); ?>
    </div>
</div>

<div class="row">
    <div class="small-24 large-24 columns ">
        <h2><?php echo JText::_("UPLOAD IMAGES") ?></h2>
        <?php require_once JPATH_BASE . "/templates/webportal/ng_templates/properties/imageupload.php"; ?>
    </div>
</div>

<br/>
<div class="row">
    <div class="small-24 large-offset-14 large-5 columns" style="padding-right: 0">
        <a type="submit" class="input-submit secondary-medium"  href="<?php echo $this->previous_step_link?>" value="<?php echo JText::_("PREVIOUS STEP")?>"><?php echo JText::_("PREVIOUS STEP")?></a>
    </div>
    <div class="small-24 large-5 columns" style="padding-right: 0">
        <input type="submit" class="input-submit secondary-medium" value="<?php echo JText::_("SUBMIT")?>">
    </div>
</div>

<?php $this->_insertTail(); ?>








