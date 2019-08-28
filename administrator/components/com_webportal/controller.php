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
 * General Controller of Webportal component
 *
 * @package     Joomla.Administrator
 * @subpackage  com_webportal
 * @since       0.0.7
 */
class WebportalController extends JControllerLegacy
{
    /**
     * The default view for the display method.
     *
     * @var string
     * @since 12.2
     */
    protected $default_view = 'sent2webs';

    function getFile()
    {
        $path = JFactory::getApplication()->input->getString('file', '');

        $path = JPATH_ROOT . '/' . $path;;

        $lang = WFactory::getHelper()->getCurrentlySelectedLanguage();


        WFactory::getLogger()->debug("Getting file from : $path");

//        if (strpos($path, 'postal_code_select_frontpage_province') !== false) {
//            $break = 1;
//        }

        echo $this->__processFile($path);
        exit(1);
    }

    private function __processFile($path)
    {

        if (file_exists($path)) {
            ob_start();
            require $path;
            $str = ob_get_clean();
            return $str;
        } else {
            return "File does not exist: " . $path;
        }
    }
}
