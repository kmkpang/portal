<?php

/**
 * Created by PhpStorm.
 * User: khan
 * Date: 5/10/14
 * Time: 4:41 PM
 */
abstract class ModelBase
{

    /**
     * Copies value FROM dbArray-->model
     * @param $dbArray
     */
    public function bindToDb($dbArray)
    {
        if (is_object($dbArray))
            $dbArray = get_object_vars($dbArray);

        $variables = get_object_vars($this);
        foreach ($variables as $key => $value) {

            if ($dbArray[$key] !== null) {
                $this->$key = $dbArray[$key];
            } else {
                if ($dbArray['__' . $key] !== null)
                    $this->$key = $dbArray['__' . $key];//for when a class.database.php is passed
            }


        }
    }

    /**
     * Copies value FROM mode-->dbArray
     * @param $dbArray
     * @return array|mixed
     */
    public function reverseBindToDb($dbArray)
    {
        $___copy = $dbArray;

        $objectType = false;
        if (is_object($dbArray)) {
            $objectType = true;
            $dbArray = get_object_vars($dbArray);
        }

        $variables = get_object_vars($this);
        foreach ($variables as $key => $value) {

            if ($value !== null) {
                if (array_key_exists($key, $dbArray)) {
                    $dbArray[$key] = $value;
                }
                if (array_key_exists("__$key", $dbArray)) {
                    $dbArray["__$key"] = $value;
                }
            }
        }

        if ($objectType) {
            foreach ($dbArray as $key => $value) {
                $___copy->$key = $value;
            }
        } else {
            foreach ($dbArray as $key => $value) {
                $___copy[$key] = $value;
            }
        }
        return $___copy;

    }

    public function getAsArray()
    {
        $variables = get_object_vars($this);
        return $variables;
    }
}