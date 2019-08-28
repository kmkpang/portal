<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_webportal
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Webportals Controller
 *
 * @since  0.0.1
 */
class WebportalControllerAgents extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  object  The model.
	 *
	 * @since   1.6
	 */
	public function getModel($name = 'Webportal', $prefix = 'WebportalModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}

	public function add()
	{
		$app = JFactory::getApplication();
		$input = $app->input;
		$id = $input->getInt('id', 0);
		$agentService = WFactory::getServices()->getSqlService()->getSqlServiceClass(__PROPPERTY_PORTAL_AGENT);

		//Dummy new ID
		$agentId = 1;

		$app->redirect(JUri::root()."administrator/index.php?option=com_webportal&view=agent&layout=edit&task=add&agent_id=$agentId");

	}
	public function edit()
	{
		$app = JFactory::getApplication();
		$input = $app->input;
		$id = $input->getInt('id', 0);

		$app->redirect(JUri::root()."administrator/index.php?option=com_webportal&view=agent&layout=edit&task=edit&agent_id=$agentId");
	}
	public function delete()
	{

		$app = JFactory::getApplication();
		$input = $app->input;

		//$id not complete
		$id = 'one or multiple id';

		$agentId = $id;
		$agentService = WFactory::getServices()->getSqlService()->getSqlServiceClass(__PROPPERTY_PORTAL_AGENT);
		$result = $agentService->deleteAgent($agentId);

		if($result){
			//success
			$app->redirect(JUri::root()."administrator/index.php?option=com_webportal&view=agents",JText::_('COM_WEBPORTAL_SUCCESS'),'message');
		}else{
			//fail!
			$app->redirect(JUri::root()."administrator/index.php?option=com_webportal&view=agents",JText::_('COM_WEBPORTAL_ERROR'),'message');
		}

	}
}
