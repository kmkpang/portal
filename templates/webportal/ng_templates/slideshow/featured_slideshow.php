<?php

?>
<div class="show-large-only">
    <div class="container slider">
        <img ng-repeat="slide in slides" class="slide slide-animation nonDraggableImage"
             ng-swipe-right="nextSlide()" ng-swipe-left="prevSlide()"
             ng-hide="!isCurrentSlideIndex($index)" ng-src="{{slide.image}}">

        <a class="arrow prev" href="#" ng-click="nextSlide()"></a>
        <a class="arrow next" href="#" ng-click="prevSlide()"></a>
        <nav class="nav">
            <div class="wrapper">
                <ul class="dots">
                    <li class="dot" ng-repeat="slide in slides">
                        <a href="#" ng-class="{'active':isCurrentSlideIndex($index)}"
                           ng-click="setCurrentSlideIndex($index);">{{slide.description}}</a></li>
                </ul>
            </div>
        </nav>
        <ul class="nav nav-pills">
            <li ng-class="{'active':isFullScreen('isFullScreen')}"><a ng-click="setView('isFullScreen')">FULL SCREEN</a></li>
        </ul>
    </div>
</div>
