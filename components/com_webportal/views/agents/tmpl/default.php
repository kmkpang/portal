<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_BASE . DS . 'templates' . DS . 'generic' . DS . 'controllers' . DS . 'helpers.php');

$agent_id = JFactory::getApplication()->input->getCmd('agent_id');
if($agent_id == null)
{
	require('agents.php');
}
else
{
 	require('agent.php');
}
?>