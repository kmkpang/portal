<?php
/**
 * Created by PhpStorm.
 * User: Lian
 * Date: 7/16/14
 * Time: 7:38 PM
 */

jimport('legacy.application.application');
jimport('joomla.filesystem.file');

class LangService
{
    function get()
    {
        $path = JFactory::getApplication()->input->getString('file', '');

        $path = JPATH_BASE . '/' . $path;;

        $lang = WFactory::getHelper()->getCurrentlySelectedLanguage();
        $template = JFactory::getApplication()->getTemplate();

        if (strpos($path, '/templates/webportal/ng_templates/') !== false)//check if there is an override
        {
            $templatePath = "/templates/$template/html/ng_templates/";
            $__tempPath = str_replace('/templates/webportal/ng_templates/', $templatePath, $path);
            if (JFile::exists($__tempPath)) {
                WFactory::getLogger()->info("Overriding $path with $__tempPath");
                $path = $__tempPath;
            }
        }

        WFactory::getLogger()->debug("Getting file from : $path");

        return $this->processFile($path);
    }


    function processFile($path)
    {

        if (JFile::exists($path)) {
            ob_start();
            require $path;
            $str = ob_get_clean();
            return $str;
        } else {
            return "File does not exist: " . $path;
        }
    }
}
