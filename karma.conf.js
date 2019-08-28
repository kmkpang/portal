// Karma configuration
// Generated on Sun Dec 21 2014 12:37:01 GMT+0700 (ICT)

module.exports = function (config) {
    config.set({

        // base path that will be used to resolve all patterns (eg. files, exclude)
        basePath: '',


        // frameworks to use
        // available frameworks: https://npmjs.org/browse/keyword/karma-adapter
        frameworks: ['jasmine'],


        // list of files / patterns to load in the browser
        files: [


            'webportal.configuration.js',

            'assets/bower_components/jquery/dist/jquery.js',
            'assets/bower_components/flexslider/jquery.flexslider.js',

            'assets/bower_components/angular/angular.js',
            'assets/bower_components/angular-mocks/angular-mocks.js',

            'assets/bower_components/angular-touch/angular-touch.js',
            'assets/bower_components/venturocket-angular-slider/build/angular-slider.js',
            'assets/bower_components/angular-flexslider/angular-flexslider.js',
            'assets/bower_components/angular-local-storage/dist/angular-local-storage.js',
            'assets/bower_components/isteven-angular-multiselect/angular-multi-select.js',
            'assets/bower_components/angular-socialshare/angular-socialshare.js',


            'assets/bower_components/foundation/js/foundation.min.js',
            'assets/bower_components/foundation/js/foundation/foundation.equalizer.js',
            'assets/bower_components/raphael/raphael.js',


            'templates/webportal/js/helper.js',

            'tests/portaltestjs/uri.js',


            'templates/webportal/js/pager.js',
            'templates/webportal/js/search.js',
            'templates/webportal/js/contact.js',
            'templates/webportal/js/map.js',

            'templates/webportal/js/property_list.js',
            'templates/webportal/js/property_details.js',
            'templates/webportal/js/property_map.js',

            'templates/webportal/js/btsmap.js',


            'tests/portaltestjs/portal.test.js'

        ],


        // list of files to exclude
        exclude: [
            '**/*.html'
        ],


        // preprocess matching files before serving them to the browser
        // available preprocessors: https://npmjs.org/browse/keyword/karma-preprocessor
        preprocessors: {},


        // test results reporter to use
        // possible values: 'dots', 'progress'
        // available reporters: https://npmjs.org/browse/keyword/karma-reporter
        reporters: ['progress'],


        // web server port
        port: 9876,


        // enable / disable colors in the output (reporters and logs)
        colors: true,


        // level of logging
        // possible values: config.LOG_DISABLE || config.LOG_ERROR || config.LOG_WARN || config.LOG_INFO || config.LOG_DEBUG
        logLevel: config.LOG_INFO,


        // enable / disable watching file and executing tests whenever any file changes
        autoWatch: true,


        // start these browsers
        // available browser launchers: https://npmjs.org/browse/keyword/karma-launcher
        browsers: ['Chrome'],


        // Continuous Integration mode
        // if true, Karma captures browsers, runs the tests and exits
        singleRun: false
    });
};
