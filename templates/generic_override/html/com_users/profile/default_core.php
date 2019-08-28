<?php

defined('_JEXEC') or die;

?>
<div class="agent-details--wrapper large-24 small-24">
	<div class="clearfix">
		<div class="column small-24 medium-6 large-6">
			<div class="agent-detail__agent-img">
				
			</div>

			<div class="agent-detail__agent-contact--bottom">
				<?php //if(!empty(trim($agent->phone)) || !isset($agent->phone)) {?>
					<div class="agent-details__label--row">
						<i class="fa fa-phone"></i> <strong><?php echo JText::_("PHONE") ?>
							:</strong> <?php //echo $agent->phone; ?>
					</div>
				<?php //} ?>
				<?php //if(!empty(trim($agent->mobile)) || !isset($agent->mobile)) {?>
					<div class="agent-details__label--row">
						<i class="fa fa-mobile"></i> <strong><?php echo JText::_("MOBILE") ?>
							:</strong> <?php //echo $agent->mobile; ?>
					</div>
				<?php //} ?>
				<?php //if(!empty(trim($agent->email)) || !isset($agent->email)) {?>
					<div class="agent-details__label--row">
						<i class="fa fa-envelope"></i> <strong><?php echo JText::_("EMAIL") ?>:</strong>
						<a href="mailto:<?php echo $this->data->email; ?>"> <?php echo $this->data->email; ?>
						</a>
					</div>
				<?php //} ?>
			</div>

		</div>

		<div class="column small-24 medium-18 large-18">
			<h1 class="agent-detail__agent-name"><?php echo $this->data->name; ?></h1>

			<h2 class="agent-details__office-name"><?php echo JText::_('COM_USERS_PROFILE_USERNAME_LABEL') . ': ' . htmlspecialchars($this->data->username); ?></h2>

			<h5 class="agent-detail__agent-title"><?php //echo $agent->title ?></h5>
			<hr/>
			<p>
				<span><?php echo JText::_('COM_USERS_PROFILE_REGISTERED_DATE_LABEL'); ?>: <?php echo JHtml::_('date', $this->data->registerDate); ?></span>
			<br />
				<span><?php echo JText::_('COM_USERS_PROFILE_LAST_VISITED_DATE_LABEL'); ?>: <?php echo JHtml::_('date', $this->data->lastvisitDate); ?></span>
			</p>
			<div class="agent-detail__agent-contact--right">
				<?php //if(!empty(trim($agent->phone)) || !isset($agent->phone)) {?>
					<div class="agent-details__label--row">
						<i class="fa fa-phone"></i> <strong><?php echo JText::_("PHONE") ?>
							:</strong> <?php //echo $agent->phone; ?>
					</div>
				<?php //} ?>
				<?php //if(!empty(trim($agent->mobile)) || !isset($agent->mobile)) {?>
					<div class="agent-details__label--row">
						<i class="fa fa-mobile"></i> <strong><?php echo JText::_("MOBILE") ?>
							:</strong> <?php //echo $agent->mobile; ?>
					</div>
				<?php //} ?>
				<?php //if(!empty(trim($agent->email)) || !isset($agent->email)) {?>
					<div class="agent-details__label--row">
						<i class="fa fa-envelope"></i> <strong><?php echo JText::_("EMAIL") ?>:</strong>
						<a href="mailto:<?php //echo ($sendtoAgent ? $agent->email : $office->email); ?>"> <?php echo ($sendtoAgent ? $agent->email : $office->email); ?>
						</a>
					</div>
				<?php //} ?>
				<hr/>
			</div>
		</div>

	</div>
</div>