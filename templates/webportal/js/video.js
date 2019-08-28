angular.module('webportal')
    .controller('VideoCtrl', ['$scope', '$http', 'ngDialog', 'portal', function ($scope, $http, ngDialog, portal) {
        $scope.name = $scope.ngDialogData.name;
    }
    ]);

angular.module('webportal')
    .directive('videosList', ['portal', function (portal) {
        return {
            restrict: 'E',
            templateUrl: portal.getApiLang() + '&file=templates/webportal/ng_templates/videos/list.php',
        }
    }]);

