<?php
?>

<div class="row large-24 office-mail-frontpage--wrapper">
    <div class="column medium-14 large-14 office-mail-frontpage">
        <h5><?php echo JText::_('MOD_WEBPORTAL_OFFICES_CONTACT'); ?></h5>
        <?php if(!empty(trim($office->phone)) || !isset($office->phone)) {?>
            <?php echo JText::_('MOD_WEBPORTAL_OFFICES_PHONE'); ?> : <?php echo $office->phone ?>
        <?php } ?>

        <?php if(!empty(trim($office->fax)) || !isset($office->fax)) {?>
            <br/>
            <?php echo JText::_('MOD_WEBPORTAL_OFFICES_FAX'); ?> : <?php echo $office->fax ?>
        <?php } ?>

        <?php if(!empty(trim($office->email)) || !isset($office->email)) {?>
            <br/>
            <br/>
            <?php echo JText::_('MOD_WEBPORTAL_OFFICES_EMAIL'); ?> : <?php echo $office->email ?>
        <?php } ?>

    </div>

    <div class="column medium-10 large-10 office-mail-frontpage">
        <i class="fa fa-phone"></i>
    </div>

</div>
