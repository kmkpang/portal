<?php
/** @var WebportalViewAddproperty $this */

$existingImage = $this->addPropertyModel->images;


?>
<div ng-controller="imageUploadCtrl"
    ng-init="initImageUploader()"
    action="<?php echo JUri::base() ?>index.php?option=com_webportal&controller=api&task=service&version=v1&format=raw&service=property&data=acceptImageUpload&propertyId=<? echo $this->addPropertyModel->property_id ?>"
    class="dropzone"
    id="imageUpload">

    <?php foreach ($existingImage as $e) {

        if(is_object($e))
            $e=get_object_vars($e);

        $imageUrl = $e['server_url'];
        $imagePath = $e['origin_url'];
        $imageName = basename($imagePath);

        require "imageUploadPreview.php";


    } ?>


</div>