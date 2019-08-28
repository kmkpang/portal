<?php
defined('_JEXEC') or die;
//require_once JPATH_BASE . "/images/projects/{$this->projectId}/projectConfig.php";

$projectId = $this->projectId;

$module = JModuleHelper::getModules("properties-insider-project");

$projectInfo = WFactory::getSqlService()->select("SELECT * FROM `jos_portal_projects` where is_deleted=0 AND id = " . $projectId)[0];
if (!$projectInfo) {
    JError::raiseError(404, JText::_("Page Not Found"));
}
$projectAddress = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_ADDRESS)->getAddress($projectInfo['address_id']);
$projectFeatures = WFactory::getSqlService()->select("SELECT jos_portal_project_features.project_id,
    jos_portal_project_feature_list.*
FROM jos_portal_project_features jos_portal_project_features
    INNER JOIN
    jos_portal_project_feature_list
    jos_portal_project_feature_list
        ON (jos_portal_project_features.feature_id =
            jos_portal_project_feature_list.id)
WHERE (jos_portal_project_features.project_id = $projectId)");

$projectImages = WFactory::getSqlService()->select("SELECT * FROM `jos_portal_project_image` where project_id = " . $projectId);

$projectUnits = WFactory::getSqlService()->select("SELECT * FROM `jos_portal_project_unit` where project_id = " . $projectId);

foreach ($projectUnits as &$unit) {
    $unit["images"] = WFactory::getSqlService()->select("SELECT * FROM 
`jos_portal_project_unit_image` where project_id = $projectId AND unit_id = {$unit["id"]}");
}

$x = 2;

function searchImageByTag($images, $tag)
{
    foreach ($images as $i) {
        if ($i['description'] === $tag)
            return $i["server_url"];
    }
}

function searchImagesNotInTag($images, $tags)
{
    $result = [];
    foreach ($images as $i) {
        if (!in_array($i['description'], $tags))
            $result[] = $i["server_url"];
    }
    return $result;
}

function getDataByLanguage($data)
{
    $currentLang = WFactory::getHelper()->getCurrentlySelectedLanguage();
    $data = get_object_vars(json_decode($data));
    if (!WFactory::getHelper()->isNullOrEmptyString($data[$currentLang])) {
        return $data[$currentLang];
    } else {
        foreach ($data as $d)
            if (!WFactory::getHelper()->isNullOrEmptyString($d))
                return $d;
    }

}

function getFacilities($facilitiesArray)
{
    $currentLang = WFactory::getHelper()->getCurrentlySelectedLanguage();
    $data = [];
    $data[] = "<ul class='small-block-grid-4'>";
    foreach ($facilitiesArray as $f) {

        $facility = $currentLang === 'en' ? $f["en_feature"] : $f["feature"];
        $data[] = "<li><i class=\"fa fa-check-circle\" style='color: #88bb41;'></i> $facility  </li> ";
    }
    $data[] = "</ul>";
    return implode("", $data);
}

function getUnitImagesByType($unitImages, $type)
{
    $result = [];
    foreach ($unitImages as $ui) {
        if ($ui["type"] === $type) {
            $result[] = [
                "title" => $ui["description"],
                "link" => $ui["server_url"]
            ];
        }
    }
    return $result;
}

function getUnitTypes($units)
{

    $result = [];
    foreach ($units as $u) {
        $unitName = "{$u["units_name"]} ( {$u["unit_code"]} )";
        $result[$unitName] = [
            "children" => [
                "{$u["unit_code"]}" => ["images" => getUnitImagesByType($u["images"], "GALLERY")]
            ]
        ];
    }

    return $result;

}

function getFloorPlants($units)
{

    $result = [];
    foreach ($units as $u) {
        $unitName = "{$u["units_name"]} ( {$u["unit_code"]} )";
        $result[$unitName] = [
            "children" => [
                "{$u["unit_code"]}" => ["images" => getUnitImagesByType($u["images"], "FLOORPLAN")]
            ]
        ];
    }

    return $result;

}


$x = 1;
$projectDetail = array(
    "projectLogo" => searchImageByTag($projectImages, "logo"),
    "projectName" => getDataByLanguage($projectInfo["name"]),
    "youtubeLink" => null,
    "facebookLink" => null,

    "project_content" => getDataByLanguage($projectInfo["remark"]),
    "project_concept_content" => null,

    "image1" => searchImageByTag($projectImages, "banner"), // banner
    "image2" => searchImageByTag($projectImages, "concept"), // concept
    "image2_2" => null, // concept image 2 , not needed
    "image3" => searchImageByTag($projectImages, "facilities"), // facilities
    "image3_2" => null, // facilities image 2 , not needed
    "image3_3" => null, // facilities image 3 , not needed

    "img_gallery" => searchImagesNotInTag($projectImages, ["logo", "banner", "concept", "facilities"]),

    "image_completion" => array(),
    "projectDetails" => array(
        "address" => array(
            "address" => getDataByLanguage($projectAddress["address"]) . ", {$projectAddress["property_region_town_zip_formatted"]}",
            "latitude" => $projectAddress["latitude"],
            "longitude" => $projectAddress["longitude"],
            "grapic_map" => null
        ),
        "landSize" => $projectInfo["land_size"] . " sqm",
        "facilities" => getFacilities($projectFeatures),
        "nearbyPlaces" => null,

        "project_total_unit" => null,
        "project_property" => null,
        "project_plan_detail" => null,
        "project_security" => null,

//        "completion" => array(
//            "totalCompletion" => $rs->totalcompletion,
//            "completonBreakDown" => array(
//                "Structural Work" => $rs->completion_structural_work,
//                "Architectural Work" => $rs->completion_architectural_work,
//                "System Installation" => $rs->completion_system_installation,
//            ),
//            "lastUpdate" => $rs->completion_lastupdate,
//            "note" => $rs->completion_note
//        ),

        "units" => array(

            "UNIT TYPES" => getUnitTypes($projectUnits),


            "FLOOR PLAN" => getFloorPlants($projectUnits)

        )
    )
);

$x = 3;

?>


<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery(".fullwidth-project-carousel").owlCarousel({
            // items: 3,
            slideSpeed: 500,
            paginationSpeed: 500,
            dots: false,
            loop: true,
            autoplay: true,
            autoplayHoverPause: true,
            responsiveClass: true,
            responsive: {
                0: {
                    items: 1,
                    nav: true
                },
                600: {
                    items: 1,
                    nav: false
                },
            }
            // autoWidth: true,
        });
    })
</script>
<style>
    .fill {
        object-fit: fill;
    }

    .contain {
        object-fit: contain;
    }

    .cover {
        object-fit: cover;
    }

    .scale-down {
        object-fit: scale-down;
    }

    .none {
        object-fit: none;
    }
</style>
<div class="project-detail small-centered" ng-Controller="ProjectCtrl">

    <?php if (!$isOld) { ?>
        <!--  top menu , large only -->
        <?php require_once "topMenu.php" ?>
        <!--  top banner -->

        <?php
        if (!WFactory::getHelper()->isNullOrEmptyString($projectDetail['image1'])) {
            $styleImage3 = "width:100%; height:500px;";
        } else {
            $styleImage4 = "width:100%; height:10px;"; // smalll change
        }
        ?>
        <div>
            <img  class="cover" style="<?php echo $styleImage3 ?>" src="<?php echo $projectDetail["image1"]; ?>" alt=" "/>
        </div>
        <div class="cover">
            <?php echo JText::_(" ") ?>
        </div>

        <!--  concept -->
        <?php if (!WFactory::getHelper()->isNullOrEmptyString($projectDetail['image2_2'])) { ?>
            <div class="row">
                <div name="concept" class="column small-24 heading">
                    <?php echo JText::_("PROJECT_CONCEPT") ?>
                </div>
            </div>
            <?php
            if (!WFactory::getHelper()->isNullOrEmptyString($projectDetail['image2_2'])) {
                $style1 = "column small-24 medium-14 large-14";
                $style2 = "column small-24 medium-10 large-8";
                $styleImage1 = "width:100%;";
            } else {
                $style1 = "column small-24 medium-24 large-24";
                $styleImage1 = "width:100%;height:auto;"; // smalll change
                $style2 = null;
            }

            ?>
            <div class="row" style="margin:0px;max-width:100%;overflow: hidden">
                <div class="<?php echo $style1 ?>" style="padding:0px;">
                    <img style="<?php echo $styleImage1 ?>" src="<?php echo $projectDetail["image2"]; ?>" alt=""/>
                </div>
                <?php if ($style2) { ?>
                    <div class="column small-24 medium-8 large-8" style="padding:0px;">
                        <img style="width:100%;" src="<?php echo $projectDetail["image2_2"]; ?>" alt=""/>
                    </div>
                <?php } ?>
            </div>

            <div>
                <div style="width:100%;margin-top:20px;padding:10px;">
                    <div style="width:80%;margin:0 auto;">
                        <?php echo $projectDetail["project_concept_content"]; ?>
                    </div>
                </div>
            </div>
        <?php } ?>
        <!--  detail -->
        <?php require_once "projectDetail.php" ?>
        <?php if (count(searchImageByTag($projectImages, "facilities")) > 0) { ?>
            <!--  facility images -->
            <div class="row">
                <div name="facility" class="column small-24 heading">
                    <?php echo JText::_("FACILITIES") ?>
                </div>
            </div>
            <?php

            $projectImageCount = 0;
            if (!WFactory::getHelper()->isNullOrEmptyString($projectDetail["image3"]))
                $projectImageCount++;
            if (!WFactory::getHelper()->isNullOrEmptyString($projectDetail["image3_2"]))
                $projectImageCount++;
            if (!WFactory::getHelper()->isNullOrEmptyString($projectDetail["image3_3"]))
                $projectImageCount++;
            $c = 24 / $projectImageCount;
        $projectStyle = "column small-24 medium-$c large-$c"

            ?>
            <div class="row" style="margin:0px;max-width:100%;overflow: hidden">
                <div class="<?php echo $projectStyle ?>" style="padding:0px;">
                    <img style="width:100%;height:auto;" src="<?php echo $projectDetail["image3"]; ?>" alt=""/>
                </div>
                <?php if (!WFactory::getHelper()->isNullOrEmptyString($projectDetail["image3_2"])) { ?>
                    <div class="<?php echo $projectStyle ?>" style="padding:0px;">
                        <img style="width:100%;height:auto;" src="<?php echo $projectDetail["image3_2"]; ?>" alt=""/>
                    </div>
                <?php } ?>
                <?php if (!WFactory::getHelper()->isNullOrEmptyString($projectDetail["image3_3"])) { ?>
                    <div class="<?php echo $projectStyle ?>" style="padding:0px;">
                        <img style="width:100%;height:auto;" src="<?php echo $projectDetail["image3_3"]; ?>" alt=""/>
                    </div>
                <?php } ?>
            </div>

        <?php } ?>

        <!--  matrix cascade stuff -->
        <?php

    require_once "matrix.php"

        ?>

        <?php
        //$bannerFolderName = "otherPictures";
        //require "fullWidthScrollingBanner.php"; ?>
        <div>
            <div style="text-align:center;">
                <div class="item image-container">
                    <!-- FIXME: <img style="width:640px;height:370px;" src="<?php echo $projectDetail["image4"]; ?>" alt=""/> -->
                </div>
            </div>
        </div>
        <!--  construction  -->
        <?php //require "progress.php"  // progress data not supported by saga yet ?>

    <?php } ?>
    <!--  Gellery  -->
    <?php require "gallery.php" ?>

    <?php if (!$isOld) { ?>

        <!-- map -->
        <?php require "map.php" ?>

        <!--  subscribe -->
        <?php // require "register.php" ?>

        <?php // require "social.php" ?>
    <?php } ?>

    <?php require "contact.php" ?>

    <!-- properties -->

    <!-- <div class="properties">
        <div style="padding-top: 74px;">
            <div class="text-heading">
                <a name="units" class="column small-24 heading" style="background-color: #f2f2f2; text-align: center;">
                    Units on project
                </a>
            </div>
            <div class="row small-24 units-project">
                <?php // echo JModuleHelper::renderModule($module[0]); ; ?>
            </div>
        </div>
    </div> -->

</div>
