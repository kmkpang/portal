/**
 * Created by Lian on 6/16/14.
 */



app.controller('imageUploadCtrl', ['$scope', '$timeout', '$log', function ($scope, $timeout, $log) {

    $scope.files = [];
    $scope.imageUpload = null;


    $scope.initImageUploader = function () {


        //enable sorting
        var dropletContainer = jQuery("div.dropzone");
        // dropletContainer.dropzone({"url": dropletContainer.attr("action")});

        dropletContainer.sortable();
        dropletContainer.disableSelection();


        /*
         * imageUpload is the Id of the dropzone form in /home/khan/www/softverk-webportal-remaxth/templates/webportal/ng_templates/properties/imageupload.php file
         * */
        Dropzone.options.imageUpload = {
            addRemoveLinks: true,
            init: function () {
                $scope.imageUpload = this;
                this.on("addedfile", $scope.filedAdded);
                this.on("error", $scope.fileUploadError);
                this.on("removedfile", $scope.fileRemoved);
                this.on("complete", $scope.fileUploaded);

            }
        };

        dropletContainer.on('sortupdate', $scope.filesSorted);

    }

    $scope.filedAdded = function (file) {


    }

    $scope.fileUploadError = function (file, error) {
        $log.log(file);
        $log.error(error);
    }

    $scope.filesSorted = function (event, ui) {

        $scope.parseFiles();
    }

    $scope.fileUploaded = function (file, error) {

        jQuery(file.previewElement).attr('serverpath', file.xhr.responseText);//because i cant find some nice way to attach server response to the UI element!

        file.previewElement.addEventListener("click", function () {
            //show lightbox
        });

        $scope.parseFiles();
    }

    $scope.fileRemoved = function (file) {
        $log.log(file);

        $scope.parseFiles();

    }

    $scope.removeUploadedFile = function (id) {
        jQuery("#" + id).remove();
        $scope.parseFiles();
    };

    $scope.parseFiles = function () {

        //var dropzone = new Dropzone("#imageUpload");
        var files = jQuery('.dz-preview');
        $scope.files = [];
        for (var i = 0; i < files.length; i++) {
            var file = jQuery(files[i]).attr('serverpath');
            $scope.files.push(file);
        }

        if (typeof $scope.$parent.currentProperty !== 'undefined')
            $scope.$parent.currentProperty.files = $scope.files;

        $log.log("----------start-----------");

        for (var i = 0; i < $scope.files.length; i++) {
            $log.log(i + " : " + $scope.files[i]);

        }
        $log.log("----------end-----------");
    }


}]);
