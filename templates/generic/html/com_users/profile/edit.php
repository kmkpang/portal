<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidator');
JHtml::_('formbehavior.chosen', 'select');

//load user_profile plugin language
$lang = JFactory::getLanguage();
$lang->load('plg_user_profile', JPATH_ADMINISTRATOR);
?>
<div class="forms--row row no-breadcrumbs profile-edit<?php echo $this->pageclass_sfx?>">
	<div class="large-12 login-center">
	<div class="row">
<?php if ($this->params->get('show_page_heading')) : ?>
	<div class="page-header">
		<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	</div>
<?php endif; ?>

<script type="text/javascript">
	Joomla.twoFactorMethodChange = function(e)
	{
		var selectedPane = 'com_users_twofactor_' + jQuery('#jform_twofactor_method').val();

		jQuery.each(jQuery('#com_users_twofactor_forms_container>div'), function(i, el) {
			if (el.id != selectedPane)
			{
				jQuery('#' + el.id).hide(0);
			}
			else
			{
				jQuery('#' + el.id).show(0);
			}
		});
	}
</script>

<form id="member-profile row--agents-details" action="<?php echo JRoute::_('index.php?option=com_users&task=profile.save'); ?>" method="post" class="form-validate form-horizontal" enctype="multipart/form-data">
<?php foreach ($this->form->getFieldsets() as $group => $fieldset):// Iterate through the form fieldsets and display each one.?>
	<?php $fields = $this->form->getFieldset($group);?>
	<?php if (count($fields)):?>
	<fieldset>
		<?php if (isset($fieldset->label)):// If the fieldset has a label set, display it as the legend.?>
		<legend><?php echo JText::_($fieldset->label); ?></legend>
		<?php endif;?>
		<?php foreach ($fields as $field):// Iterate through the fields in the set and display them.?>
			<?php if ($field->hidden):// If the field is hidden, just display the input.?>
				<div class="form-field--row">
					<div class="input-textbox--wrapper">
						<?php echo $field->input;?>
					</div>
				</div>
			<?php else:?>
				<div class="form-field--row">
					<div class="contact__textbox-label">
						<?php echo $field->label; ?>
						<?php if (!$field->required && $field->type != 'Spacer') : ?>
						<span class="optional"><?php echo JText::_('COM_USERS_OPTIONAL'); ?></span>
						<?php endif; ?>
					</div>
					<div class="input-textbox--wrapper">
						<?php echo $field->input; ?>
					</div>
				</div>
			<?php endif;?>
		<?php endforeach;?>
	</fieldset>
	<?php endif;?>
<?php endforeach;?>

<?php if (count($this->twofactormethods) > 1): ?>
	<fieldset>
		<legend><?php echo JText::_('COM_USERS_PROFILE_TWO_FACTOR_AUTH') ?></legend>

		<div class="form-field--row">
			<div class="contact__textbox-label">
				<label id="jform_twofactor_method-lbl" for="jform_twofactor_method" class="hasTooltip"
					   title="<strong><?php echo JText::_('COM_USERS_PROFILE_TWOFACTOR_LABEL') ?></strong><br/><?php echo JText::_('COM_USERS_PROFILE_TWOFACTOR_DESC') ?>">
					<?php echo JText::_('COM_USERS_PROFILE_TWOFACTOR_LABEL'); ?>
				</label>
			</div>
			<div class="contact__textbox-label">
				<?php echo JHtml::_('select.genericlist', $this->twofactormethods, 'jform[twofactor][method]', array('onchange' => 'Joomla.twoFactorMethodChange()'), 'value', 'text', $this->otpConfig->method, 'jform_twofactor_method', false) ?>
			</div>
		</div>
		<div id="com_users_twofactor_forms_container">
			<?php foreach($this->twofactorform as $form): ?>
			<?php $style = $form['method'] == $this->otpConfig->method ? 'display: block' : 'display: none'; ?>
			<div id="com_users_twofactor_<?php echo $form['method'] ?>" style="<?php echo $style; ?>">
				<?php echo $form['form'] ?>
			</div>
			<?php endforeach; ?>
		</div>
	</fieldset>

	<fieldset>
		<legend>
			<?php echo JText::_('COM_USERS_PROFILE_OTEPS') ?>
		</legend>
		<div class="alert alert-info">
			<?php echo JText::_('COM_USERS_PROFILE_OTEPS_DESC') ?>
		</div>
		<?php if (empty($this->otpConfig->otep)): ?>
		<div class="alert alert-warning">
			<?php echo JText::_('COM_USERS_PROFILE_OTEPS_WAIT_DESC') ?>
		</div>
		<?php else: ?>
		<?php foreach ($this->otpConfig->otep as $otep): ?>
		<span class="span3">
			<?php echo substr($otep, 0, 4) ?>-<?php echo substr($otep, 4, 4) ?>-<?php echo substr($otep, 8, 4) ?>-<?php echo substr($otep, 12, 4) ?>
		</span>
		<?php endforeach; ?>
		<div class="clearfix"></div>
		<?php endif; ?>
	</fieldset>
<?php endif; ?>

		<div class="form-actions">
			<div class="column small-24 medium-16">
				<button type="submit" class="input-submit primary-medium validate"><span><?php echo JText::_('JSUBMIT'); ?></span></button>
			</div>
			<div class="column small-24 medium-8">
				<a class="input-submit grey-medium" href="<?php echo JRoute::_(''); ?>" title="<?php echo JText::_('JCANCEL'); ?>"><?php echo JText::_('JCANCEL'); ?></a>
			</div>

			<input type="hidden" name="option" value="com_users" />
			<input type="hidden" name="task" value="profile.save" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</div>
</div>
</div>
