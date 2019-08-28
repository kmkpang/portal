<?php
/**
 * Created by PhpStorm.
 * User: Lian
 * Date: 7/2/14
 * Time: 4:40 AM
 */

$doc = JFactory::getDocument();
$app = JFactory::getApplication();
require_once(JPATH_BASE . DS . 'templates' . DS . 'generic' . DS . 'controllers' . DS . 'helpers.php');

$office_id = getParam('office_id');
if (intval($office_id) == 0) {
    //get default office..
    $office_id = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_OFFICE)->getDefaultOfficeId();


}
$contactType = getParam('contactStyle');
if (WFactory::getHelper()->isNullOrEmptyString($contactType))
    $contactType = 'c2';
?>

<div class="row row--contact no-breadcrumbs">
    <?php if ($contactType == 'c1') {
        require('singleContact.php');
    } else if ($contactType == 'c2') {
        require('detailContact.php');
    }
    ?>
</div>
