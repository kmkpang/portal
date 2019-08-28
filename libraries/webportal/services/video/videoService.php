<?php

/**
 * Created by PhpStorm.
 * User: khan
 * Date: 6/26/14
 * Time: 9:52 PM
 */
class VideoService
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
        //Content-Type: image/ <---- 
        header('Content-Type: image/' . $extension);
        readfile($fileName);

        exit(0);

    }

    public function getJRouteFormattedVideoRoute($propertyId)
    {

        global $currentPropertyModel;

        $result = "video/$propertyId";
        $result = iconv(mb_detect_encoding($result, mb_detect_order(), true), "UTF-8", $result);


        return $result;
    }

    function getUrlToDirectPage($propertyId)
    {

        $directPath = JRoute::_("index.php?option=com_webportal&view=video&propertyID={$propertyId}");
        //remove /api/v1/whatever!!
        $directPath = WFactory::getHelper()->getCurrentlySelectedLanguage() . substr($directPath, strpos($directPath, '/video/'));

        $directPath = str_replace('/video/', '/', $directPath);
        $directPath = str_replace('/video/', '/', $directPath);

        return JUri::base() . $directPath;
    }
    
    public function getAllVideos($propertyId = null)
    {
        /**
         * @var $videoClass PortalPortalVideosSql
         */

        $videoTable = WFactory::getSqlService()->getDbClass(__PORTAL_PORTAL_PROPERTY_VIDEOS_SQL);
        $videoTable->__property_id = $propertyId;

        $videos = $videoTable->loadDataFromDatabase(false, "ORDER BY sequence asc");


        foreach ($videos as $v) {
            if (!WFactory::getHelper()->isNullOrEmptyString($v->__origin_url)) {


                $__tempVideo = new PropertyVideo();
                $__tempVideo->originUrl = $v->__origin_url;
                $__tempVideo->providerUrl = $v->__provider;
                $__tempVideo->sequence = intval($v->__sequence);
                $__tempVideo->alt = $v->__alt;
                $__tempVideo->description = trim($v->__description);

                if (strpos($v->__provider, 'youtube.com') !== false || strpos($v->__provider, 'youtu.be') !== false) {
                    $__tempVideo->providerName = 'youtube';
                    $__tempVideo->providerVideoFileName = WFactory::getHelper()->getYoutubeVideoFileName($__tempVideo->originUrl);
                }


                $result[] = $__tempVideo;

            }
        }
        return $result;

    }

}