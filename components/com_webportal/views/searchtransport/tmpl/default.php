<?php
/**
 * Created by PhpStorm.
 * User: Lian
 * Date: 6/19/14
 * Time: 2:40 PM
 */

defined('_JEXEC') or die('Restricted access');


JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
$doc = JFactory::getDocument();

$app = JFactory::getApplication();
$menu = $app->getMenu();
$menuMapItem = $menu->getItems( 'link', 'index.php?option=com_webportal&view=map', true );
$menuListItem = $menu->getItems( 'link', 'index.php?option=com_webportal&view=list', true );

$propertiesMenuItemId = WFactory::getConfig()->getWebportalConfigurationArray()['propertiesItemId'][JFactory::getLanguage()->getTag()];
$propertiesMenuItem = JFactory::getApplication()->getMenu()->getItem($propertiesMenuItemId);
$propertiesListRoute = JUri::base() . $propertiesMenuItem->route;

WFactory::getHelper()->setCurrentPage('search');

require_once(JPATH_BASE . DS . 'templates' . DS . 'generic' . DS . 'controllers' . DS . 'helpers.php');

?>

<div class="clearfix" ng-cloak >
    <h3 class="modules__caption" style="margin-top: 1em;"><?php echo JText::_('PUBLIC TRANSPORTATION'); ?></h3>

    <div ng-controller="SearchCtrl">
        <form id="front-page-search-form"
              action="<?php echo JRoute::_('index.php?Itemid='. $menuListItem->id); ?>" method="post"
              ng-submit="savefilter()">

            <search-filters-bts-full></search-filters-bts-full>

        </form>
    </div>
</div>
