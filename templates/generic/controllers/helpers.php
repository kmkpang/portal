<?php

/**
 * @deprecated Use WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_TEMPLATE)->getParam('whatever');
 * @param $param
 * @return string
 * @throws Exception
 */
function getParam($param){
	$app        = JFactory::getApplication();
	$template   = $app->getTemplate(true);
	$params     = $template->params;
	$result 	= htmlspecialchars($params->get($param));

	return $result;
}

function getLogo(){

	$app        = JFactory::getApplication();
	$template   = $app->getTemplate(true);
	$params     = $template->params;

	if ($params->get('logoFile')) {
	  $logo = '<img src="'. JUri::root()
					  .$params->get('logoFile')
					  .'" alt="' .$params->get('sitealt')
					  .'" title="' .$params->get('sitetitle')
		  	 	      .'" style="margin-left:' .$params->get('logoMargin') .';"/>';
	} elseif ($params->get('sitetitle')) {
	  $logo = '<span class="site-title" title="'. $sitename .'">'. htmlspecialchars($params->get('sitetitle')) .'</span>';
	} else {
	  $logo = '<span class="site-title" title="'. $sitename .'">'. $sitename .'</span>';
	}

	return $logo;
}

function getColumns($columns) {
	$result = round(24/array_sum($columns));

	return $result;
}

function getStyle($style) {
	if ($style == 't1') {
		$style = JUri::base() . 'assets/css/app_style_a.min.css';
	}

	if ($style == 't2') {
		$style = JUri::base() . 'assets/css/app_style_b.min.css';
	}

	return $style;
}

//Enable default company email address
if (getParam('useOfficeEmailInsteadOfAgents') == 'true') {
	$sendtoAgent = false;
} else {
	$sendtoAgent = true;
}