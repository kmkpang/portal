

<?php
/*
*
* -------------------------------------------------------
* CLASSNAME:        portalPropertyInformation
* GENERATION DATE:  01.07.2014
* CLASS FILE:       /var/www/softverk-webportal/libraries/webportal/services/dbclasses/class.portalPropertyInformation.php
* FOR MYSQL TABLE:  jos_portal_property_information
* FOR MYSQL DB:     softverk-webportal
* -------------------------------------------------------
* CODE GENERATED BY:
* MY PHP-MYSQL-CLASS GENERATOR
* from: >> www.voegeli.li >> (download for free!)
* -------------------------------------------------------
*
*/

// **********************
// CLASS DECLARATION
// **********************

class PortalPortalPropertyInformationSql
{ // class : begin


// **********************
// ATTRIBUTE DECLARATION
// **********************

var $__id;   // KEY ATTR. WITH AUTOINCREMENT

var $__current_listing_price;   // (normal Attribute)
var $__current_listing_currency;   // (normal Attribute)
var $__language_code;   // (normal Attribute)
var $__rental_price_granularity;   // (normal Attribute)
var $__property_status;   // (normal Attribute)
var $__floor_level;   // (normal Attribute)
var $__total_area;   // (normal Attribute)
var $__living_area;   // (normal Attribute)
var $__land_area;   // (normal Attribute)
var $__cubic_volume;   // (normal Attribute)
var $__total_number_of_rooms;   // (normal Attribute)
var $__number_of_bathrooms;   // (normal Attribute)
var $__number_of_bedrooms;   // (normal Attribute)
var $__number_of_toilet_rooms;   // (normal Attribute)
var $__number_of_floors;   // (normal Attribute)
var $__year_build;   // (normal Attribute)
var $__unit_of_measue;   // (normal Attribute)
var $__possession_date;   // (normal Attribute)
var $__availability_date;   // (normal Attribute)
var $__original_listing_date;   // (normal Attribute)
var $__expiry_date;   // (normal Attribute)
var $__alternate_url;   // (normal Attribute)
var $__virtual_tour;   // (normal Attribute)
var $__description_text;   // (normal Attribute)
var $__notes;   // (normal Attribute)
var $__open_house_start;   // (normal Attribute)
var $__open_house_end;   // (normal Attribute)
var $__register_date_requested;   // (normal Attribute)
var $__property_appraisal;   // (normal Attribute)
var $__fire_appraisal;   // (normal Attribute)
var $__mortgage;   // (normal Attribute)
var $__number_of_livingrooms;   // (normal Attribute)
var $__entrance;   // (normal Attribute)
var $__garage;   // (normal Attribute)
var $__garage_area;   // (normal Attribute)
var $__elevator;   // (normal Attribute)

var $database; // Instance of class database

    function getTableName()
    {
        return "jos_portal_property_information";
    }



    /*********************************************************************************
    *  This following part applies to only agents , offices and properties tables
    ***********************************************************************************/
    var $xmlData; // (normal Attribute)
    var $xmlSentData;
    var $xmlType;
    var $xmlVersion;
    var $xmlPublicKey;
    var $xmlSystemName;
    var $xmlSentDate;
    var $xmlOrder;
    var $xmlAddress;
    var $xmlComment;
    var $xmlInformation;
    var $xmlImages;


    function loadDataFromXml($xmlString)
    {
        $xml = simplexml_load_string($xmlString);
        if (empty($xml) || $xml == null)
            throw new Exception("office xml data malformed");

        $this->xmlData = json_decode(json_encode($xml), true);

        $xml = $this->xmlData;

        $systemInfo = $xml["System"];

        $this->loadXmlVariableData($systemInfo);

        $this->xmlSentData = $systemInfo["SentData"]["@attributes"];

        $this->loadXmlVariableData($this->xmlSentData);

        $mainData = "";
        if (ucfirst($this->xmlType) == "Office") {
            $mainData = $xml["Offices"]["Office"];
        } else if (ucfirst($this->xmlType) == "Property") {
            $mainData = $xml["Properties"]["Property"];
        } else if (ucfirst($this->xmlType) == "Agent") {
            $mainData = $xml["SalesAssociates"]["SalesAssociate"];
        }

        $this->loadXmlVariableData($mainData);
        $this->loadClassVariableFromXmlData($mainData["Information"]);
        $this->loadClassVariableFromXmlData($mainData["Address"]);
        $this->loadClassVariableFromXmlData($systemInfo);

        return true;
    }

    function loadClassVariableFromXmlData($array)
    {
        foreach ($array as $key => $value) {

            $pattern = '/([a-z])([A-Z])/e';
            $replace = "'\${1}_' . strtolower('\${2}')";
            $snakeVariable = "__".strtolower(preg_replace($pattern, $replace, $key));

            if (property_exists($this, $snakeVariable)) {
                $this->{$snakeVariable} = $value;
            }
            else{
                $xmlVariableName = "xml".ucfirst($key);
                $this->{$xmlVariableName} = $value;
            }

        }
    }
    
    function loadDataFromDatabase($single = true)
    {

        $valuesToUse = array();
        $refclass = new ReflectionClass($this);
        foreach ($refclass->getProperties() as $property) {
            $name = $property->name;
            if ($property->class == $refclass->name) {
                if (strpos($property->name, "__") === 0) {

                    if ($this->$name !== null) {
                        $key = str_replace("__", '',$name);
                        $value = $this->$name;
                        if (!is_array($value) && !is_object($value))
                            $valuesToUse[] = "{$key} = '{$this->$name}'";
                    }


                }
            }

        }

        $query = WFactory::getServices()->getSqlService()->getQuery();
        $query->select('*')
            ->from($this->getTableName());
        foreach ($valuesToUse as $v)
            $query->where($v);
        $query = (string)$query;
        $result = WFactory::getServices()->getSqlService()->select($query);

        if ($result[0] == null)
            return false;
        if ($single) {
            $result = $result[0];
            return $this->bind($result, $this);
        } else {
            $classes = array();
            foreach ($result as $r) {
                $className = __CLASS__;
                $class = new $className();
                $class->bind($r, $class);
                $classes[] = $class;
            }

            return $classes;
        }

    }

    /**
     * @param $array
     * @param $object
     * @param bool $bindStrict if true, will bind to ONLY the variables defined in the class, no extra variables will be bound
     * @return object
     */
    function bind($array, $object = null, $bindStrict = true)
    {
        if ($object == null)
            $object = $this;
        $classProperties = get_object_vars($object);

        foreach ($array as $key => $value) {
            $key =  WFactory::getHelper()->camelToSnakeCase($key);

            $key = "__$key";

            if ($bindStrict) {
                if (array_key_exists($key, $classProperties))
                    $object->$key = $value;
            } else
                $object->$key = $value;

        }

        return $object;
    }

    /**
     * Opposite of bind, will return the variables as array ...wil remove the leading __ in the process
     * @param null $object ( if null, will return $this )
     * @return array
     */
    function unbind($object = null)
    {
        if ($object == null)
            $object = $this;
        $classProperties = get_object_vars($object);

        $result = array();

        foreach ($classProperties as $key => $value) {

            if (strpos($key, "__") === 0) {
                $key = substr($key, 2); //remote __
                $result[$key] = $value;
            }

        }

        return $result;
    }



    function loadXmlVariableData($array)
    {
        foreach ($array as $key => $value) {
            $variableName = "xml$key";
            $this->{$variableName} = $value;


        }
    }

    /*********************************************************************************
    *  End of exception handling
    ***********************************************************************************/

    function getKey()
    {
        return "id";
    }

} // class : end

?>
