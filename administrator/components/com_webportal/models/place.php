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
 * Webportal Model
 *
 * @since  0.0.1
 */
class WebportalModelPlace extends JModelAdmin
{
    /**
     * Method to get a table object, load it if necessary.
     *
     * @param   string $type The table name. Optional.
     * @param   string $prefix The class prefix. Optional.
     * @param   array $config Configuration array for model. Optional.
     *
     * @return  JTable  A JTable object
     *
     * @since   1.6
     */
    public function getTable($type = 'Webportal', $prefix = 'WebportalTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to get the record form.
     * I DONT USE IT
     *
     * @param   array $data Data for the form.
     * @param   boolean $loadData True if the form is to load its own data (default case), false if not.
     *
     * @return  mixed    A JForm object on success, false on failure
     *
     * @since   1.6
     */
    public function getForm($data = array(), $loadData = true)
    {
        return false;
    }

    /**
     * Method to get the script that have to be included on the form
     *
     * @return string    Script files
     */
    public function getScript()
    {
        return '';
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return  mixed  The data for the form.
     *
     * @since   1.6
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState(
            'com_webportal.edit.webportal.data',
            array()
        );

        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }

    /**
     * Method to check if it's OK to delete a message. Overwrites JModelAdmin::canDelete
     */
    protected function canDelete($record)
    {
        if (!empty($record->id)) {
            return JFactory::getUser()->authorise("core.delete", "com_webportal.message." . $record->id);
        }
    }

    public function save(){
        $x=1;
    }

    public function apply(){
        $x=1;
    }
}
