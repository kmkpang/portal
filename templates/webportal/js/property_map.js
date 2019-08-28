/**
 * Created by Lian on 6/18/14.
 */

angular.module('webportal')
    .controller('MapCtrl', ['portal', '$http', '$scope', 'events', '$log', function (portal, $http, $scope, events, $log) {

        jQuery(document).ready(function () {
            $scope.getInfoWindowPopupContents();
        });


        function hideAllOpenWindows() {
            for (var i = 0; i < $scope.open_infowindows.length; i++) {
                $scope.open_infowindows[i].close();
            }
        }

        function buildInfoContent(item) {


            var link = item.url_to_direct_page;
            var title = item.title;
            var address = item.region_name + ', ' + item.city_town_name + ', ' + item.address;
            var image = item.list_page_thumb_path;
            var type = item.category_name;
            var bedrooms = item.number_of_bedrooms;
            var bathrooms = item.number_of_bathrooms;
            var area = item.total_area;
            var price = item.current_listing_price_formatted;
            var salerent = item.mode

            // $log.debug("Getting content");
            var content = null;
            content = $scope.getInfoWindowPopupContents();

            //$log.debug("Content already loaded..because: " + $scope.infoWindowRecived);


            content = content.clone();

            content.find("#gmap-title").text(title);
            content.find("#gmap-address").text(address);
            content.find("#gmap-type").text(type);
            content.find("#gmap-bedrooms").text(bedrooms);
            content.find("#gmap-bathrooms").text(bathrooms);
            content.find("#gmap-size").text(area);
            content.find("#gmap-price").text(price);

            content.find('#gmap-image').attr('src', image);
            content.find('#gmap-property-link').attr('href', link);

            return content.html();
        }

        function addInfoWindow(map, content, marker) {

            var infowindow = new google.maps.InfoWindow({
                content: content
            });

            marker.infow = infowindow;

            google.maps.event.addListener(marker, 'click', function () {
                // close open windows
                hideAllOpenWindows();

                $scope.open_infowindows = [];

                // open the current window
                infowindow.open(map, marker);

                // add to list of open windows
                $scope.open_infowindows.push(infowindow);
            });
        }

        $scope.showlist = false;
        $scope.markers = [];
        $scope.markers_map = {};
        $scope.open_infowindows = [];
        $scope.popup_windowContent = null;
        $scope.infoWindowRecived = false;
        $scope.mapstatus = '';

        function showOnMap(map, items) {
            if (typeof(map) == 'undefined' || map == null ||
                typeof(google) == 'undefined' || google == null ||
                typeof(google.maps) == 'undefined' || google.maps == null) {
                $log.warn('map is not initialized when trying to show properties');


                setTimeout(function () {
                    showOnMap(portal.map, $scope.items);
                }, 1000);
                //return false;
            }
            else
                $log.log("map loaded,continuing...");

            console.log(map.getBounds().getNorthEast());

            $log.log('showing ' + items.length + ' items on map');

            // clear markers
            $log.log('clearing markers');
            for (var i = 0; $scope.markers && i < $scope.markers.length; i++) {
                $scope.markers[i].setMap(null);
            }
            $scope.markers = [];
            $scope.markers_map = {};


            // close windows
            hideAllOpenWindows();

            if (webportalConfiguration.__enableMarkerClustering !== false) {
                $scope.markerCluster = new MarkerClusterer(map, [],
                    {
                        imagePath: documentRootRaw + 'templates/webportal/images/m',
                        maxZoom: 16
                    });
            }

            $scope.isRedrawing = true;
            asyncLoopProperties(items, map);
            // clicking on map will hide all open info windows

        }

        function asyncLoopProperties(items, map) {
            (function loop(i) {
                //--------------------------
                // var items = arr;
                var item = items[i];
                if (typeof item.latitude != 'undefined' && typeof item.longitude != undefined) {
                    var latlng = new google.maps.LatLng(item.latitude, item.longitude);
                    var marker = null;

                    var key = item.latitude + ':' + item.longitude;
                    var old_marker = $scope.markers_map[key];


                    var icon = new google.maps.MarkerImage(
                        documentRootRaw + 'templates/webportal/images/map_point.png', //url
                        new google.maps.Size(59, 76)//size
                        //new google.maps.Point(0, 0), //origin
                        //new google.maps.Point(anchor_left, anchor_top) //anchor
                    );

                    if (old_marker) {
                        // create a new grouped marker
                        marker = new google.maps.Marker({
                            position: latlng,
                            title: 'Grouped',

                            icon: documentRootRaw + 'templates/webportal/images/map_point.png', //url
                        });

                        var old_content = old_marker.infow.content;

                        addInfoWindow(map, old_content + '<hr class="divider"/>' + buildInfoContent(item), marker);

                        // remove old marker
                        old_marker.setMap(null);

                    } else {
                        marker = new google.maps.Marker({
                            position: latlng,
                            title: 'Property',

                            icon: documentRootRaw + 'templates/webportal/images/map_point.png', //url
                        });

                        // info window
                        addInfoWindow(map, buildInfoContent(item), marker);
                    }

                    $scope.markers.push(marker);
                    $scope.markers_map[item.latitude + ':' + item.longitude] = marker;

                    // add marker
                    //  console.log("Setting marker map for i : " + i);
                    // marker.setMap(map);

                    // keep a reference to marker
                    item.marker = marker;


                    var redraw = (i % 100 == 0);
                    var sleepTime = (i % 5 == 0) ? 1 : 0;
                    if (parseFloat(item.latitude) == 0 && parseFloat(item.longitude)) {

                    } else
                    {
                        if (webportalConfiguration.__enableMarkerClustering !== false) {
                            $scope.markerCluster.addMarker(marker, redraw);
                        }else{
                            marker.setMap(map);
                        }
                    }

                    if (redraw) {
                        var text = "Load status : " + (i / items.length) * 100 + " % ( " + i + " of " + items.length + " ) ";
                        console.log(text);
                        $scope.mapstatus = text;
                        //   $scope.$apply();
                    }
                    //console.log("Current i : " + i + " , total size : " + items.length);

                }
                i++;
                if (i < items.length) {                      //the condition
                    setTimeout(function () {
                        loop(i)
                    }, sleepTime); //rerun when condition is true
                } else {
                    if (webportalConfiguration.__enableMarkerClustering !== false) {
                        $scope.markerCluster.redraw();
                    }
                    $scope.isRedrawing = false;
                    // console.log("Adding Marker cluster for " + $scope.markers.length);
                    // $scope.markerCluster = new MarkerClusterer(map, $scope.markers, {imagePath: documentRootRaw + 'templates/webportal/images/m'});
                    // hideAllOpenWindows();                      //callback when the loop ends
                }
            }(0));                                         //start with 0
        }


        events.on('filter_done', function () {
            showOnMap(portal.map, $scope.items);
        });

        $scope.focusMap = function (lat, lng) {
            if (portal.map) {
                var loc = new google.maps.LatLng(lat, lng);
                portal.map.panTo(loc)
            }
        };

        // showlist changes the size of map. therefore, let that be known.
        $scope.$watch('showlist', function () {
            if (typeof(google) != 'undefined' && typeof(google.maps) != 'undefined') {
                setTimeout(function () {
                    window.dispatchEvent(new Event('resize'));
                }, 100);
            }
        });


        $scope.getInfoWindowPopupContents = function () {

            //  $log.debug("Attempting getInfoWindowPopupContents ");
            if ($scope.popup_windowContent === null) {


                jQuery.ajax({
                    type: 'GET',
                    url: portal.getPropertyMap(),
                    async: false
                }).done(function (data) {
                    $scope.infoWindowRecived = true;
                    //  $log.debug("Got getInfoWindowPopupContents");
                    $scope.popup_windowContent = jQuery(data);
                })
                    .fail(function () {
                        $scope.infoWindowRecived = false;
                        $log.warn('Error getting map popup template');
                    });
            }
            else {
                //$log.debug("Already had! getInfoWindowPopupContents");
            }

            return $scope.popup_windowContent;
        };

    }]);
