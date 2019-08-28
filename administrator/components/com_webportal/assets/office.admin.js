/**
 * Created by khan on 10/10/15.
 */
angular.module('webportal')
    .controller('OfficeAdminCtrl', ['$scope', '$http', '$log', '$sce', '$window', 'ngDialog', 'portal', 'events', 'localStorageService',
        function ($scope, $http, $log, $sce, $window, ngDialog, portal, events, localStorageService) { //dont think i need all these though !

            portal.setScope($scope);
            $scope.office = {};//set from php
            $scope.postal_code_tree = {};

            $scope.office.new_image = false;
            $scope.office.new_logo = false;
            $scope.processing_msg = '';

            $scope.initOffice = function () {

                $scope.office = window.office;

                //console.log($scope.office.address);

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

                var uploadUrl = portal.getOfficePublishToggleLink();
                uploadUrl += "&office-id=" + $scope.office.id;

                if (publish)
                    uploadUrl += "&publish=1";
                else
                    uploadUrl += "&publish=0";

                $scope.processing_msg = 'Setting publish state to : ' + publish;

                $http.get(uploadUrl, {}).success(function (data) {

                    if (data['success']) {
                        $scope.office.show_on_web = data['message'];
                        $scope.processing_msg = 'Setting publish state to : ' + publish + ' Successful';
                    } else {
                        $scope.processing_msg = 'Setting publish state to : ' + publish + ' failed, msg -> ' + data['message'];
                    }


                }).error(function (data) {

                });

            };

            $scope.deleteOffice = function () {
                var uploadUrl = portal.getOfficeDeleteLink();
                uploadUrl += "&office-id=" + $scope.office.id;

                $scope.processing_msg = 'Deleting office... ';

                $http.get(uploadUrl, {}).success(function (data) {

                    if (data['success']) {
                        //$scope.office.show_on_web = data['message'];
                        $scope.processing_msg = 'Setting delete state to : ' + '1' + ' Successful';
                    } else {
                        $scope.processing_msg = 'Setting delete state to : ' + '0' + ' failed, msg -> ' + data['message'];
                    }


                }).error(function (data) {

                });
            };

            $scope.saveOffice = function () {

                $scope.processing_msg = 'Processing images..';

                if ($scope.office.new_image) {
                    $scope.uploadFile($scope.office.new_image, 'office_image');
                }
                if ($scope.office.new_logo) {
                    $scope.uploadFile($scope.office.new_logo, 'office_logo');
                }

                $scope.processing_msg = 'Processing office data..';
                $scope.office.new_logo = null;//no need to send big pics again !
                $scope.office.new_image = null;

                var uploadUrl = portal.getOfficeUpdateLink();
                $http.post(uploadUrl, $scope.office).success(function (data) {

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
                if (type == 'office_image')
                    $scope.office.new_image = files[0];
                if (type == 'office_logo')
                    $scope.office.new_logo = files[0];
            };

            $scope.uploadFile = function (file, type) {


                var fd = new FormData();
                //Take the first selected file
                fd.append("officeImageFile", file);
                var uploadUrl = '';
                if (type == 'office_image')
                    uploadUrl = portal.getOfficeImageUploadLink();
                if (type == 'office_logo')
                    uploadUrl = portal.getOfficeLogoUploadLink();
                uploadUrl += "&office-id=" + $scope.office.id;


                $scope.processing_msg = 'uploading ' + type + ' -> ' + file;

                $http.post(uploadUrl, fd, {
                    withCredentials: true,
                    headers: {'Content-Type': undefined},
                    transformRequest: angular.identity
                }).success(function (data) {

                    if (data['success']) {
                        if (type == 'office_image')
                            $scope.office.image_file_path = data['message'];
                        if (type == 'office_logo')
                            $scope.office.logo = data['message'];

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
                $scope.office.address.latitude = args.lat;
                $scope.office.address.longitude = args.lng;

                $scope.processing_msg = 'map location updated to : ' + args.lat + ' / ' + args.lng;
                $scope.$apply();
            });


            $scope.updateGoogleMapCenter = function (type) {

                if (typeof portal.pindrop_map == 'undefined')
                    return;

                var location_id = '';
                if (type == 'regions')
                    location_id = $scope.office.address.region_id;
                if (type == 'towns')
                    location_id = $scope.office.address.town_id;
                if (type == 'postal_codes')
                    location_id = $scope.office.address.postal_code_id;

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
                return town.parent_id == $scope.office.address.region_id;
            };

            $scope.filterPostal = function (postal) {
                //return true;
                return postal.parent_id == $scope.office.address.town_id;
            };

        }]);