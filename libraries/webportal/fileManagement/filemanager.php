<?php
/**
 * Created by JetBrains PhpStorm.
 * User: khan
 * Date: 7/2/13
 * Time: 5:24 PM
 * To change this template use File | Settings | File Templates.
 */

class WebportalFileManager
{


    /*******************************************************/
    protected static $instance = null;

    protected function __construct()
    {
        //Thou shalt not construct that which is unconstructable!
    }

    protected function __clone()
    {
        //Me not like clones! Me smash clones!
    }

    /**
     * @return WebportalFileManager
     */
    public static function getInstance()
    {
        if (!isset(static::$instance)) {
            static::$instance = new static;
        }
        return static::$instance;
    }

    /*********************************************************/

    private $s3FileManager;

    /**
     * @param string $managerType
     * @return iFileManager
     */
    public function getFileManager($managerType = "s3")
    {
        if ($managerType == "s3") {
            if ($this->s3FileManager == null) {
                ////var/www/softverk-webportal/libraries/webportal/fileManagement/s3Gq/webportalS3FileManager.php
                require_once JPATH_ROOT  . DS . "libraries" . DS . "webportal" . DS . "fileManagement" . DS . "s3" . DS . "webportalS3FileManager.php";
                $this->s3FileManager = new WebportalS3FileManager();
            }
            return $this->s3FileManager;
        }
    }


}