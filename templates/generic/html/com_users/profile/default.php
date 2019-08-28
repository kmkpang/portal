<?php

defined('_JEXEC') or die;
?>
<div class="forms--row row no-breadcrumbs row--agents-details <?php echo $this->pageclass_sfx?>">

	<div class="row pad">
		<div class="profile <?php echo $this->pageclass_sfx?>">
		<?php if ($this->params->get('show_page_heading')) : ?>
		<div class="page-header">
			<h1>
				<?php echo $this->escape($this->params->get('page_heading')); ?>
			</h1>
		</div>
		<?php endif; ?>
		<?php if (JFactory::getUser()->id == $this->data->id) : ?>
		<div class="btn-toolbar pull-right">
			<a class="input-submit primary-medium" href="<?php echo JRoute::_('index.php?option=com_users&task=profile.edit&user_id='.(int) $this->data->id);?>">
				<span class="icon-user"></span> <?php echo JText::_('COM_USERS_EDIT_PROFILE'); ?></a>
		</div>
		<?php endif; ?>
		<?php echo $this->loadTemplate('core'); ?>

		<?php //echo $this->loadTemplate('params'); ?>

		<?php //echo $this->loadTemplate('custom'); ?>

		</div>
	</div>
</div>