<?php
/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */


?>

<script type="text/javascript">
    $(document).ready(function(){
        var tab = $('.tabs li');
        $('ul.tabs li').removeClass('current');
        $(tab[0]).click();
    })

    lightbox.option({
        'positionFromTop': 100,
    })

    function showImage(link, floorPlan, id) {
        var decoded = atob(link);
        var data = JSON.parse(decoded.trim());

        $('.tab-content').empty();

        $('ul.tabs li').removeClass('current');
        $('.tab-content').removeClass('current');

        $("#tab-title-"+id).addClass('current');
        $("#tab-"+id).addClass('current');

        var tabContent =  $("#tab-"+id);
        tabContent.empty();

        var tabImage = jQuery('<div class="tab-image owl-carousel owl-theme"></div>')
        var element = '';
        var lightbox = 0;

        for (var key in data) {
            var images = data[key]['images'];

            if (images) {
                for (var i = 0; i < images.length; i++) {
                    element += '<div class="item">' +
                                    '<a href="' + images[0].link + '" data-lightbox="image-' + lightbox + '">' +
                                        '<img src="' + images[0].link + '" />' +
                                    '</a>' +
                                '</div>';
                    lightbox++;
                }
            }
        }

        floorPlan = JSON.parse(atob(floorPlan).trim())

        for (var key in floorPlan) {
            var floorPlanImages = floorPlan[key] && floorPlan[key]['images']
            if (floorPlanImages) {
                for (var i = 0; i < floorPlanImages.length; i++) {
                    element += '<div class="item item-floorplan">' +
                                    '<a href="' + floorPlanImages[0].link + '" data-lightbox="image-' + lightbox + '">' +
                                        '<img src="' + floorPlanImages[0].link + '" />' +
                                    '</a>' +
                                '</div>';
                    lightbox++;
                }

            }

        }

        jQuery(element).appendTo(tabImage);

        tabImage.clone().appendTo(tabContent)
        tabImage.empty();

        var tabImageCarousel = $(tabContent).find('.tab-image');

        $(tabImageCarousel).owlCarousel({
            loop: false,
            items: 1,
            dots: false,
            nav: true,
            navText: ['<i class="fa fa-chevron-left" aria-hidden="true"></i>',
                    '<i class="fa fa-chevron-right" aria-hidden="true"></i>'
            ],
        });
        
        $(tabImageCarousel).append('<p class="text-overlay">Floor plan</p>');
        
        items = $('.owl-item .item');
        if($(items[0]).attr('class') != 'item item-floorplan') {
            $('.text-overlay').css('display','none');
        };

        $('.owl-prev').css('display','none');

        var owl = $('.owl-carousel');
        owl.on('changed.owl.carousel', function(event) {
            var items = $('.owl-item .item');
            var index = event.item.index;
            if($(items[index]).attr('class') != 'item item-floorplan') {
                $('.text-overlay').css('display','none');
            } else {
                $('.text-overlay').css('display','inline');
            }
        });
    }
</script>

<?php
$index = 0;
$firstImage = null;
$firstDetail = null;


$unitTypes = $projectDetail['projectDetails']['units']['UNIT TYPES'];
$floorPlans = $projectDetail['projectDetails']['units']['FLOOR PLAN'];

$lvl0Title = JText::_("UNIT_TYPES");
$lvl0Detail = $unitTypes;

?>

<?php if(sizeof($unitTypes) != 0 && sizeof($floorPlans) != 0) {?>
    <div class="unit-matrix" style="padding-top: 74px;">
        <div class="text-heading">
            <div name="unit" class="column small-24 heading" style="background-color: #f2f2f2; text-align: center;">
                <?php echo $lvl0Title ?>
            </div>
        </div>

        <div class="row small-24 project-matrix">
            <div class="row" style="padding-top: 2rem;">

                <div class="column list-tab medium-6 show-for-large-up">

                    <ul class="tabs">
                    <?php
                        $childAnchorIndex = 0;
                        $lvlIndex = 0;
                        
                        foreach ($lvl0Detail as $lvl1title => $lvl1Detail) {
                            
                            $floorPlan = null;
                            $i = 0;
                            foreach ($floorPlans as $f => $plan) {

                                if ($i == $lvlIndex) {
                                    $floorPlan = $plan;
                                    break;
                                }
                                $i++;
                            }

                            $floorPlan = $floorPlan['children'];

                            if ($floorPlan !== null)
                                $floorPlan = base64_encode(json_encode($floorPlan))
                            ?>

                            <?php if ($lvl1title != ""): ?>
                                <li id='tab-title-<?php echo $lvlIndex + 1 ?>'  class="tab-link" onclick="showImage('<?php echo base64_encode(json_encode($lvl1Detail['children'])) ?>','<?php echo $floorPlan ?>','<?php echo $lvlIndex + 1 ?>')">
                                    <?php echo $lvl1title ?>
                                    <i class="fa fa-chevron-right" aria-hidden="true"></i>
                                </li>
                            <?php endif; ?>
                            <?php $lvlIndex++;
                        } ?>
                    </ul>

                </div>
                <div class="column list-image small-24 medium-18 clearfix ">
                    <?php
                        $lvlIndex = 0;
                        
                        foreach ($lvl0Detail as $lvl1title => $lvl1Detail) { ?>
                            
                            <?php if ($lvl1title != ""): ?>
                                <div id="tab-<?php echo $lvlIndex + 1 ?>" class="tab-content">
                                    <?php echo $lvlIndex + 1 ?>
                                </div>
                            <?php endif; ?>
                            <?php $lvlIndex++;
                        } ?>
                </div>
            </div>
        </div>
    </div>
<?php }?>






