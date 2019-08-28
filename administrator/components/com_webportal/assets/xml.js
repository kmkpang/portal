/**
 * Created by khan on 8/22/15.
 */

angular.module('webportal')
    .controller('XmlSearchCtrl', ['$scope', '$filter', 'ngDialog', '$controller', '$log', 'portal', 'uri', 'pager', '$sce', '$http', 'events', 'localStorageService', '$element', '$timeout',
        function ($scope, $filter, ngDialog, $controller, $log, portal, uri, pager, $sce, $http, events, localStorageService, $element, $timeout) {

            $scope.createDatePicker = function () {
                jQuery("input[name='date']").datepicker(
                    {dateFormat: 'yy-mm-dd'}
                );
            };

            $scope.xmlSearchModel = {
                date: '',
                direction: '',
                command: '',
                type: '',
                propertyUniqueId: '',
                agentUniqueId: '',
                officeUniqueId: '',
                getAssociated: true,
            };

            $scope.xmlData = {};
            $scope.searchText = "Search";
            $scope.executeState = "";
            $scope.xmlResponse = "";

            $scope.filterXml = function () {
                $scope.xmlData = {};
                $scope.searchText = "Searching...";
                $http(
                    {
                        method: 'POST',
                        url: portal.getApiXmlSearch(),
                        data: $scope.xmlSearchModel
                    })
                    .success(function (data) {
                        $scope.xmlData = data;
                        $scope.searchText = "Search";
                        $scope.executeState = "";

                    })
                    .error(function () {
                        $log.warn('Error getting office list');
                        $scope.searchText = "Search";
                    });


            };

            $scope.resendXml = function (xmlId) {
                $scope.executeState = "Executing curl...";
                $http(
                    {
                        method: 'POST',
                        url: portal.resendSentToWebXml(),
                        data: xmlId
                    })
                    .success(function (data) {
                        $scope.xmlResponse = data;

                        $scope.executeState = "Done,refreshing...";
                        $scope.filterXml();
                    })
                    .error(function () {
                        $log.warn('Error getting office list');
                        $scope.executeState = "Failed to Execute xml ";
                    });


            };

            $scope.showXml = function (data) {

                data = window.atob(data);
                jQuery('#XMLHolder').find('pre').html(data);
                //prettyPrint();

                // var xmlData = jQuery('#XMLHolder').html();
                ngDialog.open({
                    template: jQuery('#XMLHolder').find("#actual-xml").html(),
                    plain: true
                });

            }


        }]);
