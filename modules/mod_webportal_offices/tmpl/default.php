<?php

$template = $params->get("template");
$office_id = $params->get("office_id");

$office = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_OFFICE)->getOffice($office_id, true);

require $template;

?>