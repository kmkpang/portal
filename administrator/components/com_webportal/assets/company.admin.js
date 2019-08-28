/**
 * Created by khan on 10/10/15.
 */
angular.module('webportal')
    .controller('CompanyAdminCtrl', ['$scope', '$http', '$log', '$sce', '$window', 'ngDialog', 'portal', 'events', 'localStorageService',
        function ($scope, $http, $log, $sce, $window, ngDialog, portal, events, localStorageService) { //dont think i need all these though !

            portal.setScope($scope);
            $scope.company = {};//set from php


            $scope.processing_msg = '';

            $scope.initCompany = function () {
                $scope.company = window.company;
                console.log($scope.company);

            };


            $scope.saveCompany = function () {

                $scope.processing_msg = "Updating...";
                var uploadUrl = portal.getCompanyUpdateLink();
                $http.post(uploadUrl, $scope.company).success(function (data) {

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