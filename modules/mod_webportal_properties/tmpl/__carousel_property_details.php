<div class="property-details__other-properties--description"><h3><?php echo JText::_("OTHER PROPERTIES") ?></h3></div>
<div class="featuredproperties-carousel owl-carousel owl-theme large-24">

    <?php
    $currentIndex = 0;
    $centerPropertyId = JFactory::getApplication()->input->getInt('propertyid', 0);
    $centerIndex = 0;
    for ($properties_items = 0; $properties_items < count($properties); $properties_items++) {
        $property = $properties[$currentIndex];
        $currentIndex++;

        if ($properties_items == 4) {
            continue;
        }
        /*
        if ($centerPropertyId !== null && $centerPropertyId == $property->property_id) {
            $style = "active";
            $centerIndex = $currentIndex;
        } else
            $style = "";
        */

        require "__single_property_property_details_page.php";
    }

    //do some mumbo jumbo here to make the middle property centered!!!!

    $itemCount = 3; //3 in each slide !

    $goTo = 0;
    if ($centerIndex >= 3)
        $goTo = 1;
    if ($centerIndex >= 6)
        $goTo = 2;

    ?>
</div>

<script type="text/javascript">

    jQuery(document).ready(function ($) {
        var owlContainer = $('.featuredproperties-carousel');
        owlContainer.owlCarousel({
            // animateOut: 'fadeOut',
            // animateIn: 'fadeIn',
            lazyLoad: true,
            loop: false,
            items: <?php echo $itemCount; ?>,
            margin: 10,
            dots: true,
            smartSpeed: 200,
            responsive: {
                0: {
                    items: 1,
                    dots: true,
                    stagePadding: 20
                },
                600: {
                    items: <?php echo $itemCount; ?>,
                }
            }
        });

        owlContainer.trigger('to.owl.carousel', <?php echo $goTo?>)


    });
</script>


