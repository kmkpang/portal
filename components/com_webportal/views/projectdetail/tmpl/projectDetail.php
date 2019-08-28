<?php
/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

?>

<div class="row small-24 project-detail-detail">


    <div class="row">
        <div name="detail" class="column small-24 heading" style="padding-top: 74px; margin-top: -74px;">
            <?php echo JText::_("PROJECT_DETAIL")?>
        </div>
    </div>

    <div class="row bottom-border">
        <div class="column small-24 large-8 subheading-detail" style="padding-bottom: 1.25rem;">
            <?php echo JText::_("PROJECT_NAME")?>
        </div>
        <div class="column small-24 large-16 detail">
            <?php echo $projectDetail['projectName'] ?>
        </div>
    </div>

    <?php
        if (strlen(trim($projectDetail['project_content'])) > 0) {
    ?>
    <div class="row bottom-border" style="padding-top: 1.25rem; padding-bottom: 1.25rem;">
        <div class="column small-24 large-8 subheading-detail">
            <?php echo JText::_("DESCRIPTION")?>
        </div>
        <div class="column small-24 large-16 detail">
            <?php echo str_replace("\n", "<br>", $projectDetail['project_content']) ?>
        </div>
    </div>
    <?php
        }
    ?>

    <div class="row bottom-border" style="padding-top: 1.25rem; padding-bottom: 1.25rem;">
        <div class="column small-24 large-8 subheading-detail">
<!--            --><?php //echo JText::_("LOCATION")?>
        </div>
        <div class="column small-24 large-16 detail">
            <?php echo $projectDetail['projectDetails']['address']['address'] ?>
        </div>
    </div>

    <div class="row bottom-border" style="padding-top: 1.25rem; padding-bottom: 1.25rem;">
        <div class="column small-24 large-8 subheading-detail">
            <?php echo JText::_("LAND_SIZE")?>
        </div>
        <div class="column small-24 large-16 detail">
            <?php echo $projectDetail['projectDetails']['landSize'] ?>
        </div>
    </div>

<!--    <div class="row bottom-border">-->
<!--        <div class="column small-24 large-8 subheading-detail">-->
<!--            จำนวนยูนิตทั้งหมด-->
<!--        </div>-->
<!--        <div class="column small-24 large-16 detail">-->
<!--            --><?php //echo $projectDetail['projectDetails']['project_total_unit'] ?>
<!--        </div>-->
<!--    </div>-->
<!--    <div class="row bottom-border">-->
<!--        <div class="column small-24 large-8 subheading-detail">-->
<!--            ลักษณะโครงการ-->
<!--        </div>-->
<!--        <div class="column small-24 large-16 detail">-->
<!--            --><?php //echo $projectDetail['projectDetails']['project_property'] ?>
<!--        </div>-->
<!--    </div>-->
<!--    <div class="row bottom-border">-->
<!--        <div class="column small-24 large-8 subheading-detail">-->
<!--            รายละเอียดแบบบ้าน-->
<!--        </div>-->
<!--        <div class="column small-24 large-16 detail">-->
<!--            --><?php //echo $projectDetail['projectDetails']['project_plan_detail'] ?>
<!--        </div>-->
<!--    </div>-->

    <div class="row bottom-border" style="padding-top: 1.25rem; padding-bottom: 1.25rem;">
        <div class="column small-24 large-8 subheading-detail">
            <?php echo JText::_("Features")?>
        </div>
        <div class="column small-24 large-16 detail">
            <?php echo $projectDetail['projectDetails']['facilities'] ?>
        </div>
    </div>

<!--    <div class="row bottom-border" style="vertical-align: top !important;">-->
<!--        <div class="column small-24 large-8 subheading-detail">-->
<!--            ระบบรักษาความปลอดภัย-->
<!--        </div>-->
<!--        <div class="column small-24 large-16 detail">-->
<!--            --><?php //echo $projectDetail['projectDetails']['project_security'] ?>
<!--        </div>-->
<!--    </div>-->
<!---->
<!---->
<!--    <div class="row bottom-border" style="vertical-align: top !important;">-->
<!--        <div class="column small-24 large-8 subheading-detail">-->
<!--        รายละเอียดอื่นๆ-->
<!--        </div>-->
<!--        <div class="column small-24 large-16 detail">-->
<!--            --><?php //echo $projectDetail['projectDetails']['nearbyPlaces'] ?>
<!--        </div>-->
<!--    </div>-->



    <div class="row">
        <div class="column small-24 large-8 subheading-detail">
            &nbsp;
        </div>
        <div class="column small-24 large-16 notice">
            <?php echo JText::_("PROJECT_NOTICE")?>
        </div>

    </div>


</div>
