<?php

// no direct access
defined('_JEXEC') or die;

// Template override
jimport('joomla.filesystem.file');
if(!defined('DS')) define('DS',DIRECTORY_SEPARATOR);
$templateParams = JFactory::getApplication()->getTemplate(true)->params;
$override .=  'mod_login' . DS . 'default_logout.php';

if(
	$templateParams->get('custom_override', '-1') !== '-1' && 
	JFile::exists($override) &&
	__FILE__ !== $override
) :
	include_once($override);
else :
?>
<?php JHtml::_('behavior.keepalive'); ?>
<div class="login gk-login-myaccount">
	<h3><?php echo JText::_('TPL_GENERIC_MYACCOUNT'); ?></h3>
	<form action="<?php echo JRoute::_(htmlspecialchars(JUri::getInstance()->toString(), ENT_COMPAT, 'UTF-8'), true, $params->get('usesecure')); ?>" method="post" id="login-form" class="form-vertical">
          <div class="logout-button">
			  <?php /*
                <?php if ($params->get('greeting')) : ?>
                    <div class="login-greeting">
                              <?php if($params->get('name') == 0) : {
					echo JText::sprintf('MOD_LOGIN_HINAME', htmlspecialchars($user->get('name'), ENT_COMPAT, 'UTF-8'));
				} else : {
					echo JText::sprintf('MOD_LOGIN_HINAME', htmlspecialchars($user->get('username'), ENT_COMPAT, 'UTF-8'));
				} endif; ?>
                    </div>
                    <?php endif; ?>
 				*/ ?>
			  <div class="column small-24 large-6 no-pad-left">
                    <input type="submit" name="Submit" class="input-submit primary-medium" value="<?php echo JText::_('JLOGOUT'); ?>" />
			  </div>
		  </div>
          
          <input type="hidden" name="option" value="com_users" />
          <input type="hidden" name="task" value="user.logout" />
          <input type="hidden" name="return" value="<?php echo $return; ?>" />
          <?php echo JHtml::_('form.token'); ?>
	</form>
	
	<?php 
	 	$document = JFactory::getDocument();
	 	$renderer = $document->loadRenderer('modules');
	 	
	 	if($document->countModules('usermenu')) {
	 		echo '<div id="UserMenu">';
	 		echo $renderer->render('usermenu', array('style' => 'gk_style'), null); 
	 		echo '</div>';
	 	}
	?> 
</div>
<?php endif; ?>