<?php
/**
 * Created by PhpStorm.
 * User: khan
 * Date: 9/1/15
 * Time: 6:06 PM
 */

/**
 * @var $project projectModel
 * */

$lat = $projectDetail['projectDetails']['address']['latitude'];
$long = $projectDetail['projectDetails']['address']['longitude'];
$grapic_map = $projectDetail['projectDetails']['address']['grapic_map'];


?>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBobyTTiskP_YfQXcokYGmND1ouViqCX8w&signed_in=true&callback=initProjectMap"
        async defer></script>
<script>

    function ImagetoPrint(source) {
        return "<html>" +
            "" +
            "<script>" +
            "function step1(){setTimeout('step2()', 10);}\n" +
            "function step2(){window.print();window.close()}\n" +
            "</scri" + "pt><body onload='step1()'>\n" +
            "<img src='" + source + "' /></body></html>";
    }
    function PrintImage(source) {
        Pagelink = "<?php echo $projectDetail["projectName"]?>";
        var pwa = window.open(Pagelink, "_new");
        pwa.document.open();
        pwa.document.write(ImagetoPrint(source));
        pwa.document.close();
    }


    function initProjectMap() {
        var projectLocation = {lat: <?php echo $lat?>, lng: <?php echo $long?>};

        var mapCanvas = document.getElementById('project-map-item');
        var map = new google.maps.Map(mapCanvas, {
            zoom: 17,
            center: projectLocation,
            disableDefaultUI: false,
            scrollwheel: true,
            navigationControl: true,
            mapTypeControl: false,
            scaleControl: true,
            draggable: true,
            mapTypeId: google.maps.MapTypeId.ROADMAP

        });
        var marker = new google.maps.Marker({
            position: projectLocation,
            map: map,

        });
    }

    function showMapPicture() {
        jQuery("#project-map-item").hide()
        jQuery("#project-map-item-picture").show()
    }
    function showGmap(){
        jQuery("#project-map-item").show()
        jQuery("#project-map-item-picture").hide()
    }


</script>
<div class="location-container" style="padding-top: 74px;">
    <div class="text-heading">
        <div name="location" class="column small-24 heading" style="background-color: #f2f2f2; text-align: center;">
            <?php echo JText::_("LOCATION") ?>
        </div>
    </div>

    <div class="row map-header-container ">
        <div class="large-24" style="width: 30%;">
            <a class="map-header" title="Direction" target="_blank" href="https://www.google.co.th/maps?saddr=My+Location&daddr=<?php echo "$lat,$long"?>">
                เปิดแผนที่
            </a>
            <!--        <a class="map-header  pull-right show-medium-up" onclick="showGmap()" title="Google Map">-->
            <!--            <i class="fa fa-map"></i>-->
            <!--        </a>-->
        </div>
            <!-- <div class="map-header">
                <a href="javascript:void(0);" class="btn btn-primary explore-map-button">เปิดแผนที่</a>
            </div> -->
        <div class="project-map">
            <div id="project-map-item" class="column small-24 large-24 project-details--map__wrapper">
            </div>
            <div id="project-map-item-picture" class="column small-24 large-24 project-details--map__wrapper" style="display: none">
                <div style="text-align:center;">
                    <div class="item image-container">
                        <img src="<?php echo $grapic_map; ?>" style="width:640px;height:370px;"/>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>



<?php if (isset($grapic_map)) { ?>
    <div style="text-align:center;" class="show-small-only">
        <div class="item image-container">
            <img src="<?php echo $grapic_map; ?>" style="width:640px;height:370px;"/>
        </div>
    </div>
<?php } ?>

