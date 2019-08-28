/**
 * Created by khan on 12/21/14.
 */
describe('Unit: Factory Test', function () {

    beforeEach(function () {

        module('webportal');
    });


    it("is testing if setting base works, as it is used as injector in portal factory", inject(function (uri) {

        var uriBase = 'teting base';
        uri.setBase(uriBase);
        expect(uri.getBase()).toBe(uriBase);

    }));


    it("is testing if portal.getURLParameter works", inject(function (uri, portal) {

        var uriBase = 'https://www.youtube.com/watch?v=V7NXA9u6At4';
        uri.setBase(uriBase);

        var result = portal.getURLParameter(null, uriBase);

        expect(result.length).toBeGreaterThan(0);

    }));

    it('is testing if search filter can be returned properly from ', inject(function (uri, portal) {

        var uriBase = 'http://localhost/softverk-webportal-remaxth/properties-search/list#?page=1&key=&type=101,SALE&order=ORDER_BY_NEWEST_FIRST&bedrooms=0,0&region=10';

        var result = portal.getSearchFilterFromUrl(scope, uriBase);

        expect(result).toBeDefined();


    }));


})




