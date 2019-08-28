/**
 * Created by khan on 10/10/15.
 */
angular.module('webportal')
    .controller('AgentAdminCtrl', ['$scope', '$http', '$log', '$sce', '$window', 'ngDialog', 'portal', 'events', 'localStorageService',
        function ($scope, $http, $log, $sce, $window, ngDialog, portal, events, localStorageService) { //dont think i need all these though !

            portal.setScope($scope);
            $scope.agent = {};//set from php


            $scope.agent.new_image = false;
            $scope.agent.new_logo = false;
            $scope.processing_msg = '';

            $scope.initAgent = function () {
                $scope.agent = window.agent;
                console.log($scope.agent);

            };

            $scope.togglePublish = function (publish) {

                var uploadUrl = portal.getAgentPublishToggleLink();
                uploadUrl += "&agent-id=" + $scope.agent.id;

                if (publish)
                    uploadUrl += "&publish=1";
                else
                    uploadUrl += "&publish=0";

                $scope.processing_msg = 'Setting publish state to : ' + publish;

                $http.get(uploadUrl, {}).success(function (data) {

                    if (data['success']) {
                        $scope.agent.show_on_web = data['message'];
                        $scope.processing_msg = 'Setting publish state to : ' + publish + ' Successful';
                    } else {
                        $scope.processing_msg = 'Setting publish state to : ' + publish + ' failed, msg -> ' + data['message'];
                    }


                }).error(function (data) {

                });

            };

            $scope.deleteAgent = function () {
                var uploadUrl = portal.getAgentDeleteLink();
                uploadUrl += "&agent-id=" + $scope.agent.id;

                $scope.processing_msg = 'Deleting agent... ';

                $http.get(uploadUrl, {}).success(function (data) {

                    if (data['success']) {
                        //$scope.agent.show_on_web = data['message'];
                        $scope.processing_msg = 'Setting delete state to : ' + '1' + ' Successful';
                    } else {
                        $scope.processing_msg = 'Setting delete state to : ' + '0' + ' failed, msg -> ' + data['message'];
                    }


                }).error(function (data) {

                });
            };

            $scope.saveAgent = function () {

                $scope.processing_msg = 'Processing images..';

                if ($scope.agent.new_image) {
                    $scope.uploadFile($scope.agent.new_image, 'agent_image');
                }


                $scope.processing_msg = 'Processing agent data..';
                $scope.agent.new_logo = null;//no need to send big pics again !
                $scope.agent.new_image = null;

                var uploadUrl = portal.getAgentUpdateLink();
                $http.post(uploadUrl, $scope.agent).success(function (data) {

                    if (data['success']) {
                        $scope.processing_msg = data['message'];
                    } else {
                        $scope.processing_msg = data['message'];
                    }


                }).error(function (data) {
                    $scope.processing_msg = data;
                });

            };

            $scope.uploadFile = function (file, type) {


                var fd = new FormData();
                //Take the first selected file
                fd.append("agentImageFile", file);
                var uploadUrl = '';
                if (type == 'agent_image')
                    uploadUrl = portal.getAgentImageUploadLink();

                uploadUrl += "&agent-id=" + $scope.agent.id;


                $scope.processing_msg = 'uploading ' + type + ' -> ' + file;

                $http.post(uploadUrl, fd, {
                    withCredentials: true,
                    headers: {'Content-Type': undefined},
                    transformRequest: angular.identity
                }).success(function (data) {

                    if (data['success']) {
                        if (type == 'agent_image')
                            $scope.agent.image_file_path = data['message'];

                        $scope.processing_msg = 'uploading ' + type + ' Successful, new url -> ' + data['message'];

                    } else {
                        $scope.processing_msg = 'uploading ' + type + ' Failed, msg -> ' + data['message'];
                    }

                }).error(function (data) {
                    $scope.processing_msg = 'uploading ' + type + ' Failed, msg -> ' + data;
                });
            };


            $scope.processNewFiles = function (files, type) {
                if (type == 'agent_image')
                    $scope.agent.new_image = files[0];

                //   $scope.uploadFile($scope.agent.new_image, type);
            };


        }]);