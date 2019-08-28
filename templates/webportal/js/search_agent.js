angular.module('webportal')
    .controller('SearchAgentCtrl', ['$scope', '$log', 'portal', 'uri', 'pager', '$sce', '$http', 'events', 'localStorageService', '$element',
        function ($scope, $log, portal, uri, pager, $sce, $http, events, localStorageService, $element) {

            $scope.url = portal.getApiSearchAgent();

            portal.setScope($scope);

            $scope.SearchAgent = function () {

                $http.post($scope.url, {"data": $scope.sale_id})
                    .success(function (data, status) {
                    $scope.status = status;
                    $scope.data = data;
                    $scope.result = data;
                })
                    .error(function (data, status) {
                        $scope.data = data || "Request failed";
                        $scope.status = status;
                    });
            }
        }
]);
