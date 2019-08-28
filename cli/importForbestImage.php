<?php
/**
 * @package        Joomla.Site
 * @copyright    Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */
if (!defined('_JEXEC'))
    define('_JEXEC', 1);

define('__ISUNITTEST', false);

error_reporting(1);


define('DS', DIRECTORY_SEPARATOR);

ini_set('display_errors', '1'); // only for xampp , because it screws up the display
ini_set('max_execution_time', '0');
ini_set('memory_limit', '2048M'); // required only on linux and ubuntu !

// We are a valid entry point.
const _JEXEC = 1;

// Load system defines
if (file_exists(dirname(__DIR__) . '/defines.php')) {
    require_once dirname(__DIR__) . '/defines.php';
}

if (!defined('_JDEFINES')) {
    define('JPATH_BASE', dirname(__DIR__));
    require_once JPATH_BASE . '/includes/defines.php';
}

/**
 * Constant that is checked in included files to prevent direct access.
 * define() is used in the installation folder rather than "const" to not error for PHP 5.2 and lower
 */
define('_JEXEC', 1);

if (file_exists(__DIR__ . '/defines.php')) {
    include_once __DIR__ . '/defines.php';
}

if (!defined('_JDEFINES')) {
    define('JPATH_BASE', __DIR__);
    require_once JPATH_BASE . '/includes/defines.php';
}
// Get the framework.
require_once JPATH_LIBRARIES . '/import.legacy.php';

// Bootstrap the CMS libraries.
require_once JPATH_LIBRARIES . '/cms.php';
// Framework import
require_once JPATH_BASE . '/includes/framework.php';
require_once JPATH_BASE . '/cliParser.php';

///var/www/softverk-webportal-remaxth/libraries/webportal/services/webservice/websending/websendingBase.php

require_once JPATH_BASE . '/libraries/webportal/services/webservice/websending/websendingBase.php';

// Instantiate the application.
$app = JFactory::getApplication('site');

ob_start(); // Start output buffering

// Execute the application.
$app->execute();

$list = ob_get_contents(); // Store buffer in variable

ob_end_clean(); // End buffering and clean up

/**
 * This script will fetch the update information for all extensions and store
 * them in the database, speeding up your administrator.
 *
 * @package  Joomla.Cli
 * @since    2.5
 */
class Importforbestimage extends JApplicationCli
{
    /**
     * Entry point for the script
     *
     * @return  void
     *
     * @since   2.5
     */
    public function doExecute()
    {

        $imagesThumbUrl = $imageThumbContent = $property_count =$insertCommand = NULL;

        $query="SELECT * FROM  forbest_old.`property_tbl`";

        $forbestResult=WFactory::getSqlService()->select($query);
        //Hard code fix property id
        $property_id=10003001;
        foreach ($forbestResult as $fr){
            $imagethumb=$fr['thumbnail'];
            $image1=$fr['image1'];
            $image2=$fr['image2'];
            $image3=$fr['image3'];
            $image4=$fr['image4'];
            $image5=$fr['image5'];
            $image6=$fr['image6'];

            if ($imagethumb != NULL) {
                $imagesThumbUrl = "http://www.forbestproperties.co.th/backend/properties/$imagethumb";
                $imageThumbContent = addslashes(file_get_contents($imagesThumbUrl));

                $property_count=1;
                $insertCommand = "INSERT INTO forbest_old.`propertypictures` (`id`, `title`, `file_name`, `property_id`, `propertyPictureId`, `property_picture`) VALUES ('','','',$property_id,$property_count,'".$imageThumbContent."')";
                WFactory::getSqlService()->update($insertCommand);
                unset($imageThumbContent);
            }
            if ($image1 != NULL) {
                $imagesImage1Url="http://www.forbestproperties.co.th/backend/properties/$image1";
                $imageImage1Content=addslashes(file_get_contents($imagesImage1Url));

                $property_count=2;
                $insertCommand = "INSERT INTO forbest_old.`propertypictures` (`id`, `title`, `file_name`, `property_id`, `propertyPictureId`, `property_picture`) VALUES ('','','',$property_id,$property_count,'".$imageImage1Content."')";
                WFactory::getSqlService()->update($insertCommand);
                unset($imageImage1Content);

            }
            if ($image2 != NULL) {
                $imagesImage2Url="http://www.forbestproperties.co.th/backend/properties/$image2";
                $imageImage2Content=addslashes(file_get_contents($imagesImage2Url));

                $property_count=3;
                $insertCommand = "INSERT INTO forbest_old.`propertypictures` (`id`, `title`, `file_name`, `property_id`, `propertyPictureId`, `property_picture`) VALUES ('','','',$property_id,$property_count,'".$imageImage2Content."')";
                WFactory::getSqlService()->update($insertCommand);
                unset($imageImage2Content);
            }
            if ($image3 != NULL) {
                $imagesImage3Url="http://www.forbestproperties.co.th/backend/properties/$image3";
                $imageImage3Content=addslashes(file_get_contents($imagesImage3Url));

                $property_count=4;
                $insertCommand = "INSERT INTO forbest_old.`propertypictures` (`id`, `title`, `file_name`, `property_id`, `propertyPictureId`, `property_picture`) VALUES ('','','',$property_id,$property_count,'".$imageImage3Content."')";
                WFactory::getSqlService()->update($insertCommand);
                unset($imageImage3Content);
            }
            if ($image4 != NULL) {
                $imagesImage4Url="http://www.forbestproperties.co.th/backend/properties/$image4";
                $imageImage4Content=addslashes(file_get_contents($imagesImage4Url));

                $property_count=5;
                $insertCommand = "INSERT INTO forbest_old.`propertypictures` (`id`, `title`, `file_name`, `property_id`, `propertyPictureId`, `property_picture`) VALUES ('','','',$property_id,$property_count,'".$imageImage4Content."')";
                WFactory::getSqlService()->update($insertCommand);
                unset($imageImage4Content);
            }
            if ($image5 != NULL) {
                $imagesImage5Url="http://www.forbestproperties.co.th/backend/properties/$image5";
                $imageImage5Content=addslashes(file_get_contents($imagesImage5Url));

                $property_count=6;
                $insertCommand = "INSERT INTO forbest_old.`propertypictures` (`id`, `title`, `file_name`, `property_id`, `propertyPictureId`, `property_picture`) VALUES ('','','',$property_id,$property_count,'".$imageImage5Content."')";
                WFactory::getSqlService()->update($insertCommand);
                unset($imageImage5Content);
            }
            if ($image6 != NULL) {
                $imagesImage6Url="http://www.forbestproperties.co.th/backend/properties/$image6";
                $imageImage6Content=addslashes(file_get_contents($imagesImage6Url));

                $property_count=7;
                $insertCommand = "INSERT INTO forbest_old.`propertypictures` (`id`, `title`, `file_name`, `property_id`, `propertyPictureId`, `property_picture`) VALUES ('','','',$property_id,$property_count,'".$imageImage6Content."')";
                WFactory::getSqlService()->update($insertCommand);
                unset($imageImage6Content);
            }
            $property_id++;
            unset($forbestResult);
            //die(var_dump($imagesContent));

        }
       // query to select images from forbest db

        //file_get_content for the images

        //insert image as blob into saga table

        // $sql = "INSERT INTO ImageStore(ImageId,Image)
        //  VALUES('$this->image_id','" . file_get_contents($tmp_image) . "')";


    }

    function simpleEcho($msg)
    {
        echo "$msg\r\n";
    }


}


JApplicationCli::getInstance('Importforbestimage')->execute();
