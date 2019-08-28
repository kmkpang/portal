<?php

// no direct access
defined('_JEXEC') or die;

// Template override
jimport('joomla.filesystem.file');
if(!defined('DS')) define('DS',DIRECTORY_SEPARATOR);
$templateParams = JFactory::getApplication()->getTemplate(true)->params;
$override .=  'mod_login' . DS . 'default.php';

if(
	$templateParams->get('custom_override', '-1') !== '-1' && 
	JFile::exists($override) &&
	__FILE__ !== $override
) :
	include_once($override);
else :
?>
<?php JHtml::_('behavior.keepalive'); ?>
<div class="login">
	<h3><?php echo JText::_('TPL_GENERIC_LOGIN_POPUP'); ?></h3>

	<form action="<?php echo JRoute::_(htmlspecialchars(JUri::getInstance()->toString(), ENT_COMPAT, 'UTF-8'), true, $params->get('usesecure')); ?>" method="post" id="login-form" class="form-inline">
          <fieldset class="userdata">
              <div class="input-textbox--wrapper">
                    <p class="login-fields">
                              <label><?php echo JText::_('MOD_LOGIN_VALUE_USERNAME') ?></label>
                              <input id="modlgn-username" type="text" name="username" class="inputbox"  size="24" />
                    </p>
              </div>
              <div class="input-textbox--wrapper">
                    <p class="login-fields">
                    		  <label><?php echo JText::_('JGLOBAL_PASSWORD') ?></label>
                              <input id="modlgn-passwd" type="password" name="password" class="inputbox" size="24" />
                    </p>
              </div>
              <div class="form-field--row">
                    <?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
                    <div id="form-login-remember">
                              <input id="modlgn-remember" type="checkbox" name="remember" class="inputbox" value="yes"/>
                              <label for="modlgn-remember"><?php echo JText::_('MOD_LOGIN_REMEMBER_ME') ?></label>
                    </div>
                    <?php endif; ?>
              </div>

              <div class="row">
                  <div class="column small-24 large-6 no-pad-left no-pad-right">
                        <button class="input-submit primary-medium"><?php echo JText::_('JLOGIN') ?></button>
                  </div>

                  <div class="column small-24 large-18">
                        <ul>
                                  <li> <a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>" class="inverse"> <?php echo JText::_('MOD_LOGIN_FORGOT_YOUR_PASSWORD'); ?></a> </li>
                                  <li> <a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>" class="inverse"> <?php echo JText::_('MOD_LOGIN_FORGOT_YOUR_USERNAME'); ?></a> </li>
                                  <?php
                                    $usersConfig = JComponentHelper::getParams('com_users');
                                    if ($usersConfig->get('allowUserRegistration')) :
                                  ?>
                                  <li class="last"> <a class="input-submit medium-grey" href="<?php echo JRoute::_('index.php?option=com_users&view=registration'); ?>"> <?php echo JText::_('TPL_GENERIC_REGISTER_POPUP'); ?></a> </li>
                                  <?php endif; ?>
                        </ul>
                  </div>
              </div>
                    <input type="hidden" name="option" value="com_users" />
                    <input type="hidden" name="task" value="user.login" />
                    <input type="hidden" name="return" value="<?php echo $return; ?>" />
                    <?php echo JHtml::_('form.token'); ?>
                    </fieldset>
          <div class="posttext"> <?php echo $params->get('posttext'); ?> </div>
	</form>
</div>
<?php endif; ?>