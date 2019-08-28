/**
 * Created by Lian on 7/31/14.
 */

angular.module('webportal') // Used in property details
    .controller('ContactCtrl', ['$scope', '$http', 'ngDialog', 'portal', function ($scope, $http, ngDialog, portal) {
        $scope.sent = false;
        $scope.setAgentEmail = function (email) {
            $scope.agent_email = email;
        };


        $scope.submitSend2Friend = function () {


            // validation
            if (!$scope.to_email) {
                $scope.error = "Friend's email is required";
                return;
            } else {
                var emails = $scope.to_email.split(',');
                for (var i = 0; i < emails.length; i++) {
                    var e = emails[i].trim();
                    if (e.length > 0) {
                        if (!portal.validateEmail(e)) {
                            $scope.error = emails[i] + ' is not valid';
                            return;
                        }
                    }
                }
            }

            // return;


            $scope.sending = true;
            $scope.error = '';
            $scope.sent = false;

            var sendToFriendData = {
                from_email: $scope.from_email,
                from_name: $scope.from_name,
                to_email: $scope.to_email,
                message: $scope.message,
                property_id: window.current_page === 'list' ? portal.propertiesForEmail : portal.propertyDetail.property_id
            };


            console.log(sendToFriendData);

            $http({
                method: 'POST',
                url: portal.getApiContactFriend(),
                data: sendToFriendData
            })
                .success(function (data) {
                    $scope.sending = false;
                    $scope.sent = true;
                })
                .error(function () {
                    $scope.sending = false;
                    $scope.error = 'There was an error sending your message. Please check the form and try again.';
                    console.log('Error sending mail');
                });


        };

        $scope.submitContact = function () {

            $scope.agentmessage = portal.propertyDetail.region_name + ", "
                + portal.propertyDetail.city_town_name + ", "
                + portal.propertyDetail.address + " - "
                + " PROPERTY ID: " + portal.propertyDetail.reg_id + " - "
                + "<a href='" + window.location.href + "'>" + window.location.href + "</a>";


            //$scope.agentmessage = $($scope.agentmessage).text();

            // validation
            if (!$scope.name) {
                $scope.error = 'Name is required.';
                return;
            }

            if (!$scope.phone) {
                $scope.error = 'Phone is required.';
                return;
            }

            if (!$scope.email) {
                $scope.error = 'Email is required.';
                return;
            }

            if (!$scope.message) {
                $scope.error = 'Your message is required.';
                return;
            }

            if (!$scope.agent_email) {
                $scope.error = 'No agent email.';
                return;
            }

            $scope.sending = true;
            $scope.error = '';

            var data = {
                contact_name: $scope.name,
                contact_phone: $scope.phone,
                contact_email: $scope.email,
                message: $scope.message,
                agent_message: $scope.agentmessage,
                agent_email: $scope.agent_email,
                agent_id: portal.propertyDetail.sale_id,
            };

            $http({method: 'POST', url: portal.getApiContactAgent(), data: data})
                .success(function (data) {
                    $scope.sending = false;
                    $scope.sent = true;
                })
                .error(function () {
                    $scope.sending = false;
                    $scope.error = 'There was an error sending your message. Please check the form and try again.';
                    console.log('Error sending mail');
                });
        };
    }]);
angular.module('webportal') //used in contact us page and front page
    .controller('ContactForm', ['$scope', '$http', 'ngDialog', 'portal', function ($scope, $http, ngDialog, portal) {

        $scope.submitContact = false;

        $scope.submitContact = function () {

            // validation
            if (!$scope.name) {
                $scope.error = 'Name is required.';
                return;
            }

            if (!$scope.phone) {
                $scope.error = 'Phone is required.';
                return;
            }

            if (!$scope.email) {
                $scope.error = 'Email is required.';
                return;
            }

            if (!$scope.message) {
                $scope.error = 'Your message is required.';
                return;
            }

            $scope.sending = true;
            $scope.error = '';


            var data = {
                contact_name: $scope.name,
                contact_phone: $scope.phone,
                contact_email: $scope.email,
                contact_message: $scope.message
            };

            $http({method: 'POST', url: portal.getApiContactDefaultCompany(), data: data})
                .success(function (data) {
                    $scope.sending = false;
                    $scope.sent = true;
                })
                .error(function () {
                    $scope.sending = false;
                    $scope.error = 'There was an error sending your message. Please check the form and try again.';
                    console.log('Error sending mail');
                });
        };
    }]);
