/**
 * Created by Lian on 6/18/14.
 */


angular.module('webportal')
/**
 * Angular directive so you can do <embed-map lat="xxx" lng="xxx"></embed-map>
 * [Optional] Specify tag ID using attribute "element-id"
 */
    .directive('embedMap', ['portal', 'events', '$http', function (portal, events, $http) {
        return {
            restrict: 'E',
            replace: true,
            template: '<div id="embed_map">Loading Map...</div>',
            link: function (scope, element, attrs) {
                // override id?
                if (attrs.elementId) {
                    element[0].id = attrs.elementId;
                }

                var callback = function () {
                    console.log('running callback');
                    if (typeof google != 'undefined' && google.maps && typeof google.maps.Map != 'undefined') {

                        if (!portal.map) {
                            var map_id = attrs.elementId ? attrs.elementId : 'embed_map';
                            var mapOptions = {zoom: webportalConfiguration.__defaultZoom};
                            var map = new google.maps.Map(document.getElementById(map_id), mapOptions);
                            portal.map = map;
                        }

                        // if details is loaded
                        if (scope.item) {
                            // console.log('item is loaded');
                            // drop property marker

                            if (scope.item && scope.item.latitude && scope.item.longitude) {
                                //console.log('item has latlng');
                                //console.log('lat: ' + scope.item.latitude);
                                //console.log('lng: ' + scope.item.longitude);

                                //If no lat or long values given, default to Bangkok. Might refactor to helper.js instead
                                if (scope.item.latitude == 0 && scope.item.longitude == 0) {
                                    scope.item.latitude = webportalConfiguration.__defaultLat;
                                    scope.item.longitude = webportalConfiguration.__defaultLang;
                                }

                                var latlng = new google.maps.LatLng(scope.item.latitude, scope.item.longitude);

                                var marker = new google.maps.Marker({
                                    position: latlng,
                                    animation: google.maps.Animation.DROP,
                                    title: 'Property',
                                });

                                // addInfoWindow(map, item, marker);

                                marker.setMap(portal.map);
                                portal.map.panTo(latlng);

                                // center map on resize
                                google.maps.event.addDomListener(window, 'resize', function () {
                                    portal.map.setCenter(latlng);
                                });


                                //if (typeof portal.localityData !== 'undefined')
                                //    portal.loadLocalityDataOnMap(portal.localityData);
                                //else
                                //    scope.$watch('portal.localityData', function (newVal, oldVal) {
                                //        console.log("localityData loaded....");
                                //        portal.loadLocalityDataOnMap(newVal)
                                //    }, true);


                            }
                        } else if (attrs.lat && attrs.lng) { // TODO: Refactor because this isn't DRY

                            var latlng = new google.maps.LatLng(attrs.lat, attrs.lng);

                            var marker = new google.maps.Marker({
                                position: latlng,
                                animation: google.maps.Animation.DROP,
                                title: 'Property',

                            });

                            // addInfoWindow(map, item, marker);

                            marker.setMap(portal.map);
                            portal.map.panTo(latlng);

                            // center map on resize
                            google.maps.event.addDomListener(window, 'resize', function () {
                                portal.map.setCenter(latlng);
                            });

                            events.fire('gmap_loaded');

                        } else { // details is not loaded yet
                            console.log('item is not loaded');

                            var center = portal.defaultMapLatLng();

                            if (center) {
                                console.log('panning to ' + center);
                                portal.map.panTo(center);
                            }

                        }
                    } else {
                        console.warn("google maps api isn't loaded");
                    }

                    ////// fix map size
                    //setTimeout(function () {
                    //    console.log("Triggering resize");
                    //    window.dispatchEvent(new Event('resize'));
                    //}, 3000);
                };


                // load and initialize map
                portal.loadGMap(callback);

                // run when details is loaded
                events.on('details_loaded', callback);


            }
        }
    }]);


angular.module('webportal')
/**
 * Angular directive so you can do <properties-map></properties-map>
 * [Optional] Specify tag ID using attribute "element-id"
 */
    .directive('propertiesMap', ['events', '$log', 'portal', function (events, $log, portal) {
        return {
            restrict: 'E',
            replace: true,
            template: '<div id="properties_map">Loading Map...</div>',
            link: function (scope, element, attrs) {
                // override id?
                if (attrs.elementId) {
                    element[0].id = attrs.elementId;
                }

                $log.log('linking directive propertiesMap: loading GMap');

                portal.loadGMap(function () {
                    $log.log('loading map..');
                    if (typeof google != 'undefined' && google.maps) {
                        var latlng = portal.defaultMapLatLng();

                        console.log('Map zoom is at : ' + webportalConfiguration.__defaultZoom);

                        //alert(webportalConfiguration.__defaultZoom);
                        var mapOptions = {
                            center: latlng,
                            zoom: webportalConfiguration.__defaultZoom
                        };

                        var map_id = attrs.elementId ? attrs.elementId : 'properties_map';
                        var map = new google.maps.Map(document.getElementById(map_id), mapOptions);

                        map.setCenter(latlng);

                        portal.map = map;

                        scope.lastNorthEastPoint = null;
                        google.maps.event.addListenerOnce(map, 'idle', function () {
                            if (webportalConfiguration.__enableMarkerAsynchronousLoad !== false) {
                                scope.searchfilter.bounds = map.getBounds().toJSON();
                                scope.lastNorthEastPoint = map.getBounds().getNorthEast();
                            }else{
                                scope.searchfilter.bounds=null;
                            }
                            scope.isRedrawing = false;
                            scope.filter();

                        });
                        if (webportalConfiguration.__enableMarkerAsynchronousLoad !== false) {
                            google.maps.event.addListener(map, 'bounds_changed', function () {

                                scope.searchfilter.bounds = map.getBounds().toJSON();

                                var distance = (google.maps.geometry.spherical.computeDistanceBetween(scope.lastNorthEastPoint, map.getBounds().getNorthEast()) / 1000).toFixed(2);
                                console.log(scope.isRedrawing);
                                if (distance > 5 && !scope.isRedrawing) {
                                    scope.filter();
                                }

                                scope.lastNorthEastPoint = map.getBounds().getNorthEast();


                            });
                        }


                        //-------------------------------------------------------


                    } else {
                        console.warn("google maps api isn't loaded");
                    }
                });
            }
        }
    }
    ])
    /**
     * Angular directive so you can do <pindrop-map></pindrop-map>
     * [Optional] Specify tag ID using attribute "element-id"
     */
    .directive('pindropMap', ['events', '$log', 'portal', function (events, $log, portal) {
        return {
            restrict: 'E',
            replace: true,

            template: '<div id="pindrop_map" style="min-height: 300px">Loading Map...</div>',
            link: function (scope, element, attrs) {
                // override id?
                if (attrs.elementId) {
                    element[0].id = attrs.elementId;
                }

                console.log(attrs);

                $log.log('linking directive pindropMap: loading GMap');

                portal.loadGMap(function () {
                    $log.log('loading map..');
                    if (typeof google != 'undefined' && google.maps) {

                        portal.geocodePosition = function geocodePosition(pos) {
                            geocoder.geocode({
                                latLng: pos
                            }, function (responses) {
                                if (responses && responses.length > 0) {
                                    portal.updateMarkerAddress(responses[0].formatted_address);
                                } else {
                                    var warning = 'Cannot determine address at this location.';
                                    console.warn(warning)
                                    portal.updateMarkerAddress(warning);
                                }
                            });
                        };


                        portal.updateMarkerPosition = function updateMarkerPosition(latLng) {
                            portal.pindropmap_marker = [
                                latLng.lat(),
                                latLng.lng()
                            ].join(', ');

                            jQuery("input[name=latitude]").val(latLng.lat());
                            jQuery("input[name=longitude]").val(latLng.lng());


                        };

                        portal.setPindropMapCenter = function (latLng) {
                            map.setCenter(latLng);
                        };


                        portal.updateMarkerAddress = function updateMarkerAddress(str) {
                            portal.pindropmap_marker_nearest_address = str;
                            //$scope.searchfilter.address=portal.pindropmap_marker_nearest_address;
                            console.log('Current marker address --> ' + portal.pindropmap_marker_nearest_address);

                            if (typeof portal.$scope !== 'undefined') {

                                var latLng = portal.pindropmap_marker.split(',');

                                portal.$scope.$broadcast(
                                    'pindropMapPoistionUpdated',
                                    {
                                        lat: latLng[0].trim(),
                                        lng: latLng[1].trim()
                                    });
                            }
                        };


                        var latlng = (attrs.lat && attrs.lng) ? portal.makeMapLatLng(attrs.lat, attrs.lng) : portal.defaultMapLatLng();
                        console.log(latlng);
                        var mapOptions = {
                            center: latlng,
                            zoom: webportalConfiguration.__defaultZoom,
                            mapTypeId: google.maps.MapTypeId.ROADMAP
                        };

                        var map_id = attrs.elementId ? attrs.elementId : 'pindrop_map';
                        var map = new google.maps.Map(document.getElementById(map_id), mapOptions);
                        var geocoder = new google.maps.Geocoder();


                        map.setCenter(latlng);

                        portal.pindrop_map = map;

                        var marker = new google.maps.Marker({
                            position: latlng,
                            title: attrs.title ? attrs.title : 'Property',
                            map: map,
                            draggable: true
                        });

                        portal.pindrop_marker = marker;


                        portal.updateMarkerPosition(latlng);
                        portal.geocodePosition(latlng);

                        // Add dragging event listeners.
                        google.maps.event.addListener(marker, 'dragstart', function () {

                        });

                        google.maps.event.addListener(marker, 'drag', function () {

                            portal.updateMarkerPosition(marker.getPosition());
                        });

                        google.maps.event.addListener(marker, 'dragend', function () {
                            portal.geocodePosition(marker.getPosition());
                        });


                        if (typeof scope.currentProperty !== 'undefined' && typeof scope.currentProperty.latitude !== 'undefined' && scope.currentProperty.latitude !== null) {
                            var scopeLatLng = portal.makeMapLatLng(scope.currentProperty.latitude, scope.currentProperty.longitude);

                            map.setCenter(scopeLatLng);
                            portal.pindrop_marker.setPosition(scopeLatLng);
                        }


                    } else {
                        console.warn("google maps api isn't loaded");
                    }
                });
            }
        }
    }
    ]);
