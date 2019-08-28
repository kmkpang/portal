<?php
$agents = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_AGENTS)->getAgentsAll();

usort($agents, "sortArray");
// office_id
// address_id
// title
?>
<!-- disgusting boiler-plate jQuery...!!!!! -->
<script type="text/javascript">
    localStorage.clear();
    function filterByText(element) {
        var needle = jQuery(element).val().toUpperCase();
        jQuery(".agent-list__name").each(function () {
            var heystack = jQuery(this).text().toUpperCase();
            //alert(heystack);
            if (heystack.match(needle)) {
                jQuery(this).closest(".agent-list__item").show();

            } else {
                jQuery(this).closest(".agent-list__item").hide();
            }

        });

        var evenOdd = 0;
        jQuery(".agent-list__item:visible").each(function () {
//            $(this).css("float", ( evenOdd ? "right" : "left" ));
//            evenOdd = !evenOdd;
        });


        updateAgentsCount();
    }

    function updateAgentsCount() {

        var agents_listed = "Agents";
        var count = jQuery(".agent-list__item:visible").length;
        var text = count + " " + agents_listed;

        jQuery("#agent_count").text(text);

    }
</script>

<div class="agent-list--wrapper large-24 medium-24 search-filters">
    <div class="row">
        <!--<h1 class="office-list__header"><?php //echo JText::_("PROPERTY AGENTS")?></h1>-->

        <div class="row collapse agent--counter--row">
            <!-- count query -->
            <div class="columns large-12 medium-12 small-24">
                <span id="agent_count"
                      class="agent--counter"><?php echo count($agents) . " " . JText::_("AGENTS"); ?></span>
            </div>

            <!-- search -->
            <div class="columns large-12 medium-12 small-24">
                <div class="input-textbox--wrapper">
                    <input type="text" name="search" placeholder="<?php echo JText::_("SEARCH") ?>"
                           onkeyup="filterByText(this)">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <ul class=" agents-listpage--wrapper small-block-grid-1 medium-block-grid-2 large-block-grid-3">

        <?php foreach ($agents as $agent) {
            $agentID = $agent->id;
            $agentRoute = JRoute::_("index.php?option=com_webportal&view=agents&agent_id=$agentID");
            $officeID = $agent->office_id;
            $officeTitle = $agent->office_name;
            $office = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_OFFICE)->getOffice($officeID,true);
            ?>
            <li class="agent-list__item" style="width: <?php echo getParam('agentBlockWidth') ?>; height: <?php echo getParam('agentBlockHeight') ?>;max-width: <?php echo getParam('agentBlockWidth') ?>; max-height: <?php echo getParam('agentBlockHeight') ?>;">
                <div class="large-24 agents-frontpage">
                    <div class="clearfix">
                        <div class="row agent-list__info">
                            <div class="
                            <?php if (getParam('agentBlock') == 'a1') { echo 'columns small-10 large-10';}
                                  if (getParam('agentBlock') == 'a2') { echo 'small-24 large-24 text-center';} ?>">
                                <div class="agent__img">
                                    <a href="<?php echo $agentRoute ?>"><img
                                            src="<?php echo $agent->image_file_path ?>"></a>
                                </div>
                            </div>

                            <div class="
                            <?php if (getParam('agentBlock') == 'a1') { echo 'columns small-14 large-14';}
                                  if (getParam('agentBlock') == 'a2') { echo 'small-24 large-24 text-center';} ?> agent__details">
                                <h5>
                                    <a href="<?php echo $agentRoute ?>" class="agent-list__name" ><?php echo $agent->first_name . ' ' . $agent->last_name ?></a>
                                </h5>
                                <h6><?php echo $officeTitle ?></h6>

                                <?php if(!empty(trim($agent->title)) || !isset($agent->title)) {?>
                                <div class="agent-list__title"><?php echo $agent->title ?></div>
                                <?php } ?>
                                <?php if(!empty(trim($agent->phone)) || !isset($agent->phone)) {?>
                                <div class="agent-list__phone"><strong><?php echo JText::_("PHONE") ?>
                                        : </strong><?php echo $agent->phone; ?></div>
                                <?php } ?>
                                <?php if(!empty(trim($agent->mobile)) || !isset($agent->mobile)) {?>
                                <div class="agent-list__mobile"><strong><?php echo JText::_("MOBILE") ?>
                                        : </strong><?php echo $agent->mobile; ?></div>
                                <?php } ?>
                                <?php if(!empty(trim($agent->email)) || !isset($agent->email)) {?>
                                <div class="agent-list__email"><a
                                        href="mailto:<?php echo ($sendtoAgent ? $agent->email : $office->email); ?>"><?php echo ($sendtoAgent ? $agent->email : $office->email); ?></a>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        <?php } ?>
    </ul>
</div>