<?php
/**
 * Created by PhpStorm.
 * User: Lian
 * Date: 7/16/14
 * Time: 7:38 PM
 */

jimport('legacy.application.application');
jimport('joomla.filesystem.file');

class HtmlmailService
{
    /**
     *
     * @param $templateName
     * @param $propertyDetailsModel PropertyDetailsModel
     * @return mixed|string
     */
    function getPropertyEmailTempalte($templateName, $propertyDetailsModel)
    {

        WFactory::getLogger()->debug("==== juri:base() is : " . JUri::base()."==================");
        $total_area = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTY)->convertSizeUnit($propertyDetailsModel->total_area);

        $title = $propertyDetailsModel->title;

        //Thailand Use
        if (__COUNTRY == "TH") {
            if ($propertyDetailsModel->category_id == 103 || $propertyDetailsModel->category_id == 105 || $propertyDetailsModel->category_id == 108 || $propertyDetailsModel->category_id == 114) {
                $rightTopDescription = "{$propertyDetailsModel->category_name} <br/>
                                    {$propertyDetailsModel->number_of_bedrooms} " . JText::_("BEDROOMS") . ", 
                                    {$propertyDetailsModel->number_of_bathrooms} " . JText::_("BATHROOMS") .
                    ", {$propertyDetailsModel->total_area} " . JText::_("SQM");
            } else if ($propertyDetailsModel->category_id == 106) {
                $rightTopDescription = "{$propertyDetailsModel->category_name} <br/>{$total_area}";
            } else {
                $rightTopDescription = "{$propertyDetailsModel->category_name} <br/>{$propertyDetailsModel->total_number_of_rooms} " . JText::_("ROOMS") .
                    ", {$total_area}";
            }
        } else {
            $rightTopDescription = "{$propertyDetailsModel->category_name} <br/>{$propertyDetailsModel->total_number_of_rooms} " . JText::_("ROOMS") .
                ", {$propertyDetailsModel->total_area} " . JText::_("SQM");
        }

        $rightTopPrice = JText::_(strtoupper($propertyDetailsModel->buy_rent)) . " " . $propertyDetailsModel->current_listing_price_formatted;

        $description = $propertyDetailsModel->getShortDescription(600);
        $full_description = $propertyDetailsModel->description_text;
        $banner_image = $propertyDetailsModel->imagesV2[0]->serverUrl;
        $url_to_direct_page = $propertyDetailsModel->url_to_direct_page;
        $address = "{$propertyDetailsModel->address}, {$propertyDetailsModel->zip_code} {$propertyDetailsModel->zip_code_name}";
        $staticMapUrl = "https://maps.googleapis.com/maps/api/staticmap?center={$propertyDetailsModel->latitude},{$propertyDetailsModel->longitude}&zoom=16&maptype=roadmap&size=280x140&markers=color:red|{$propertyDetailsModel->latitude},{$propertyDetailsModel->longitude}&key=AIzaSyBobyTTiskP_YfQXcokYGmND1ouViqCX8w";

        $agentName = $propertyDetailsModel->sales_agent_full_name;
        $agentImage = $propertyDetailsModel->sales_agent_image;

        /**
         * @var $office OfficeModel
        */
        $office=WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_OFFICE)->getDefaultOffice();

        $officeImage = $office->image_file_path;
        $agentDescription = JText::_('AGENT PHONE') . " : " . $propertyDetailsModel->sales_agent_mobile_phone . "<br/>" .
            JText::_('OFFICE PHONE') . " : " . $propertyDetailsModel->sales_agent_office_phone . "<br/>" .
            JText::_('EMAIL') . " : " . $propertyDetailsModel->sales_agent_email . "<br/>";
        $officeName = $office->office_name;
        $message = $propertyDetailsModel->message;
        $officeLink = JUri::base();

        $htmlMailFile = JPATH_BASE . "/libraries/webportal/services/htmlmail/$templateName/index.html";
        if (file_exists($htmlMailFile)) {

            $htmlMail = file_get_contents($htmlMailFile);

            $mergeFields = $this->__getMergeFieldsWithValues();
            $mergeFields['title'] = $title;
            $mergeFields['description'] = $description;
            $mergeFields['full_description'] = $full_description;
            $mergeFields['banner_image'] = $banner_image;
            $mergeFields['url_to_direct_page'] = $url_to_direct_page;
            $mergeFields['address'] = $address;
            $mergeFields['staticMapUrl'] = $staticMapUrl;
            $mergeFields['rightTopDescription'] = $rightTopDescription;
            $mergeFields['rightTopPrice'] = $rightTopPrice;
            $mergeFields['agentName'] = $agentName;
            $mergeFields['agentImage'] = $agentImage;
            $mergeFields['agentDescription'] = $agentDescription;
            $mergeFields['officeName'] = $officeName;
            $mergeFields['officeUrl'] = $officeLink;
            $mergeFields['officeImage'] = $officeImage;
            $mergeFields['message'] = $message;
           // WFactory::getLogger()->debug("merge fields look like this : " . json_encode($mergeFields),__LINE__,__FILE__);
            $htmlMail = $this->__applyMergeFields($htmlMail, $mergeFields);
            //WFactory::getLogger()->debug("Html mail content is : \n".$htmlMail );
            if (WFactory::getHelper()->isUnitTest()) {
                file_put_contents(JPATH_BASE . '/tmp/tempo.html', $htmlMail);
            }



            return $htmlMail;

        } else {
            return "";
        }


    }

    function getPropertiesEmailTemplate($templateName, $propertyDetailsModel, $message)
    {
        $leftcolumn = array();
        $rightColumn = array();
        $mergeFields = $this->__getMergeFieldsWithValues();
        $singleProperty = JPATH_BASE . "/libraries/webportal/services/htmlmail/$templateName/singleProperty.php";
        /** @var $p PropertyListModel */
        foreach ($propertyDetailsModel as $i => $p) {
            $filePath = JPATH_BASE . DS . "tmp/singleProperty_{$p->property_id}.html";

            $htmlMailFile = file_get_contents($singleProperty);

            $mergeFields['title'] = $p->title;
            $mergeFields['description'] = $p->description_text;
            $mergeFields['property_image'] = $p->initial_picture_path;
            $mergeFields['url_to_direct_page'] = $p->url_to_direct_page;
            $mergeFields['address'] = $p->property_region_town_zip_formatted;
            $mergeFields['category_name'] = $p->category_name;
            $mergeFields['rooms'] = $p->total_number_of_rooms . JText::_("ROOMS");
            $mergeFields['size'] = $p->total_area . JText::_("SQM");
            $mergeFields['price'] = JText::_(strtoupper($p->buy_rent)) . " " . $p->current_listing_price_formatted;;

            $htmlContent = $this->__applyMergeFields($htmlMailFile, $mergeFields);

            file_put_contents($filePath, $htmlContent);

            if ($i % 2 == 0) {
                $leftcolumn[] = $filePath;

            } else {
                $rightColumn[] = $filePath;
            }
        }

        $x = JPATH_BASE . "/libraries/webportal/services/htmlmail/$templateName/index.php";
        ob_clean();
        ob_start();
        require_once $x;
        $content = ob_get_contents();
        ob_end_clean();




        $mergeFields["message"] = $message;
        $content = $this->__applyMergeFields($content, $mergeFields);


        if (WFactory::getHelper()->isUnitTest()) {
            file_put_contents(JPATH_BASE . '/tmp/2columnProperties.html', $content);
        }

        return $content;
    }


    private function __getMergeFieldsWithValues()
    {
        //   $configurationArray = WFactory::getConfig()->getWebportalConfigurationArray();

        $logoTop = file_exists(JPATH_ROOT . WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_TEMPLATE)->getParam('logoFile')) ? WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_TEMPLATE)->getParam('logoFile') : "images/logo.png";
        //$logoTop = JUri::base().$logoTop;
        $background = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_TEMPLATE)->getParam('$generic-primary-color-dark');
        $foreground = "#f2f2f2";//WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_TEMPLATE)->getParam('$generic-secondary-color-dark');

        $mergeFields = array(
            'logo_top' => JUri::base() . $logoTop,
            'logo' => JUri::base() . $logoTop,
            'company_name' => WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_COMPANY)->getCompany()->company_name,
            'generic-primary-color-dark' => $background,
            'generic-grey-color-light' => $foreground,
        );

//        foreach ($configurationArray['htmlMail'] as $key => $val) {
//            $mergeFields[$key] = $val;
//        }

        return $mergeFields;
    }

    private function __applyMergeFields($text, $mergeFields)
    {
        if (WFactory::getHelper()->isUnitTest()) {
            $base = JUri::base();
            $realBase = "http://localhost/softverk-webportal-generic/";
            foreach ($mergeFields as $key => $value) {
                $mergeFields[$key] = str_replace($base, $realBase, $value);
            }
        }

        foreach ($mergeFields as $k => $v) {
            WFactory::getLogger()->debug("Replacing : {{{$k}}} --> $v");
            $text = str_replace('{{' . $k . '}}', $v, $text);
        }

        return $text;
    }


}
