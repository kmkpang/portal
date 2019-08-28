<?php
/**
 * Created by PhpStorm.
 * User: khan
 * Date: 9/1/15
 * Time: 6:06 PM
 */

/**
 * @var $office OfficeModel
 * */

$lat = floatval($office->address->latitude);
$long = floatval($office->address->longitude);




?>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD99Q2tf-txKdcxWQZrqbEZ9JrtFNWoULg&signed_in=true&callback=initOfficeMapFrontPage" async defer></script>
<script>

    function initOfficeMapFrontPage() {
        var officeLocation = {lat: <?php echo $lat?>, lng: <?php echo $long?>};
        if (officeLocation.lat == 0 || officeLocation.lng == 0) {
            officeLocation.lat = webportalConfiguration.__defaultLat;
            officeLocation.lng = webportalConfiguration.__defaultLang;
        }
        var mapCanvas = document.getElementById('front-page-office-map');
        var map = new google.maps.Map(mapCanvas, {
            zoom: 14,
            center: officeLocation,
            disableDefaultUI: true,
            scrollwheel: false,
            navigationControl: false,
            mapTypeControl: false,
            scaleControl: false,
            draggable: false,
            mapTypeId: google.maps.MapTypeId.ROADMAP

        });
        var marker = new google.maps.Marker({
            position: officeLocation,
            map: map,

        });
    }


</script>

<div id="front-page-office-map" class="column small-24 large-24 office-details-frontpage--map__wrapper">


</div>
