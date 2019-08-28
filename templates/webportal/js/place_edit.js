/**
 * Created by Lian on 6/19/14.
 */


angular.module('webportal')
    .controller('PlaceEditCtrl', ['$scope', '$log', 'portal', '$element',
        function ($scope, $log, portal, $element) {


            $scope.defaultPlace = {

                name: '',
                category: '',
                link: '',
                zipcode: '',
                region_id: '',
                city_town_id: '',
                zip_code_id: '',
                address: '',
                latitude: 0.00,
                longitude: 0.00

            };


            $scope.currentPlace = {};




        }])


;