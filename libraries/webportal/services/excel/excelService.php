<?php
/**
 * Created by PhpStorm.
 * User: khan
 * Date: 6/25/14
 * Time: 5:36 PM
 */

require_once JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . 'excel' . DS . 'PHPExcel.php';

class ExcelService
{

    /**
     * @return PHPExcel
     */
    function getPhpExcel()
    {
        return new PHPExcel();
    }
}