<?php

if ($doc === null)
    $doc = JFactory::getDocument();

if (__COMPILE_JAVASCRIPT === false) {
///home/khan/www/softverk-webportal-generic/administrator/components/com_webportal/assets/admin.js
    $doc->addScript(JUri::root() . 'administrator/components/com_webportal/assets/properties.admin.js');
    $doc->addScript(JUri::root() . 'administrator/components/com_webportal/assets/property.admin.js');
    $doc->addScript(JUri::root() . 'administrator/components/com_webportal/assets/office.admin.js');
    $doc->addScript(JUri::root() . 'administrator/components/com_webportal/assets/agent.admin.js');
    $doc->addScript(JUri::root() . 'administrator/components/com_webportal/assets/company.admin.js');
    $doc->addScript(JUri::root() . 'administrator/components/com_webportal/assets/config.admin.js');
    $doc->addScript(JUri::root() . 'administrator/components/com_webportal/assets/xml.js');
    $doc->addScript(JUri::root() . 'administrator/components/com_webportal/assets/admin.js');

///home/khan/www/softverk-webportal-generic/assets/bower_components/ng-file-upload/ng-file-upload-all.min.js
} else {

    $doc->addScript(JUri::root() . 'assets/js/webportal.admin.min.js');

}

