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
 * Webportal Controller
 *
 * @package     Joomla.Administrator
 * @subpackage  com_webportal
 * @since       0.0.9
 */
class WebportalControllerAgent extends JControllerForm
{
    /**
     * Implement to allowAdd or not
     *
     * Not used at this time (but you can look at how other components use it....)
     * Overwrites: JControllerForm::allowAdd
     *
     * @param array $data
     * @return bool
     */
    protected function allowAdd($data = array())
    {
        return parent::allowAdd($data);
    }

    /**
     * Implement to allow edit or not
     * Overwrites: JControllerForm::allowEdit
     *
     * @param array $data
     * @param string $key
     * @return bool
     */
    protected function allowEdit($data = array(), $key = 'id')
    {
        $id = isset($data[$key]) ? $data[$key] : 0;
        if (!empty($id)) {
            return JFactory::getUser()->authorise("core.edit", "com_webportal.message." . $id);
        }
    }

    /**
     * Autometically called by joomla framework
     */
    public function save()
    {
        $app = JFactory::getApplication();
        $input = $app->input;


        //save stuff

        //step 1 get data frm post
        $id = $input->getInt('id', 0);
        $unique_id = $input->getString('unique_id', null);
        $office_id = $input->getInt('office_id', 0);
        $address_id = $input->getInt('address_id', 0);
        $first_name = $input->getString('first_name', null);
        $middle_name = $input->getString('middle_name', null);
        $last_name = $input->getString('last_name', null);

        $email = $input->getString('email', null);
        $fax = $input->getString('fax', null);
        $phone = $input->getString('phone', null);
        $mobile = $input->getString('mobile', null);
        $url_to_private_page = $input->getString('url_to_private_page', null);
        $gender = $input->getString('gender', null);
        $SIN = $input->getString('SIN', null);
        $DOB = $input->getString('DOB', null);
        $image_file_path = $input->getString('image_file_path', null);
        $image_file_name = $input->getString('image_file_name', null);
        $date_entered = $input->getString('date_entered', null);
        $date_modified = $input->getString('date_modifie', null);
        $title = $input->getString('title', null);
        $language_spoken1 = $input->getString('language_spoken1', null);
        $language_spoken2 = $input->getString('language_spoken2', null);
        $language_spoken3 = $input->getString('language_spoken3', null);
        $language_spoken4 = $input->getString('language_spoken4', null);
        $language_spoken5 = $input->getString('language_spoken5', null);
        //$show_on_web = $input->getString('show_on_web',null);
        //$address = $input->getString('address',null);
        //$marketingInfo = $input->getString('marketingInfo',null);
        $office = $input->getInt('office', 0);
        $office_name = $input->getString('office_name', null);

        $agentId = $input->get('agent_id', 0);

        //first get the current office details

        $agentService = WFactory::getServices()->getSqlService()->getSqlServiceClass(__PROPPERTY_PORTAL_AGENT);


        //step 2 : get current office
        /**
         * @var $office OfficeModel
         */
        $agent = $agentService->getAgent($agentId, true);

        //step 3 : change office information

        $agent->id = $id;
        $agent->unique_id = $unique_id;
        $agent->office_id = $office_id;
        $agent->address_id = $address_id;
        $agent->first_name = $first_name;
        $agent->middle_name = $middle_name;
        $agent->last_name = $last_name;
        $agent->email = $email;
        $agent->fax = $fax;
        $agent->phone = $phone;
        $agent->mobile = $mobile;
        $agent->url_to_private_page = $url_to_private_page;
        $agent->gender = $gender;
        $agent->SIN = $SIN;
        $agent->DOB = $DOB;
        $agent->image_file_path = $image_file_path;
        $agent->image_file_name = $image_file_name;
        $agent->date_entered = $date_entered;
        $agent->date_modified = $date_modified;
        $agent->title = $title;
        $agent->language_spoken1 = $language_spoken1;
        $agent->language_spoken2 = $language_spoken2;
        $agent->language_spoken3 = $language_spoken3;
        $agent->language_spoken4 = $language_spoken4;
        $agent->language_spoken5 = $language_spoken5;
        //$agent->show_on_web = $show_on_web;
        //$agent->address = $address;
        //$agent->marketingInfo = $marketingInfo;
        $agent->office = $office;
        $agent->office_name = $office_name;

        //step 4 : update database
        $result = $agentService->updateAgent($agent);

        if ($result) {
            //success
            $app->redirect(JUri::root() . "administrator/index.php?option=com_webportal&view=agents", JText::_('COM_WEBPORTAL_SUCCESS'), 'message');
        } else {
            //fail!
            echo 'error';
            $app->redirect(JUri::root() . "administrator/index.php?option=com_webportal&view=agent&layout=edit&agent_id=$agentId", JText::_('COM_WEBPORTAL_ERROR'), 'message');
        }

    }

    public function apply()
    {
        //$this->edit();
        $app = JFactory::getApplication();
        $input = $app->input;
        $agentId = $input->get('agent_id', 0);

        $app->redirect(JUri::root() . "administrator/index.php?option=com_webportal&view=agent&layout=edit&agent_id=$agentId", JText::_('COM_WEBPORTAL_SUCCESS'), 'message');
    }

    public function cancel()
    {
        JFactory::getApplication()->redirect(JUri::root() . "administrator/index.php?option=com_webportal&view=agents");
    }

    public function edit()
    {
        $this->save();
    }


}
