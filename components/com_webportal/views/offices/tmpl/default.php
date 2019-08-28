<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_BASE . DS . 'templates' . DS . 'generic' . DS . 'controllers' . DS . 'helpers.php');

$office_id = JFactory::getApplication()->input->getCmd('office_id');
if($office_id == null)
{
	require('offices.php');
}
else
{
 	require('office.php');
}
?>