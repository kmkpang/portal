<?php
/**
 * Created by PhpStorm.
 * User: khan
 * Date: 1/11/15
 * Time: 3:30 PM
 */
/**
 * @var $property PropertyListModel
 * @var $image PortalPortalPropertyImagesSql
 * */
$image = $property->list_page_thumb_path;
?>

<div class="webportalproperties-container--property">

    <?php if ($property !== null) { ?>
        <a href="<?php echo $property->url_to_direct_page ?>">

            <div class="non-popup">

                <div class="row">
                    <img class="webportalproperties-container--property__img" src="<?php echo $image ?>">
                </div>
                <div class="categroy-name row">
                    <?php echo $property->category_name ?>
                </div>
                <div class="address row">
                    <?php if(getParam(propertyTitle) == 'true') {
                        echo "{$property->title}";
                    } else {
                        echo "{$property->region_name} , {$property->city_town_name}";
                    } ?>
                </div>

            </div>

            <div class="popup">
                <div class="short-description">
                    <?php echo $property->buy_rent . ", " . $property->current_listing_price_formatted ?>
                </div>
            </div>

        </a>

    <?php } ?>
</div>