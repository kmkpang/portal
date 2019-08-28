<?php
/**
 * Created by PhpStorm.
 * User: khan
 * Date: 4/24/14
 * Time: 5:37 PM
 */

defined('_JEXEC') or die ("Restricted area");


class WebportalHelper extends JHelperContent
{

    /**
     * @var WebportalHelper
     */
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
     * @return null | WebportalHelper
     */
    public static function getInstance()
    {
        if (!isset(static::$instance)) {
            static::$instance = new static;
        }
        return static::$instance;
    }

    public function parseYoutubeUrl()
    {
        return null;
    }

    public function snakeToCamelCase($val)
    {
        $val = str_replace(' ', '', ucwords(str_replace('_', ' ', $val)));
        $val = strtolower(substr($val, 0, 1)) . substr($val, 1);
        return $val;
    }

    public function camelToSnakeCase($str)
    {
        $str = lcfirst($str);
        $lc = strtolower($str);
        $result = '';
        $length = strlen($str);
        for ($i = 0; $i < $length; ++$i) {
            $result .= ($str[$i] == $lc[$i] ? '' : '_') . $lc[$i];
        }
        return $result;
    }

    public function camelToSpaces($str)
    {
        $str = $this->camelToSnakeCase($str);
        $str = explode("_", $str);

        foreach ($str as &$s)
            $s = ucfirst($s);

        $str = trim(implode(" ", $str));
        return $str;
    }

    public function getFomattedXml($xmlString)
    {
        $doc = new DOMDocument();
        $doc->loadXML($xmlString);
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = true;
        return $doc->saveXML();
    }


    public function getPublicIp()
    {
        $externalContent = file_get_contents('http://checkip.dyndns.com/');
        preg_match('/Current IP Address: \[?([:.0-9a-fA-F]+)\]?/', $externalContent, $m);
        return $m[1];

    }

    public function checkIfUrl($url)
    {
        $regex = "((https?|ftp)\:\/\/)?"; // SCHEME
        $regex .= "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?"; // User and Pass
        $regex .= "([a-z0-9-.]*)\.([a-z]{2,3})"; // Host or IP
        $regex .= "(\:[0-9]{2,5})?"; // Port
        $regex .= "(\/([a-z0-9+\$_-]\.?)+)*\/?"; // Path
        $regex .= "(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?"; // GET Query
        $regex .= "(#[a-z_.-][a-z0-9+\$_.-]*)?"; // Anchor

        if (preg_match("/^$regex$/", $url)) {
            return true;
        }
        return false;
    }

    public function isAdminOrExit()
    {
        if (!$this->isAdmin()) {
            $this->exitScript();
        }

    }

    public function isAdmin()
    {
        $user = JFactory::getUser();
        return $user->authorise('core.admin');
    }

    public function exitScript($msg = "Unauthorized access")
    {
        echo $msg;
        WFactory::getLogger()->fatal($msg);
        exit(1);
    }


    private static $halftag = "";
    private static $fulltag = "";

    /**
     * @param bool $halftag | will return only leading part of the languge..like if language is en-GB, will return en
     * @return string
     */
    public static function getCurrentlySelectedLanguage($halftag = true)
    {
        if ($halftag && !empty(static::$halftag)) {
            return static::$halftag;
        } else if (!$halftag && !empty(static::$fulltag)) {
            return static::$fulltag;
        }


        $language = JFactory::getLanguage()->getTag();
        if ($halftag) {

            static::$halftag = explode('-', $language)[0];
            return static::$halftag;
        }

        static::$fulltag = $language;
        return static::$fulltag;
    }

    private $__currentUrl = null;

    public function getCurrentUrl()
    {
        if (!$this->__currentUrl) {
            JURI::current();// It's very strange, but without this line at least Joomla 3 fails to fulfill the task
            $router =& JSite::getRouter();// get router
            $query = $router->parse(JURI::getInstance()); // Get the real joomla query as an array - parse current joomla link
            $this->__currentUrl = JUri::base() . 'index.php?' . JURI::getInstance()->buildQuery($query);
        }
        return $this->__currentUrl;
    }

    public function updateWebportalConfigurationJavascript()
    {
        $jsConfig = new stdClass();

        if (__COUNTRY == "IS") {


            $officeLatLngZom = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_OFFICE)->getFirstOfficeLatitudeLongitudeZoomLevel();
            if ($officeLatLngZom['latitude'] !== null) {

                $jsConfig->__defaultLat = floatval($officeLatLngZom['latitude']);
                $jsConfig->__defaultLang = floatval($officeLatLngZom['longitude']);
                $jsConfig->__defaultZoom = intval($officeLatLngZom['zoom']);
            }

        }


        JFactory::getDocument()->addScriptDeclaration('var webportalConfigurationJson = ' . json_encode($jsConfig) . ';');
        JFactory::getDocument()->addScriptDeclaration('
         for (var key in webportalConfigurationJson) {
            webportalConfiguration[key] = webportalConfigurationJson[key];
            console.log("data is : " + key + " --> " + webportalConfiguration[key]);
         }');


    }

    /**
     * Given a $centre (latitude, longitude) co-ordinates and a
     * distance $radius (miles), returns a random point (latitude,longtitude)
     * which is within $radius miles of $centre.
     *
     * @param  array $centre Numeric array of floats. First element is
     *                       latitude, second is longitude.
     * @param  float $radius The radius (in miles).
     * @return array         Numeric array of floats (lat/lng). First
     *                       element is latitude, second is longitude.
     */
    function getRandomLatitudeLongitude($centre, $radius = 15)
    {

        $radius_earth = 3959; //miles

        //Pick random distance within $distance;
        $distance = lcg_value() * $radius;

        //Convert degrees to radians.
        $centre_rads = array_map('deg2rad', $centre);

        //First suppose our point is the north pole.
        //Find a random point $distance miles away
        $lat_rads = (pi() / 2) - $distance / $radius_earth;
        $lng_rads = lcg_value() * 2 * pi();


        //($lat_rads,$lng_rads) is a point on the circle which is
        //$distance miles from the north pole. Convert to Cartesian
        $x1 = cos($lat_rads) * sin($lng_rads);
        $y1 = cos($lat_rads) * cos($lng_rads);
        $z1 = sin($lat_rads);


        //Rotate that sphere so that the north pole is now at $centre.

        //Rotate in x axis by $rot = (pi()/2) - $centre_rads[0];
        $rot = (pi() / 2) - $centre_rads[0];
        $x2 = $x1;
        $y2 = $y1 * cos($rot) + $z1 * sin($rot);
        $z2 = -$y1 * sin($rot) + $z1 * cos($rot);

        //Rotate in z axis by $rot = $centre_rads[1]
        $rot = $centre_rads[1];
        $x3 = $x2 * cos($rot) + $y2 * sin($rot);
        $y3 = -$x2 * sin($rot) + $y2 * cos($rot);
        $z3 = $z2;


        //Finally convert this point to polar co-ords
        $lng_rads = atan2($x3, $y3);
        $lat_rads = asin($z3);

        return array_map('rad2deg', array($lat_rads, $lng_rads));
    }


    private $installedLang = array();

    /**
     * @return array | key=halftag, value=fulltag
     */
    public function getAllLang($onlyEnabledLanguages = true)
    {
        if ($onlyEnabledLanguages) {
            $config = WFactory::getConfig()->getWebportalConfigurationArray();
            if ($config['enabledLanguages'] !== null)
                return $config['enabledLanguages'];
        }

        if (empty($this->installedLang)) {


            $otherLangs = $this->getKnownLanguages();
            foreach ($otherLangs as $lang) {

                $l = explode('-', $lang['tag'])[0];
                $this->installedLang[$l] = $lang['tag'];

            }
        }
        return $this->installedLang;
    }

    private $__knownLang = null;

    public function getKnownLanguages()
    {
        if (!$this->__knownLang) {
            $this->__knownLang = JFactory::getLanguage()->getKnownLanguages();
        }
        return $this->__knownLang;
    }


    /**
     * @param $moduleName String mod_webportal_...... (module name)
     * @param $params Array  Sample : $params = array(
     * "property_type=next_previous",
     * "template=carousel_property_detail_page",
     * "category_id=0",
     * "office_id=0",
     * "agent_id=0",
     * "region_id=0",
     * "city_town_id=0",
     * "zip_code_id=0",
     * "rows=3",
     * "columns=3",
     * "moduleclass_sfx=",
     * "module_tag=div",
     * "bootstrap_size=0",
     * "header_tag=h3",
     * "header_class=",
     * "style=0"
     * );
     * @return string
     */
    function getModule($moduleName, $params)
    {
        $renderer = JFactory::getDocument()->loadRenderer('module');
        $mod = $nextPrevProperties = JModuleHelper::getModule($moduleName);
        $mod->params = implode("\n", $params);
        return $renderer->render($mod, $params);
    }


    function getLogo($sitename = null)
    {

        $app = JFactory::getApplication();
        $template = $app->getTemplate(true);
        $params = $template->params;

        if ($params->get('logoFile')) {
            $logo = '<img src="' . JUri::root()
                . $params->get('logoFile')
                . '" alt="' . $params->get('sitealt')
                . '" title="' . $params->get('sitetitle') . '"/>';
        } elseif ($params->get('sitetitle')) {
            $logo = '<span class="site-title" title="' . $sitename . '">' . htmlspecialchars($params->get('sitetitle')) . '</span>';
        } else {
            $logo = '<span class="site-title" title="' . $sitename . '">' . $sitename . '</span>';
        }

        return $logo;
    }

    public static function castObject(&$destination, stdClass $source)
    {
        $sourceReflection = new \ReflectionObject($source);
        $sourceProperties = $sourceReflection->getProperties();
        foreach ($sourceProperties as $sourceProperty) {
            $name = $sourceProperty->getName();
            if (gettype($destination->{$name}) == "object") {
                self::castObject($destination->{$name}, $source->$name);
            } else {
                $destination->{$name} = $source->$name;
            }
        }

        return $destination;
    }


    /**vi
     * This fixes the crappy html generated by ms word into something more pleasant!
     * @param $s
     * @return string
     */
    public static function escapePercentU($s)
    {
        $s = preg_replace("/%u([A-Fa-f0-9]{4})/", "&#x$1;", $s);
        $s = preg_replace("/font-size:.*pt;/i", "font-size: 10pt;", $s);
        $s = preg_replace('/<font[^>]*>/', '<font size="2">', $s);

        return html_entity_decode($s, ENT_COMPAT, 'utf-8');
    }

    function splitAtUpperCase($s)
    {
        return preg_split('/(?=[A-Z])/', $s, -1, PREG_SPLIT_NO_EMPTY);
    }

    function generateJsonResponse($array)
    {

        if (!is_array($array)) {
            WFactory::getLogger()->warn('non array supplied for generating json reponse');
            return false;
        }


        return json_encode($array, JSON_PRETTY_PRINT);

    }

    public function getYoutubeVideoFileName($youtubeUrl)
    {

        if (strpos($youtubeUrl, 'youtube.com')) {
            parse_str(parse_url($youtubeUrl, PHP_URL_QUERY), $query_params);
            return $query_params['v'];
        } else if (strpos($youtubeUrl, 'youtu.be')) {
            $path_param = explode('/', $youtubeUrl);
            return str_replace('/', '', $path_param[count($path_param) - 1]);
        }
    }

    //Checks if the attempted link is a deleted property agent of office
    function doSanityCheck($type, $id, &$v2_v3_id = 0)
    {
        $id = trim($id);

        if (empty($id) || $id == null)
            return true;

        if (!is_numeric($id))
            return true;

        if (WFactory::getSqlService()->returnDeletedRecord())
            return true;


        $sql = '';
        if ($type === PROPERTY)
            $sql .= 'select is_deleted from  #__portal_properties ';
        else if ($type === AGENT)
            $sql .= 'select is_deleted from  #__portal_sales ';
        else if ($type === OFFICE)
            $sql .= 'select show_on_web from  #__portal_offices ';

        $sql .= ' where id = ' . $id;


        $result = WFactory::getSqlService()->select($sql);
        if (count($result) === 0 && WFactory::getSqlService()->allowPortalV2CompatibleRouting()) {
            $v3Id = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTY)->getPortalV3IdFromV2Id($id);
            if (intval($v3Id) > 0) {
                JFactory::getApplication()->redirect(JUri::base() . "property/$v3Id");
            }
        }

        if ($result[0]['is_deleted'] === '0')
            return true;
        if ($result[0]['show_on_web'] === '1')
            return true;

        $msg = "Sanity check failed for $type , id $id";

        WFactory::getLogger()->warn($msg, __LINE__, __FILE__);

        WFactory::throwPortalException("Agent / Office / Property not found");

        return false;


    }

    function isJson($string)
    {
        return ((is_string($string) &&
            (is_object(json_decode($string)) ||
                is_array(json_decode($string))))) ? true : false;
    }

    function printArrayAsClassToGenerateModel($array)
    {
        foreach ($array as $a => $b) {
            echo "var \$$a = '';\r\n";
        }
    }

    function isUnitTest()
    {
        if (defined('__ISUNITTEST') && __ISUNITTEST === true)
            return true;
        return false;
    }

    function isNullOrEmptyString($question)
    {
        if (is_array($question) && empty($question))
            return true;

        if ($question === null)
            return true;

        if (is_string($question)) {
            $question = trim($question);
            $strLength = strlen($question);
            return $strLength === 0;
        }
        return false;
        //return (!isset($question) || trim($question) === '' || trim($question) === "");
    }

    function getLanguageText($lang, $text)
    {
        $result = $text;
        $text = $this->removeRepeatedWords($text);
        $english = trim(WFactory::getHelper()->extractEnglish($text));


        if ($lang == "en" || $lang == "zh" || $lang == "is") {
            $result = !WFactory::getHelper()->isNullOrEmptyString($english) ? $english : $text;
        } else {

            if (!WFactory::getHelper()->isNullOrEmptyString($english))
                $result = trim(str_replace($english, "", $text));
            else
                $result = $text;
        }

        return $result;

    }


    function extractEnglish($text)
    {
        if (__COUNTRY === "IS")//dont process this for Iceland
            return $text;
        $n_words = preg_match_all('/[\s\w\d\?><;,\{\}\[\]\-_\+=!@\#\$%^&\*\|\'\/]/', $text, $match_arr);
        return implode('', $match_arr[0]);
    }

    function removeRepeatedWords($text)
    {
        return implode(' ', array_unique(explode(' ', $text)));
    }


    public function getFormattedDate($dateString, $withHoureMinutes = false)
    {
        $configuration = WFactory::getConfig()->getWebportalConfigurationArray();
        if (!$withHoureMinutes)
            $locale = $configuration['date_format'];
        else
            $locale = $configuration['date_format_long'];
        $date = date($locale, strtotime($dateString));
        return $date;

    }

    public function checkIfFileAtURLExists($url)
    {

        WFactory::getLogger()->debug("Checking if file at URL exists: $url");

        $url = trim($url);
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        if (!file_get_contents(($url), 0, NULL, 0, 1)) {
           WFactory::getLogger()->warn("File at url $url does not exist");
            return false;
        }
        return true;
    }

    public function downloadFileToTmpFolder($url)
    {
        $configuration = WFactory::getConfig()->getWebportalConfigurationArray();

        $uniqueFilePath = $this->checkAndRemoveTrailingSlash($configuration["tempFolderPath"]) . DS . uniqid() . "." . strtolower(pathinfo($url, PATHINFO_EXTENSION));

        $content = file_get_contents($url);

        file_put_contents($uniqueFilePath, $content);

        return $uniqueFilePath;

    }

    private function checkAndRemoveTrailingSlash($path)
    {
        return rtrim($path, '/');
    }

    public function removeUnsupportedUrlChars($text)
    {
        // -------- v1 ---------
        // return strtolower(trim(preg_replace('~[^0-9a-z]+~i', '-', html_entity_decode(preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', htmlentities($string, ENT_QUOTES, 'UTF-8')), ENT_QUOTES, 'UTF-8')), '-'));

        //-------- v2 ---------------
        // Swap out Non "Letters" with a -
        //$text = preg_replace('/[^\\pL\d]+/u', '-', $text);
        $text = preg_replace('/([^a-zA-Z0-9ก-๙เ])/', '-', $text);
        // Trim out extra -'s
        $text = trim($text, '-');

        // Convert letters that we have left to the closest ASCII representation
        //$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // Make text lowercase
        $text = strtolower($text);

        // Strip out anything we haven't been able to convert
        //$text = preg_replace('/[^-\w]+/', '', $text);

        return $text;

    }

    function safe_json_encode($value)
    {
        if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
            $encoded = json_encode($value, JSON_PRETTY_PRINT);
        } else {
            $encoded = json_encode($value);
        }
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                return $encoded;
            case JSON_ERROR_DEPTH:
                WFactory::getLogger()->fatal('[JSON]Maximum stack depth exceeded');
                break;
            case JSON_ERROR_STATE_MISMATCH:
                WFactory::getLogger()->fatal('[JSON]Underflow or the modes mismatch');
                break;
            case JSON_ERROR_CTRL_CHAR:
                WFactory::getLogger()->fatal('[JSON]Unexpected control character found');
                break;
            case JSON_ERROR_SYNTAX:
                WFactory::getLogger()->fatal('[JSON]Syntax error, malformed JSON');
                break;
            case JSON_ERROR_UTF8:
                $clean = WFactory::getHelper()->utf8ize($value);
                return WFactory::getHelper()->safe_json_encode($clean);
            default:
                WFactory::getLogger()->fatal('[JSON] Unknown error'); // or trigger_error() or throw new Exception()
                return null;

        }
    }

    function utf8ize($mixed)
    {
        if (is_object($mixed))
            $mixed = get_object_vars($mixed);
        if (is_array($mixed)) {
            foreach ($mixed as $key => $value) {
                $mixed[$key] = WFactory::getHelper()->utf8ize($value);
            }
        } else if (is_string($mixed)) {
            return utf8_encode($mixed);
        }
        return $mixed;
    }

    /**
     * @param $url
     * @param int $post
     * @return resource
     */
    function getCurl($url, $post = 0)
    {
        $curl_connection = curl_init($url);
        curl_setopt($curl_connection, CURLOPT_HTTPHEADER, array('text/html; charset=utf-8', ""));
        curl_setopt($curl_connection, CURLOPT_HEADER, false);
        curl_setopt($curl_connection, CURLOPT_TIMEOUT, 300);
        curl_setopt($curl_connection, CURLOPT_POST, $post);
        curl_setopt($curl_connection, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl_connection, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl_connection, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl_connection, CURLOPT_COOKIESESSION, TRUE);

        return $curl_connection;
    }


    private $currentPage;

    function setCurrentPage($pageName)
    {
        $this->currentPage = true;
        $this->setSessionVariable('webportal.current_page', $pageName);

        JFactory::getDocument()->addScriptDeclaration('var current_page = \'' . $pageName . '\'');
    }

    function getCurrentPage()
    {
        if ($this->currentPage)//return ONLY if it has actually been set in current execution circle
            return $this->getSessionVariable('webportal.current_page');
        return null;
    }

//    function getCurrentUrl()
//    {
//        return JUri::current();
//    }

    function startsWith($haystack, $needle)
    {
        // search backwards starting from haystack length characters from the end
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
    }

    function endsWith($haystack, $needle)
    {
        // search forward starting from end minus needle length characters
        return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
    }

    function getCurrentUrlPathWithOutLanguage()
    {
        $currentLang = '/' . WFactory::getHelper()->getCurrentlySelectedLanguage() . '/';
        $requestedPath = str_replace($currentLang, '/', JUri::current());
        $requestedPath = str_replace(JUri::base(), '', $requestedPath);
        return $requestedPath;
    }

    function setSessionVariable($key, $value)
    {
        $session =& JFactory::getSession();
        $session->set($key, $value);
    }

    function getSessionVariable($key)
    {
        $session =& JFactory::getSession();
        return $session->get($key, null);
    }

    /**
     * This function sanitizes any given name for using in URL
     * @param $name
     * @return mixed
     */
    function sanitizeName($name)
    {
        $name = strtolower($name);
        $name = str_replace('re/max', '', $name);
        $accents = '/&([A-Za-z]{1,2})(grave|acute|circ|cedil|uml|lig);/';

//        $string_encoded = htmlentities($name,ENT_NOQUOTES,'UTF-8');
//        $name = preg_replace($accents,'$1',$string_encoded);

        //       $name = preg_replace('/[^0-9a-zA-Z-_\s]/', '', $name);
        $name = str_replace(' ', '-', $name);
        $name = str_replace('--', '-', $name);
        $name = str_replace('.', '', $name);
        $name = str_replace('-', '', $name);

        return trim(strtolower($name));
    }

    function checkIfArticleExistsInAllLanguages($url)
    {
        $query = "SELECT *  FROM `jos_menu` WHERE `path` LIKE '$url'";
        $result = WFactory::getSqlService()->select($query);

        $languages = array_flip($this->getAllLang());

        foreach ($languages as &$l)
            $l = false;


        foreach ($result as $r) {
            $langTag = $r['language'];
            $languages[$langTag] = true;
        }

        return $languages;


    }

    function setCookie($key, $value)
    {
        JFactory::getApplication()->input->cookie->set($key, $value);
    }

    function getCookie($key)
    {
        return JFactory::getApplication()->input->cookie->get($key);
    }

    function array2Object($array)
    {
        return json_decode(json_encode($array), FALSE);
    }

    function doDownload($file, $fileName)
    {
        if (file_exists($file)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimetype = finfo_file($finfo, $file);
            header('Content-Description: File Transfer');
            header('Content-Type: ' . $mimetype);
            header('Content-Disposition: attachment; filename=' . basename($file));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            ob_clean();
            flush();
            //readfile('uploads/' . $file);
            readfile($file);
            exit;
        }
    }

    function getBowerPackages($returnOnlyDepency = true)
    {
        $bowerPath = JPATH_ROOT . '/bower.json';
        $bowerData = json_decode(file_get_contents($bowerPath));

        return $returnOnlyDepency ? $bowerData->dependencies : $bowerData;
    }


    function varDumpToSctring($someVar)
    {
        ob_get_clean();
        ob_start();
        var_dump($someVar);
        $result = ob_get_clean();
        return $result;
    }

    /**
     * A simple method to build URL...
     * @param $url
     * @return string
     */
    function buildUrl($url)
    {
        //$url = $this->getCurrentlySelectedLanguage() . '' . $url;
        $url = str_replace("//", "/", $url);
        if (strpos($url, '/') === 0)
            $url = substr($url, 1);

        return JUri::base() . $url;// str_replace("//", "/", $url);
    }

    function isValidEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }


    function getMemoryLimit()
    {
        $memory_limit = ini_get('memory_limit');
        if (preg_match('/^(\d+)(.)$/', $memory_limit, $matches)) {
            if ($matches[2] == 'M') {
                $memory_limit = $matches[1] * 1024 * 1024; // nnnM -> nnn MB
            } else if ($matches[2] == 'K') {
                $memory_limit = $matches[1] * 1024; // nnnK -> nnn KB
            }
        }
        return $memory_limit;
    }

    function redirect404()
    {
        JFactory::getApplication()->redirect(JUri::base() . 'not-found');
    }

    function getCountryFullName()
    {
        if (__COUNTRY === 'TH')
            return "Thailand";
        if (__COUNTRY === 'IS')
            return "Iceland";

        return "Thailand";
    }

    function tidyHtml($html)
    {
        if (class_exists("tidy")) {


            $config = array(
                'indent' => true,
                'output-xhtml' => true,
                'wrap' => 200);

// Tidy
            $tidy = new tidy;
            $tidy->parseString($html, $config, 'utf8');
            $tidy->cleanRepair();

            return tidy_get_output($tidy);
        } else
            WFactory::getLogger()->error("PHP HTML TIDY NOT INSTALLED!");
        return $html;
    }


    /**
     * Encode the form data into a string (base64)
     * @param $data
     * @return string
     */
    static function encodeData($data)
    {
        return base64_encode(json_encode($data));
    }

    /**
     * Decode a base64 string into a data object
     * @param $data
     * @return mixed
     */
    static function decodeData($data)
    {
        return json_decode(base64_decode($data));
    }

    function imageToBase64($path, $withMetaData = true)
    {
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $metaData = $withMetaData ? 'data:image/' . $type . ';base64,' : "";
        return $metaData . base64_encode($data);
    }

    function base64ImageToFile($base64_image_string)
    {
        $splited = explode(',', substr($base64_image_string, 5), 2);
        $mime = $splited[0];
        $data = $splited[1];
        $output_file_without_extentnion = uniqid("add_property_");
        $output_file_with_extentnion = "";
        $mime_split_without_base64 = explode(';', $mime, 2);
        $mime_split = explode('/', $mime_split_without_base64[0], 2);
        if (count($mime_split) == 2) {
            $extension = $mime_split[1];
            if ($extension == 'jpeg') $extension = 'jpg';
            //if($extension=='javascript')$extension='js';
            //if($extension=='text')$extension='txt';
            $output_file_with_extentnion = $output_file_without_extentnion . '.' . $extension;
        }
        file_put_contents(JPATH_BASE . "/tmp/$output_file_with_extentnion", base64_decode($data));
        return $output_file_with_extentnion;
    }


    function getInputData()
    {
        WFactory::getLogger()->warn("----------BAD IDEA----------------");
        return array_merge($_POST, $_GET);
    }


    function insertReCaptcha($fieldName)
    {
        $dispatcher = JEventDispatcher::getInstance();
        $dispatcher->trigger('onInit', $fieldName);
    }

    function getReCaptchaVerification()
    {
        JPluginHelper::importPlugin('captcha');
        $dispatcher = JEventDispatcher::getInstance();
        $res = $dispatcher->trigger('onCheckAnswer');
        if (!$res[0]) {
            return false;
        }
        return true;
    }

    function isPostBack()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Configure the Linkbar.
     */
    public static function addSubmenu($submenu)
    {
//        JHtmlSidebar::addEntry(
//            'Places',
//            'index.php?option=com_webportal&view=places',
//            $submenu == 'places'
//        );
//
//        JHtmlSidebar::addEntry(
//            'Properties',
//            'index.php?option=com_categories&view=categories&extension=com_webportal',
//            $submenu == 'categories'
//        );


        JHtmlSidebar::addEntry(
            '<div style="font-weight: bold;font-size: 1.2em">Configuration</div>',
            '',
            $submenu == 'configuration'
        );

        JHtmlSidebar::addEntry(
            '<div style="margin-left:30px">Logging</div>',
            'index.php?option=com_webportal&view=configlogging&layout=edit',
            $submenu == 'configloggin'
        );
        JHtmlSidebar::addEntry(
            '<div style="margin-left:30px">PHP Configuration</div>',
            'index.php?option=com_webportal&view=configphp&layout=edit',
            $submenu == 'configphp'
        );
        JHtmlSidebar::addEntry(
            '<div style="margin-left:30px">JS Configuration</div>',
            'index.php?option=com_webportal&view=configjs&layout=edit',
            $submenu == 'configjs'
        );

        JHtmlSidebar::addEntry(
            '<div style="margin-left:30px">Miscellaneous</div>',
            'index.php?option=com_webportal&view=configmisc&layout=edit',
            $submenu == 'configmisc'
        );
        //----------------------------------

        JHtmlSidebar::addEntry(
            '<div style="font-weight: bold;font-size: 1.2em">Components</div>',
            '',
            $submenu == 'components'
        );


        JHtmlSidebar::addEntry(
            '<div style="margin-left:30px">Company</div>',
            'index.php?option=com_webportal&view=company&layout=edit',
            $submenu == 'company'
        );

        JHtmlSidebar::addEntry(
            '<div style="margin-left:30px">Offices</div>',
            'index.php?option=com_webportal&view=offices',
            $submenu == 'offices'
        );

        JHtmlSidebar::addEntry(
            '<div style="margin-left:30px">Agents</div>',
            'index.php?option=com_webportal&view=agents',
            $submenu == 'agents'
        );

        JHtmlSidebar::addEntry(
            '<div style="margin-left:30px">Properties (ALPHA/DONT USE)</div>',
            'index.php?option=com_webportal&view=properties',
            $submenu == 'properties'
        );


        //-------------------

        JHtmlSidebar::addEntry(
            '<div style="font-weight: bold;font-size: 1.2em">Tools</div>',
            '',
            $submenu == 'components'
        );

        JHtmlSidebar::addEntry(
            '<div style="margin-left:30px">Sent2Web Xmls</div>',
            'index.php?option=com_webportal&view=sent2webs',
            $submenu == 'sent2webs'
        );
    }

    /**
     * Get the actions
     */
    public static function getActions($messageId = 0)
    {
        $result = new JObject;

        if (empty($messageId)) {
            $assetName = 'com_webportal';
        } else {
            $assetName = 'com_webportal.message.' . (int)$messageId;
        }

        $actions = JAccess::getActions('com_webportal', 'component');

        foreach ($actions as $action) {
            $result->set($action->name, JFactory::getUser()->authorise($action->name, $assetName));
        }

        return $result;
    }


    public function getVersionInfoFormatted()
    {
        $versionInfo = $this->getVersionInfo();

        return "{$versionInfo["branch"]} - v{$versionInfo["version"]} ( {$versionInfo["commit"]} )";
    }

    public function getVersionInfo()
    {
        $branch = $this->getGitBranch();
        $commit = $this->getLastCommit();
        $version = JVERSION;
        return array("branch" => $branch, "commit" => $commit, "version" => $version);
    }

    public function getLastCommit()
    {
        return shell_exec('git log -1 --pretty=format:"[%cN][%h][%cd] %s"');
    }

    public function getGitBranch()
    {
        try {
            $shellOutput = [];
            exec('git branch | ' . "grep ' * '", $shellOutput);
            foreach ($shellOutput as $line) {
                if (strpos($line, '* ') !== false) {
                    return trim(strtolower(str_replace('* ', '', $line)));
                }
            }
            return null;
        } catch (Exception $e) {

        }

    }

    function getLoadingIcon()
    {

        echo <<<JAE
<div class="loading-icon--wrapper">
  <div class="loading-icon">
    <i class="loading-icon__icon"></i>
    <div class="dot dot-1"></div>
    <div class="dot dot-2"></div>
    <div class="dot dot-3"></div>
    <div class="dot dot-4"></div>
    <div class="dot dot-5"></div>
  </div>
</div>
JAE;
    }

    /**
     * Thai AREA Stuff...
     * @param $total_area
     * @return string
     */
    function AreaSize($total_area)
    {
        if ($total_area < 400) {
            return $total_area . ' m<sup>2</sup>';
        } else {
            if ($total_area >= 400 && $total_area < 400 * 400) {
                return (string)($total_area / 400) . ' Ngan';
            } else {
                return (string)($total_area / (400 * 400)) . ' Rai';
            }
        }
    }


    function sortArray(&$array, $keyName, $order = "dsc", $type = "int")
    {
        $sorter = new FieldSorter($keyName, $type);
        usort($array, array($sorter, $order));
        return $array;
    }


    public function doHealthCheck(&$message)
    {

        $result = true;
        $result &= $this->manageLog4NetXml($message);
        $result &= $this->checkSEO($message);
        $result &= $this->checkEmail($message);

        return $result;

    }

    private function checkSEO(&$result)
    {
        $seoOk = true;
        // sitemap.xml !
        $xmlFile = JPATH_BASE . DS . "sitemap.xml";
        if (!file_exists($xmlFile)) {
            $seoOk &= false;
            $result[] = "sitemap.xml not found! make sure an entry is created in crontab";
        } else {

            $dateTime = new DateTime();
            $dateTimeFile = new DateTime();
            $dateTimeFile->setTimestamp(filemtime($xmlFile));
            $interval = $dateTime->diff($dateTimeFile);
            $message = "$xmlFile was modified : " . sprintf(
                    "%s%s%s%s",
                    $interval->d > 0 ? $interval->d . "d " : "",
                    $interval->h > 0 ? $interval->h . "h " : "",
                    $interval->i > 0 ? $interval->i . "m " : "",
                    $interval->s > 0 ? $interval->s . "s " : ""
                ) . " ago ";
            if ($interval->d > 2) {
                $seoOk &= false;
                $result[] = $message;
            }
        }

        //check robots.txt
        $robotsFile = JPATH_BASE . DS . "robots.txt";
        if (!file_exists($robotsFile)) {
            $seoOk &= false;
            $result[] = "robots.txt not found!";
        } else {

            $content = file_get_contents($robotsFile);

            $siteMapString = $this->endsWith(__SITEURL, "/") ? __SITEURL . "sitemap.xml" : __SITEURL . "/sitemap.xml";
            $robotContainsCorrectText = strpos($content, $siteMapString) !== false;

            if (!$robotContainsCorrectText) {
                $result[] = "robots.txt contains incorrect sitemap.xml link!";
                $seoOk &= false;
            }
        }

        return $seoOk;
    }

    private function checkEmail(&$result)
    {
        $mandrilConfig = WFactory::getConfig()->getWebportalConfigurationArray();
        $mandrilConfig = $mandrilConfig['mandrill'];


        $model = json_decode($mandrilConfig['modal']);
        $subaccountOk = false;
        if ($model->message->subaccount === 'private-portals-asia' || $model->message->subaccount === 'private-portals-is') {
            $subaccountOk = true;
        }

        if (!$subaccountOk)
            $result[] = 'mandrill subaccount incorrect , must be private-portals-asia or private-portals-is';

        $keyOk = $mandrilConfig['key'] === 'hAfi_F7dNFB5bNNgHTt4Ew';
        if (!$keyOk)
            $result[] = 'mandrill key incorrect';

        return $keyOk & $subaccountOk;

    }

    private function manageLog4NetXml(&$result)
    {

        $emailAppenderFound = false;
        $xmlFile = simplexml_load_file(JPATH_BASE . DS . "log4phpConfiguration.xml");

        foreach ($xmlFile->root->appender_ref as $i => $v) {
            $appenderRef = (string)$v->attributes()['ref'];
            if ($appenderRef === 'emailNotice') {
                $emailAppenderFound = true;
                $result[] = "emailNotice found in \\<root\\/> node in log4phpConfiguration.xml";
            }
        }


        foreach ($xmlFile->logger->appender_ref as $i => $v) {
            $appenderRef = (string)$v->attributes()['ref'];
            if ($appenderRef === 'emailNotice') {
                $emailAppenderFound = true;
                $result[] = "emailNotice found in \\<logger\\/> node in log4phpConfiguration.xml";
            }
        }


        if ($emailAppenderFound)
            return false;
        return true;

    }


}

class FieldSorter
{
    public $field;
    private $type;


    function __construct($field, $type)
    {
        $this->field = $field;
        $this->type = $type;
    }

    function asc($a, $b)
    {
        if ($this->type === "datetime") {
            $a[$this->field] = new DateTime($a[$this->field]);
            $b[$this->field] = new DateTime($b[$this->field]);
        }

        if ($a[$this->field] == $b[$this->field]) return 0;
        return ($a[$this->field] > $b[$this->field]) ? 1 : -1;
    }

    function dsc($a, $b)
    {
        if ($this->type === "datetime") {
            $a[$this->field] = new DateTime($a[$this->field]);
            $b[$this->field] = new DateTime($b[$this->field]);
        }

        if ($a[$this->field] == $b[$this->field]) return 0;
        return ($a[$this->field] < $b[$this->field]) ? 1 : -1;
    }


}
