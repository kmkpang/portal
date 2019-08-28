<?php
/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

$bannerFolder = JPATH_BASE . "/images/projects/{$this->projectId}/constructionpictures/";
$imageRoot = JUri::base() . "images/projects/{$this->projectId}/constructionpictures/";
$files = array_values(array_diff(scandir($bannerFolder), array('..', '.')));

?>

<script type="text/javascript">
    $(function () {
        $("#bluecircle").percircle();

        $("#progressbar-strt").progressbar({
            value: <?php echo $projectDetail['projectDetails']['completion']['completonBreakDown']['Structural Work']?>
        });
        $("#progressbar-arch").progressbar({
            value: <?php echo $projectDetail['projectDetails']['completion']['completonBreakDown']['Architectural Work']?>
        });
        $("#progressbar-sys").progressbar({
            value: <?php echo $projectDetail['projectDetails']['completion']['completonBreakDown']['System Installation']?>
        });
        $('#carousel').flexslider({
            animation: "slide",
            controlNav: false,
            animationLoop: false,
            slideshow: false,
            itemWidth: 210,
            itemMargin: 5,
            asNavFor: '#slider'
        });

        $('#slider').flexslider({
            animation: "slide",
            controlNav: false,
            animationLoop: false,
            slideshow: false,
            sync: "#carousel"
        });
    });
</script>

<div class="row small-24 project-progress">

    <div class="row">
        <div name="progress" class="column small-24 heading">
            <?php echo JText::_("PROGRESS")?>
        </div>
        <div class="column small-24 medium-8 subheading">
            <?php echo JText::_("LAST_UPDATE")?> : <?php echo $projectDetail['projectDetails']['completion']['lastUpdate'] ?>
        </div>
        <div class="column small-24 medium-12 pull-right subheading">
            * <?php echo $projectDetail['projectDetails']['completion']['note'] ?>
        </div>
    </div>

    <div class="row">
        <div class="column small-24 medium-8 progress-bar-wrapper">

            <div class="row center">
                <div id="bluecircle" data-percent="<?php echo $projectDetail['projectDetails']['completion']['totalCompletion'] ?>"
                     class="medium">
                </div>
            </div>
            <div class="row">
                <div id="progressbar-strt"><div class="progress-label">
                        <?php echo JText::_("STRUCTURAL")?>
                    </div></div>
            </div>
            <div class="row">
                <div id="progressbar-arch"><div class="progress-label">
                        <?php echo JText::_("ARCHITECTURAL")?>
                    </div></div>
            </div>
            <div class="row">
                <div id="progressbar-sys"><div class="progress-label">
                        <?php echo JText::_("SYSTEM")?>
                    </div></div>
            </div>


        </div>
        <div class="column small-24 medium-16 progress-image-wrapper">


            <div id="slider" class="flexslider">
                <ul class="slides">
                    <?php  foreach ($projectDetail["image_completion"] as $_img){//foreach ($files as $f){ ?>
                        <?php if($_img!=""):?>
                        <li>
                            <img style="width:100%;height:auto" src="<?php echo $_img?>" />
                        </li>
                    <?php endif;?>
                    <?php } ?>
                </ul>
            </div>

        </div>
    </div>


</div>