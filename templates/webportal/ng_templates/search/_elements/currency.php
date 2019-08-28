<?php
/**
 * Created by PhpStorm.
 * User: khan
 * Date: 7/29/15
 * Time: 10:11 PM
 */

///home/khan/www/softverk-webportal-remaxth/templates/webportal/images/flags
///home/khan/www/softverk-webportal-remaxth/templates/webportal/ng_templates/search/_elements/currency.php
///home/khan/www/softverk-webportal-remaxth/templates/webportal/ng_templates/search/_elements/currency.php


/*
$format = numfmt_create('en_US', NumberFormatter::CURRENCY);
foreach ($currencies as $c) {

    if (!class_exists("NumberFormatter")) {
        return "sudo apt-get install php5-intl missing !";
    }

    $data = numfmt_format_currency($format, 123456775645645.121212121, $c->code);

   // echo $data . "<br/>";

}
*/
//ng-options="currency.display disable when currency.disabled for currency  in currency_tree track by currency.code"
?>

<div class="input-textbox--wrapper">

    <select class="currency-select" ng-model="searchfilter.preferred_currency"
            ng-change="currencyChanged()"
            ng-options="currency.code as currency.display disable when currency.disabled for currency  in currency_tree"
        >

    </select>

</div>

