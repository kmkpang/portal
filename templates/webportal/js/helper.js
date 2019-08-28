/**
 * Created by Lian on 6/16/14.
 */

// define the app. this needs to be the same as the "ng-app" attribute in the <html> tag.
var app = angular.
    module('webportal',
    [
        'vr.directives.slider',
        'angular-flexslider',
        'LocalStorageModule',
        'multi-select',
        'djds4rce.angular-socialshare',
        'ngDialog',
        'ngTagsInput',
        'ngAnimate',
        'round',
        //'ngFileUpload',
        //'ngDroplet', // drag drop image upload
        //'angular-sortable-view' //sort uploaded image..
    ]).run(function ($FB) {//facebook share...should we move this somewhere else?

        $FB.init(( typeof webportalConfiguration.__facebookKey !== 'undefined' && webportalConfiguration.__facebookKey.length > 0) ? webportalConfiguration.__facebookKey : '726557050763228'); //726557050763228 is remax key..whops!

    });

// webportalConfigurationJson is defined in WFactory::getHelper()->updateWebportalConfigurationJavascript()


//app.factory('ngDroplet',function(){
//    return angular.module('ngDroplet', []);
//});


// singleton helper
app.factory('portal', ['uri', function (uri) {
    var portal = {};


    portal.getLink = function (id) {
        return uri.getBase() + langHalf + '/property/' + id;
    };

    portal.getVideoLink = function (id) {
        return uri.getBase() + langHalf + '/video/' + id;
    };

    portal.getPropertiesSearch = function () {
        return documentRoot + '';
        //   return documentRoot + 'api/v1/properties/search';
    };

    portal.getApiPropertiesSearch = function () {
        return documentRoot + '?option=com_webportal&controller=api&task=service&version=v1&format=raw&service=properties&data=search';
        //   return documentRoot + 'api/v1/properties/search';
    };

    portal.getApiPropertyDetails = function (id) {
        // return documentRoot + 'api/v1/property/getDetail/' + id;
        return documentRoot + '?option=com_webportal&controller=api&task=service&version=v1&format=raw&service=property&data=getDetail&propertyId=' + id;
    };

    portal.getApiLocality = function (id) {
        //return documentRoot + 'api/v1/contacts/sendMailToAgent';
        return documentRoot + '?option=com_webportal&controller=api&task=service&version=v1&format=raw&service=locality&data=getLocalAttractions&propertyId=' + id;
    };

    portal.getApiAddressPostalTree = function () {
        //return documentRoot + 'api/v1/address/postalCodeTree';
        return documentRoot + '?option=com_webportal&controller=api&task=service&version=v1&format=raw&service=address&data=postalCodeTree';
    };

    portal.getApiAddressCurrency = function () {
        //return documentRoot + 'api/v1/address/postalCodeTree';
        return documentRoot + '?option=com_webportal&controller=api&task=service&version=v1&format=raw&service=currency&data=getCurrency';
    };

    portal.getApiAddressSetCurrency = function () {
        //return documentRoot + 'api/v1/address/postalCodeTree';
        return documentRoot + '?option=com_webportal&controller=api&task=service&version=v1&format=raw&service=currency&data=setPreferredCurrency';
    };

    portal.getApiAddressConvertCurrency = function () {
        //return documentRoot + 'api/v1/address/postalCodeTree';
        return documentRoot + '?option=com_webportal&controller=api&task=service&version=v1&format=raw&service=currency&data=convertCurrency';
    };

    portal.getApiSearchOfficeByLocation = function () {
        //return documentRoot + 'api/v1/address/postalCodeTree';
        return documentRoot + '?option=com_webportal&controller=api&task=service&version=v1&format=raw&service=office&data=searchOfficeByLocation';
    };

    portal.getApiSearchLocationByName = function () {
        return documentRoot + '?option=com_webportal&controller=api&task=service&version=v1&format=raw&service=address&data=getLatLangOfGeoLocationByName';
    };

    portal.getOfficeImageUploadLink = function () {
        return documentRoot + '?option=com_webportal&controller=api&task=service&version=v1&format=raw&service=office&data=updateOfficeImage';
    };

    portal.getOfficePublishToggleLink = function () {
        return documentRoot + '?option=com_webportal&controller=api&task=service&version=v1&format=raw&service=office&data=toggleOfficePublish';
    };

    portal.getOfficeLogoUploadLink = function () {
        return documentRoot + '?option=com_webportal&controller=api&task=service&version=v1&format=raw&service=office&data=updateOfficeLogo';
    };

    portal.getOfficeUpdateLink = function () {
        return documentRoot + '?option=com_webportal&controller=api&task=service&version=v1&format=raw&service=office&data=updateOffice';
    };

    portal.getCompanyUpdateLink = function () {
        return documentRoot + '?option=com_webportal&controller=api&task=service&version=v1&format=raw&service=company&data=updateCompany';
    };

    portal.getLog4phpUpdateLink = function () {
        return documentRoot + '?option=com_webportal&controller=api&task=service&version=v1&format=raw&service=configuration&data=updateLog4phpConfiguration';
    };

    portal.getPhpUpdateLink = function () {
        return documentRoot + '?option=com_webportal&controller=api&task=service&version=v1&format=raw&service=configuration&data=updatePhpConfiguration';
    };

    portal.getJsUpdateLink = function () {
        return documentRoot + '?option=com_webportal&controller=api&task=service&version=v1&format=raw&service=configuration&data=updateJsConfiguration';
    };

    portal.getOfficeDeleteLink = function () {
        return documentRoot + '?option=com_webportal&controller=api&task=service&version=v1&format=raw&service=office&data=deleteOffice';
    };

    portal.getApiSearchAgent = function () {
        //return documentRoot + 'api/v1/address/postalCodeTree';
        return documentRoot + '?option=com_webportal&controller=api&task=service&version=v1&format=raw&service=agents&data=getSearchAgents';
    };

    portal.getAgentImageUploadLink = function () {
        return documentRoot + '?option=com_webportal&controller=api&task=service&version=v1&format=raw&service=agent&data=updateAgentImage';
    };

    portal.getAgentPublishToggleLink = function () {
        return documentRoot + '?option=com_webportal&controller=api&task=service&version=v1&format=raw&service=agent&data=toggleAgentPublish';
    };

    portal.getAgentUpdateLink = function () {
        return documentRoot + '?option=com_webportal&controller=api&task=service&version=v1&format=raw&service=agent&data=updateAgent';
    };

    portal.getAgentDeleteLink = function () {
        return documentRoot + '?option=com_webportal&controller=api&task=service&version=v1&format=raw&service=agent&data=deleteAgent';
    };


    portal.getApiXmlSearch = function () {
        return documentRoot + '?option=com_webportal&controller=api&task=service&version=v1&format=raw&service=senttoweb&data=searchSendToWebXml';
    };

    portal.resendSentToWebXml = function () {
        return documentRoot + '?option=com_webportal&controller=api&task=service&version=v1&format=raw&service=senttoweb&data=resendSentToWebXml';
    };

    portal.getPropertyPublishToggleLink = function () {
        return documentRoot + '?option=com_webportal&controller=api&task=service&version=v1&format=raw&service=property&data=togglePropertyPublish';
    };

    portal.getPropertyUpdateLink = function () {
        return documentRoot + '?option=com_webportal&controller=api&task=service&version=v1&format=raw&service=property&data=updateProperty';
    };

    portal.getPropertyDeleteLink = function () {
        return documentRoot + '?option=com_webportal&controller=api&task=service&version=v1&format=raw&service=property&data=deleteProperty';
    };

    portal.getPropertyMap = function () {
        return portal.getApiLang() + '&file=templates/webportal/ng_templates/properties/map.php';
    };

    portal.getApiPropCategoriesTree = function () {
        // return documentRoot + 'api/v1/address/propCategoriesTree';
        return documentRoot + '?option=com_webportal&controller=api&task=service&version=v1&format=raw&service=address&data=propCategoriesTree';
    };
    portal.getApiContactAgent = function () {
        //return documentRoot + 'api/v1/contacts/sendMailToAgent';
        return documentRoot + '?option=com_webportal&controller=api&task=service&version=v1&format=raw&service=contacts&data=sendMailToAgent';
    };
    portal.getApiContactFriend = function () {
        //return documentRoot + 'api/v1/contacts/sendMailToAgent';
        return documentRoot + '?option=com_webportal&controller=api&task=service&version=v1&format=raw&service=contacts&data=sendPropertyMailToFriend';
    };
    portal.getApiContactDefaultCompany = function () {
        return documentRoot + '?option=com_webportal&controller=api&task=service&version=v1&format=raw&service=contacts&data=sendMailToDefaultCompany';
    };


    portal.getApiLang = function () {
        //if (location.href.indexOf('/th/') != -1) {
        //    return uri.getBase() + 'th/index.php?option=com_webportal&controller=api&task=service&service=lang&data=get';
        //}
        ///home/khan/www/softverk-webportal-remaxth/administrator/components/com_webportal/views/sent2webs/tmpl/xmlFile.php
        return uri.getBase() + langHalf + '/index.php?option=com_webportal&controller=api&task=service&service=lang&data=get';
//      return documentRoot + 'api/v1/lang/get';
    };

    portal.loadScript = function (url) {
        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = url;

        document.body.appendChild(script);
    };

    portal.loadGMap = function (callback) {
        if (typeof google == 'undefined') {

            var key = 'AIzaSyD99Q2tf-txKdcxWQZrqbEZ9JrtFNWoULg';
            //this.loadScript('//maps.googleapis.com/maps/api/js?v=3&sensor=false&libraries=geometry');
            this.loadScript('//maps.googleapis.com/maps/api/js?v=3&libraries=geometry&sensor=false&key=' + key + '&callback=gmaploaded');


            window.gmaploaded = function () {
                if (callback != null && typeof callback == 'function')
                    callback.apply();
            };
        } else {
            if (callback != null && typeof callback == 'function')
                callback.apply();
        }
    };

    portal.defaultMapLatLng = function () {
        if (google && google.maps) {
            //Defaults to Central Bangkok
            console.log('Default lat / lan is : ' + webportalConfiguration.__defaultLat + '  /  ' + webportalConfiguration.__defaultLang);
            return new google.maps.LatLng(webportalConfiguration.__defaultLat, webportalConfiguration.__defaultLang);
        }
        alert('google map not defined !');
        return null;
    };

    portal.makeMapLatLng = function (lat, lng) {
        if (google && google.maps) {
            //Defaults to Central Bangkok
            return new google.maps.LatLng(lat, lng);
        }
        return null;
    };

    // reference to google map object (for maps page)
    portal.map = null;

    portal.localityMarkers = [];

    portal.filterLocalityMarkers = function (type) {
        // console.log("----------Filtering markers ----------- \ngiven type : " + type);
        for (var i = 0; i < portal.localityMarkers.length; i++) {

            console.log("marker type : " + portal.localityMarkers[i].type);

            if (type == "ALL") {
                if (portal.localityMarkers[i].getMap() == null)
                    portal.localityMarkers[i].setMap(portal.map);
                continue;
            }

            if (portal.localityMarkers[i].type == type) {
                if (portal.localityMarkers[i].getMap() == null)
                    portal.localityMarkers[i].setMap(portal.map);
                //  console.log("Showing marker...");
            }
            else {
                portal.localityMarkers[i].setMap(null);
                //   console.log("Hiding marker...");
            }
        }

    };


    //portal.getTravelTimeBetweenPoints = function (fromLat, fromLng, destination, cb) {
    //    var origin = new google.maps.LatLng(fromLat, fromLng); // using google.maps.LatLng class
    //    //var destination = toLat + ', ' + toLng; // using string
    //    console.log('Destinations are : ' + destination);
    //    var directionsService = new google.maps.DirectionsService();
    //    var request = {
    //        origin: origin, // LatLng|string
    //        destination: destination, // LatLng|string
    //        travelMode: google.maps.DirectionsTravelMode.WALKING
    //    };
    //
    //    directionsService.route(request, function (response, status) {
    //        console.log('Got response');
    //        console.log(response);
    //
    //        if (status === 'OK') {
    //
    //            var point = response.routes[0].legs[0];
    //            console.log("Distance between from property to " + destination + " is : " + point.duration.text);
    //            cb(cbItem, point.duration.text);
    //            //$( '#travel_data' ).html( 'Estimated travel time: ' + point.duration.text + ' (' + point.distance.text + ')' );
    //        }
    //    });
    //};

    portal.drawDirection = function (fromLat, fromLng, toLat, toLng, cbObject, cb) {

        if (!portal.directionsService) {
            portal.directionsService = new google.maps.DirectionsService();
        }
        if (!portal.directionsDisplay) {
            portal.directionsDisplay = new google.maps.DirectionsRenderer();
        }


        var start = new google.maps.LatLng(fromLat, fromLng);
        var end = new google.maps.LatLng(toLat, toLng);

        var request = {
            origin: start,
            destination: end,
            travelMode: google.maps.TravelMode.WALKING
        };
        portal.directionsService.route(request, function (response, status) {
            if (status == google.maps.DirectionsStatus.OK) {
                portal.directionsDisplay.setDirections(response);
                portal.directionsDisplay.setOptions({suppressMarkers: true});
                portal.directionsDisplay.setMap(portal.map);

                var point = response.routes[0].legs[0];

                var bounds = new google.maps.LatLngBounds();
                bounds.extend(start);
                bounds.extend(end);
                portal.map.fitBounds(bounds);


                cb(cbObject, point.duration.text);

            } else {
                console.error("Directions Request from " + start.toUrlValue(6) + " to " + end.toUrlValue(6) + " failed: " + status);
            }
        });

    };

    portal.clearDirection = function () {
        portal.directionsDisplay.setMap(null);
    };

    portal.loadLocalityDataOnMap = function (data) {

        //console.log("loading locality data on map : total data : ");
        //console.log(data);

        var iconArray = {
            BUSSTAND: documentRootRaw + 'templates/webportal/images/busstand.png',
            SCHOOL: documentRootRaw + 'templates/webportal/images/school.png',
            AREAOFINTEREST: documentRootRaw + 'templates/webportal/images/areaofinterest.png',
            KINDERGARTEN: documentRootRaw + 'templates/webportal/images/kindergarten.png',
            SKATEBOARDING: documentRootRaw + 'templates/webportal/images/skateboarding.png',
            SKILIFT: documentRootRaw + 'templates/webportal/images/ski.png',
            SPORTSAREA: documentRootRaw + 'templates/webportal/images/sports.png',
        };




        for (var i = 0; i < data.length; i++) {


            var t = data[i];
            //console.log(t);
            var icon = {
                url: iconArray[t.type],
                size: new google.maps.Size(16, 16),
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(8, 8)
            };


            var titleString = t.name + ' ( ' + parseFloat(t.distance).toFixed(1) * 1000 + ' m )';
            //console.log(titleString);


            var marker = null;
            marker = new google.maps.Marker({
                position: new google.maps.LatLng(t.latitude, t.longitude),
                animation: google.maps.Animation.DROP,
                title: titleString,
                icon: icon,
                map: portal.map,
                //infowindow: new google.maps.InfoWindow({
                //    content: titleString // here, 'this' is the marker
                //})

            });
            marker.type = t.type;
            marker.localityObject = t;

            //attachEvent(marker, i);

            marker.addListener('click', function () {
                var object = this.localityObject;
                var myself = this;
                portal.drawDirection(propertyDetails.latitude, propertyDetails.longitude,
                    object.latitude, object.longitude,
                    object,
                    function (item, time) {
                        myself.setTitle(myself.getTitle() + ", " + time);
                        //scope.localityDataValues[item.index].travelTime = time;
                        //scope.$apply();
                        //item.travelTime = time;
                        //console.log('got time : ' + time);
                        //callback();
                    });


            });
            portal.localityMarkers.push(marker);
        }


        /*
         for (var type in data) {

         var icon = iconBus;
         if (type == 'SCHOOL')
         icon = iconSchool;


         for (var i = 0; i < data[type].length; i++) {


         var t = data[type][i];
         var titleString = t.name + ' ( ' + parseFloat(t.distance).toFixed(1) * 1000 + ' m )';
         //console.log(titleString);


         var marker = null;
         marker = new google.maps.Marker({
         position: new google.maps.LatLng(t.latitude, t.longitude),
         animation: google.maps.Animation.DROP,
         title: titleString,
         icon: icon,
         map: portal.map,
         //infowindow: new google.maps.InfoWindow({
         //    content: titleString // here, 'this' is the marker
         //})

         });
         marker.type = type;
         marker.localityObject=t;
         portal.localityMarkers.push(marker);

         //  marker.addListener('click', clickEvent);
         }

         }
         */

    };

    portal.encode_utf8 = function (s) {
        return window.atob(unescape(encodeURIComponent(s)));
    };

    portal.decode_utf8 = function (s) {
        return window.btoa(decodeURIComponent(escape(s)));
    };

    /**
     * if name is provided,it will return null or value for that name
     * if name is not provided,it will retuend ALL the params
     * @param name
     * @returns {*}
     */
    portal.getURLParameter = function (name, href) {
        var hash;
        var vars = [];

        //console.log('Searching for param : ' + name + ' in the url');

        if (!href)
            href = window.location.href;
        var indexOfQuestion = href.indexOf('?');

        if (indexOfQuestion != -1) {

            var hashes = href.slice(indexOfQuestion + 1).split('&');
            for (var i = 0; i < hashes.length; i++) {

                if (typeof name == 'undefined' || name === null)
                    vars.push(hashes[i]);

                else {
                    hash = hashes[i].split('=');
                    if (hash[0] == name)
                        return hash[1].trim();
                }
            }
        }
        if (typeof name == 'undefined' || name == null) {

            return vars.getUnique();
        }
    };


    portal.stringToArray = function (data, splitter) {
        if (!splitter)
            splitter = ',';

        return data.split(splitter);
    };

    portal.isInt = function (value) {
        var er = /^-?[0-9]+$/;

        return er.test(value);
    };

    portal.shouldRestoreSearchFilterFromUrl = function () {

        if (
            typeof webportalConfiguration !== 'undefined'
            && webportalConfiguration.enableRestoreOfSearchPanelFromURLVariables === true

                // do not restore search from url if it is office or agent page
            && typeof window.officeId == 'undefined'
            && typeof window.agentId == 'undefined'
        ) {

            return true;
        }

        return false;

    };

    portal.validateEmail = function (email) {
        var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    };


    /**
     * href is used only during unit testing
     * @param href
     */
    portal.getSearchFilterFromUrl = function ($scope, href) {

        var urlParams = portal.getURLParameter(null, href);

        if (!href)
            href = window.location.href;

        var localFilter = {};

        //$scope.defaultfilter = {
        //    text: '',
        //    type_id: 0,
        //    mode_id: 2,//1 = ALL (residential + commercial )
        //    current_listing_price: [$scope.sliders.current_listing_price.floor, $scope.sliders.current_listing_price.ceiling],
        //    rent_price: [$scope.sliders.rent_price.floor, $scope.sliders.rent_price.ceiling],
        //    total_number_of_rooms: [$scope.sliders.total_number_of_rooms.floor, $scope.sliders.total_number_of_rooms.ceiling],
        //    number_of_bedrooms: [$scope.sliders.number_of_bedrooms.floor, $scope.sliders.number_of_bedrooms.ceiling],
        //    total_area: [$scope.sliders.total_area.floor, $scope.sliders.total_area.ceiling],
        //    rent_total_area: [$scope.sliders.rent_total_area.floor, $scope.sliders.rent_total_area.ceiling],
        //    region_id: '',
        //    city_town_id: '',
        //    zip_code_id: '',
        //    order: 'ORDER_BY_NEWEST_FIRST',
        //    office_id: '',
        //    sale_id: '',
        //    office_name: '',
        //    sale_name: '',
        //    search_key: '',
        //    latitude: '',
        //    longitude: '',
        //    radius: webportalConfiguration.transportationSearchRadius,
        //    transport_line: null,
        //    transport_station: ''
        //};

        for (var j = 0; j < urlParams.length; j++) {
            var tempParam = urlParams[j].split('=');
            var tempKey = tempParam[0];
            var tempValue = tempParam[1];

            console.log('parsing:' + tempKey);
            if (tempKey == 'type') {

                if (tempValue.indexOf('ALL') !== -1)
                    localFilter.type_id = 1;
                if (tempValue.indexOf('SALE') !== -1)
                    localFilter.type_id = 2;
                if (tempValue.indexOf('RENT') !== -1)
                    localFilter.type_id = 3;

                tempValue = portal.stringToArray(tempValue);

                var propertyCats = [];
                for (var i = 0; i < tempValue.length; i++) {

                    if (portal.isInt(tempValue[i])) {
                        propertyCats.push(tempValue[i]);
                    }

                }

                localFilter.category_id = propertyCats;
            }
            else if (tempKey == 'price') {
                tempValue = portal.stringToArray(tempValue);
                var pricetype = 'current_listing_price';
                if (href.indexOf(',RENT') !== -1)
                    pricetype = 'rent_price';

                var price = [];
                for (var i = 0; i < tempValue.length; i++) {
                    var priceValue = tempValue[i];
                    if (portal.isInt(priceValue)) {

                        priceValue = parseInt(priceValue);

                        if (priceValue == 0) {
                            if (i == 0)//min
                            {
                                if (pricetype == 'current_listing_price')
                                    priceValue = $scope.sliders.current_listing_price.floor;
                                if (pricetype == 'rent_price')
                                    priceValue = $scope.sliders.rent_price.floor;
                            }
                            if (i == 1)//max
                            {
                                if (pricetype == 'current_listing_price')
                                    priceValue = $scope.sliders.current_listing_price.ceiling;
                                if (pricetype == 'rent_price')
                                    priceValue = $scope.sliders.rent_price.ceiling;
                            }

                        }
                        else {

                            if (pricetype == 'current_listing_price')
                                priceValue = $scope.current_listing_price.indexOf(priceValue);
                            if (pricetype == 'rent_price')
                                priceValue = $scope.rent_price.indexOf(priceValue);

                        }
                        price.push(priceValue);
                    }

                }
                if (pricetype == 'rent_price')
                    localFilter.rent_price = price;
                else
                    localFilter.current_listing_price = price;


            }
            else if (tempKey == 'order') {
                localFilter.order = tempValue;
            }
            else if (tempKey == 'bedrooms') {
                tempValue = portal.stringToArray(tempValue);
                if (tempValue[0] == '0') {
                    tempValue[0] = $scope.sliders.number_of_bedrooms.floor;
                }
                if (tempValue[1] == '0') {
                    tempValue[1] = $scope.sliders.number_of_bedrooms.ceiling;
                }

                localFilter.number_of_bedrooms = tempValue;
            }
            else if (tempKey == 'rooms') {
                tempValue = portal.stringToArray(tempValue);
                if (tempValue[0] == '0') {
                    tempValue[0] = $scope.sliders.total_number_of_rooms.floor;
                }
                if (tempValue[1] == '0') {
                    tempValue[1] = $scope.sliders.total_number_of_rooms.ceiling;
                }

                localFilter.total_number_of_rooms = tempValue;
            }
            else if (tempKey == 'town') {
                //  tempValue = portal.stringToArray(tempValue);
                localFilter.city_town_id = tempValue;
            }
            else if (tempKey == 'region') {
                // tempValue = portal.stringToArray(tempValue);
                localFilter.region_id = tempValue;
            }
            else if (tempKey == 'lat') {
                localFilter.latitude = tempValue;
            }
            else if (tempKey == 'lan') {
                localFilter.longitude = tempValue;
            }
            else if (tempKey == 'line') {
                localFilter.transport_line = tempValue;
            }
            else if (tempKey == 'station') {
                localFilter.transport_station = tempValue;
            }
            else if (tempKey == 'office') {
                localFilter.office_id = tempValue;
            }
            else if (tempKey == 'agent') {
                localFilter.sale_id = tempValue;
            }
            else if (tempKey == 'text') {
                localFilter.text = decodeURIComponent(tempValue);
            }

            /*
             * loan80: '',
             garage: '',
             elevator: '',
             new_today: '',
             new_this_week: '',
             * */
            else if (tempKey == 'loan80') {
                localFilter.loan80 = (tempValue === "YES") ? true : false;
            }
            else if (tempKey == 'garage') {
                localFilter.garage = (tempValue === "YES") ? true : false;
            }
            else if (tempKey == 'elevator') {
                localFilter.elevator = (tempValue === "YES") ? true : false;
            }
            else if (tempKey == 'new_today') {
                localFilter.new_today = (tempValue === "YES") ? true : false;
            }
            else if (tempKey == 'new_this_week') {
                localFilter.new_this_week = (tempValue === "YES") ? true : false;
            }
            else if (tempKey == 'swapping') {
                localFilter.swapping = (tempValue === "YES") ? true : false;
            }
            else if (tempKey == 'featured') {
                localFilter.is_featured = (tempValue === "YES") ? true : false;
            }
        }


        return localFilter;

    };

    portal.createDatePicker = function () {
        jQuery("input[name='movein']").datepicker();
    };

    portal.setScope = function ($scope) {
        portal.$scope = $scope;
    };

    portal.updateUrlHashParameter = function (name, value) {


        var urlParam = portal.getURLParameter();
        var nameFound = false;
        for (var i = 0; i < urlParam.length; i++) {
            hash = urlParam[i].split('=');
            if (hash[0] == name) {

                if (value == '') {
                    urlParam.splice(i, 1);
                }
                else
                    urlParam[i] = name + '=' + value;

                nameFound = true;
            }

        }

        if (!nameFound && value != '') {
            urlParam.push(name + '=' + value);
        }
        if (typeof urlParam == 'array')
            urlParam = urlParam.getUnique();

        if (urlParam.length > 0) {
            var hash = "?" + urlParam.join('&');
            window.location.hash = hash;
        }
        else
            window.location.hash = '';

        if (typeof  portal.$scope !== 'undefined')
            portal.$scope.$broadcast('hashUpdated', window.location.hash);

    };


    return portal;
}])
;


// singleton events manager
app.factory('events', [function () {
    return {
        __events: {},
        on: function (event, func) {
            console.log('on: ' + event);
            if (typeof this.__events[event] != 'Array')
                this.__events[event] = [];

            this.__events[event].push(func);
        },
        fire: function (event) {
            console.log('fire: ' + event);
            if (typeof this.__events[event] != 'undefined') {
                for (var i = 0; i < this.__events[event].length; i++) {
                    if (typeof this.__events[event][i] == 'function') {
                        console.log('bang: ' + event);
                        this.__events[event][i].apply();
                    }
                }
            }
        }
    };
}]);

app.directive('ngConfirmClick', [
    function () {
        return {
            link: function (scope, element, attr) {
                var msg = attr.ngConfirmClick || "Are you sure?";
                var clickAction = attr.confirmedClick;
                element.bind('click', function (event) {
                    if (window.confirm(msg)) {
                        scope.$eval(clickAction)
                    }
                });
            }
        };
    }]);


Array.prototype.getUnique = function () {
    var u = {}, a = [];
    for (var i = 0, l = this.length; i < l; ++i) {
        if (u.hasOwnProperty(this[i])) {
            continue;
        }
        a.push(this[i]);
        u[this[i]] = 1;
    }
    return a;
}

String.prototype.toUnicode = function () {
    var result = "";
    for (var i = 0; i < this.length; i++) {
        result += "\\u" + ("000" + this[i].charCodeAt(0).toString(16)).substr(-4);
    }
    return result;
};

http://localhost/softverk-webportal-generic/is/fjolbyli-vallargrund-221-hafnarfjoreur-10438?hash=

//////////// utf 8 base 64 encode decode

    (function (global) {
        'use strict';

        var log = function () {
            },
            padding = '=',
            chrTable = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' +
                '0123456789+/',
            binTable = [
                -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
                -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
                -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 62, -1, -1, -1, 63,
                52, 53, 54, 55, 56, 57, 58, 59, 60, 61, -1, -1, -1, 0, -1, -1,
                -1, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14,
                15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, -1, -1, -1, -1, -1,
                -1, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40,
                41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, -1, -1, -1, -1, -1
            ];

        if (global.console && global.console.log) {
            log = function (message) {
                global.console.log(message);
            };
        }

        // internal helpers //////////////////////////////////////////////////////////

        function utf8Encode(str) {
            var bytes = [], offset = 0, length, char;

            str = encodeURI(str);
            length = str.length;

            while (offset < length) {
                char = str[offset];
                offset += 1;

                if ('%' !== char) {
                    bytes.push(char.charCodeAt(0));
                } else {
                    char = str[offset] + str[offset + 1];
                    bytes.push(parseInt(char, 16));
                    offset += 2;
                }
            }

            return bytes;
        }

        function utf8Decode(bytes) {
            var chars = [], offset = 0, length = bytes.length, c, c2, c3;

            while (offset < length) {
                c = bytes[offset];
                c2 = bytes[offset + 1];
                c3 = bytes[offset + 2];

                if (128 > c) {
                    chars.push(String.fromCharCode(c));
                    offset += 1;
                } else if (191 < c && c < 224) {
                    chars.push(String.fromCharCode(((c & 31) << 6) | (c2 & 63)));
                    offset += 2;
                } else {
                    chars.push(String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63)));
                    offset += 3;
                }
            }

            return chars.join('');
        }

        // public api ////////////////////////////////////////////////////////////////

        function encode(str) {
            var result = '',
                bytes = utf8Encode(str),
                length = bytes.length,
                i;

            // Convert every three bytes to 4 ascii characters.
            for (i = 0; i < (length - 2); i += 3) {
                result += chrTable[bytes[i] >> 2];
                result += chrTable[((bytes[i] & 0x03) << 4) + (bytes[i + 1] >> 4)];
                result += chrTable[((bytes[i + 1] & 0x0f) << 2) + (bytes[i + 2] >> 6)];
                result += chrTable[bytes[i + 2] & 0x3f];
            }

            // Convert the remaining 1 or 2 bytes, pad out to 4 characters.
            if (length % 3) {
                i = length - (length % 3);
                result += chrTable[bytes[i] >> 2];
                if ((length % 3) === 2) {
                    result += chrTable[((bytes[i] & 0x03) << 4) + (bytes[i + 1] >> 4)];
                    result += chrTable[(bytes[i + 1] & 0x0f) << 2];
                    result += padding;
                } else {
                    result += chrTable[(bytes[i] & 0x03) << 4];
                    result += padding + padding;
                }
            }

            return result;
        }

        function decode(data) {
            var value, code, idx = 0,
                bytes = [],
                leftbits = 0, // number of bits decoded, but yet to be appended
                leftdata = 0; // bits decoded, but yet to be appended

            // Convert one by one.
            for (idx = 0; idx < data.length; idx++) {
                code = data.charCodeAt(idx);
                value = binTable[code & 0x7F];

                if (-1 === value) {
                    // Skip illegal characters and whitespace
                    log("WARN: Illegal characters (code=" + code + ") in position " + idx);
                } else {
                    // Collect data into leftdata, update bitcount
                    leftdata = (leftdata << 6) | value;
                    leftbits += 6;

                    // If we have 8 or more bits, append 8 bits to the result
                    if (leftbits >= 8) {
                        leftbits -= 8;
                        // Append if not padding.
                        if (padding !== data.charAt(idx)) {
                            bytes.push((leftdata >> leftbits) & 0xFF);
                        }
                        leftdata &= (1 << leftbits) - 1;
                    }
                }
            }

            // If there are any bits left, the base64 string was corrupted
            if (leftbits) {
                log("ERROR: Corrupted base64 string");
                return null;
            }

            return utf8Decode(bytes);
        }

        global.base64 = {encode: encode, decode: decode};
    }(window));




