<?php
/**
 * @var $office OfficeModel
 * @var $address PropertyAddressModel
 * */

$officeAddress = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_ADDRESS)->getAddress($office->address_id);

$addressLine = array();

if ($officeAddress["city_town_name"])
    $addressLine[] = $officeAddress["city_town_name"];
if ($officeAddress["postal_code_name"])
{
    $addressLine[] = $officeAddress["postal_code_name"];
    $addressLine[] = $officeAddress["postal_code"];
}

$addressLine = implode(", ", $addressLine);

?>

<div class="row large-24 office-mail-frontpage--wrapper">

    <div class="column medium-14 large-14 office-mail-frontpage">
        <h5><?php echo JText::_('MOD_WEBPORTAL_OFFICES_ADDRESS'); ?></h5>

        <?php echo $office->office_name ?>
        <br/>
        <br/>
        <?php echo $officeAddress["address"] ?>
        <br/>
        <?php echo $officeAddress["postal_code"] . ' ' . $officeAddress["postal_code_name"] ?>
    </div>

    <div class="column medium-10 large-10 office-mail-frontpage">
        <i class="fa fa-home"></i>
    </div>

</div>
