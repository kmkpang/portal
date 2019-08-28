<?php

function getLogo(){

	$app        = JFactory::getApplication();
	$template   = $app->getTemplate(true);
	$params     = $template->params;

	if ($params->get('logoFile')) {
	  $logo = '<img src="'. JUri::root() . $params->get('logoFile') .'" alt="'. $sitename .'" />';
	} elseif ($params->get('sitetitle')) {
	  $logo = '<span class="site-title" title="'. $sitename .'">'. htmlspecialchars($params->get('sitetitle')) .'</span>';
	} else {
	  $logo = '<span class="site-title" title="'. $sitename .'">'. $sitename .'</span>';
	}

	return $logo;
}
