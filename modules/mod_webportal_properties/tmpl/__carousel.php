<?php

?>

<script type="text/javascript">

jQuery(document).ready(function ($) {
    $('.featuredproperties-carousel').owlCarousel({
    // animateOut: 'fadeOut',
    // animateIn: 'fadeIn',
    lazyLoad: true,
    loop: true,
    items: <?php echo $columns; ?>,
    margin: 10,
    autoWidth: false,
    nav: false,
    rtl:false,
    video:true,
    // mouseDrag:false,
    // touchDrag:false,
    // pullDrag: false,
    // freeDrag: false,
    autoplay: true,
    // autoplayTimeout: 20000,
    dots: true,
	smartSpeed:200,
    responsive:{
        0:{
            items:1,
            dots: true,
            stagePadding: 50
        },
        600:{
            items: <?php echo $columns; ?>,
        }
    }
	});
});
</script>

<div class="featuredproperties-carousel owl-carousel owl-theme large-24 row">

<?php
	$currentIndex = 0;
    $colrows = $columns * $rows;

    if(count($properties) < $colrows) {
        $colrows = count($properties);
    }

	for ($properties_items = 0; $properties_items < $colrows; $properties_items++) {
		$property = $properties[$currentIndex];
		$currentIndex++;

        require "__single_property_property_details_page.php";
	}
?>
</div>

<?php if($params->get('viewall') == 'true' ) { ?>
<div>
    <a href="<?php echo JRoute::_('index.php?Itemid='.$menuListItem->id); ?>" class="input-submit primary-medium" style="max-width: 300px;"><?php echo JText::_('VIEW_ALL'); ?></a>
</div>
<?php } ?>
