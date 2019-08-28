<?php

/**
 * Created by PhpStorm.
 * User: khan
 * Date: 6/26/14
 * Time: 9:52 PM
 */
class ImageService
{

    /**
     * @return iFileManager
     */
    private function __getFileManager()
    {
        $commonConfig = WFactory::getConfig()->getWebportalConfigurationArray();
        $fileManager = WFactory::getFileManager();
        /**
         * @var iFileManager
         */
        $fileManager = $fileManager->getFileManager($commonConfig["filesystem"]);
        JFactory::getSession()->set('filemanager', $fileManager);
        return $fileManager;
    }


    /**
     * gets s3 image from whatever buckets configured currently
     */
    function get($path, $size)
    {
        //http://localhost/softverk-webportal-generic/api/v1/image/get?path=1/104/logo-1.jpg
        if (is_array($path) && $size == null) {
            $size = $path['size'];
            $path = $path['path'];
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $fileName = JPATH_ROOT . "/tmp/" . md5($path) . ".$extension";

        if (!file_exists($fileName)) {
            $fileManager = $this->__getFileManager();
            $fileManager->getFile($path, $fileName);
        }
        header('Content-Type: image/' . $extension);
        readfile($fileName);

        exit(0);

    }


}