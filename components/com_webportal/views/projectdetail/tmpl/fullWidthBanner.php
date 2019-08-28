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

?>
<div >
    <?php 
    foreach ($contents as $aContent => $link) {
        echo '<div ><img src="'.$aContent.'" style="width:100%;" alt=""/></div>';
    }
    ?>
</div>

