<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$doc = JFactory::getDocument();
$app = JFactory::getApplication();
$lang = WFactory::getHelper()->getCurrentlySelectedLanguage();
require_once(JPATH_BASE . DS . 'templates' . DS . 'generic' . DS . 'controllers' . DS . 'helpers.php');

$property_id = $this->propertyId;
$property = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTY)->getDetail($property_id);
$address = WFactory::getSqlService()->getSqlServiceClass(__PROPPERTY_PORTAL_ADDRESS)->getAddressByLanguage($property_id,$lang);

?>

<link rel="stylesheet" type="text/css" href="assets/css/print.min.css"/>

<body onLoad="javascript:window.print()">

<table>
    <thead>
    <tr>
        <th>
            <div style="text-align: left; margin-bottom:10px; max-width: 200px; max-height: 100px;"><?php echo '<img src="'. JUri::root()
                    .getParam('logoPrint')
                    .'" alt="' .getParam('sitealt')
                    .'" title="' .getParam('sitetitle') .'"/>'; ?></div>
        </th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>
            <div id="webportal_property_print">
                <div>

                    <?php

                    foreach ($property->images as $i) {
                        $picpath[] = $i; // append to array of pics
                    }
                    //                            DebugBreak();
                    ?>
                    <div id="webportal_property_detail_slide_row">
                        <div id="webportal_property_detail_slide">
                            <img src="<?php echo $picpath[0] ?>" style="width: 100%;"/>
                        </div>

                        <div id="webportal_gmap_area" class="webportal_property_detail_gmap">

                            <?php $staticUrl="https://maps.googleapis.com/maps/api/staticmap?center={$property->latitude},{$property->longitude}&zoom=16&maptype=roadmap&size=245x235&markers=color:red|{$property->latitude},{$property->longitude}&key=AIzaSyBobyTTiskP_YfQXcokYGmND1ouViqCX8w"
                            ?>

                            <div class="embed-container">
                                <img src="<?php echo $staticUrl?>">
                            </div>
                        </div>
                    </div>
                    <!--<br />-->
                    <div id="webportal_property_print_detail_detail_row">

                        <div class="row property-details__address">
                            <?php
                            //<!--  ICELAND -->
                            if (getParam('countryCode') == 'is') { ?>
                                <span class="property-details__street"><?php echo $address; ?>,</span>
                                <span
                                    class="property-details__zipcode-name"><?php echo $property->zip_code . " " . $property->zip_code_name ?> </span>

                            <?php }
                            // <!--  THAILAND -->
                            if (getParam('countryCode') == 'th') {

                                if (getParam('propertyTitle') == 'false') {
                                    ?>
                                    <span class="property-details__street"><?php echo $address; ?>,</span>
                                    <span
                                        class="property-details__zipcode-name"><?php echo $property->zip_code_name . " " . $property->zip_code ?> </span>
                                <?php } else if (getParam('propertyTitle') == 'true') { ?>
                                    <span class="property-details__street"><?php echo $property->title; ?></span>

                                <?php }
                            }
                            // <!--  PHILIPPINE -->
                            if (getParam('countryCode') == 'ph') { ?>
                                <span class="property-details__street"><?php echo $address; ?> </span>
                                <span
                                    class="property-details__zipcode-name"><?php echo $property->zip_code_name; ?> </span>
                            <?php } ?>

                        </div>

                    </div>
                    <div id="webportal_property_detail_rating">
                        <!--rating-->
                    </div>
                    <div id="webportal_property_print_detail_detail">
                        <div class="first_column">
                            <ul>
                                <li><?php echo '<b>' . JText::_("PRICE") . ': </b>' ?>
                                    <?php echo $property->current_listing_price_formatted ?>
                                    &nbsp;</li>
                                <li><?php echo '<b>' . JText::_("PRICE PER SQ METER") . ': </b>' ?>
                                    <?php echo $property->total_area == 0 ? 0 : WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_CURRENCY)->convertCurrency($property->current_listing_price / $property->total_area) ?>
                                    &nbsp;</li>
                                <li><?php echo '<b>' . JText::_("SIZE") . ': </b>' ?><?php echo $property->total_area . ' m<sup>2</sup>' ?>
                                    &nbsp;</li>
                                <li><?php echo '<b>' . JText::_("TYPE") . ': </b>' ?><?php echo $property->category_name
                                    ?>&nbsp;</li>

                            </ul>
                        </div>
                        <div class="second_column">
                            <ul>
                                <li><?php echo '<b>' . JText::_("TOTAL ROOMS") . ': </b>' ?><?php echo $property->total_number_of_rooms ?>
                                    &nbsp;</li>
                                <li><?php echo '<b>' . JText::_("BATHROOMS") . ': </b>' ?><?php echo $property->number_of_bathrooms ?>
                                    &nbsp;</li>
                                <li><?php echo '<b>' . JText::_("LIVING ROOMS") . ': </b>' ?><?php echo $property->number_of_livingrooms ?>
                                    &nbsp;</li>
                                <li><?php echo '<b>' . JText::_("BEDROOMS") . ': </b>' ?><?php echo $property->number_of_bedrooms ?>
                                    &nbsp;</li>

                            </ul>
                        </div>
                        <div class="third_column">
                            <ul>
                                <li>&nbsp;</li>
                                <li>&nbsp;</li>
                                <li>&nbsp;</li>
                                <li>&nbsp;</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!--<br />-->
                <div id="webportal_property_print_detail_description_row">
                    <div id="webportal_property_detail_agent_print">
                        <div id="webportal_property_detail_agent_col">
                            <img id="agent_image" src="<?php echo $property->sales_agent_image ?>"/>

                            <div id="webportal_property_detail_agent_detail">
                                <ul>
                                    <li class="webportal_agent_detail_tag_name"><?php echo $property->sales_agent_full_name ?></li>
                                    <li class="webportal_agent_detail_tag_logo"><img
                                            src="<?php echo $property->office_logo_path ?>"/></li>
                                    <li class="webportal_agent_detail_tag_office"><?php echo $property->office_name ?></li>
                                    <li class="webportal_agent_detail_tag_tel"><b><?php echo JText::_("AGENT PHONE") ?>
                                            : </b><?php echo $property->sales_agent_mobile_phone ?></li>
                                    <li class="webportal_agent_detail_tag_tel"><b><?php echo JText::_("OFFICE PHONE") ?>
                                            : </b><?php echo $property->sales_agent_office_phone ?></li>
                                    <li class="webportal_agent_detail_tag_email"><b><?php echo JText::_("EMAIL") ?>
                                            : </b><?php echo $property->sales_agent_email ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div id="webportal_property_print_detail_description_col">
                        <div id="webportal_property_detail_description">
                            <p><?php echo $property->description_text; ?> </p>
                            <br/>
                        </div>
                    </div>
                </div>

            </div>
        </td>
    </tr>
    <tr>
        <td>&nbsp;</td>
    </tr>
    </tbody>
</table>
<div id="webportal_property_print_photos" style="page-break-before: always;">
    <?php
    $limit = count($picpath); //limit 6 is for thailand :)
    //$limit = 6;
    for ($i = 0, $j = 0; $i < $limit; $i++) { // skip the first one
        $path = $picpath[$i];
        $j++;
        echo '<img align="center" class=" avoidPageBreak ' . ($j % 2 ? 'odd' : 'even') . '" src="' . $path . '" />';


    }
    ?>

</div>
</body>
