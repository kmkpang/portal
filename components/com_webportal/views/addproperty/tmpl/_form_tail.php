<?php

// IDE Hint
/** @var WebportalViewAddproperty $this */

// THIS FILE CLOSES THE FORM TAG
?>
<?php echo JHtml::_('form.token') ?>


<!--<div class="row" style="border: 1px solid #808080;padding: 10px;margin: 10px;background: #808080;opacity:.5">-->
<!--    <pre>-->
<!--        {{currentProperty | json}}-->
<!--    </pre>-->
<!--</div>-->

<input type="hidden" name="form_next_step" value="<?php echo $this->form_next_step ?>"/>
<input type="hidden" name="current_step" value="<?php echo $this->current_step ?>"/>
<input type="hidden" name="form_data_value" value="<?php echo $this->form_data_value ?>"/>
<input type="hidden" id="submit_value" name="submit_value" value=""/>
</form>

