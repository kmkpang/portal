/**
 * Created by Khan on 25/4/15.
 */



angular.module('webportal')// extends SearchCtrl because search.js contains all the goodies !!
    .controller('AddPropertyCtrl', ['$scope', '$filter', '$controller', '$log', 'portal', 'uri', 'pager', '$sce', '$http', 'events', 'localStorageService', '$element', '$timeout',
        function ($scope, $filter, $controller, $log, portal, uri, pager, $sce, $http, events, localStorageService, $element, $timeout) {

            angular.extend(this, $controller('SearchCtrl', {

                $scope: $scope,
                $log: $log,
                portal: portal,
                uri: uri,
                pager: pager,
                $sce: $sce,
                $http: $http,
                events: events,
                localStorageService: localStorageService,
                $element: $element

            }));


            // default filtesr
            $scope.defaultProperty = {
                text: '',
                type_id: 0,
                mode_id: 2,//1 = ALL (residential + commercial )
                current_listing_price: [$scope.sliders.current_listing_price.floor, $scope.sliders.current_listing_price.ceiling],
                rent_price: [$scope.sliders.rent_price.floor, $scope.sliders.rent_price.ceiling],
                total_number_of_rooms: [$scope.sliders.total_number_of_rooms.floor, $scope.sliders.total_number_of_rooms.ceiling],
                number_of_bedrooms: [$scope.sliders.number_of_bedrooms.floor, $scope.sliders.number_of_bedrooms.ceiling],
                total_area: [$scope.sliders.total_area.floor, $scope.sliders.total_area.ceiling],
                rent_total_area: [$scope.sliders.rent_total_area.floor, $scope.sliders.rent_total_area.ceiling],
                region_id: '',
                city_town_id: '',
                zip_code_id: '',
                address: '',
                order: 'ORDER_BY_NEWEST_FIRST',
                office_id: '',
                sale_id: '',
                office_name: '',
                sale_name: '',
                search_key: '',
                latitude: '',
                longitude: '',
                radius: webportalConfiguration.transportationSearchRadius,
                transport_line: null,
                transport_station: '',
                //----------------------------------------------//
                features: [],//used ONLY in add your property,  //
                price: '',   //used ONLY in add your property,  //
                size: '',    //used ONLY in add your property,  //
                floor_level: '',   //used ONLY in add your property,  // <<-----------------
                unit: '',    //used ONLY in add your property,  //
                noof: '',    //used ONLY in add your property,  //
                movein: '',   //used ONLY in add your property,  //
                //----------------------------------------------//
                category_id: [],
                name: '',
                email: '',
                phone: '',
                password1: '',
                password2: '',
                currentStep: '',
                nextStep: '',
                files: '',
                user_region_id: '',
                desc_english: '',
                desc_thai: '',
                exclusive: '',
                addAnother: '',



            };

            $scope.currentProperty = {};


            //var saved_property = localStorageService.get('currentProperty');
            //
            //if (saved_property) {
            //    $log.log('using saved property from storage');
            //    // clone default property - we don't want to edit the default property
            //    var def_property = JSON.parse(JSON.stringify($scope.defaultProperty));
            //
            //    // merge default property and saved property - so we get default values for newly added property
            //    var merged_values = jQuery.extend({}, def_property, saved_property);
            //
            //    // clone the merged values - otherwise, inner objects are kept as references
            //    merged_values = JSON.parse(JSON.stringify(merged_values));
            //    $scope.currentProperty = merged_values;
            //} else {
            //    $log.log('using default property')
            //    $scope.currentProperty = JSON.parse(JSON.stringify($scope.defaultProperty));
            //
            //}

            $scope.filterTown = function (town) {
                //$log.debug('town filer ' + town);
                return town.parent_id == $scope.currentProperty.region_id;
            };

            $scope.filterPostal = function (postal) {
                return postal.parent_id == $scope.currentProperty.city_town_id;
            };

            $scope.restoreData = function () {

                var form_data_value = jQuery("input[name='form_data_value']").val();
                var localData = form_data_value;
                //var localData = form_data_value.replace(/\"/g, '').trim();

                $log.log('restoring account from saved data -->\n' + localData);
                localData = JSON.parse(atob(localData)); //because it is base64 encoded

                $log.log('Merging with current property data');
                $scope.currentProperty = localData;

                $scope.currentProperty.region_id = localData.region_id != null ? localData.region_id.toString() : '';
                $scope.currentProperty.city_town_id = localData.city_town_id != null ? localData.city_town_id.toString() : '';
                $scope.currentProperty.zip_code_id = localData.zip_code_id != null ? localData.zip_code_id.toString() : '';
                $scope.currentProperty.user_region_id = localData.user_region_id != null ? localData.user_region_id.toString() : '';


                //console.log($scope.currentProperty.user_region_id);

            };


            //watch the password match

            $scope.$watch('currentProperty', function () {

                /*----------------------------------------------- step 1  start -------------------------------------------*/

                if ($scope.currentProperty.currentStep == '1') {

                    /*--- inactive for now --*/

                    //$scope.$watch('currentProperty.password2', function () {
                    //
                    //    if ($scope.currentProperty.password2 !== $scope.currentProperty.password1) {
                    //        $scope.add_property_form.password2.$setValidity('add_property_form.password2', false);
                    //    } else
                    //        $scope.add_property_form.password2.$setValidity('add_property_form.password2', true);
                    //
                    //
                    //});


                }

                /*----------------------------------------------- step 1 finish-------------------------------------------*/

                if ($scope.currentProperty.currentStep == '2') {


                    $scope.$watch('currentProperty.price', function () {

                        $scope.currentProperty.price_formatted = $filter('currency')($scope.currentProperty.price, 'BHT ', 0);

                    });

                }

                if ($scope.currentProperty.currentStep == '3') {
                }

            });

            $scope.changeExclusivity = function (exclusive) {
                $scope.currentProperty.exclusive = exclusive;
            };

            $scope.addAnother = function (addAnother) {
                $scope.currentProperty.addAnother = addAnother;
            };


            $scope.reformatPrice = function () {
                $scope.currentProperty.price = $scope.currentProperty.price_formatted;
            };
            $scope.createDatePicker = function () {
                portal.createDatePicker();
            };

            $scope.processFeatures = function (data) {

                //data= jQuery.parseJSON(data);

                $scope.currentProperty.features = angular.fromJson(MERGED_FEATURES);
                $log.log($scope.currentProperty.features);

            };

            $scope.saveProperty = function () {
                $log.log('saving property');
                localStorageService.set('currentProperty', $scope.currentProperty);

            };


            $scope.addProperty = function ($event) {

                if ($scope.add_property_form.$valid) {

                    if ($scope.currentProperty.currentStep == '2') {
                        var latLng = portal.pindropmap_marker.split(',');
                        $scope.currentProperty.latitude = latLng[0];
                        $scope.currentProperty.longitude = latLng[1];
                    }

                    if ($scope.currentProperty.currentStep == '3') {
                        $scope.currentProperty.desc_english = tinyMCE.get('desc_english').getContent();
                        $scope.currentProperty.desc_thai = tinyMCE.get('desc_thai').getContent();
                    }

                    var toSend = $scope.currentProperty;
                    console.log("step1 object: -->");
                    console.log(toSend);
                    console.log("step2 stringfy: -->");
                    toSend = JSON.stringify(toSend).toUnicode();
                    console.log(toSend);
                    console.log("step3: btoa-->");
                    toSend = btoa(toSend);
                    console.log(toSend);

                    console.log("submit : --> toSend");
                    jQuery("#submit_value").val(toSend);
                }
                else {
                    $event.preventDefault();
                }
            };

            $scope.office_list = [];
            $scope.noofficefound = false;


            $scope.updateOfficeList = function () {
                $scope.office_list = [];
                $scope.noofficefound = false;

                $http(
                    {
                        method: 'POST',
                        url: portal.getApiSearchOfficeByLocation(),
                        data: $scope.currentProperty
                    })
                    .success(function (data) {

                        var i = 0;
                        if (data[0] === 'EMPTY_OFFICE_RETURN_ALL') {
                            $scope.noofficefound = true;
                            i = 1;
                        }


                        for (i; i < data.length; i++) {
                            $scope.office_list.push(data[i]);
                            //console.log(data[i]);
                        }

                    })
                    .error(function () {
                        $log.warn('Error getting office list');
                    });
                // }

            };

            $scope.category_filter_changed = function () {
                $scope.currentProperty.category_id = [];


                var foundResidential = false;
                var foundCommecial = false;
                for (var i = 0; i < $scope.multiselectcats.length; i++) {
                    var item = $scope.multiselectcats[i];

                    if (item.checked) {
                        $scope.currentProperty.category_id.push(item.id);
                        if (item.mode_id == 2)
                            foundResidential = true;
                        if (item.mode_id == 3)
                            foundCommecial = true;
                    }
                }
                //1= ALL,2=RESIDENTIAL,3=COMMERCIAL
                if (foundResidential && !foundCommecial)
                    $scope.currentProperty.mode_id = 2;
                if (foundResidential && foundCommecial)
                    $scope.currentProperty.mode_id = 1;
                if (!foundResidential && foundCommecial)
                    $scope.currentProperty.mode_id = 3;
                if (!foundResidential && !foundCommecial)
                    $scope.currentProperty.mode_id = 2;
                //  $scope.currentProperty.mode_id = 1;

                $log.log("Current MODE ID IS: " + $scope.currentProperty.mode_id + " because res:" + foundResidential + " and com:" + foundCommecial);
            };

            $scope.category_filter_changed_old = function () {
                $scope.currentProperty.category_id = [];

                for (var i = 0; i < $scope.prop_categories_tree.length; i++) {
                    var mode = $scope.prop_categories_tree[i];

                    for (var j = 0; j < mode.categories.length; j++) {
                        var cat = mode.categories[j];
                        if (cat.checked) {
                            $scope.currentProperty.category_id.push(cat.id);
                        }
                    }
                }
            };


            $scope.updateGoogleMapCenter = function (type) {

                if (typeof portal.pindrop_map == 'undefined')
                    return;

                var location_id = '';
                if (type == 'regions')
                    location_id = $scope.currentProperty.region_id;
                if (type == 'towns')
                    location_id = $scope.currentProperty.city_town_id;
                if (type == 'postal_codes')
                    location_id = $scope.currentProperty.zip_code_id;


                $http(
                    {
                        method: 'POST',
                        url: portal.getApiSearchLocationByName(),
                        data: {type: type, id: location_id}
                    })
                    .success(function (data) {

                        portal.pindrop_map.panTo(new google.maps.LatLng(data.lat, data.long));
                        portal.pindrop_marker.setPosition(new google.maps.LatLng(data.lat, data.long));

                    })
                    .error(function () {
                        $log.warn('Error getting office list');
                    });
                // }
            };


        }])

    .directive('postalCodeSelectAddpropertyProvince', ['portal', function (portal) {
        return {
            restrict: 'E',
            templateUrl: portal.getApiLang() + '&file=templates/webportal/ng_templates/search/addproperty/postal_code_select_province.php'
        };
    }])
    .directive('imageUploadControl', ['portal', function (portal) {
        return {
            restrict: 'E',
            templateUrl: portal.getApiLang() + '&file=/templates/webportal/ng_templates/properties/imageupload.php'
        };
    }])
    .directive('filterDatePicker', ['portal', function (portal) {
        return {
            restrict: 'E',
            templateUrl: portal.getApiLang() + '&file=templates/webportal/ng_templates/search/_elements/datepicker.php'
            //  link:
        }
    }])


;