<?php if(sizeof($projectDetail["img_gallery"]) != 0) {?>
    <div class="gallery-container" style="padding-top: 74px;">
        <div class="text-heading">
            <div name="gallery" class="column small-24 heading" style="background-color: #f2f2f2; text-align: center;">
                <?php echo JText::_("GALLERY") ?>
            </div>
        </div>

        <div class="row small-24 project-progress">
            <div class="row">
                <div class="column small-24 medium-24 progress-image-wrapper">
                    <div id="slider-gallery" class="flexslider">
                        <ul class="slides">
                            <?php  foreach ($projectDetail["img_gallery"] as $_img){//foreach ($files as $f){ ?>
                                <?php if($_img!=""):?>
                                <li style="background:#292929;">
                                    <img src="<?php echo $_img?>" />
                                </li>
                            <?php endif;?>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php }?>



<script type="text/javascript">
    $(function () {
        $('#slider-gallery').flexslider({
            animation: "slide",
            controlNav: false,
            animationLoop: false,
            slideshow: false,
            sync: "#carousel"
        });
    });
</script>
