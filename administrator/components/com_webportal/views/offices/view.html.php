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
 * Webportals View
 *
 * @since  0.0.1
 */
class WebportalViewOffices extends JViewLegacy
{
    /**
     * Display the Hello World view
     *
     * @param   string $tpl The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */
    function display($tpl = null)
    {

        // Get application
        $app = JFactory::getApplication();
        $context = "webportal.list.admin.webportal";

        // What Access Permissions does this user have? What can (s)he do?
        $this->canDo = WebportalHelper::getActions();

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode('<br />', $errors));

            return false;
        }

        // Set the submenu
        WebportalHelper::addSubmenu('offices');

        $this->sidebar = JHtmlSidebar::render();

        // Set the toolbar and number of found items
        $this->addToolBar();

        // Display the template
        parent::display($tpl);

        // Set the document
        $this->setDocument();

    }

    /**
     * Add the page title and toolbar.
     *
     * @return  void
     *
     * @since   1.6
     */
    protected function addToolBar()
    {
        $title = JText::_('COM_WEBPORTAL_OFFICES_MANAGER');

        if ($this->pagination->total) {
            $title .= "<span style='font-size: 0.5em; vertical-align: middle;'>(" . $this->pagination->total . ")</span>";
        }

        JToolBarHelper::title($title, 'webportal');

//        if ($this->canDo->get('core.create')) {
//            JToolBarHelper::addNew('offices.add', 'JTOOLBAR_NEW');
//        }
//        if ($this->canDo->get('core.delete')) {
//            JToolBarHelper::deleteList('', 'offices.delete', 'JTOOLBAR_DELETE');
//        }


        if ($this->canDo->get('core.admin')) {
            JToolBarHelper::divider();
            JToolBarHelper::preferences('com_webportal');
        }
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument()
    {
        $document = JFactory::getDocument();
        $document->setTitle(JText::_('COM_WEBPORTAL_ADMINISTRATION'));
    }
}