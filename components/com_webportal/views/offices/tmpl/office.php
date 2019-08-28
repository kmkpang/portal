<?php
$offices = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_OFFICE)->getOffice($office_id);
$officeAddress = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_ADDRESS)->getAddress($offices['address']['id']);
$office_agents = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_AGENTS)->getAgents($office_id);
//$office_properties = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTIES)->getPropertiesByOffice($office_id);

JFactory::getDocument()->setTitle($offices['office_name']);


$shareUrl = __SITEURL . $_SERVER['REQUEST_URI'];

?>

<script src="http://platform.twitter.com/widgets.js"></script>
<script type="text/javascript">

    window.officeId = <?php echo $office_id?>;
    window.officeName = "<?php echo $offices['office_name']?>";
    localStorage.clear();
    $(document).ready(function () {


        function scrollTo(element) {
            jQuery('html, body').animate({
                scrollTop: jQuery(element).offset().top - 100
            }, 2000);
        }

//TODO: Refactor this mess!angularise...

        $('#office-tab-option').click(function () {
            $('#agents-tab').hide();
            $('#property-tab').hide();
        });

        $('#agent-tab-option').click(function () {
            $('#agents-tab').show();
            $('#property-tab').hide();
            updateHash('agent-tab');

            scrollTo('#agents-tab');
        });

        $('#property-tab-option').click(function () {
            $('#agents-tab').hide();
            $('#property-tab').show();
            updateHash('property-tab');

            scrollTo('#property-tab');
        });

        $('#tab-options li').click(function () {
            $(this).addClass('active').siblings().removeClass('active');
        });


        var hash = window.location.hash;
        if (hash.indexOf("property-tab") != -1) {
            $('#property-tab-option').trigger('click');
        } else if (hash.indexOf("agent-tab") != -1) {
            $('#agent-tab-option').trigger('click');
        }

        function updateHash(value) {
            angular.injector(['ng', 'webportal']).get('portal').updateUrlHashParameter('tab', value)
        }

    });
</script>

<div class="row row--office-details no-breadcrumbs">

    <div class="office-details--wrapper small-24 large-24 clearfix">

        <div class="office-details">
            <div class="office-details__image-map--wrapper row collapse clearfix">
                <div class="office-details__image-wrapper column medium-14">
                    <div class="office-details__image-container">
                        <img src="<?php echo $offices['image_file_path']; ?>" class="office-details__image">
                    </div>

                    <div class="office-details__office-name"><?php echo $offices['office_name']; ?>
                        <?php echo $offices['office_name']; ?></div>
                    <div class="office-details__office-address">

                        <?php echo $officeAddress["address"] . "<br/>" .
                            "{$officeAddress["property_region_town_zip_formatted"]}" . "<br/>";
                        ?>
                    </div>
                    <div class="office-details__office-description">
                        <?php echo $offices['marketingInfo']['description']; ?>
                        <br/>
                        <br/>
                        <a facebook class="facebookShare"
                           data-url='<?php echo $shareUrl ?>'
                           data-shares='shares'>{{ shares }}</a>

                        <a twitter data-lang="en"
                           data-count='horizontal'
                           data-url='<?php echo $shareUrl ?>'
                           data-size="medium"
                           data-text=''></a>

                        <div gplus class="g-plus"
                             data-size="tall"
                             data-annotation="bubble"
                             data-href='<?php echo $shareUrl ?>'
                             data-action='share'></div>

                    </div>
                </div>
                <div class="office-details__maps-wrapper column medium-10">
                    <div class="embed-container">
                        <embed-map element-id="office-map" lat="<?php echo $offices['address']['latitude'] ?>"
                                   lng="<?php echo $offices['address']['longitude'] ?>"></embed-map>
                    </div>
                    <div class="office-details__info">
                        <span class="office-details__info-header"><?php echo JText::_("INFORMATION")?></span>
                        <span class="office-details__office-email">
                            <strong><?php echo JText::_("EMAIL")?>: </strong> <a
                                href="mailto:<?php echo $offices['email']; ?>"><?php echo $offices['email']; ?></a>
                        </span>
                        <span class="office-details__office-phone">
                            <strong><?php echo JText::_("PHONE")?>: </strong><?php echo $offices['phone']; ?>
                        </span>
                    </div>
                    <div class="office-details__submenu">
                        <ul id="tab-options" class="no-bullet">
                            <li id="office-tab-option" class="active"><?php echo JText::_("OFFICE INFORMATION")?></li>
                            <li id="agent-tab-option"><?php echo JText::_("OUR AGENTS")?></li>
                            <li id="property-tab-option"><?php echo JText::_("OFFICE PROPERTIES LISTED")?></li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>

        <div id="agents-tab" class="agent-list--wrapper large-24" style="display:none">
                <div class="row--office-list">

                    <div class="row">
                        <?php foreach ($office_agents as $agent) {
                        $agentID = $agent->id;
                        $agentRoute = JRoute::_("index.php?option=com_webportal&view=agents&agent_id=$agentID");
                        $officeID = $agent->office_id;
                        $officeTitle = $agent->office_name;
                        ?>
                        <div class="columns large-<?php echo 24/getParam('agentBlockColumns'); if($agent === end($office_agents)) {echo " end";} else {}?> small-24 ">
                            <div class="columns large-24 agents-frontpage">
                                <div class="row agent-list__item clearfix" style="/*width: <?php echo getParam('agentBlockWidth') ?>;*/ height: <?php echo getParam('agentBlockHeight') ?>;max-width: <?php echo getParam('agentBlockWidth') ?>; max-height: <?php echo getParam('agentBlockHeight') ?>;">
                                    <div class="row agent-list__info">
                                        <div class="
                                            <?php if (getParam('agentBlock') == 'a1') { echo 'columns small-10 large-10';}
                                                  if (getParam('agentBlock') == 'a2') { echo 'small-24 large-24 text-center';} ?>">
                                            <div class="agent__img">
                                                <a href="<?php echo $agentRoute?>"><img src="<?php echo $agent->image_file_path ?>"></a>
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
                                                        href="mailto:<?php echo ($sendtoAgent ? $agent->email : $offices['email']); ?>"><?php echo ($sendtoAgent ? $agent->email : $offices['email']); ?></a>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php } ?>
                    </div>
                </div>
        </div>

        <!-- ========================================================================================================= -->

        <div id="property-tab" style="display:none" ng-controller="SearchCtrl">

            <div class="row collapse pager-list-header clearfix">
                <div class="property-list__pager column large-24">
                    <?php require JPATH_BASE . "/templates/webportal/ng_templates/properties/controls.php";?>
                </div>
            </div>
            <br/>

            <div id="search">
                <div class="large-24">
                    <properties-list></properties-list>
                </div>


            </div>
            <br/>

            <div class="row collapse pager-list-header clearfix">
                <div class="property-list__pager column large-24">
                    <?php require JPATH_BASE . "/templates/webportal/ng_templates/properties/controls.php";?>
                </div>
            </div>

        </div>

    </div>


</div>