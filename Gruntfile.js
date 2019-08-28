'use strict';

module.exports = function (grunt) {
    var settings = grunt.file.readJSON('Grunt-settings.json');
    var mozjpeg = require('imagemin-mozjpeg'); //required for imagemin plugin

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        uglify: {

            all: {
                options: {
                    sourceMap: false,
                    mangle: false // otherwise the fucking facebook share module stops working..we should rewrite that shit !
                },
                files: {

                    'assets/js/jquery.min.js': [
                        'assets/bower_components/jquery/dist/jquery.js',
                        'assets/bower_components/flexslider/jquery.flexslider-min.js',
                        'assets/bower_components/jquery-ui/jquery-ui.min.js',
                        'assets/bower_components/jquery-validation/dist/jquery.validate.min.js',
                        'assets/bower_components/jquery-validation/dist/additional-methods.min.js'
                    ],

                    'assets/js/angular.min.js': [
                        'assets/bower_components/angular/angular.js',
                        'assets/bower_components/angular-touch/angular-touch.min.js',
                        'assets/bower_components/venturocket-angular-slider/build/angular-slider.min.js',
                        'assets/bower_components/angular-flexslider/angular-flexslider.js',
                        'assets/bower_components/angular-local-storage/dist/angular-local-storage.min.js',
                        //'assets/bower_components/isteven-angular-multiselect/angular-multi-select.js',
                        'assets/bower_components/angular-socialshare/angular-socialshare.min.js',
                        'assets/bower_components/angular-animate/angular-animate.min.js',
                        'assets/bower_components/angular-round/release/angular-round.js',
                        
                        'assets/bower_components/ngDialog/js/ngDialog.min.js',
                        'assets/bower_components/ng-tags-input/ng-tags-input.min.js'

                    ],

                    'assets/js/foundation.min.js': [
                        'assets/bower_components/foundation/js/foundation.min.js',
                        'assets/bower_components/foundation/js/foundation/foundation.equalizer.js'
                    ],
                    
                    'assets/js/webportal.min.js': [

                        'webportal.configuration.js',
                        
                        'assets/bower_components/raphael/raphael.min.js',
                        'assets/bower_components/owl.carousel/dist/owl.carousel.min.js',

                        'assets/bower_components/dropzone/dist/min/dropzone.min.js',
                        'assets/bower_components/owl.carousel/dist/owl.carousel.min.js',
                        'assets/bower_components/bootstrap/dist/js/bootstrap.min.js',
                        'assets/bower_components/bootstrap-form-helpers/dist/js/bootstrap-formhelpers.min.js',
                        'assets/bower_components/gsap/src/minified/TweenMax.min.js',
                        
                        'templates/webportal/js/helper.js',
                        'templates/webportal/js/pager.js',
                        'templates/webportal/js/search.js',
                        'templates/webportal/js/search_agent.js',
                        'templates/webportal/js/addproperty.js',
                        'templates/webportal/js/image_upload.js',
                        'templates/webportal/js/contact.js',
                        'templates/webportal/js/map.js',

                        //'templates/webportal/js/property_list.js',
                        'templates/webportal/js/markerclusterer.js',
                        'templates/webportal/js/property_map.js',
                        'templates/webportal/js/property_details.js',

                        'templates/webportal/js/btsmap.js',

                        'templates/webportal/js/viewportchecker.js',
                        'templates/webportal/js/video.js',
                        'templates/webportal/js/featured_slideshow.js',
                        'templates/webportal/js/backtotop.js',
                        'templates/webportal/js/login.js',

                        'assets/bower_components/isteven-angular-multiselect/angular-multi-select.js'
                    ],
                    
                    'assets/js/webportal.admin.min.js': [
                        'administrator/components/com_webportal/assets/properties.admin.js',
                        'administrator/components/com_webportal/assets/property.admin.js',
                        'administrator/components/com_webportal/assets/office.admin.js',
                        'administrator/components/com_webportal/assets/agent.admin.js',
                        'administrator/components/com_webportal/assets/company.admin.js',
                        'administrator/components/com_webportal/assets/config.admin.js',
                        'administrator/components/com_webportal/assets/xml.js',
                        'administrator/components/com_webportal/assets/admin.js',
                    ]
                }
            }
        },

        // 'sass-convert': {
        //     /**
        //      * NOTE: make sure to change the images to something like this:
        //      * background-image: url("images/ui-icons_444444_256x240.png") --> background-image: url($template-path +"images/ui-icons_444444_256x240.png");
        //      * */
        //     options: {
        //         from: 'css',
        //         to: 'scss'
        //     },
        //     files: { ///home/khan/www/softverk-webportal-remaxth/assets/bower_components/jquery-ui/themes/base
        //         cwd: 'assets/bower_components/jquery-ui/themes/base/',
        //         src: 'jquery-ui.css',
        //         filePrefix: '_',
        //         dest: settings.template.path + 'scss/'
        //     }

        // },

        sass: {
            options: {
                includePaths: [
                    'assets/bower_components/font-awesome/scss',
                    'assets/bower_components/foundation/scss',
                    'assets/bower_components/angular-socialshare',
                    ///home/khan/www/softverk-webportal-remaxth/assets/bower_components/jquery-ui/themes
                ]
            },
            dist: {
                options: {
                    outputStyle: 'compressed', // nested or compressed
                    sourceComments: false,
                    sourceMap: 'assets/css/app.min.map',
                    // outFile: 'assets/css/app.min.map',
                    sourceMapRoot: false,
                    sourceMapContents: false
                },
                files: {
                    'assets/css/app_style_a.css': [
                        settings.template.path + 'scss/app_style_a.scss'
                    ],
                    'assets/css/app_style_b.css': [
                        settings.template.path + 'scss/app_style_b.scss'
                    ],
                    'assets/css/print.css': 'templates/generic/scss/print.scss'
                }
            }
        },

        cssmin: {
            options: {
            sourceMap: false,
            shorthandCompacting: true,
            // roundingPrecision: -1,
            keepSpecialComments: 0
        },
        minify: {
            expand: true,
            cwd: 'assets/css/',
            src: ['*.css', '!*.min.css'],
            dest: 'assets/css/',
            ext: '.min.css'
            }
        },

        /*-----------------------------*/
        imagemin: {
            target: {
                options: {
                    optimizationLevel: 3,
                    use: [mozjpeg()]
                },
                files: [{
                    expand: true,
                    cwd: settings.template.path + 'images_source/',
                    src: ['**/*.{jpg,gif,svg,jpeg,png}'],
                    dest: settings.template.path + 'images/'
                }]
            }
        },

        sync: {
            main: {
                 files: [{
                    expand: false,
                    cwd: 'assets/bower_components/font-awesome/fonts/',
                    src: ['**'],
                    dest: 'assets/fonts/'
                }],
                verbose: true,
            }
        },

        watch: {
            grunt: {
                files: ['Gruntfile.js'],
                tasks: ['sass', 'cssmin']
            },
            sass: {
                files: settings.template.path + 'scss/*.scss',
                tasks: ['sass', 'cssmin']
            },
            livereload: {
                // files: ['assets/js/**/*.js', 'assets/css/**/*.css', settings.template.path + 'images/**/*.{jpg,gif,svg,jpeg,png}'],
                files: ['assets/js/**/*.js', 'assets/css/**/*.css'],
                options: {
                    livereload: true
                }
            }
        }

    })

    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-sass');
    grunt.loadNpmTasks('grunt-contrib-imagemin');
    grunt.loadNpmTasks('grunt-newer');
    grunt.loadNpmTasks('grunt-sync');


    grunt.registerTask('image',['imagemin']); 

    // newer:imagemin:target
    //removed image min for now

    grunt.registerTask('compile-sass', ['sass']);
    grunt.registerTask('build', ['compile-sass', 'uglify', 'sass', 'cssmin', 'sync']);
    grunt.registerTask('default', ['compile-sass', 'uglify', 'sass', 'cssmin'], 'watch');

};
