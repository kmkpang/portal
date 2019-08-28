/**
 * Created by khan on 10/10/15.
 */
angular.module('webportal')
    .controller('ConfigAdminCtrl', ['$scope', '$http', '$log', '$sce', '$window', 'ngDialog', 'portal', 'events', 'localStorageService',
        function ($scope, $http, $log, $sce, $window, ngDialog, portal, events, localStorageService) { //dont think i need all these though !

            portal.setScope($scope);
            $scope.processing_msg = '';

            //log4php

            $scope.log4php = {};//set from php
            $scope.initLog4php = function () {
                $scope.log4php.config = window.atob(window.log4php);
                console.log($scope.log4php);

            };
            $scope.saveLog4php = function () {

                $scope.processing_msg = "Updating...";
                var uploadUrl = portal.getLog4phpUpdateLink();

                $http.post(uploadUrl, {config: window.btoa($scope.log4php.config)}).success(function (data) {

                    if (data['success']) {
                        $scope.processing_msg = data['message'];
                    } else {
                        $scope.processing_msg = data['message'];
                    }


                }).error(function (data) {
                    $scope.processing_msg = data;
                });

            };

            //php

            $scope.php = {};//set from php
            $scope.initPhp = function () {
                $scope.php.config = window.atob(window.php);
                console.log($scope.php);

            };
            $scope.savePhp = function () {

                $scope.processing_msg = "Updating...";
                var uploadUrl = portal.getPhpUpdateLink();

                $http.post(uploadUrl, {config: window.btoa($scope.php.config)}).success(function (data) {

                    if (data['success']) {
                        $scope.processing_msg = data['message'];
                    } else {
                        $scope.processing_msg = data['message'];
                    }


                }).error(function (data) {
                    $scope.processing_msg = data;
                });

            };


            //js

            $scope.js = {};//set from php
            $scope.initJs = function () {
                $scope.js.config = window.atob(window.js);
                console.log($scope.js);

            };
            $scope.saveJs = function () {

                $scope.processing_msg = "Updating...";
                var uploadUrl = portal.getJsUpdateLink();

                $http.post(uploadUrl, {config: window.btoa($scope.js.config)}).success(function (data) {

                    if (data['success']) {
                        $scope.processing_msg = data['message'];
                    } else {
                        $scope.processing_msg = data['message'];
                    }


                }).error(function (data) {
                    $scope.processing_msg = data;
                });

            };
            
            


        }]);