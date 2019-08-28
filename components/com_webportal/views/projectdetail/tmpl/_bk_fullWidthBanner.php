<?php

$doc = JFactory::getDocument();
$root = JURI::root();
// JHtml::script('../assets/webportal_carousel.js'); Doesn't work because my purging script is so awesome

$bannerFolder = JPATH_BASE . "/images/projects/{$this->projectId}/topBanner";
$imageRoot = JUri::base() . "images/projects/{$this->projectId}/topBanner/";
$files = array_values(array_diff(scandir($bannerFolder), array('..', '.')));
$youtubeLink = $projectDetail['youtubeLink'];


if (count($files) === 0) {
    $files = array($youtubeLink);
} else {
    $result = [$imageRoot . $files[0]];
    $result[] = $youtubeLink;
    for ($i = 2; $i < count($files); $i++) {
        $result = [$imageRoot . $files[$i]];
    }
    $files = $result;
}


$contents = array();
foreach ($files as $i => $file) {
    $contents[$file] = "";
}


$doc->addScriptDeclaration("carouselVids=[];")
?>


<div class="full-width-top-banner">
    <div class="frontpage__carousel owl-carousel owl-theme">


        <?php
        $index = 1;
        foreach ($contents as $aContent => $link) {

            $videoId = WFactory::getHelper()->parseYoutubeUrl($aContent);
            if ($videoId !== null) {//youtube
                $doc->addScriptDeclaration("
                
                carouselVids.push(
                
                 {
                 vid:'player$index', 
                 data:{
                    playerVars: {'rel': 0, 'autoplay': 1, 'controls': 0, 'autohide': 1, 'showinfo': 0, 'wmode': 'opaque'},
                    videoId: '$videoId',
                    height:'$height',
                    suggestedQuality: 'hd720',
                    events: {
                        'onReady': onPlayerReady
                    }
                }}
                
                )
               
                ")
                ?>
                <div class="item wrap-video">
                    <div class="video fluid-width-video-wrapper" style="padding-top: 56.25%;">
                        <div id="player<?php echo $index ?>"></div>
                        <i class="fa fa-volume-off"></i>
                        <i class="fa fa-volume-up"></i>
                    </div>
                </div>
                <?php
            } else {
                if (WFactory::getHelper()->isNullOrEmptyString($link)) {
                    ?>
                    <div class="item"><img src="<?php echo $aContent ?>" style="width:100%;" alt=""/></div>
                    <?php
                } else {
                    ?>
                    <div class="item image-container">
                        <a href="<?php echo $link ?>">
                            <img src="<?php echo $aContent ?>" alt=""/>
                        </a>
                    </div>
                    <?php
                }
            }
            $index++;
        }
        ?>


    </div>
</div>

<div class="full-width-top-banner">
    <?php 
    foreach ($contents as $aContent => $link) {
        echo '<div class="item"><img src="'.$aContent.'" style="width:100%;" alt=""/></div>';
    }
    ?>
</div>

