<?php

/**
 * Created by PhpStorm.
 * User: khan
 * Date: 6/22/14
 * Time: 3:49 PM
 */

require_once JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . 'modelbase.php';

class BeanstalkdJobModel extends ModelBase
{

    var $payLoad;
    var $functionName;
    var $serviceProviderName;


    public function __construct($serviceProviderName = null, $functionName = null, $payLoad = null)
    {
        if (is_object($payLoad))
            $payLoad = get_object_vars($payLoad);

        $this->payLoad = $payLoad;
        $this->functionName = $functionName;
        $this->serviceProviderName = $serviceProviderName;
    }

    public function toString()
    {
        return get_object_vars($this);
    }


}