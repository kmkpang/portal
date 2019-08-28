<?php
/**
 * @var $property PropertyListModel
 * @var $image PortalPortalPropertyImagesSql
 * */
$images = $property->images;
$image = $images[0];

?>

<div class="slides-container--slides__slide">

    <img u="image" src="<?php echo $image->__server_url ?>"/>

    <div u="caption" class="slides-container--slides__caption">

        <a href="<?php echo $property->url_to_direct_page ?>">
            <span class="title">
                <?php echo $property->title ?>
            </span>

            <span class="address">
                 <?php //echo " , {$property->region_name} , {$property->city_town_name}" ?>
            </span>
            <br>
            <span class="short-description">
                 <?php echo ucfirst($property->category_name) . " for " . $property->buy_rent . ", " . $property->current_listing_price . " ฿" ?>
            </span>
        </a>

    </div>
</div>