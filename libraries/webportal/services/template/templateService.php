<?php


/**
 * Created by PhpStorm.
 * User: khan
 * Date: 10/7/14
 * Time: 1:19 PM
 */
class TemplateService
{

    // {"countryCode":"is","sitetitle":"Softverk Webportal","sitealt":"Softverk Webportal","sitedescription":"Softverk Webportal Development Version","logoFile":"images\/webportal_logo.png","logoPrint":"images\/print_logo.png","languageEnable":"true","languageCode":["en-GB"],"dateFormat":"d.m.Y","selectTemplate":"t1","$template-path":"..\/..\/templates\/generic_b\/","$generic-primary-color-dark":"#293642","$generic-primary-color-medium":"#415569","$generic-primary-color-light":"#81A7CF","$generic-grey-color-dark":"","$generic-grey-color-medium":"","$generic-grey-color-light":"","$generic-grey-color-light-75percent":"","searchFrontPage":"full","mapFrontPage":"true","agentBlock":"a2","agentBlockWidth":"350px","agentBlockHeight":"350px","agentBlockColumns":"4","propertyID":"false","propertyTitle":"false","busFilter":"true","isNew":"true","viewportLoad":"true"}
    function generateTemplateVariable($param, $template = "")
    {
        if (WFactory::getHelper()->isNullOrEmptyString($template))
            $template = __TEMPLATE;
        $outputPath = JPATH_ROOT . "/templates/" . $template . "/scss/_variable_generated.scss";

        $fileContent = array();
        $fileContent[] = "/*--------------------------------------------------------*\\";
        $fileContent[] = "\t\$VARIABLES generated on " . WFactory::getSqlService()->getMySqlDateTime();
        $fileContent[] = "\\*--------------------------------------------------------*/";
        $fileContent[] = "";
        $param = json_decode($param);

        foreach ($param as $key => $value) {
            if (WFactory::getHelper()->startsWith($key, "$") && !WFactory::getHelper()->isNullOrEmptyString($value)) {
                $fileContent[] = "$key: $value;";
            }
        }

        $fileContent = implode("\n", $fileContent);

        file_put_contents($outputPath, $fileContent);

        return is_file($outputPath);


    }

    function getParam($param){
        $template   = JFactory::getApplication()->getTemplate(true);
        $params     = $template->params;
        $result 	= htmlspecialchars($params->get($param));

        return $result;
    }

}