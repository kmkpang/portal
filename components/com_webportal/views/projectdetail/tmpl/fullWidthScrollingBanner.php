<?php

$doc = JFactory::getDocument();
$root = JURI::root();
// JHtml::script('../assets/webportal_carousel.js'); Doesn't work because my purging script is so awesome

$bannerFolder = JPATH_BASE . "/images/projects/{$this->projectId}/$bannerFolderName";
$imageRoot = JUri::base() . "images/projects/{$this->projectId}/$bannerFolderName/";
$files = array_values(array_diff(scandir($bannerFolder), array('..', '.')));


$contents = array();
foreach ($files as $i => $file) {
    $contents[$imageRoot.$file] = "";
}

?>


<div >
    <div style="text-align:center;">


        <?php
        //class="full-width-scrolling-banner"
        //class="fullwidth-project-carousel owl-carousel owl-theme"
        $index = 1;
        foreach ($contents as $aContent => $link) {
            $pos = strpos($aContent, ".DS_Store");
            if($pos){

            }else{
            echo '<div class="item image-container">
            <img style="width:640px;height:370px;" src="'.$aContent.'" alt=""/>
            </div>';
            }
        }
        $index++;

        ?>


    </div>
</div>

