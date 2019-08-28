<?php
/**
 * Created by PhpStorm.
 * User: Lian
 * Date: 6/19/14
 * Time: 2:40 PM
 */

defined('_JEXEC') or die('Restricted access');


JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
require_once(JPATH_BASE . DS . 'templates' . DS . 'generic' . DS . 'controllers' . DS . 'helpers.php');

?>

  <div ng-controller="AgentsMapCtrl" class="ng-cloak">

        <div class="property-map--wrapper large-24">
            <div class="embed-container">
                <agents-map></agents-map>
            </div>
        </div>
        <?php //<label class="pull-left" ng-show="!listloading">{{mapstatus}}</label> ?>
    </div>
