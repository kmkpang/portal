/**
 * Created by Lian on 6/17/14.
 */

angular.module('webportal')
    .factory('pager', ['$http', '$log', function ($http, $log) {
        var pager = {};

        /** Current page */
        pager.page = 0;

        pager.disableNav = false;

        /** Page size (max number of items on each page) */
        pager.size = 25;

        pager.available_size = [10, 25, 50, 100];


        /** Maxium visible pages*/
        pager.range = 5;

        pager.previewSize = 10;


        pager.setCurrentPage = function (page) {

            $log.log('setting page to ' + page);
            this.page = page;
        };

        pager.setSize = function (size) {
            this.size = size;
        };

        pager.setTotalPages = function (total) {
            $log.log('setting total pages to ' + total);
            $log.log('current page is ' + this.page);


            this.totalpages = total;


            if (total == 0) return;

            var pager_start = this.page - Math.floor(this.range / 2);
            pager_start = Math.max(pager_start, 0); // cannot be below 0

            var pager_end = pager_start + this.range;
            pager_end = Math.min(pager_end, total); // cannot be more than total

            // build page range
            this.pagenumbers = [];
            for (var i = pager_start; i < pager_end; i++) {
                this.pagenumbers.push(i);
            }

        };

        pager.query = function (url, data, callback) {
            this.success = callback;
            this.url = url;
            this.data = data;
            var _this = this;

            $log.log('Sending page is: ' + this.page);


            if (this.size != -1) {
                // prepare pagination for model
                this.data.limit_start = this.page * this.size;
                this.data.limit_length = this.size;
            }

            if (this.onloading)
                this.onloading();

            _this.disableNav = true;

            //console.log(url);
            //console.log(data);

            var httprequest = $http(
                {
                    method: 'POST',
                    data: data,
                    url: url
                });


            httprequest
                .success(function (data) {
                    if (data.length) {
                        var totalPages = data[0].pagination_total_results;
                        if (typeof totalPages !== 'undefined') {
                            var total = Math.ceil(data[0].pagination_total_results / pager.size);

                            if (pager.page >= total)
                                pager.setCurrentPage(total - 1);

                            pager.setTotalPages(total);

                            // showing x - y of z.. kinda thing
                            pager.totalResults = data[0].pagination_total_results;
                            if (data.length) {
                                pager.showing_from = pager.page * pager.size + 1;
                                pager.showing_to = pager.showing_from + data.length - 1;
                            }
                        }
                    }
                })
                .success(this.success) // run callback
                .then(function () {
                    _this.disableNav = false;
                });

            // console.log(httprequest);
            return httprequest;
        };

        pager.nextPage = function () {
            this.setPage(this.page + 1);
        };

        pager.prevPage = function () {
            this.setPage(this.page - 1);
        };

        pager.setPage = function (page) {

            // $log.log("Current page index is: " + page);

            if (this.disableNav) return;

            if (page == this.page) return; //if same page, don't trigger reloading.

            if (page >= 0 && page <= this.totalpages) {
                this.page = page;
            } else {
                return; //prevent reloading if min/max has been reached
            }

            // reload data
            if (this.onreload != null)
                this.onreload();

            this.query(this.url, this.data, this.success)
        };

        return pager;
    }])
    .directive('wpPager', ['uri', function (uri) {
        return {
            restrict: 'E',
            templateUrl: uri.getBase() + 'templates/webportal/ng_templates/pager/controls.html'
        };
    }]);
