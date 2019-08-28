<?php
/**
 * Created by PhpStorm.
 * User: khan
 * Date: 8/4/15
 * Time: 3:11 PM
 */

?>

<div class="locality-panel--filter-wrapper">
    <input type="checkbox" class="locality_searchckbox" name="locality_searchckbox"
           id="locality_searchckbox"/>

    <label class="locality_searchckbox--toggle"
           for="locality_searchckbox"><i class="fa fa-location-arrow"></i></label>

    <div class="locality-panel--filter">

        <div class="input-textbox--wrapper">

            <!--            <select name="select-locality-type-filter"-->
            <!--                    ng-model="currentlySelectedLocalityType"-->
            <!--                    ng-options="localityType as localityType for localityType in localityDataKeys"-->
            <!--                    ng-change="localityTypeUpdated()">-->
            <!--            </select>-->


            <select ng-model="currentlySelectedLocalityType" ng-change="localityTypeUpdated()">
                <option value="ALL"><?php echo JText::_("SHOW ALL") ?></option>
                <option value="BUSSTAND"><?php echo JText::_("BUSSTAND") ?></option>
                <option value="SCHOOL"><?php echo JText::_("SCHOOL") ?></option>
                <option value="KINDERGARTEN"><?php echo JText::_("KINDERGARTEN") ?></option>
                <option value="SPORTSAREA"><?php echo JText::_("SPORTSAREA") ?></option>
                <option value="SKATEBOARDING"><?php echo JText::_("SKATEBOARDING") ?></option>
                <option value="SKILIFT"><?php echo JText::_("SKILIFT") ?></option>
<!--                <option value="AREAOFINTEREST">--><?php //echo JText::_("AREAOFINTEREST") ?><!--</option>-->
            </select>


        </div>

        <div class="locality--item locality--item-background-1"
             ng-repeat="type in localityDataValues | filter:currentySelectedLocality"

             ng-click="drawDirectionBetweenMarkers(type)">
            <div class="row">
                <div class="column large-6 small-6 locality--item-icon">

                    <i class="fa fa-bus" ng-if="type.type=='BUSSTAND'"></i>
                    <i class="fa fa-graduation-cap" ng-if="type.type=='SCHOOL'"></i>
                    <i class="fa fa-child" ng-if="type.type=='KINDERGARTEN'"></i>
                    <i class="fa fa-futbol-o" ng-if="type.type=='SPORTSAREA'"></i>
                    <i class="fa fa-square" ng-if="type.type=='SKATEBOARDING'"></i>
                    <i class="fa fa-tree" ng-if="type.type=='SKILIFT'"></i>
<!--                    <i class="fa fa-question-circle" ng-if="type.type=='AREAOFINTEREST'"></i>-->
                </div>
                <div class="column large-18 small-18 left locality--item-description">
                    <h3>{{type.name}}</h3>

                    <p>{{type.distance | portalLocalityDistance}}
                        <b ng-show="type.travelTime">
                            , {{type.travelTime}}
                        </b>
                    </p>
                </div>

            </div>


        </div>
    </div>
</div>