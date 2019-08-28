/**
 * Created by Lian on 6/16/14.
 */
angular.module('webportal')
    .controller('PropertyCtrl', ['$scope', '$http', '$log', '$sce', '$window', 'ngDialog', 'portal', 'events', 'localStorageService',
        function ($scope, $http, $log, $sce, $window, ngDialog, portal, events, localStorageService) {
            $scope.loadDetails = function (propertyid) {
                var url = portal.getApiPropertyDetails(propertyid);


                $scope.itemloading = true;
                $scope.propertesRoute = $window.propertesRoute;
                $scope.sent = false;
                $scope.streetviewAvailable = true;

                $scope.openMorePicturesPopup = function (url) {
                    window.open(url);
                };


                $scope.localityDataValues = [];
                $scope.localityDataKeys = [];

                $scope.currentlySelectedLocalityType = "ALL";
                $scope.currentySelectedLocality = function (item) {
                    if ($scope.currentlySelectedLocalityType == "ALL")
                        return true;
                    return item.type == $scope.currentlySelectedLocalityType;
                };


                $scope.drawDirectionBetweenMarkers = function (object) {
                    var scope = $scope;
                    portal.drawDirection(propertyDetails.latitude, propertyDetails.longitude,
                        object.latitude, object.longitude,
                        object,
                        function (item, time) {
                            scope.localityDataValues[item.index].travelTime = time;
                            scope.$apply();
                            //item.travelTime = time;
                            console.log('got time : ' + time);
                            //callback();
                        });
                };

                var lastLocalityItem = "";
                var lastClassName = "locality--item-background-1";

                $scope.getOtherClassName = function (currentClassName) {
                    if (currentClassName == 'locality--item-background-1')
                        return "locality--item-background-2";
                    return 'locality--item-background-1';
                };

                $scope.getLocalityClassName = function (name) {
                    var same = false;
                    var className = "";
                    if (lastLocalityItem == name)
                        same = true;

                    if (same) {
                        className = lastClassName;
                    }
                    else {
                        className = $scope.getOtherClassName(lastClassName);
                    }

                    lastLocalityItem = name;
                    lastClassName = className;

                    //  console.log("returning : " + name + " -> " + className);

                    return className;
                };

                var localityUrl = portal.getApiLocality(propertyid);
                $http({method: 'GET', url: localityUrl, dataType: "json",})
                    .success(function (data) {
                        // console.log(data);
                        portal.localityData = data;
                        $scope.localityDataValues = [];

                        for (var type in data) {
                            var array = data[type];

                            $scope.localityDataKeys.push(type);
                            $scope.localityDataValues = $scope.localityDataValues.concat(array);
                        }

                        for (var i = 0; i < $scope.localityDataValues.length; i++) {
                            $scope.localityDataValues[i].travelTime = '';
                            $scope.localityDataValues[i].index = i;
                        }
                        //  console.log($scope.localityDataValues);

                        if (typeof google != 'undefined' && google.maps && typeof google.maps.Map != 'undefined') {
                            console.log('Gmap Already loaded..getting locality data');
                            portal.loadLocalityDataOnMap($scope.localityDataValues);
                        } else {
                            var localityDataValues = $scope.localityDataValues;
                            console.log('Gmap NOT loaded..waiting for it to fire');
                            events.on('gmap_loaded', portal.loadLocalityDataOnMap(localityDataValues));
                        }


                    });


                $scope.localityTypeUpdated = function () {
                    portal.filterLocalityMarkers($scope.currentlySelectedLocalityType);
                };


                $scope.showSendMailForm = function (data) {
                    //prettyPrint();

                    // var xmlData = jQuery('#XMLHolder').html();
                    ngDialog.open({
                        template: portal.getApiLang() + '&file=templates/webportal/ng_templates/property/send_mail_to_friend.php',
                        controller: 'ContactCtrl',
                        //disableAnimation: true,
                    });

                };

                $scope.showVideo = function (data) {
                    ngDialog.open({
                        template: portal.getApiLang() + '&file=templates/webportal/ng_templates/property/video.php',
                        className: 'ngdialog-theme-video',
                        controller: 'VideoCtrl',
                        data: {name:data},
                    });
                };
                
                $scope.resizeMap = function () {
                    //if (!$scope.mapResized) {
                        setTimeout(function () {
                            console.log("Triggering resize");
                            window.dispatchEvent(new Event('resize'));

                        }, 4000);
                        $scope.mapResized = true;
                    //}
                };

                $scope.showPanorama = function () {
                    //console.log.show
                    if (!$scope.panorama) {


                        var streetViewService = new google.maps.StreetViewService();
                        var STREETVIEW_MAX_DISTANCE = 100;
                        var latLng = new google.maps.LatLng($scope.item.latitude, $scope.item.longitude);
                        streetViewService.getPanoramaByLocation(latLng, STREETVIEW_MAX_DISTANCE, function (streetViewPanoramaData, status) {
                            if (status === google.maps.StreetViewStatus.OK) {
                                // ok
                                $scope.streetviewAvailable = true;
                                var myPlace = new google.maps.LatLng($scope.item.latitude, $scope.item.longitude);

                                var map = new google.maps.Map(document.getElementById('property_map_hidden'), {
                                    center: myPlace,
                                    zoom: 18
                                });

                                $scope.panorama = new google.maps.StreetViewPanorama(document.getElementById('pano'), {
                                    position: myPlace
                                });

                                var marker = new google.maps.Marker({
                                    position: myPlace,
                                    map: map
                                });

                                map.setStreetView($scope.panorama);

                                var sv = streetViewService; //new google.maps.StreetViewService();

                                sv.getPanorama({
                                    location: myPlace,
                                    radius: 50
                                }, $scope.computePanoramaHeading);


                            } else {
                                $scope.streetviewAvailable = false;
                                // no street view available in this range, or some error occurred
                            }
                        });


                    }
                };

                $scope.computePanoramaHeading = function (data, status) {
                    //console.log("--->>>" + status);
                    if (status === google.maps.StreetViewStatus.OK) {
                        var myPlace = new google.maps.LatLng($scope.item.latitude, $scope.item.longitude);
                        var marker_pano = new google.maps.Marker({
                            position: myPlace,
                            map: $scope.panorama
                        });

                        var heading = google.maps.geometry.spherical.computeHeading(data.location.latLng, marker_pano.getPosition());

                        $scope.panorama.setPov({
                            heading: heading,
                            pitch: -15
                        });

                        //google map sends me WRONG data time to time, even though street view is NOT available,it will
                        //still send me OK, so after doing all the things, wait 2 seconds and see if it succeed, if not,
                        //show panorama not available !
                        //var scope=$scope;
                        //setTimeout(function () {
                        //    if (jQuery('#pano').children(':visible').length == 0) {
                        //        console.log("Google hiccup ! no panorama available..abort abort!!")
                        //        scope.streetviewAvailable = true;
                        //    }
                        //    else
                        //        console.log("OK..! google isnt a complete screw-up !")
                        //
                        //}, 2);
                    }
                    else {
                        $scope.streetviewAvailable = false;

                        jQuery("#pano").remove();

                        $scope.$apply();

                    }
                };

                // console.log(localStorageService.get('searchHash') + " <<--------");


                //TODO: refractor THIS ! property details is already loaded in the php code.  Only purpose of this following piece of code is to show the picture slider!
                $http({
                    method: 'POST',
                    data: {'property_id': '' + propertyid, returnType: 'RETURN_TYPE_DETAIL'},
                    url: url
                })
                    .success(function (data) {
                        if (data instanceof Array && data.length == 1) {
                            $scope.item = data[0];
                        } else {
                            $scope.item = data;
                            portal.propertyDetail = data;
                        }
                        $scope.item.description_text = $sce.trustAsHtml($scope.item.description_text);

                        events.fire('details_loaded');

                        var nag = function () {
                            if (jQuery('#slider').find('img[src]').length == 0 || jQuery('#slider').find('img[src]').length != jQuery('#carousel').find('img[src]').length) {
                                // console.log(jQuery('#slider').find('img[src]').length);
                                //  console.log('no img');
                                setTimeout(nag, 100);
                                return;
                            } else {
                                console.log(jQuery('#slider').find('img[src]').length);
                            }

                            // do this first cuz slider needs this to be initialized.
                            jQuery('#carousel').flexslider({
                                animation: 'slide',
                                controlNav: false,
                                animationLoop: false,
                                slideShow: false,
                                itemWidth: 102,
                                itemMargin: 5,
                                asNavFor: '#slider'
                            });

                            // then the slider
                            jQuery('#slider').flexslider({
                                animation: 'fade',
                                controlNav: false,
                                animationLoop: false,
                                slideShow: false,
                                sync: '#carousel'
                            });

                        };
                        nag();


                    })
                    .error(function () {
                        alert("Error loading data.");


                    })
                    .then(function (data) {
                        $scope.itemloading = false;
                    });
            };

        }
    ])
;

app.filter('portalLocalityDistance', function () {
    return function (input) {
        return parseFloat(input).toFixed(1) * 1000 + ' m ';
    }

});
angular.module('webportal')


    .directive('nextPreviousProperties', ['events', '$log', 'portal', 'localStorageService', function (events, $log, portal, localStorageService) {
        return {
            restrict: 'E',
            replace: true,

            link: function (scope, elem, attr) {
                scope.getNexPreviousPropertiesUrl = function () {
                    return portal.getApiLang() + '&file=templates/webportal/ng_templates/property/next_prev_properties.php&propertyid=' + attr.propertyid + '&hash=' + localStorageService.get('searchHash');
                }
            },
            template: '<div ng-include="getNexPreviousPropertiesUrl()"></div>'
        }
    }]);
