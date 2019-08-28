<?php
// No direct access to this file

//This is Dummy, created in order to generate a bacnet menu item.
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HTML View class for the Webportal Component
 */
class WebportalViewProjectDetail extends JViewLegacy
{
	// Overwriting JView display method
    function display($tpl = null)
    {
        // Assign data to the view
        $this->msg = "running from:" . __FILE__ . ":" . __LINE__;

        $this->projectId = JFactory::getApplication()->input->get('project-id');


        //test


        // Display the view
        parent::display($tpl);
    }
}