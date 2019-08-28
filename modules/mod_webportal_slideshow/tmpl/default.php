<?php

$doc = JFactory::getDocument();

$lang = WFactory::getHelper()->getCurrentlySelectedLanguage();

if ($lang === 'en')
    $newsCat = 16;
else if ($lang === 'th')
    $newsCat = 17;
else
    $newsCat = 16;//default

$articles = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_ARTICLES)->getArticles($newsCat, 5);

$properties = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTIES)->getNewestProperties(15, RETURN_TYPE_DETAIL);

?>



<div id="slides-container" class="slides-container">

    <!-- Slides Container -->
    <div u="slides" class="slides-container--slides">

        <?php

        foreach ($properties as $property) {
            require "property_slide.php";
        }

        ?>


    </div>


    <!-- Bullet Navigator Skin Begin -->
    <!-- bullet navigator container -->
    <div u="navigator" class="jssorb16" style="position: absolute; bottom: 16px; right: 6px;">
        <!-- bullet navigator item prototype -->
        <div u="prototype" style="POSITION: absolute; WIDTH: 21px; HEIGHT: 21px;"></div>
    </div>
    <!-- Bullet Navigator Skin End -->


    <!-- Arrow Navigator Skin Begin -->
    <!-- Arrow Left -->
        <span u="arrowleft" class="jssora21l" style="width: 55px; height: 55px; top: 123px; left: 8px;">
        </span>
    <!-- Arrow Right -->
        <span u="arrowright" class="jssora21r" style="width: 55px; height: 55px; top: 123px; right: 8px">
        </span>
    <!-- Arrow Navigator Skin End -->


</div>

