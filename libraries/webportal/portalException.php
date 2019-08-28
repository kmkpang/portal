<?php
/**
 * Created by PhpStorm.
 * User: khan
 * Date: 5/20/14
 * Time: 12:38 PM
 */

// Test_Exception.php
class PortalException extends Exception
{
    public function __construct($message = null, $code = 0, Exception $previous = null)
    {

        parent::__construct($message, $code, $previous);
    }
}