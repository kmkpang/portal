<?php

$doc = JFactory::getDocument();
$lang = WFactory::getHelper()->getCurrentlySelectedLanguage();
$root = JURI::root();
// JHtml::script('../assets/webportal_carousel.js'); Doesn't work because my purging script is so awesome

//$path = $params->get('folderPath');
$load = $params->get('lazyLoad','true');
$loop = $params->get('loop','true');
$autoplay = $params->get('autoPlay','true');
$timeout = $params->get('timeOut','20000');
$speed = $params->get('smartSpeed','1000');
//$pagination = $params->get('pagination');
$height = $params->get('minHeight');
$islink = $params->get('imgLink');

?>

<style>
    .frontpage__carousel.owl-carousel .item {
        max-height: <?php echo $height;?> !important;
    }
</style>

<script type="text/javascript">
    //This causes JS blocking onLoad but why even bother with quality work if no one sees it
    jQuery(document).ready(function ($) {
        $('.frontpage__carousel').owlCarousel({
            animateOut: 'fadeOut',
            animateIn: 'fadeIn',
            lazyLoad: <?php echo $load;?>,
            loop: <?php echo $loop;?>,
            items: 1,
            mouseDrag: false,
            touchDrag: false,
            pullDrag: false,
            freeDrag: false,
            autoplay: <?php echo $autoplay;?>,
            autoplayTimeout: <?php echo $timeout;?>,
            dots: false,
            smartSpeed: <?php echo $speed;?>
        });
    });

</script>

<div class="show-large-only">
    <div class="frontpage__carousel owl-carousel owl-theme">

        <?php

        $images = array();
        if (file_exists(JPATH_BASE . '/images/carousel_bg'))
            $images = glob("images/carousel_bg/*" . ".{jpg,jpeg,png,gif}", GLOB_BRACE);
        if (count($images)) {
            natcasesort($images);
            foreach ($images as $image) {
                ?>
                <div class="item">
                    <?php $propertyid = substr(str_replace(".jpg","", str_replace("images/carousel_bg/","",$image)), -5, 5); ?>
                    <?php if($islink == 'true' && strlen($propertyid) == 5) : ?>
                        <a href="<?php echo $propertyid;?>"><img src="<?php echo $image ?>" alt=""/></a>
                    <?php else : ?>
                        <img src="<?php echo $image ?>" alt=""/>
                    <?php endif; ?>
                </div>
            <?php }
        } else {

            echo "<h1 class='no-image'>No images</h1>";
        }
        ?>
    </div>
</div>

