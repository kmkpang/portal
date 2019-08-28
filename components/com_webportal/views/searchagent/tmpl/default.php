<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_BASE . DS . 'templates' . DS . 'generic' . DS . 'controllers' . DS . 'helpers.php');
$app = JFactory::getApplication();
$menu = $app->getMenu();
$menuMapItem = $menu->getItems( 'link', 'index.php?option=com_webportal&view=map', true );
$menuListItem = $menu->getItems( 'link', 'index.php?option=com_webportal&view=list', true );
$offices = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_OFFICE)->getOffices();
$postalCodeTree = WFactory::getServices()->getSqlService()->getSqlServiceClass(__PROPPERTY_PORTAL_ADDRESS)->postalCodeTree();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

//    $first_last_phone = $_POST["$first_last_phone"];
//	$last_name = $_POST["last_name"];
    $first_last_phone = $_POST["first_last_phone"];
    $regional_id = $_POST["regional_id"];
    $office_id = $_POST["office_id"];
    $agents = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_AGENTS)->getSearchAgents($first_last_phone,$office_id,$regional_id,false);
}

?>

<div id="search" class="small-24 search-filters">
    <div class="row">
        <form method="post" class="row" action="<?php echo JRoute::_("index.php?option=com_webportal&views=searchagent");?>">

            <div class="input-textbox--wrapper search-filters-listmap">

                <div class="column large-10 search-filters--frontpage--row clearfix">
                    <div class="input-textbox--wrapper">
                        <input type="text" name="first_last_phone" placeholder="<?php echo JText::_('SEARCH_BY_FIRST_LAST_PHONE') ?>"/>
                    </div>
                </div>

                <div class="column large-5 search-filters--frontpage--row clearfix">
                    <select id="regional_id" name="regional_id">
                        <option value="" selected default><?php echo JText::_('SELECT PROVINCE') ?></option>
                        <?php foreach($postalCodeTree as $region) { ?>
                            <option value="<?php echo $region['id'] ?>"><?php echo $region['name'] ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="column large-5 search-filters--frontpage--row clearfix">
                    <select id="office_id" name="office_id">
                        <option value="" selected default><?php echo JText::_('SELECT OFFICE') ?></option>
                        <?php foreach($offices as $o) { ?>
                            <option value="<?php echo $o->id ?>"><?php echo $o->office_name ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="column large-4 search-filters--frontpage--row clearfix">
                    <input type="submit" value="<?php echo JText::_("SEARCH") ?>" class="input-submit primary-medium" />
                </div>
            </div>

        </form>
    </div>
</div>

<?php

if(!empty($agent_id))
{
    require('agent.php');
}
else
{
    require('agents.php');
}

?>
