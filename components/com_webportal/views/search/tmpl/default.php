<?php
/**
 * Created by PhpStorm.
 * User: Lian
 * Date: 6/19/14
 * Time: 2:40 PM
 */

defined('_JEXEC') or die('Restricted access');

$doc = JFactory::getDocument();
$doc->addScript(JUri::base() . 'templates/webportal/js/property_list.js');
?>

<style type="text/css">


    slider {
        display:     block;
        position:    relative;
        height:      30px;
        width:       500px;
    }

    slider span.bar {
        height:     50%;
        z-index:    0;
        background: #eee;
        left:       0;
        top:        25%;
        cursor:     pointer;
    }

    slider span.bar.selection, slider span.bar.unselected {
    }

    slider span.bar.selection {
        background: #0a0;
        opacity:    0.5;
    }

    slider span.bar.unselected {
        width:      auto;
        background: #a00;
        opacity:    0.5;
    }

    slider span.pointer {
        cursor:           pointer;
        width:            15px;
        top:              0;
        bottom:           0;
        background-color: #00a;
    }

    slider span.pointer:hover {
    }

    slider span.pointer.active {
    }

    slider span.bubble {
        font-size:   1em;
        line-height: 2em;
        font-family: sans-serif;
        text-align:  center;
        text-shadow: none;
        top:         -1.3em;
        cursor:      pointer;
    }

    slider span.bubble.selection, slider span.bubble.limit {
        top: 25%;
    }

    slider span.bubble.selection {
    }

    slider span.bubble.limit {
    }

    slider span.bubble.low, slider span.bubble.high {
    }

    ::-ms-tooltip {
        display: none;
    }

    #slider-container {
        -webkit-touch-callout: none;
        -webkit-user-select:   none;
        -khtml-user-select:    none;
        -moz-user-select:      -moz-none;
        -ms-user-select:       none;
        user-select:           none;
    }



    .pager ul li, .pager .pager-prev, .pager .pager-next {
        list-style-type: none;
        display: inline;
        padding: 2px;
    }

    .pager ul {
        word-wrap: break-word;
    }
</style>
<div id="search" ng-controller="SearchCtrl">
    <search-filters-frontpage></search-filters-frontpage>
    <property-list></property-list>
</div>