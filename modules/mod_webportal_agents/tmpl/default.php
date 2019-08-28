<?php

$template = $params->get("template");
$office_id = $params->get("office_id");
$office = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_OFFICE)->getOffice($office_id);

$agents = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_AGENTS)->getAgents($office_id);
require_once(JPATH_BASE . DS . 'templates' . DS . 'generic' . DS . 'controllers' . DS . 'helpers.php');

require $template;

?>