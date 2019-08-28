<?php

/**
 * Created by JetBrains PhpStorm.
 * User: khan
 * Date: 7/9/13
 * Time: 8:30 AM
 * To change this template use File | Settings | File Templates.
 */
interface iFileManager
{
    public function createFolder($folderPath);

    public function deleteFolder($folderPath);

    public function putFile($sourceFilePath, $destinationFilePath, &$webPathURL);

    public function deleteFile($filePath);

    public function getFile($path,&$stream);
}