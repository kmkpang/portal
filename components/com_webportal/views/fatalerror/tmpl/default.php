<?php

$error = base64_decode(JFactory::getApplication()->input->get('message'));
//This is Dummy, created in order to generate a bacnet menu item.
// No direct access to this file
//defined('_JEXEC') or die('Restricted access');
?>
<div class="fatal-error-wrapper row">
    <h1 class="fatal-error-header"><?php echo JText::_('COM_WEBPORTAL_NOT_FOUND_HEADER') ?></h1>

    <div class="fatal-error-content">
        <p><?php echo $error ?></p>

        <div class="small-24 large-8 fatal-error-button-wrapper">
            <a class="input-submit primary-medium"
               href="<?php echo JURI::root(); ?>"><?php echo JText::_('COM_WEBPORTAL_NOT_FOUND_BACK_TO_HOMEPAGE') ?></a>
        </div>
    </div>
</div>