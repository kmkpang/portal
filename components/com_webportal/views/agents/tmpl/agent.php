<?php
/**
 * @var $agent AgentModel
 */
$agent = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_AGENT)->getAgent($agent_id);

$officeId = $agent->office_id;
$office = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_OFFICE)->getOffice($officeId,true);
//$agent_properties = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTIES)->getPropertiesByAgent($agent_id);

JFactory::getDocument()->setTitle($agent->office["office_name"] . ' - ' . $agent->first_name . " " . $agent->middle_name . " " . $agent->last_name);

defined('__SITEURL'); // just in case i didnt do it already!!
$shareUrl = __SITEURL . $_SERVER['REQUEST_URI'];
$breadcrumbs = JModuleHelper::getModules('breadcrumbs');

?>
<script src="http://platform.twitter.com/widgets.js"></script>
<script type="text/javascript">

    window.agentId = <?php echo $agent_id?>;
    window.agentName = "<?php echo $agent->first_name . " " . $agent->middle_name . " " . $agent->last_name; ?>";

    localStorage.clear();

</script>

<div class="row row--agents-details no-breadcrumbs">
    <div class="agent-details--wrapper large-24 small-24">
        <div class="agent-details clearfix">
            <div class="column small-24 medium-6 large-6">
                <div class="agent-detail__agent-img">
                    <img src="<?php echo $agent->image_file_path; ?>">
                </div>

                <div class="agent-detail__agent-contact--bottom">
                    <?php if(!empty(trim($agent->phone)) || !isset($agent->phone)) {?>
                    <div class="agent-details__label--row">
                        <i class="fa fa-phone"></i> <strong><?php echo JText::_("PHONE") ?>
                            :</strong> <?php echo $agent->phone; ?>
                    </div>
                    <?php } ?>
                    <?php if(!empty(trim($agent->mobile)) || !isset($agent->mobile)) {?>
                    <div class="agent-details__label--row">
                        <i class="fa fa-mobile"></i> <strong><?php echo JText::_("MOBILE") ?>
                            :</strong> <?php echo $agent->mobile; ?>
                    </div>
                    <?php } ?>
                    <?php if(!empty(trim($agent->email)) || !isset($agent->email)) {?>
                    <div class="agent-details__label--row">
                        <i class="fa fa-envelope"></i> <strong><?php echo JText::_("EMAIL") ?>:</strong>
                        <a href="mailto:<?php echo ($sendtoAgent ? $agent->email : $office->email); ?>"> <?php echo ($sendtoAgent ? $agent->email : $office->email); ?>
                        </a>
                    </div>
                    <?php } ?>
                </div>
                <!-- logo -->
                <!--                <div class="agent-details__label--row">-->
                <!--                    <a href="-->
                <?php //echo "http://" . $agent->office['url_to_private_page']; ?><!--">-->
                <!--                        <img class="agent-details__office-logo" src="-->
                <?php //echo $agent->office['logo'] ?><!--" alt="">-->
                <!--                    </a>-->
                <!--                </div>-->

            </div>
            <div class="column small-24 medium-18 large-18">
                <h1 class="agent-detail__agent-name"><?php echo $agent->first_name . " " . $agent->middle_name . " " . $agent->last_name; ?></h1>

                <h2 class="agent-details__office-name"><?php echo $agent->office["office_name"]; ?></h2>

                <h5 class="agent-detail__agent-title"><?php echo $agent->title ?></h5>
                <hr/>

                <div class="agent-detail__agent-contact--right">
                    <?php if(!empty(trim($agent->phone)) || !isset($agent->phone)) {?>
                    <div class="agent-details__label--row">
                        <i class="fa fa-phone"></i> <strong><?php echo JText::_("PHONE") ?>
                            :</strong> <?php echo $agent->phone; ?>
                    </div>
                    <?php } ?>
                    <?php if(!empty(trim($agent->mobile)) || !isset($agent->mobile)) {?>
                    <div class="agent-details__label--row">
                        <i class="fa fa-mobile"></i> <strong><?php echo JText::_("MOBILE") ?>
                            :</strong> <?php echo $agent->mobile; ?>
                    </div>
                    <?php } ?>
                    <?php if(!empty(trim($agent->email)) || !isset($agent->email)) {?>
                    <div class="agent-details__label--row">
                        <i class="fa fa-envelope"></i> <strong><?php echo JText::_("EMAIL") ?>:</strong>
                        <a href="mailto:<?php echo ($sendtoAgent ? $agent->email : $office->email); ?>"> <?php echo ($sendtoAgent ? $agent->email : $office->email); ?>
                        </a>
                    </div>
                    <?php } ?>
                    <hr/>
                </div>

                <?php
                $marketingInfo = array("slogan", "closer", "bullet_point1", "bullet_point2", "bullet_point3", "description");
                foreach ($marketingInfo as $m) {
                    if (!WFactory::getHelper()->isNullOrEmptyString($agent->marketing_info[$m])) {
                        ?>

                        <div class="agent-details__label--row">
                            <?php echo $agent->marketing_info[$m]; ?>
                        </div>

                        <?php
                    }
                }

                ?>

                <br/>
                <br/>
                <a facebook class="facebookShare"
                   data-url='<?php echo $shareUrl ?>'
                   data-shares='shares'>{{ shares }}</a>

                <a twitter data-lang="<?php echo WFactory::getHelper()->getCurrentlySelectedLanguage(true) ?>"
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
    </div>


    <div class="agent-details--wrapper small-24 large-24">
        <!-- ========================================================================================================= -->

        <div class="row " id="property-tab" ng-controller="SearchCtrl">
            <div class="agent-details__properties-container">
                <div class="row collapse pager-list-header clearfix">
                    <div class="property-list__pager column large-24">
                        <?php require JPATH_BASE . "/templates/webportal/ng_templates/properties/controls.php"; ?>
                    </div>
                </div>
                <br/>

                <div class="row agent-details__properties" id="search">
                    <div class="large-24">
                        <properties-list></properties-list>
                    </div>

                </div>
                <br/>

                <div class="row collapse pager-list-header clearfix">
                    <div class="property-list__pager column large-24">
                        <?php require JPATH_BASE . "/templates/webportal/ng_templates/properties/controls.php"; ?>
                    </div>
                </div>

            </div>

        </div>


    </div>

</div>