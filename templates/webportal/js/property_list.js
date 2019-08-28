/**
 * Created by Lian on 6/16/14.
 */

angular.module('webportal')
    .directive('propertyList', ['uri', function(uri) {
        return {
            restrict: 'E',
            templateUrl: uri.getBase() + 'templates/remax-th/ng_templates/properties/list.html'
        }
    }]);

