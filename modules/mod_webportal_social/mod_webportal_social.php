<?php

// no direct access
defined('_JEXEC') or die;
if(!defined('DS')){
   define('DS',DIRECTORY_SEPARATOR);
}


// helper loading
$config = $params->toArray();
// load helper file depends of source type
if($config['module_data_source'] == 'twitter') {
	require_once (dirname(__FILE__).DS.'data_source'.DS.'twitter.php');
	$helper = new SocialTwitterHelper($module, $params);
	// try to parse the data
	try{
		$helper->getData();
		//$helper->parseData();    
	} catch (Exception $e) {
		// use backup
		$helper->useBackup();
	}
} else if ($config['module_data_source'] == 'fb') {
	require_once (dirname(__FILE__).DS.'data_source'.DS.'facebook.php');
	$helper = new SocialFacebookHelper($module, $params);
} else {
	require_once (dirname(__FILE__).DS.'data_source'.DS.'gplus.php');
	$helper = new SocialGPLusHelper($module, $params);
}

// creating HTML code	
$helper->render();

// EOF