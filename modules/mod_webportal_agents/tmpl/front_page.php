<?php
/**
 * Created by PhpStorm.
 * User: khan
 * Date: 9/1/15
 * Time: 11:17 PM
 */
/**
 * @var $agent AgentModel
 */

$agentsPerRow = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_TEMPLATE)->getParam('agentBlockColumns');
$breakAt = intval(24 / $agentsPerRow);
$i = 1;
$agentCount = count($agents);

$sendtoAgent=WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_TEMPLATE)->getParam('useOfficeEmailInsteadOfAgents') !=='true';


?>
<h1 class="modules__caption" xmlns="http://www.w3.org/1999/html"><?php echo JText::_("OUR_AGENTS") ?></h1>

<div class="column small-24 large-24 agents-frontpage--wrapper">

    <ul class="small-block-grid-1 medium-block-grid-2 large-block-grid-<?php echo $agentsPerRow;?>">

        <?php foreach ($agents as $agent) {
            $route = JRoute::_("index.php?option=com_webportal&view=agents&agent_id=" . $agent->id);
            ?>
            <li>

                <div class="agents-frontpage">

                    <div class="row">
                        <div class="columns
                        <?php if (getParam('agentBlock') == 'a1') { echo 'small-10 large-10';}
                              if (getParam('agentBlock') == 'a2') { echo 'small-24 large-24';} ?>">
                            <div class="agent__img">
                                <a href="<?php echo $route ?>"><img src="<?php echo $agent->image_file_path ?>"></a>
                            </div>
                        </div>

                        <div class="columns
                        <?php if (getParam('agentBlock') == 'a1') { echo 'small-14 large-14';}
                              if (getParam('agentBlock') == 'a2') { echo 'small-24 large-24 text-center';} ?> agent__details">
                            <h5><a href="<?php echo $route ?>"><?php echo $agent->first_name . ' ' . $agent->last_name ?></a>
                            </h5>
                            <?php if(!empty(trim($agent->title)) || !isset($agent->title)) {?>
                            <span class="agent-list__title"><?php echo $agent->title ?></span>
                            <?php }?>

                            <hr />
                            <?php if(!empty(trim($agent->phone)) || !isset($agent->phone)) {?>
                                <?php echo '<strong>' . JText::_("PHONE") . ': </strong>' . $agent->phone . '<br/>';
                            }?>
                            <?php if(!empty(trim($agent->mobile)) || !isset($agent->mobile)) { ?>
                                <?php echo '<strong>' . JText::_("MOBILE") . ': </strong>' . $agent->mobile . '<br/>';
                            }?>
                            <?php if(!empty(trim($agent->email)) || !isset($agent->email)) { ?>
                                <?php echo '<strong>' . JText::_("EMAIL") . ': </strong>' ?>
                                <a href="mailto:<?php echo ($sendtoAgent ? $agent->email : $office["email"]); ?>"><?php echo ($sendtoAgent ? $agent->email : $office["email"]); ?></a>
                            <?php } ?>
                    </div>
                </div>

            </li>
        <?php } ?>
    </ul>

</div>


