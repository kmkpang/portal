/**
 * Created by Lian on 6/19/14.
 */


angular.module('webportal')
    .controller('SearchCtrl', ['$scope', '$log', 'portal', 'uri', 'pager', '$sce', '$http', 'events', 'localStorageService', '$element', 'ngDialog',
        function ($scope, $log, portal, uri, pager, $sce, $http, events, localStorageService, $element, ngDialog) {

            var rent_price_scale = [
                0,
                1000,
                2000,
                3000,
                4000,
                5000,
                6000,
                7000,
                8000,
                9000,
                10000,
                11000,
                12000,
                13000,
                14000,
                15000,
                17500,
                20000,
                22500,
                25000,
                27500,
                30000,
                35000,
                40000,
                45000,
                50000,
                55000,
                60000,
                65000,
                70000,
                75000,
                80000,
                85000,
                90000,
                95000,
                100000,
                110000,
                120000,
                130000,
                140000,
                150000,
                160000,
                170000,
                180000,
                190000,
                200000,
                240000,
                280000,
                320000,
                360000,
                400000,
                440000,
                480000,
                520000,
                560000,
                600000,
                680000,
                760000,
                840000,
                920000,
                1000000
            ];

            $scope.rent_price = rent_price_scale;

            var price_scale = [
                0,
                100000,
                200000,
                300000,
                400000,
                500000,
                600000,
                700000,
                800000,
                900000,
                1000000,
                1100000,
                1200000,
                1300000,
                1400000,
                1500000,
                1750000,
                2000000,
                2250000,
                2500000,
                2750000,
                3000000,
                3500000,
                4000000,
                4500000,
                5000000,
                5500000,
                6000000,
                6500000,
                7000000,
                7500000,
                8000000,
                8500000,
                9000000,
                9500000,
                10000000,
                11000000,
                12000000,
                13000000,
                14000000,
                15000000,
                16000000,
                17000000,
                18000000,
                19000000,
                20000000,
                24000000,
                28000000,
                32000000,
                36000000,
                40000000,
                44000000,
                48000000,
                52000000,
                56000000,
                60000000,
                68000000,
                76000000,
                84000000,
                92000000,
                100000000
            ];
            $scope.current_listing_price = price_scale;
            
            var size_scale = [
                0,
                10,
                20,
                30,
                50,
                100,
                200,
                300,
                400,
                500,
                750,
                1000,
                2000,
                3000,
                4000,
                5000
            ];

            var rent_size_scale = [
                0,
                10,
                20,
                30,
                50,
                100
            ];

            var rooms_scale = [
                0,
                1,
                2,
                3,
                4,
                5,
                10
            ];

            $scope.sliders = {
                rent_price: {
                    floor: 0,
                    ceiling: rent_price_scale.length - 1
                },
                current_listing_price: {
                    floor: 0,
                    ceiling: price_scale.length - 1
                },
                total_number_of_rooms: {
                    floor: 0,
                    ceiling: rooms_scale.length - 1
                },
                number_of_bedrooms: {
                    floor: 0,
                    ceiling: rooms_scale.length - 1
                },
                number_of_bathrooms: {
                    floor: 0,
                    ceiling: rooms_scale.length - 1
                },
                rent_total_area: {
                    floor: 0,
                    ceiling: rent_size_scale.length - 1
                },
                total_area: {
                    floor: 0,
                    ceiling: size_scale.length - 1
                }
            };

            $scope.postal_code_tree_multiselect = [];
            $scope.postal_code_tree_multiselect_output = [];


            function processCodes(data) {
                var regions = [];
                var towns = [];
                var postals = [];

                for (var i = 0; i < data.length; i++) {
                    var region = data[i];
                    regions.push({id: region.id, name: region.name});
                    $scope.postal_code_tree_multiselect.push({
                        id: region.id,
                        name: region.name,
                        type: 'region',
                        html: '<label class="multi-select-province" id="multi-select-province-id-' + region.id.trim() + '" >' + region.name + '</label>',
                        checked: $scope.searchfilter.region_id.indexOf(region.id) > -1
                    });

                    for (var j = 0; j < region.towns.length; j++) {
                        var town = region.towns[j];
                        towns.push({id: town.id, name: town.name, parent_id: region.id});
                        $scope.postal_code_tree_multiselect.push({
                            id: town.id,
                            name: town.name,
                            type: 'town',
                            parent_id: region.id,
                            html: '<label class="multi-select-district" id="multi-select-district-id-' + town.id.trim() + '">' + town.name + '</label>',
                            checked: $scope.searchfilter.city_town_id.indexOf(town.id) > -1
                        });

                        for (var k = 0; k < town.postal_codes.length; k++) {
                            var postal = town.postal_codes[k];

                            postals.push({id: postal.id, name: postal.name, parent_id: town.id});
                            $scope.postal_code_tree_multiselect.push({
                                id: postal.id,
                                html: '<label class="multi-select-postal">' + postal.name + '</label>',
                                name: postal.name,
                                type: 'postal',
                                parent_id: town.id,
                                checked: $scope.searchfilter.zip_code_id.indexOf(postal.id) > -1
                            });
                        }
                    }
                }


                $scope.postal_code_tree = {
                    regions: regions,
                    towns: towns,
                    postals: postals
                };

                // console.log($scope.postal_code_tree_multiselect);


            }


            $scope.showAdvancedSearch = typeof(webportalConfiguration.showAdvancedSearch !== null) ? webportalConfiguration.showAdvancedSearch : true;
            $scope.pager = pager;
            $scope.getLink = portal.getLink;
            $scope.getVideoLink = portal.getVideoLink;
            $scope.listloading = false;


            pager.onloading = function () {
                $scope.listloading = true;
            };


            // default filtesr
            $scope.defaultfilter = {
                text: '',
                type_id: 1,
                mode_id: 2,//1 = ALL (residential + commercial )
                current_listing_price: [$scope.sliders.current_listing_price.floor, $scope.sliders.current_listing_price.ceiling],
                rent_price: [$scope.sliders.rent_price.floor, $scope.sliders.rent_price.ceiling],
                total_number_of_rooms: [$scope.sliders.total_number_of_rooms.floor, $scope.sliders.total_number_of_rooms.ceiling],
                number_of_bedrooms: [$scope.sliders.number_of_bedrooms.floor, $scope.sliders.number_of_bedrooms.ceiling],
                number_of_bathrooms: [$scope.sliders.number_of_bathrooms.floor, $scope.sliders.number_of_bathrooms.ceiling],
                total_area: [$scope.sliders.total_area.floor, $scope.sliders.total_area.ceiling],
                rent_total_area: [$scope.sliders.rent_total_area.floor, $scope.sliders.rent_total_area.ceiling],
                region_id: [webportalConfiguration.defaultRegionID],
                city_town_id: [webportalConfiguration.defaultTownID],
                zip_code_id: [webportalConfiguration.defaultZipCodeID],
                order: 'ORDER_BY_NEWEST_FIRST',
                office_id: '',
                sale_id: '',
                office_name: '',
                sale_name: '',
                search_key: '',
                latitude: '',
                longitude: '',
                radius: [webportalConfiguration.transportationSearchRadius],
                transport_line: null,
                transport_station: '',
                preferred_currency: webportalConfiguration.preferred_currency,


                loan80: '',
                garage: '',
                elevator: '',
                new_today: '',
                new_this_week: '',

                swapping: '',
                is_featured: '',
            };

            $scope.views = ['list', 'grid'];
            $scope.selection = $scope.views[0];
            $scope.viewFlipped = function (view) {

                if (view == 'list') {
                    $scope.selection = 'list';
                }
                if (view == 'grid') {
                    $scope.selection = 'grid';
                }
            }

            portal.setScope($scope);
            var saved_filter = localStorageService.get('searchfilter');

            if (saved_filter) {
                $log.log('using saved filters from storage');
                // clone default filter - we don't want to edit the default filter
                var def_filter = JSON.parse(JSON.stringify($scope.defaultfilter));

                // merge default filter and saved filters - so we get default values for newly added filters
                var merged_values = jQuery.extend({}, def_filter, saved_filter);

                // clone the merged values - otherwise, inner objects are kept as references
                merged_values = JSON.parse(JSON.stringify(merged_values));
                $scope.searchfilter = merged_values;
            } else {
                $log.log('using default filters')
                $scope.searchfilter = JSON.parse(JSON.stringify($scope.defaultfilter));
            }


            var propertiesForEmail = localStorageService.get('propertiesForEmail');
            if (propertiesForEmail) {
                $log.log("Using saved propertiesForEmail");
                var merged_values = JSON.parse(JSON.stringify(propertiesForEmail));
                $scope.propertiesForEmail = merged_values;

            } else {
                $scope.propertiesForEmail = {};
            }

            // Default show bts line in Thailand
            //$scope.searchfilter.search_bts = true;

            $scope.is_btsmap_initialized = false;
            $scope.transport_line = '';
            $scope.transport_station = null;
            $scope.transport_name = '';

            // function to dynamically load bts data
            var load_btsmap = function () {
                $log.log("element: ");
                $log.log($element);
                var bts_map_element = jQuery($element).find('#bts_map').get(0);
                $log.log(bts_map_element);
                if (!bts_map_element) {
                    return;
                }
                window.loadBtsMap(
                    bts_map_element,
                    uri.getBase() + 'templates/webportal/js/bts.json',
                    function () {
                        $scope.is_btsmap_initialized = true;
                    }
                );
            };

            var initialize_btsmap = function () {
                if (!$scope.is_btsmap_initialized && $scope.searchfilter.search_bts) {
                    // so that the element is show and has a height/width before we load the svg map
                    setTimeout(load_btsmap, 100);
                }
            };

            events.on('frontpage_search_loaded', initialize_btsmap);

            $scope.$watch('searchfilter.search_bts', initialize_btsmap);
            /*
             * https://github.com/Venturocket/angular-slider
             * When hidden during initialization (display: none;) the slider might not display correctly when shown. Issue $scope.$broadcast('refreshSlider'); in a parent scope to tell the slider to update the DOM.
             * */
            $scope.$watch('showAdvancedSearch', function () {
                $scope.$broadcast('refreshSlider');
            });


            // called when station dropdown is changed
            $scope.selected_transport_station = function (station) {
                if (station != null) {

                    station = $scope.stations_data_flattened[station];

                    $scope.searchfilter.latitude = station.lat;
                    $scope.searchfilter.longitude = station.lng;

                    $scope.searchfilter.selected_transport = station;

                    $scope.searchfilter.transport_name = station["name_" + window.langHalf];

                }
            };
            //this is done in this manner so that changing the bts station does not autometically update the name
            $scope.$watch('searchfilter.filter_done', function () {

                if ($scope.searchfilter.latitude && $scope.searchfilter.longitude) {
                    $scope.searchfilter.latitude_display = $scope.searchfilter.latitude;
                    $scope.searchfilter.longitude_display = $scope.searchfilter.longitude;
                    $scope.searchfilter.transport_name_display = $scope.searchfilter.transport_name;
                }

            });

            $scope.get_transport_name_in_current_language = function (item) {
                var currentName = "name_" + window.langHalf; // select current short lang tag...
                return item[currentName];
            };


            $scope.resetfilters = function ($event) {

                // $event.stopPropagation();
                // console.log($event);
                // $event.preventDefault();
                // $event.stopProp();

                $log.log('resetting filters');
                var currency = $scope.searchfilter.preferred_currency;
                localStorage.clear();

                $scope.searchfilter = JSON.parse(JSON.stringify($scope.defaultfilter));


                $scope.searchfilter.type_id = null; // defaults to sale button for better UX presentation
                $scope.searchfilter.mode_id = 1; // 1= ALL,2=RESIDENTIAL,3=COMMERCIAL
                $scope.searchfilter.preferred_currency = currency;


                for (var i = 0; i < $scope.postal_code_tree_multiselect.length; i++)
                    $scope.postal_code_tree_multiselect[i].checked = false;


                $scope.savefilter();
                $scope.clearHash();
                updatecatui();

                $scope.propertiesForEmail = {};
            };

            $scope.resetfiltersFrontpage = function () {
                localStorage.clear();
                $scope.searchfilter.type_id = $scope.defaultfilter.type_id; // defaults to sale button for better UX presentation
                $scope.searchfilter.mode_id = $scope.defaultfilter.mode_id; // 1= ALL,2=RESIDENTIAL,3=COMMERCIAL

                $scope.searchfilter = JSON.parse(JSON.stringify($scope.defaultfilter));


                $scope.searchfilter.type_id = 1; // defaults to sale button for better UX presentation
                $scope.searchfilter.mode_id = 1; // 1= ALL,2=RESIDENTIAL,3=COMMERCIAL
                $scope.savefilter();
                $scope.clearHash();
                updatecatui();
            };


            $scope.resetfiltersForSideBarModule = function () {

                if (typeof window.officeId !== 'undefined' || typeof window.agentId !== 'undefined') {

                    //hi driven development( HDD ) because firefox jetbrain extension on linux is crap ...
                    // alert('hi');
                    $scope.resetfilters();
                }
                //otherwise dont reset ( for the property details page)
            };

            function pretty_format(num) {
                if (num < 1000) {
                    return num;
                } else if (num >= 1000 && num < 1000000) {
                    return num;//return num / 1000 + 'K'; // because agust said only programmers understand 'K' , no one else does!
                } else if (num >= 1000000) {
                    return num / 1000000 + 'M';
                }
            }

            function unpretty_format(num) {
                var mul = 1;
                if (typeof num == 'undefined') {
                    console.log("num unefined: " + num);
                    return;
                }
                if (num.toString().indexOf('M') != -1) {
                    num = num.replace('M', '');
                    mul = 1000000;
                } else if (num.toString().indexOf('K') != -1) {
                    num = num.replace('K', '');
                    mul = 1000;
                }
                num = parseFloat(num);
                num *= mul;
                return num;
            }


            $scope.format_postal = function (postal) {
                return postal.replace(/[0-9]+\s*-\s*/, '');
            };

            $scope.scale_fn = function (scale) {
                return function (x) {
                    var map = scale;
                    var int = parseInt(parseFloat(x).toFixed());
                    if (int < map.length && int >= 0) {
                        var y1 = map[int];

                        if (int + 1 < map.length) {
                            var y2 = map[int + 1];

                            var fraction = x - int;
                            var y_diff = y2 - y1;

                            var y = y1 + (fraction * y_diff);

                            return pretty_format(y);
                        } else {
                            return pretty_format(map[map.length - 1]) + '+';
                        }
                    } else {
                        console.log('cannot handle: ' + x);
                    }
                };
            };

            $scope.price_scale = $scope.scale_fn(price_scale);
            $scope.rent_price_scale = $scope.scale_fn(rent_price_scale);
            $scope.rooms_scale = $scope.scale_fn(rooms_scale);
            $scope.size_scale = $scope.scale_fn(size_scale);
            $scope.rent_size_scale = $scope.scale_fn(rent_size_scale);

            $scope.price_iscale = function (y) {
                var map = price_scale;
                for (var i = 0; i < map.length; i++) {

                    if (i + 1 < map.length) {
                        if (y >= map[i] && y < map[i + 1]) {
                            var x1 = i;

                            var y1 = map[i];
                            var y2 = map[i + 1];

                            var fraction = (y - y1) / (y2 - y1);
                            var x = x1 + fraction;

                            return x;
                        }
                    } else {
                        return map.length - 1;
                    }
                }
            };

            $scope.onPropertyRowClicked = function (url) {
                window.location = url;
            };


            $scope.savefilter = function () {
                $log.log('saving filter');
                if (typeof pager.data !== 'undefined') {
                    pager.data.preferred_currency = $scope.searchfilter.preferred_currency;//because this needs to go between the pages..live data!
                    pager.data.order = $scope.searchfilter.order;
                }
                localStorageService.set('searchfilter', $scope.searchfilter);

            };


            $scope.filterSidebarModule = function () {
                if (typeof window.officeId !== 'undefined') {
                    $scope.searchfilter.office_id = window.officeId;
                    $scope.searchfilter.office_name = window.officeName;
                }
                if (typeof window.agentId !== 'undefined') {
                    $scope.searchfilter.sale_id = window.agentId;
                    $scope.searchfilter.sale_name = window.agentName;
                }
                $scope.savefilter();
                $scope.clearHash();
                //TODO KHAN: find better way to redirect..preferably using JROUTE::_() , but i dont know angular very well yet
                //langHalf defined in /var/www/softverk-webportal-remaxth/templates/remax-th/index.php
                //var type = "SALE";
                window.location = portal.getPropertiesSearch();
            };

            $scope.sendEmail = false;
            $scope.checkedAllPropertiesForEmail = false;

            $scope.filter = function () {

                if (typeof window.officeId !== 'undefined') {
                    //  $scope.resetfilters();
                    $scope.searchfilter.office_id = window.officeId;
                    $scope.searchfilter.office_name = window.officeName;
                }
                else if (typeof window.agentId !== 'undefined') {
                    //    $scope.resetfilters();
                    $scope.searchfilter.sale_id = window.agentId;
                    $scope.searchfilter.sale_name = window.agentName;
                }

                var url = portal.getApiPropertiesSearch();

                $log.log('saving filters in storage');

                if (portal.shouldRestoreSearchFilterFromUrl()) {

                    var filter = portal.getSearchFilterFromUrl($scope);

                    if (!jQuery.isEmptyObject(filter)) {

                        jQuery.extend($scope.searchfilter, $scope.defaultfilter, filter);

                        updatecatui();

                    }
                }

                $scope.checkedAllPropertiesForEmail = false;

                $scope.savefilter();

                $scope.searchfilter.returnType = 'RETURN_TYPE_LIST';

                var scaled_filter = JSON.parse(JSON.stringify($scope.searchfilter));

                // Scaling values
                scaled_filter.current_listing_price[0] = unpretty_format($scope.price_scale(scaled_filter.current_listing_price[0]));
                scaled_filter.current_listing_price[1] = unpretty_format($scope.price_scale(scaled_filter.current_listing_price[1]));

                scaled_filter.rent_price[0] = unpretty_format($scope.rent_price_scale(scaled_filter.rent_price[0]));
                scaled_filter.rent_price[1] = unpretty_format($scope.rent_price_scale(scaled_filter.rent_price[1]));

                scaled_filter.total_area[0] = unpretty_format($scope.size_scale(scaled_filter.total_area[0]));
                scaled_filter.total_area[1] = unpretty_format($scope.size_scale(scaled_filter.total_area[1]));

                scaled_filter.rent_total_area[0] = unpretty_format($scope.rent_size_scale(scaled_filter.rent_total_area[0]));
                scaled_filter.rent_total_area[1] = unpretty_format($scope.rent_size_scale(scaled_filter.rent_total_area[1]));

                scaled_filter.total_number_of_rooms[0] = unpretty_format($scope.rooms_scale(scaled_filter.total_number_of_rooms[0]));
                scaled_filter.total_number_of_rooms[1] = unpretty_format($scope.rooms_scale(scaled_filter.total_number_of_rooms[1]));

                scaled_filter.number_of_bedrooms[0] = unpretty_format($scope.rooms_scale(scaled_filter.number_of_bedrooms[0]));
                scaled_filter.number_of_bedrooms[1] = unpretty_format($scope.rooms_scale(scaled_filter.number_of_bedrooms[1]));

                scaled_filter.number_of_bathrooms[0] = unpretty_format($scope.rooms_scale(scaled_filter.number_of_bathrooms[0]));
                scaled_filter.number_of_bathrooms[1] = unpretty_format($scope.rooms_scale(scaled_filter.number_of_bathrooms[1]));

                // max values
                // price
                if (scaled_filter["current_listing_price"][0] == $scope.sliders["current_listing_price"].floor) {
                    scaled_filter["current_listing_price"][0] = 0;
                }
                if (scaled_filter["current_listing_price"][1] == unpretty_format($scope.price_scale($scope.sliders["current_listing_price"].ceiling))) {
                    scaled_filter["current_listing_price"][1] = 0;
                }
                if (scaled_filter["rent_price"][0] == $scope.sliders["rent_price"].floor) {
                    scaled_filter["rent_price"][0] = 0;
                }
                if (scaled_filter["rent_price"][1] == unpretty_format($scope.rent_price_scale($scope.sliders["rent_price"].ceiling))) {
                    scaled_filter["rent_price"][1] = 0;
                }
                if (scaled_filter["total_area"][0] == $scope.sliders["total_area"].floor) {
                    scaled_filter["total_area"][0] = 0;
                }
                if (scaled_filter["total_area"][1] == unpretty_format($scope.size_scale($scope.sliders["total_area"].ceiling))) {
                    scaled_filter["total_area"][1] = 0;
                }
                if (scaled_filter["rent_total_area"][0] == $scope.sliders["rent_total_area"].floor) {
                    scaled_filter["rent_total_area"][0] = 0;
                }
                if (scaled_filter["rent_total_area"][1] == unpretty_format($scope.rent_size_scale($scope.sliders["rent_total_area"].ceiling))) {
                    scaled_filter["rent_total_area"][1] = 0;
                }

                var isRoomFilterSet = false;

                if (scaled_filter["number_of_bedrooms"][0] == $scope.sliders["number_of_bedrooms"].floor) {
                    scaled_filter["number_of_bedrooms"][0] = 0;
                }
                else {
                    isRoomFilterSet = true;
                    scaled_filter["number_of_bedrooms"][0] = rooms_scale[scaled_filter["number_of_bedrooms"][0]];
                }

                if (scaled_filter["number_of_bedrooms"][1] == unpretty_format($scope.rooms_scale($scope.sliders["number_of_bedrooms"].ceiling))) {
                    scaled_filter["number_of_bedrooms"][1] = 0;
                } 
                else {
                    isRoomFilterSet = true;
                    scaled_filter["number_of_bedrooms"][1] = rooms_scale[scaled_filter["number_of_bedrooms"][1]];
                }

                // bathrooms

                if (scaled_filter["number_of_bathrooms"][0] == $scope.sliders["number_of_bathrooms"].floor) {
                    scaled_filter["number_of_bathrooms"][0] = 0;
                }
                else {
                    isRoomFilterSet = true;
                    scaled_filter["number_of_bathrooms"][0] = rooms_scale[scaled_filter["number_of_bathrooms"][0]];
                }

                if (scaled_filter["number_of_bathrooms"][1] == unpretty_format($scope.rooms_scale($scope.sliders["number_of_bathrooms"].ceiling))) {
                    scaled_filter["number_of_bathrooms"][1] = 0;
                }
                else {
                    isRoomFilterSet = true;
                    scaled_filter["number_of_bathrooms"][1] = rooms_scale[scaled_filter["number_of_bathrooms"][1]];
                }
                
                // if isRoomFilterSet has been set, do NOT use the totalRooms filter...

                if (scaled_filter["total_number_of_rooms"][0] == $scope.sliders["total_number_of_rooms"].floor || isRoomFilterSet) {
                    scaled_filter["total_number_of_rooms"][0] = 0;
                }
                else
                    scaled_filter["total_number_of_rooms"][0] = rooms_scale[scaled_filter["total_number_of_rooms"][0]];

                if (scaled_filter["total_number_of_rooms"][1] == unpretty_format($scope.rooms_scale($scope.sliders["total_number_of_rooms"].ceiling)) || isRoomFilterSet) {
                    scaled_filter["total_number_of_rooms"][1] = 0;
                }
                else
                    scaled_filter["total_number_of_rooms"][1] = rooms_scale[scaled_filter["total_number_of_rooms"][1]];

                // other unscaled props
                var props = []; // no other unscaled props
                for (var i = 0; i < props.length; i++) {
                    if (scaled_filter[props[i]][0] == $scope.sliders[props[i]].floor) {
                        scaled_filter[props[i]][0] = 0;
                        $log.log('made ' + props[i] + ' min.');
                    }
                    if (scaled_filter[props[i]][1] == $scope.sliders[props[i]].ceiling) {
                        scaled_filter[props[i]][1] = 0;
                        $log.log('made ' + props[i] + ' max.');
                    }
                }

                // apply rent price and size if type is rent
                if ($scope.searchfilter.type_id == 3) {
                    scaled_filter.current_listing_price = scaled_filter.rent_price;
                    scaled_filter.total_area = scaled_filter.rent_total_area;
                }


                var currentPage = portal.getURLParameter('page');
                if (currentPage != null && parseInt(currentPage) > 0) {
                    $log.log("current page from url is : " + currentPage);

                    pager.setCurrentPage((currentPage - 1));
                }

                if(window.current_page  != 'map')
                {
                    $scope.searchfilter.bounds=null;
                }

                //save hash because i want to use this to retrive next / prerivious properties later
                localStorageService.set('searchHash', window.base64.encode(JSON.stringify(scaled_filter)));

                //$scope.clearHash();
                //make actual ajax call
                pager.query(url, scaled_filter,
                    /*-----------------------------------------*/
                    function (data) {

                        // console.log("GOT DATA : " + data);
                        var hasResult = true;
                        if (data.length > 0) {

                            if (typeof data[0].search_key_only !== 'undefined') {
                                //this contains ONLY search key and NO search result!!!
                                $scope.searchfilter.search_key = data[0].search_key_only;
                                hasResult = false;
                                //data = {};
                            }
                            else
                                $scope.searchfilter.search_key = data[0].search_key;
                        }

                        if (hasResult) {
                            $scope.items = data;


                            angular.forEach($scope.items, function (val, i) {
                                //console.log(typeof $scope.items[i].property_id); //

                                if ($scope.propertiesForEmail[$scope.items[i].property_id]) {
                                    $scope.propertiesForEmail[$scope.items[i].property_id] = true;
                                } else
                                    $scope.propertiesForEmail[$scope.items[i].property_id] = false;


                                $scope.items[i].description_text = $sce.trustAsHtml(
                                    $scope.items[i].description_text
                                );
                            });

                            //console.log($scope.propertiesForEmail);


                        }
                        else {
                            $scope.items = {};
                        }
                        $scope.listloading = false;
                        events.fire('filter_done');


                        $scope.updateHash();


                    }).error(function () {
                        $log.error("Error loading list.");
                        //alert("Error loading list.")
                    }
                    /*-----------------------------------------*/
                );
            };

            $scope.$on('hashUpdated', function (event, e) {
                console.log('Hash updated..But nothing to do..shame!');
            });

            $scope.updateHash = function () {
                if (parseInt(pager.page) > -1) {
                    portal.updateUrlHashParameter('page', (pager.page + 1));
                }

                if (portal.shouldRestoreSearchFilterFromUrl()
                    && typeof $scope.searchfilter !== 'undefined'
                    &&  typeof $scope.searchfilter.search_key !== 'undefined') {
                    var hashes = $scope.searchfilter.search_key.split('&');
                    for (var i = 0; i < hashes.length; i++) {
                        var keyval = hashes[i].split('=');
                        if (keyval.length > 1) {
                            if (keyval[0] == 'currency')
                                keyval[1] = $scope.searchfilter.preferred_currency;
                            portal.updateUrlHashParameter(keyval[0], keyval[1]);
                        }

                    }

                }
            };

            $scope.clearHash = function () {
                window.location.hash = '';
            };


            $scope.submit = function () {
                $scope.listloading = true;
                $scope.filter();
            };

            $scope.scaletest = function (val) {
                $log.log('scaling: ' + val);
                if (val > 5000000 && val <= 10000000) {
                    return val + 1000000;
                }
                return val;
            };

            $scope.scaleitest = function (val) {
                $log.log('inverse-scaling: ' + val);
                if (val > 6000000 && val <= 11000000) {
                    return val - 1000000;
                }
                return val;
            };

            $scope.postal_code_tree = [];//TODO: consider declaring this as Object
            $scope.prop_categories_tree = [];//TODO: consider declaring this as Object
            $scope.currency_tree = [];

            $scope.multiselect_keypress_bound = false;
            $scope.multiSelect_container = null;
            $scope.multieSelect_scrollTo = null;
            $scope.bind_to_keypress_select = function () {

                if (typeof webportalConfiguration.select_scroll_to_province_id != 'undefined') {

                    if (!$scope.multiSelect_container) {
                        $scope.multiSelect_container = jQuery('.checkBoxContainer:visible');
                    }
                    var scrollTo = jQuery("#multi-select-province-id-" + webportalConfiguration.select_scroll_to_province_id);

                    if (scrollTo.length > 0) {
                        $scope.multiSelect_container.scrollTop(
                            scrollTo.offset().top - $scope.multiSelect_container.offset().top + $scope.multiSelect_container.scrollTop()
                        );
                    }

                }

                if (typeof webportalConfiguration.select_scroll_to_town_id != 'undefined') {

                    if (!$scope.multiSelect_container) {
                        $scope.multiSelect_container = jQuery('.checkBoxContainer:visible');
                    }
                    var scrollTo = jQuery("#multi-select-district-id-" + webportalConfiguration.select_scroll_to_town_id);

                    if (scrollTo.length > 0) {
                        $scope.multiSelect_container.scrollTop(
                            scrollTo.offset().top - $scope.multiSelect_container.offset().top + $scope.multiSelect_container.scrollTop()
                        );
                    }

                }


                if (!$scope.multiselect_keypress_bound) {
                    if (jQuery('.multiSelectItem').length > 0) {
                        jQuery('.multiSelectItem').bind('keydown keypress', function (event) {
                            console.log(event);
                            if (event.which === 13) {
                                event.preventDefault();
                                var check = angular.element(event.target);
                                check.trigger('click');
                                $scope.$apply();
                            }
                        });
                        $scope.multiselect_keypress_bound = true;
                    }
                }
            };

            $scope.geodata_filter_changed = function () {


                var region = [];
                var town = [];
                var postal = [];


                for (var i = 0; i < $scope.postal_code_tree_multiselect_output.length; i++) {

                    if ($scope.postal_code_tree_multiselect_output[i].type == 'region') {
                        region.push($scope.postal_code_tree_multiselect_output[i].id);
                    }
                    if ($scope.postal_code_tree_multiselect_output[i].type == 'town') {
                        town.push($scope.postal_code_tree_multiselect_output[i].id);
                    }

                    if ($scope.postal_code_tree_multiselect_output[i].type == 'postal') {
                        postal.push($scope.postal_code_tree_multiselect_output[i].id);
                    }

                }

                $scope.searchfilter.region_id = region;
                $scope.searchfilter.city_town_id = town;
                $scope.searchfilter.zip_code_id = postal;

                $scope.updateHash();

            };

            $scope.category_filter_changed = function () {
                $scope.searchfilter.category_id = [];


                var foundResidential = false;
                var foundCommecial = false;
                for (var i = 0; i < $scope.multiselectcats.length; i++) {
                    var item = $scope.multiselectcats[i];

                    if (item.checked) {
                        $scope.searchfilter.category_id.push(item.id);
                        if (item.mode_id == 2)
                            foundResidential = true;
                        if (item.mode_id == 3)
                            foundCommecial = true;
                    }
                }
                //1= ALL,2=RESIDENTIAL,3=COMMERCIAL
                if (foundResidential && !foundCommecial)
                    $scope.searchfilter.mode_id = 2;
                if (foundResidential && foundCommecial)
                    $scope.searchfilter.mode_id = 1;
                if (!foundResidential && foundCommecial)
                    $scope.searchfilter.mode_id = 3;
                if (!foundResidential && !foundCommecial)
                    $scope.searchfilter.mode_id = 2;
                //  $scope.searchfilter.mode_id = 1;

                $log.log("Current MODE ID IS: " + $scope.searchfilter.mode_id + " because res:" + foundResidential + " and com:" + foundCommecial);
            };

            $scope.category_filter_changed_old = function () {
                $scope.searchfilter.category_id = [];

                for (var i = 0; i < $scope.prop_categories_tree.length; i++) {
                    var mode = $scope.prop_categories_tree[i];

                    for (var j = 0; j < mode.categories.length; j++) {
                        var cat = mode.categories[j];
                        if (cat.checked) {
                            $scope.searchfilter.category_id.push(cat.id);
                        }
                    }
                }
            };

            var postal_codes = [];

            $http({method: 'GET', url: portal.getApiAddressPostalTree()})
                .success(function (data) {
                    postal_codes = data;
                    processCodes(postal_codes);
                    localStorageService.set('postal_code_tree', data);


                })
                .error(function () {
                    $log.warn('Error getting postal tree');
                });

            var currency = [];

            $http({method: 'GET', url: portal.getApiAddressCurrency()})
                .success(function (data) {
                    currency = data;
                    $scope.currency_tree = currency;
                    localStorageService.set('currency_tree', data);
                })
                .error(function () {
                    $log.warn('Error getting currency tree');
                });

            $scope.updateCurrency = function (i, newCurrency) {

                var itemTmp = $scope.items[i];

                $http({
                    method: 'GET',
                    url: portal.getApiAddressConvertCurrency() + "&price=" + itemTmp.current_listing_price + "&currency=" + newCurrency
                })
                    .success(function (data) {
                        $scope.items[i].current_listing_price_formatted = data;
                    })
                    .error(function () {
                        $log.warn('Error getting currenct_listing_price_formatted');
                    });

            }

            /*http://redmine.softverk.is/issues/1832*/
            $scope.price_order = "";
            $scope.size_order = "";
            $scope.newest_order = "";

            $scope.orderFlipped = function (orderType) {

                if (orderType == 'size') {
                    if ($scope.size_order == 'ORDER_BY_SMALLEST_FIRST') {
                        $scope.size_order = 'ORDER_BY_LARGEST_FIRST';
                    } else if ($scope.size_order == 'ORDER_BY_LARGEST_FIRST') {
                        $scope.size_order = 'ORDER_BY_SMALLEST_FIRST';
                    } else
                        $scope.size_order = 'ORDER_BY_LARGEST_FIRST';

                    $scope.orderChanged($scope.size_order);
                }
                if (orderType == 'price') {
                    if ($scope.price_order == 'ORDER_BY_LEAST_EXPENSIVE_FIRST') {
                        $scope.price_order = 'ORDER_BY_MOST_EXPENSIVE_FIRST';
                    } else if ($scope.price_order == 'ORDER_BY_MOST_EXPENSIVE_FIRST') {
                        $scope.price_order = 'ORDER_BY_LEAST_EXPENSIVE_FIRST';
                    } else
                        $scope.price_order = 'ORDER_BY_MOST_EXPENSIVE_FIRST';

                    $scope.orderChanged($scope.price_order);
                }
                if (orderType == 'newest') {
                    if ($scope.newest_order == 'ORDER_BY_NEWEST_FIRST') {
                        $scope.newest_order = 'ORDER_BY_OLDEST_FIRST';
                    } else if ($scope.newest_order == 'ORDER_BY_OLDEST_FIRST') {
                        $scope.newest_order = 'ORDER_BY_NEWEST_FIRST';
                    } else
                        $scope.newest_order = 'ORDER_BY_OLDEST_FIRST';

                    $scope.orderChanged($scope.newest_order);
                }

            };
//wrong commit
            $scope.checkboxFilterChanged = function (checkbox, value) {
                //console.log(checkbox + " raw : " + value);
                //console.log(checkbox + "  : " + $scope.searchfilter[checkbox]);

                if (value) {
                    portal.updateUrlHashParameter(checkbox, 'YES');
                }
                else
                    portal.updateUrlHashParameter(checkbox, '');
            };

            $scope.orderChanged = function (newOrder) {
                portal.updateUrlHashParameter('order', newOrder);
                $scope.searchfilter.order = newOrder;
            };

            $scope.currencyChanged = function () {
                console.log($scope.searchfilter.preferred_currency);
                $scope.savefilter();
                if (window.current_page == "list") {
                    portal.updateUrlHashParameter('currency', $scope.searchfilter.preferred_currency);
                    $http({
                        method: 'GET',
                        url: portal.getApiAddressSetCurrency() + "&currencyCode=" + $scope.searchfilter.preferred_currency
                    })
                        .success(function (newCurrency) {

                            //now update ALL existing ones..
                            if (typeof $scope.items !== 'undefined') {
                                for (var i = 0; i < $scope.items.length; i++) {
                                    $scope.updateCurrency(i, newCurrency);

                                }
                            }


                        })
                        .error(function () {
                            $log.warn('Error setting preferred currency');
                        });
                }
            };

            // initial array for multi select
            $scope.multiselectcats = [];

            function updatecatui() {
                $scope.multiselectcats = [];

                // updating UI with selected categories
                for (var i = 0; i < $scope.prop_categories_tree.length; i++) {
                    var mode = $scope.prop_categories_tree[i];
                    if ($scope.prop_categories_tree.length > 1) // show group only when thre are multiple groups
                        $scope.multiselectcats.push({multiSelectGroup: true, description: mode.description});

                    for (var j = 0; j < mode.categories.length; j++) {
                        var cat = mode.categories[j];
                        //      console.log(cat);
                        if (typeof $scope.searchfilter.category_id != 'undefined' && $scope.searchfilter.category_id.indexOf(cat.id) != -1) {
                            cat.checked = true;
                            $scope.multiselectcats.push({
                                id: cat.id,
                                description: cat.description,
                                checked: true,
                                mode_id: cat.mode_id
                            });
                        } else {
                            cat.checked = false;
                            $scope.multiselectcats.push({
                                id: cat.id,
                                description: cat.description,
                                mode_id: cat.mode_id
                            });
                        }
                    }
                    if ($scope.prop_categories_tree.length > 1) // show group only when thre are multiple groups
                        $scope.multiselectcats.push({multiSelectGroup: false, description: mode.description});
                }
            }

            $http({method: 'GET', url: portal.getApiPropCategoriesTree()})
                .success(function (data) {
                    $scope.prop_categories_tree = data;
                    localStorageService.set('prop_categories_tree', data);
                    updatecatui();
                })
                .error(function () {
                    $log.warn('Error getting prop categories tree');
                });
            // }

            $scope.$watch(
                function () {
                    $(document).foundation();
                }
            );
            $scope.$watch(
                function () {
                    return $scope.searchfilter.region_id
                },
                function () {
                    // $scope.searchfilter.city_town_id = ""; //had to remove this otherwise saved filters wouldn't load
                    // $scope.searchfilter.zip_code_id = "";
                });
            $scope.$watch(function () {
                    return $scope.searchfilter.city_town_id
                },
                function () {
                    // $scope.searchfilter.zip_code_id = "";
                });

            $scope.filterTown = function (town) {
                //$log.debug('town filer ' + town);
                return town.parent_id == $scope.searchfilter.region_id;
            };

            $scope.filterPostal = function (postal) {
                return postal.parent_id == $scope.searchfilter.city_town_id;
            };


            $scope.updatePropertiesForEmail = function () {
                $scope.checkedAllPropertiesForEmail = false;//because this means ALL should be unchecked
                localStorageService.set('propertiesForEmail', $scope.propertiesForEmail);
            };

            $scope.toggleSelectAllPropertiesForEmail = function () {
                angular.forEach($scope.items, function (val, i) {
                    //console.log(typeof $scope.items[i].property_id); //
                    $scope.propertiesForEmail[$scope.items[i].property_id] = $scope.checkedAllPropertiesForEmail;
                });
            };

            $scope.emailToggle = function () {
                $scope.sendEmail = $scope.sendEmail === false ? true : false;
                $scope.propertyChecked = $scope.propertyChecked === false ? true : false;

            };

            $scope.showSendMailForm = function (data) {

                portal.propertiesForEmail=[];
                for(var key in $scope.propertiesForEmail){
                    if($scope.propertiesForEmail[key]===true){
                        portal.propertiesForEmail.push(key);
                    }
                }
                
                ngDialog.open({
                    template: portal.getApiLang() + '&file=templates/webportal/ng_templates/property/send_mail_to_friend.php',
                    controller: 'ContactCtrl',
                    //disableAnimation: true,
                });

            };

        }])
    .directive('searchFiltersFrontpageHalf', ['portal', 'events', function (portal, events) {
        return {
            restrict: 'E',
            templateUrl: portal.getApiLang() + '&file=templates/webportal/ng_templates/search/frontpage/search_form_frontpage_half.php',
            link: function (scope, element, attrs) {
                setTimeout(function () {
                    scope.loaded = true;
                    scope.$apply();
                    events.fire('frontpage_search_loaded')

                    scope.resetfiltersFrontpage();

                }, 300);
            }
        }
    }])
    .directive('searchFiltersFrontpageFull', ['portal', 'events', function (portal, events) {
        return {
            restrict: 'E',
            templateUrl: portal.getApiLang() + '&file=templates/webportal/ng_templates/search/frontpage/search_form_frontpage_full.php',
            link: function (scope, element, attrs) {
                setTimeout(function () {
                    scope.loaded = true;
                    scope.$apply();
                    events.fire('frontpage_search_loaded')

                    scope.resetfiltersFrontpage();

                }, 300);
            }
        }
    }])
    .directive('searchFiltersFrontpageCommercial', ['portal', 'events', function (portal, events) {
        return {
            restrict: 'E',
            templateUrl: portal.getApiLang() + '&file=templates/webportal/ng_templates/search/frontpage/search_form_frontpage_commercial.php',
            link: function (scope, element, attrs) {
                setTimeout(function () {
                    scope.loaded = true;
                    scope.$apply();
                    events.fire('frontpage_search_loaded')

                    scope.resetfiltersFrontpage();

                }, 300);
            }
        }
    }])
    .directive('searchFiltersFrontpageModule', ['portal', function (portal) {
        return {
            restrict: 'E',
            templateUrl: portal.getApiLang() + '&file=templates/webportal/ng_templates/search/frontpage/search_form_frontpageModule.php',
            link: function (scope, element, attrs) {
                scope.loaded = true;
                scope.$apply();
            }
        }
    }])

    .directive('postalCodeSelectFrontpage', ['portal', function (portal) {
        return {
            restrict: 'E',
            templateUrl: portal.getApiLang() + '&file=templates/webportal/ng_templates/search/frontpage/postal_code_select_frontpage.html'
        };
    }])
    .directive('postalCodeSelectFrontpageProvince', ['portal', function (portal) {
        return {
            restrict: 'E',
            templateUrl: portal.getApiLang() + '&file=templates/webportal/ng_templates/search/frontpage/postal_code_select_frontpage_province.php'
        };
    }])
    .directive('postalCodeSelectFrontpageDistrict', ['portal', function (portal) {
        return {
            restrict: 'E',
            templateUrl: portal.getApiLang() + '&file=templates/webportal/ng_templates/search/frontpage/postal_code_select_frontpage_district.php'
        };
    }])
    .directive('propPriceSlider', ['portal', function (portal) {
        return {
            restrict: 'E',
            ///home/khan/www/softverk-webportal-generic/templates/generic/html/ng_templates/search/_elements/price_slider.html
            templateUrl: portal.getApiLang() + '&file=templates/generic/html/ng_templates/search/_elements/price_slider.html'
        }
    }])
    .directive('propRoomsSlider', ['portal', function (portal) {
        return {
            restrict: 'E',
            ///home/khan/www/softverk-webportal-generic/templates/generic/html/ng_templates/search/_elements/rooms_slider.html
            templateUrl: portal.getApiLang() + '&file=templates/generic/html/ng_templates/search/_elements/rooms_slider.html'
        }
    }])
    .directive('propSizeSlider', ['portal', function (portal) {
        return {
            restrict: 'E',
            ///home/khan/www/softverk-webportal-generic/templates/generic/html/ng_templates/search/_elements/size_slider.html
            templateUrl: portal.getApiLang() + '&file=templates/generic/html/ng_templates/search/_elements/size_slider.html'
        }
    }])
    .directive('propCategoriesSelectFrontpage', ['portal', function (portal) {
        return {
            restrict: 'E',
            templateUrl: portal.getApiLang() + '&file=templates/webportal/ng_templates/search/frontpage/property_category_select_frontpage.html'
        }
    }])
    .directive('searchFiltersTopbar', ['portal', function (portal) {
        return {
            restrict: 'E',
            templateUrl: portal.getApiLang() + '&file=templates/webportal/ng_templates/search/topbar/search_form_topbar.php',
            link: function (scope, element, attrs) {
                setTimeout(function () {
                    scope.loaded = true;
                    scope.$apply();
                }, 300);
            }
        }
    }])
    .directive('searchFiltersSidebar', ['portal', function (portal) {
        return {
            restrict: 'E',
            templateUrl: portal.getApiLang() + '&file=templates/webportal/ng_templates/search/sidebar/search_form_sidebar.php',
            link: function (scope, element, attrs) {
                setTimeout(function () {
                    scope.loaded = true;
                    scope.$apply();
                }, 300);
            }
        }
    }])
    .directive('searchFiltersSidebarModule', ['portal', function (portal) {
        return {
            restrict: 'E',
            templateUrl: portal.getApiLang() + '&file=templates/webportal/ng_templates/search/sidebar/search_form_sidebar_module.html',
            link: function (scope, element, attrs) {
                setTimeout(function () {
                    scope.loaded = true;
                    scope.$apply();
                }, 300);
            }
        }
    }])

    .directive('postalCodeSelectSidebar', ['portal', function (portal) {
        return {
            restrict: 'E',
            templateUrl: portal.getApiLang() + '&file=templates/webportal/ng_templates/search/sidebar/postal_code_select_sidebar.php'
        }
    }])
    .directive('propCategoriesSelectSidebar', ['portal', function (portal) {
        return {
            restrict: 'E',
            templateUrl: portal.getApiLang() + '&file=templates/webportal/ng_templates/search/sidebar/property_category_select_sidebar.php'
        }
    }])
    .directive('filterSliderPrice', ['portal', function (portal) {
        return {
            restrict: 'E',
            templateUrl: portal.getApiLang() + '&file=templates/webportal/ng_templates/search/_elements/price_slider.html'
        }
    }])
    .directive('filterSliderRooms', ['portal', function (portal) {
        return {
            restrict: 'E',
            templateUrl: portal.getApiLang() + '&file=templates/webportal/ng_templates/search/_elements/rooms_slider.html'
        }
    }])
    .directive('sortControlMap', ['portal', function (portal) {
        return {
            restrict: 'E',
            ///home/khan/www/softverk-webportal-remaxth/templates/webportal/ng_templates/search/_elements/sortcontrol_map.php
            templateUrl: portal.getApiLang() + '&file=templates/webportal/ng_templates/search/_elements/sortcontrol_map.php'
        }
    }])
    .directive('filterSliderSize', ['portal', function (portal) {
        return {
            restrict: 'E',
            templateUrl: portal.getApiLang() + '&file=templates/webportal/ng_templates/search/_elements/size_slider.html'
        }
    }])
    .directive('filterCurrency', ['portal', function (portal) {
        return {
            restrict: 'E',
            templateUrl: portal.getApiLang() + '&file=templates/webportal/ng_templates/search/_elements/currency.php'
        }
    }])
    .directive('sortControls', ['portal', function (portal) {
        return {
            restrict: 'E',
            templateUrl: portal.getApiLang() + '&file=templates/webportal/ng_templates/properties/sort_controls.html'
        }
    }])
    .directive('propertiesList', ['portal', function (portal) {
        return {
            restrict: 'E',
            templateUrl: portal.getApiLang() + '&file=templates/webportal/ng_templates/properties/list.php',
            link: function (scope, element, attrs) {
                setTimeout(function () {
                    scope.loaded = true;
                    scope.$apply();

                    scope.resetfilters();

                }, 300);
            }
        }
    }])
    .directive('videoList', ['portal', function (portal) {
        return {
            restrict: 'E',
            templateUrl: portal.getApiLang() + '&file=templates/webportal/ng_templates/videos/list.php',
            link: function (scope, element, attrs) {
                setTimeout(function () {
                    scope.loaded = true;
                    scope.$apply();

                    scope.resetfilters();

                }, 300);
            }
        }
    }])
    .directive('transportSelect', ['portal', 'uri', '$http', function (portal, uri, $http) {
        return {
            restrict: 'E',
            templateUrl: portal.getApiLang() + '&file=templates/webportal/ng_templates/search/sidebar/select_transport.html',
            link: function (scope, element, attrs) {
                $http({method: 'GET', url: uri.getBase() + 'templates/webportal/js/bts.json'})
                    .success(function (data) {

                        var stationDataFlattened = new Object();

                        var data = $.map(data, function (value, index) {
                            return [value];
                        });

                        for (var j = 0; j < data.length; j++) {
                            var lineElement = data[j];
                            var lineStations = lineElement.stations;

                            for (var i = 0; i < lineStations.length; i++) {
                                var id = lineStations[i].id;
                                stationDataFlattened[id] = lineStations[i];
                            }
                        }


                        scope.stations_data = data;
                        scope.stations_data_flattened = stationDataFlattened;
                    })
                    .error(function () {
                        $log.warn('Error getting stations data');
                    });
            }
        }
    }])
    .directive('transportSelectTop', ['portal', 'uri', '$http', function (portal, uri, $http) {
        return {
            restrict: 'E',
            templateUrl: portal.getApiLang() + '&file=templates/webportal/ng_templates/search/topbar/select_transport.html',
            link: function (scope, element, attrs) {
                $http({method: 'GET', url: uri.getBase() + 'templates/webportal/js/bts.json'})
                    .success(function (data) {

                        var stationDataFlattened = new Object();

                        var data = $.map(data, function (value, index) {
                            return [value];
                        });

                        for (var j = 0; j < data.length; j++) {
                            var lineElement = data[j];
                            var lineStations = lineElement.stations;

                            for (var i = 0; i < lineStations.length; i++) {
                                var id = lineStations[i].id;
                                stationDataFlattened[id] = lineStations[i];
                            }
                        }


                        scope.stations_data = data;
                        scope.stations_data_flattened = stationDataFlattened;
                    })
                    .error(function () {
                        $log.warn('Error getting stations data');
                    });
            }
        }
    }])
    .directive('convertToNumber', function() {
        return {
            require: 'ngModel',
            link: function(scope, element, attrs, ngModel) {
                ngModel.$parsers.push(function(val) {
                    return val != null ? parseInt(val, 10) : null;
                });
                ngModel.$formatters.push(function(val) {
                    return val != null ? '' + val : null;
                });
            }
        };
    });
;