/**
 * Created by khan on 10/10/15.
 */
angular.module('webportal')
    .controller('PropertiesAdminCtrl', ['$scope', '$http', '$log', '$sce', '$window', 'ngDialog', 'portal', 'events', 'localStorageService',
        function ($scope, $http, $log, $sce, $window, ngDialog, portal, events, localStorageService) { //dont think i need all these though !

            portal.setScope($scope);
            $scope.property = {};//set from php
            $scope.agent = {};//set from php
            $scope.office = {};//set from php
            $scope.postal_code_tree = {};

            $scope.property.new_image = false;
            $scope.processing_msg = '';

            $scope.initProperty = function () {
                $scope.property = window.property;

                //console.log($scope.property.address);

                var regions = [];
                var towns = [];
                var postals = [];
                var data = window.postal_code_tree;
                for (var i = 0; i < data.length; i++) {
                    var region = data[i];
                    regions.push({id: region.id, name: region.name});
                    for (var j = 0; j < region.towns.length; j++) {
                        var town = region.towns[j];
                        towns.push({id: town.id, name: town.name, parent_id: region.id});
                        for (var k = 0; k < town.postal_codes.length; k++) {
                            var postal = town.postal_codes[k];
                            postals.push({id: postal.id, name: postal.name, parent_id: town.id});
                        }
                    }
                }

                $scope.postal_code_tree = {
                    regions: regions,
                    towns: towns,
                    postals: postals
                };


            };

            $scope.togglePublish = function (publish) {

                var uploadUrl = portal.getpropertyPublishToggleLink();
                uploadUrl += "&property-id=" + $scope.property.id;

                if (publish)
                    uploadUrl += "&publish=1";
                else
                    uploadUrl += "&publish=0";

                $scope.processing_msg = 'Setting publish state to : ' + publish;

                $http.get(uploadUrl, {}).success(function (data) {

                    if (data['success']) {
                        $scope.property.show_on_web = data['message'];
                        $scope.processing_msg = 'Setting publish state to : ' + publish + ' Successful';
                    } else {
                        $scope.processing_msg = 'Setting publish state to : ' + publish + ' failed, msg -> ' + data['message'];
                    }


                }).error(function (data) {

                });

            };

            $scope.deleteproperty = function () {
                var uploadUrl = portal.getpropertyDeleteLink();
                uploadUrl += "&property-id=" + $scope.property.id;

                $scope.processing_msg = 'Deleting property... ';

                $http.get(uploadUrl, {}).success(function (data) {

                    if (data['success']) {
                        //$scope.property.show_on_web = data['message'];
                        $scope.processing_msg = 'Setting delete state to : ' + '1' + ' Successful';
                    } else {
                        $scope.processing_msg = 'Setting delete state to : ' + '0' + ' failed, msg -> ' + data['message'];
                    }


                }).error(function (data) {

                });
            };

            $scope.saveproperty = function () {

                $scope.processing_msg = 'Processing images..';

                if ($scope.property.new_image) {
                    $scope.uploadFile($scope.property.new_image, 'property_image');
                }

                $scope.processing_msg = 'Processing property data..';
                $scope.property.new_logo = null;//no need to send big pics again !
                $scope.property.new_image = null;

                var uploadUrl = portal.getpropertyUpdateLink();
                $http.post(uploadUrl, $scope.property).success(function (data) {

                    if (data['success']) {
                        $scope.processing_msg = data['message'];
                    } else {
                        $scope.processing_msg = data['message'];
                    }


                }).error(function (data) {
                    $scope.processing_msg = data;
                });

            };


            $scope.processNewFiles = function (files, type) {
                if (type == 'property_image')
                    $scope.property.new_image = files[0];
            };

            $scope.uploadFile = function (file, type) {


                var fd = new FormData();
                //Take the first selected file
                fd.append("propertyImageFile", file);
                var uploadUrl = '';
                if (type == 'property_image')
                    uploadUrl = portal.getpropertyImageUploadLink();
                if (type == 'property_logo')
                    uploadUrl = portal.getpropertyLogoUploadLink();
                uploadUrl += "&property-id=" + $scope.property.id;


                $scope.processing_msg = 'uploading ' + type + ' -> ' + file;

                $http.post(uploadUrl, fd, {
                    withCredentials: true,
                    headers: {'Content-Type': undefined},
                    transformRequest: angular.identity
                }).success(function (data) {

                    if (data['success']) {
                        if (type == 'property_image')
                            $scope.property.image_file_path = data['message'];

                        $scope.processing_msg = 'uploading ' + type + ' Successful, new url -> ' + data['message'];

                    } else {
                        $scope.processing_msg = 'uploading ' + type + ' Failed, msg -> ' + data['message'];
                    }

                }).error(function (data) {
                    $scope.processing_msg = 'uploading ' + type + ' Failed, msg -> ' + data;
                });
            };

            $scope.$on('pindropMapPoistionUpdated', function (event, args) {
                //console.log(args);
                //console.log(event);
                $scope.property.latitude = args.lat;
                $scope.property.longitude = args.lng;

                $scope.processing_msg = 'map location updated to : ' + args.lat + ' / ' + args.lng;
                $scope.$apply();
            });


            $scope.updateGoogleMapCenter = function (type) {

                if (typeof portal.pindrop_map == 'undefined')
                    return;

                var location_id = '';
                if (type == 'regions')
                    location_id = $scope.property.address.region_id;
                if (type == 'towns')
                    location_id = $scope.property.address.town_id;
                if (type == 'postal_codes')
                    location_id = $scope.property.address.postal_code_id;

                $scope.processing_msg = 'updating google map...';
                if (location_id !== '') {

                    $http(
                        {
                            method: 'POST',
                            url: portal.getApiSearchLocationByName(),
                            data: {type: type, id: location_id}
                        })
                        .success(function (data) {

                            portal.pindrop_map.panTo(new google.maps.LatLng(data.lat, data.long));
                            portal.pindrop_marker.setPosition(new google.maps.LatLng(data.lat, data.long));

                            $scope.processing_msg = 'google map updated...';

                        })
                        .error(function () {
                        });
                }


            };

            $scope.filterTown = function (town) {
                return town.parent_id == $scope.property.address.region_id;
            };

            $scope.filterPostal = function (postal) {
                //return true;
                return postal.parent_id == $scope.property.address.town_id;
            };

        }]);