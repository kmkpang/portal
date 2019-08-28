/**
 * Created by khan on 12/23/2014.
 */


angular.module('webportal').factory('uri', function () {

    var uri = {};

    uri.base = '';

    uri.setBase = function (base) {
        uri.base = base;
    };


    uri.getBase = function () {
        return uri.base;
    }

    return uri;
});
