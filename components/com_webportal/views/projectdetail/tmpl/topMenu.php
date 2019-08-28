<?php
/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */


?>
<script>
    jQuery(document).ready(function () {
        //alert("xxx")
        jQuery("#sticker").sticky({topSpacing: 0, zIndex: 999999});
    })

</script>
<div class="project-menu show-large-only" id="sticker">
    <div class="project-menu--wrapper">
        <?php
            if (strlen($projectDetail['projectLogo']) > 1) {
        ?>
        <img alt="" class="project-logo" src="<?php echo $projectDetail['projectLogo'] ?>">
        <?php
            }
        ?>
        <div class="row">
            <div class="large-24 ul-wrapper">
                <ul>
                    <?php if(!WFactory::getHelper()->isNullOrEmptyString($projectDetail['image2_2'])){?>
                    <li data-rel="concept" onclick="" class=""><a href="<?php echo $_SERVER['REQUEST_URI'] ?>#concept"><?php echo JText::_("CONCEPT")?></a></li>
                    <li>|</li>
                    <?php } ?>
                    <li data-rel="detail" onclick="" class=""><a href="<?php echo $_SERVER['REQUEST_URI'] ?>#detail"><?php echo JText::_("DETAIL")?></a></li>
                    <li>|</li>
                    <li data-rel="facility" onclick="" class=""><a href="<?php echo $_SERVER['REQUEST_URI'] ?>#facility"><?php echo JText::_("FACILITY")?></a></li>
                    <li>|</li>
                    <li data-rel="unit" onclick="" class=""><a href="<?php echo $_SERVER['REQUEST_URI'] ?>#unit"><?php echo JText::_("ROOM_TYPE")?></a></li>
                    <li>|</li>
                    <li data-rel="gallery" onclick="" class=""><a href="<?php echo $_SERVER['REQUEST_URI'] ?>#gallery"><?php echo JText::_("GALLERY")?></a></li>
                    <li>|</li>
                    <li data-rel="location" onclick="" class=""><a href="<?php echo $_SERVER['REQUEST_URI'] ?>#location"><?php echo JText::_("LOCATION")?></a></li>

                </ul>
            </div>

        </div>
    </div>
</div>
