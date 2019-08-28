<?php
/**
 * Created by PhpStorm.
 * User: khan
 * Date: 8/2/15
 * Time: 12:08 PM
 */
$app = JFactory::getApplication();
$menu = $app->getMenu();
$menuListItem = $menu->getItems( 'link', 'index.php?option=com_webportal&view=list', true );

?>

<div class="large-24 column" style="padding: 0px">
    <div class="row collapse list-map-picker">
        <div class="column large-12">
            <a href="<?php echo JRoute::_('index.php?Itemid='. $menuListItem->id); ?>"
               class="sort-control__list-map--button left">List</a>
        </div>
        <div class="column large-12">
            <div
                class="sort-control__list-map--button right active">Map
            </div>
        </div>
    </div>
</div>
