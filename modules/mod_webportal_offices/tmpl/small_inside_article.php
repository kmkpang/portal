<?php
/**
 * Created by PhpStorm.
 * User: khan
 * Date: 1/9/15
 * Time: 10:34 AM
 */
$office_id = $params->get("office_id");

$offices = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_OFFICE)->getOfficeAll($office_id);

$singleOfficePath = JPATH_ROOT . "/components/com_webportal/views/offices/tmpl/singleOffice.php";



?>
<div class="single-office">
    <?php foreach ($offices as $num => $office)
        require $singleOfficePath;
    ?>
</div>


