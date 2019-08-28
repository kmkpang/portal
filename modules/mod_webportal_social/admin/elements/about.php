<?php

defined('JPATH_BASE') or die;
jimport('joomla.version');
jimport('joomla.form.formfield');

class JFormFieldAbout extends JFormField {
	protected $type = 'About';

	protected function getInput() {
		$version = new JVersion;
		$ver = $version->getShortVersion();
		
		return '<div id="gk_about_us" data-jversion="'.$ver.'">' . JText::_('MOD_SOCIAL_ABOUT_US_CONTENT') . '</div></div>';
	}
}

// EOF