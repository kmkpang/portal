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
class WebportalControllerOffice extends JControllerForm
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
        $public_key = $input->getString('public_key',null);
        $unique_id = $input->getString('unique_id',null);
        $company_id = $input->getInt('company_id', 0);
        $address_id = $input->getInt('address_id', 0);
        $country_id = $input->getInt('country_id', 0);
        $office_name = $input->getString('office_name',null);
        $email = $input->getString('email',null);
        $fax = $input->getString('fax',null);
        $phone = $input->getString('phone',null);
        $url_to_private_page = $input->getString('url_to_private_page',null);
        $image_file_path = $input->getString('image_file_path',null);
        $image_file_name = $input->getString('image_file_name',null);
        $date_entered = $input->getString('date_entered',null);
        $date_modified = $input->getString('date_modifie',null);
        $manager_id = $input->getInt('$manager_id',null);
        $certified_agent_id = $input->getInt('certified_agent_id',null);
        $logo = $input->getString('logo',null);
        $show_on_web = $input->getString('show_on_web',null);
        $address = $input->getString('address',null);
        $marketingInfo = $input->getString('marketingInfo',null);

        $officeId = $input->get('office_id',0);

        //first get the current office details

        $officeService = WFactory::getServices()->getSqlService()->getSqlServiceClass(__PROPPERTY_PORTAL_OFFICE);


        //step 2 : get current office
        /**
         * @var $office OfficeModel
         */
        $office = $officeService->getOffice($officeId,true);

        //step 3 : change office information

        //$office->id = $id;
        $office->public_key = $public_key;
        $office->unique_id = $unique_id;
        $office->company_id = $company_id;
        $office->address_id = $address_id;
        $office->country_id = $country_id;
        $office->office_name = $office_name;
        $office->email = $email;
        $office->fax = $fax;
        $office->phone = $phone;
        $office->url_to_private_page = $url_to_private_page;
        $office->image_file_path = $image_file_path;
        $office->image_file_name = $image_file_name;
        $office->date_entered = $date_entered;
        $office->date_modified = $date_modified;
        $office->manager_id = $manager_id;
        $office->certified_agent_id = $certified_agent_id;
        $office->logo = $logo;
        $office->show_on_web = $show_on_web;
        $office->address = $address;
        $office->marketingInfo = $marketingInfo;

        //step 4 : update database
       $result =  $officeService->updateOffice($office);

        if($result){
            //success
            $app->redirect(JUri::root()."administrator/index.php?option=com_webportal&view=offices",JText::_('COM_WEBPORTAL_SUCCESS'),'message');
        }else{
            //fail!
            $app->redirect(JUri::root()."administrator/index.php?option=com_webportal&view=office&layout=edit&office_id=$officeId",JText::_('COM_WEBPORTAL_ERROR'),'message');
        }

    }

    public function apply()
    {
        //$this->edit();
        $app = JFactory::getApplication();
        $app->redirect(JUri::root()."administrator/index.php?option=com_webportal&view=office&layout=edit&office_id=$officeId",JText::_('COM_WEBPORTAL_SUCCESS'),'message');
    }

    public function cancel() {
        JFactory::getApplication()->redirect(JUri::root()."administrator/index.php?option=com_webportal&view=offices");
    }

}
