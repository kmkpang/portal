<?php

if ($doc === null)
    $doc = JFactory::getDocument();

if (__COMPILE_JAVASCRIPT === false) {


    $doc->addScript(JUri::root() . 'webportal.configuration.js');

    $doc->addScript(JUri::root() . 'assets/bower_components/jquery/dist/jquery.js');
    $doc->addScript(JUri::root() . 'assets/bower_components/flexslider/jquery.flexslider.js');

    $doc->addScript(JUri::root() . 'assets/bower_components/angular/angular.js');
    $doc->addScript(JUri::root() . 'assets/bower_components/angular-touch/angular-touch.js');
    $doc->addScript(JUri::root() . 'assets/bower_components/venturocket-angular-slider/build/angular-slider.js');
    $doc->addScript(JUri::root() . 'assets/bower_components/angular-flexslider/angular-flexslider.js');
    $doc->addScript(JUri::root() . 'assets/bower_components/angular-local-storage/dist/angular-local-storage.js');
    $doc->addScript(JUri::root() . 'assets/bower_components/isteven-angular-multiselect/angular-multi-select.js');
    $doc->addScript(JUri::root() . 'assets/bower_components/angular-socialshare/angular-socialshare.js');

    $doc->addScript(JUri::root() . 'assets/bower_components/angular-animate/angular-animate.min.js');

    $doc->addScript(JUri::root() . 'assets/bower_components/angular-round/release/angular-round.js');
    
    $doc->addScript(JUri::root() . 'assets/bower_components/raphael/raphael.min.js');
    $doc->addScript(JUri::root() . 'assets/bower_components/jquery-ui/jquery-ui.min.js');

    $doc->addScript(JUri::root() . 'assets/bower_components/foundation/js/foundation.min.js');
    $doc->addScript(JUri::root() . 'assets/bower_components/foundation/js/foundation/foundation.equalizer.js');
//    $doc->addScript(JUri::root() . 'assets/bower_components/angular-sortable-view/src/angular-sortable-view.min.js');

    $doc->addScript(JUri::root() . 'assets/bower_components/jquery-validation/dist/jquery.validate.min.js');
    $doc->addScript(JUri::root() . 'assets/bower_components/jquery-validation/dist/additional-methods.min.js');
    $doc->addScript(JUri::root() . 'assets/bower_components/owl.carousel/dist/owl.carousel.min.js');
//    $doc->addScript(JUri::root() . 'assets/bower_components/ng-droplet/dist/ng-droplet.js');
//    $doc->addScript(JUri::root() . 'assets/bower_components/progressbar.js/dist/progressbar.js');
    
    ///$doc->addScript(JUri::root() . 'assets/bower_components/ng-file-upload/ng-file-upload-all.min.js');
    $doc->addScript(JUri::root() . 'assets/bower_components/dropzone/dist/min/dropzone.min.js');
    $doc->addScript(JUri::root() . 'assets/bower_components/bootstrap/dist/js/bootstrap.min.js');
    $doc->addScript(JUri::root() . 'assets/bower_components/bootstrap-form-helpers/dist/js/bootstrap-formhelpers.min.js');

    $doc->addScript(JUri::root() . 'assets/bower_components/ngDialog/js/ngDialog.min.js');
    $doc->addScript(JUri::root() . 'assets/bower_components/ng-tags-input/ng-tags-input.min.js');

    $doc->addScript(JUri::root() . 'assets/bower_components/gsap/src/minified/TweenMax.min.js');
    
    $doc->addScript(JUri::root() . 'templates/webportal/js/helper.js');
    $doc->addScript(JUri::root() . 'templates/webportal/js/pager.js');
    $doc->addScript(JUri::root() . 'templates/webportal/js/search.js');
    $doc->addScript(JUri::root() . 'templates/webportal/js/search_agent.js');
    $doc->addScript(JUri::root() . 'templates/webportal/js/addproperty.js');
    $doc->addScript(JUri::root() . 'templates/webportal/js/image_upload.js');
    $doc->addScript(JUri::root() . 'templates/webportal/js/contact.js');
    $doc->addScript(JUri::root() . 'templates/webportal/js/map.js');

    //$doc->addScript(JUri::root() . 'templates/webportal/js/property_list.js');
    $doc->addScript(JUri::root() . 'templates/webportal/js/markerclusterer.js');
    $doc->addScript(JUri::root() . 'templates/webportal/js/property_map.js');
    $doc->addScript(JUri::root() . 'templates/webportal/js/property_details.js');

    $doc->addScript(JUri::root() . 'templates/webportal/js/btsmap.js');

    $doc->addScript(JUri::root() . 'templates/webportal/js/viewportchecker.js');
    $doc->addScript(JUri::root() . 'templates/webportal/js/video.js');
    $doc->addScript(JUri::root() . 'templates/webportal/js/featured_slideshow.js');
    $doc->addScript(JUri::root() . 'templates/webportal/js/backtotop.js');
    $doc->addScript(JUri::root() . 'templates/webportal/js/login.js');


} else {

    $doc->addScript(JUri::root() . 'assets/js/jquery.min.js');
    $doc->addScript(JUri::root() . 'assets/js/angular.min.js');
    $doc->addScript(JUri::root() . 'assets/js/foundation.min.js');
    $doc->addScript(JUri::root() . 'assets/js/webportal.min.js');
   
}
