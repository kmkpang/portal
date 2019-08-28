<?php


require_once JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . 'sitemap' . DS . 'sitemapModel.php';

/**
 * Created by PhpStorm.
 * User: khan
 * Date: 10/7/14
 * Time: 1:19 PM
 */
class SitemapService
{


    function generateSitemapText()
    {
        $articles = $this->generateArticleLists();
        $offices = $this->generateOfficeList();
        $agents = $this->generateAgentList();
        $properties = $this->generatePropertyList();

        $result = array_merge($articles, $offices, $agents, $properties);

        $fileTemplate = $this->getSiteMapTextFile();
        $fileTemplate = explode("\n", file_get_contents($fileTemplate));

        $header = [];
        $footer = [];
        $foundStart = false;
        foreach ($fileTemplate as $i => $f) {

            $f = trim($f);

            if (!$foundStart)
                $header[] = $f;
            else
                $footer[] = $f;


            if (strcmp($f, "# START DATA") === 0) {
                $foundStart = true;
                $header[] = "";
                $header[] = "";
            }
        }

        $file = array_merge($header, $result, $footer);

        $file = implode("\n", $file);

        $output = JPATH_BASE . DS . 'sitemap.txt';
        file_put_contents($output, $file);

        return $output;

    }

    function generateGoogleSiteMap()
    {
        require_once __DIR__ . DS . "sitemap.php";

        $sitemapTxtFile = $this->generateSitemapText();
        $rootUrl = __SITEURL;
        $sitemapTmplFile = __DIR__ . DS . "sitemap.tmpl";
        $sitemapHtmlTitle = JFactory::getConfig()->get('sitename', "Sitemap");
        $sitemapHtmlFile = JPATH_BASE . DS . "sitemap.htm";
        $sitemapXmlFile = JPATH_BASE . DS . "sitemap.xml";

        generateGoogleSiteMap($sitemapTxtFile, $rootUrl, $sitemapTmplFile, $sitemapHtmlTitle, $sitemapHtmlFile, $sitemapXmlFile);

    }


    /**
     * @return SitemapModel
     */
    function getModel()
    {
        return new SitemapModel();
    }

    function getSiteMapTextFile()
    {
        return __DIR__ . DS . 'sitemap_sample.txt';
    }

    function generateArticleLists()
    {
        $query = "SELECT #__menu.*
                  FROM #__menu #__menu
                 WHERE     (#__menu.link LIKE '%option=com_content&view=article%')
                       AND (#__menu.published = 1) AND (#__menu.access = 1)";

        $menus = WFactory::getSqlService()->select($query);

        $result = array();

        $defaultLang = JFactory::getLanguage()->getDefault();
        $defaultLang = explode('-', $defaultLang);
        $defaultLang = $defaultLang[0];

        $enabledLangs = WFactory::getHelper()->getAllLang(true);

        foreach ($menus as $m) {

            $link = explode('&', $m['link']);
            foreach ($link as $l) {
                if (strpos($l, 'id=') !== false) {
                    $articleId = explode('=', $link[2]);
                    $articleId = $articleId[1];
                    break;
                }
            }

            $menuLang = $m['language'];

            $query = "SELECT #__content.* from #__content
                      WHERE   #__content.id = $articleId";

            $articles = WFactory::getSqlService()->select($query);
            $a = $articles[0];

            $articleLang = $a['language'];

            if ($articleLang !== $menuLang && $articleLang !== '*' && $menuLang !== '*') {
                WFactory::getLogger()->warn("Artcle Language ($articleLang) does not match Menu Language($menuLang) for menu -> {$m['path']} / article -> {$a['title']} skipping ", __LINE__, __FILE__);
                continue;
            }


            $model = $this->getModel();

            $model->pageDescription = $a['introtext'];
            $model->pageTitle = $a['title'];

            $lang = $m['language'];
            if ($lang == '*')
                $lang = $defaultLang;
            else {
                $lang = explode('-', $lang);
                $lang = $lang[0];
            }


            $model->relativeUrl = '/' . $lang . '/' . $m['path'];
            $model->changeFrequency = 'weekly';
            $model->crawlPriority = '0.4';
            $model->level = 2;
            $model->modified = $a['modified'];


            if (array_key_exists($lang, $enabledLangs)) {
                $result[] = $model->toString();
            } else {
                WFactory::getLogger()->warn("Path {$model->relativeUrl} is not enabled in allowed languages, skipping ", __LINE__, __FILE__);
            }


        }

        return $result;
    }
    
    function generateOfficeList()
    {
        $offices = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_OFFICE)->getOfficeAll();

        $langs = WFactory::getHelper()->getAllLang();


        $result = array();

        $model = $this->getModel();

        $model->pageDescription = "Property Offices";
        $model->pageTitle = "Offices";
        //$model->relativeUrl = '/' . WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_OFFICE)->getJRouteFormattedOfficeName($o['id']);
        $model->changeFrequency = 'monthly';
        $model->crawlPriority = '0.8';
        $model->level = 1;
        //$model->modified = $o['date_modified'];

        foreach ($langs as $lang => $val) {
            $model->relativeUrl = '/' . $lang . '/' . 'offices';
            $result[] = $model->toString();
        }


        foreach ($offices as $o) {


            $model = $this->getModel();

            $model->pageDescription = $o['office_name'];
            $model->pageTitle = $o['office_name'];
            //$model->relativeUrl = '/' . WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_OFFICE)->getJRouteFormattedOfficeName($o['id']);
            $model->changeFrequency = 'weekly';
            $model->crawlPriority = '0.8';
            $model->level = 2;
            $model->modified = $o['date_modified'];

            foreach ($langs as $lang => $val) {
                $model->relativeUrl = '/' . $lang . '/' . WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_OFFICE)->getJRouteFormattedOfficeName($o['id']);
                $result[] = $model->toString();
            }


        }

        return $result;
    }

    function generateAgentList()
    {
        $agents = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_AGENTS)->getAgentsAll();

        $langs = WFactory::getHelper()->getAllLang();


        $result = array();

        $model = $this->getModel();

        $model->pageDescription = "Property Agents";
        $model->pageTitle = "Agents";
        //$model->relativeUrl = '/' . WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_AGENT)->getJRouteFormattedAgentName($o['id']);
        $model->changeFrequency = 'weekly';
        $model->crawlPriority = '0.8';
        $model->level = 1;
        //$model->modified = $o['date_modified'];

        foreach ($langs as $lang => $val) {
            $model->relativeUrl = '/' . $lang . '/' . 'agents';
            $result[] = $model->toString();
        }

        /**
         * @var $o AgentModel
         */
        foreach ($agents as $o) {


            $model = $this->getModel();

            $description = $o->office_name . ' - ' . $o->first_name . " " . $o->middle_name . " " . $o->last_name;

            $model->pageDescription = $description;
            $model->pageTitle = $description;
            //$model->relativeUrl = '/' . WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_AGENT)->getJRouteFormattedAgentName($o['id']);
            $model->changeFrequency = 'weekly';
            $model->crawlPriority = '0.8';
            $model->level = 2;
            //$model->modified = $o->;

            foreach ($langs as $lang => $val) {
                $model->relativeUrl = '/' . $lang . '/' . WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_AGENTS)->getJRouteFormattedAgentName($o->id);
                $result[] = $model->toString();
            }


        }

        return $result;
    }

    function generatePropertyList()
    {
        $result = array();
        $langs = WFactory::getHelper()->getAllLang();
        $searchModel = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_SEARCH)->getSearchModel();
        $searchModel->returnType = RETURN_TYPE_LIST;

        $properties = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTIES)->search($searchModel);


        $model = $this->getModel();

        $model->pageDescription = "Properties";
        $model->pageTitle = "Properties";
        //$model->relativeUrl = '/' . WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_AGENT)->getJRouteFormattedAgentName($o['id']);
        $model->changeFrequency = 'hourly';
        $model->crawlPriority = '0.9';
        $model->level = 1;

        foreach ($langs as $lang => $val) {
            $model->relativeUrl = '/' . $lang . '/' . 'properties-search/list';
            $result[] = $model->toString();
        }

        $model->pageDescription = "Map";
        $model->pageTitle = "Map";

        foreach ($langs as $lang => $val) {
            $model->relativeUrl = '/' . $lang . '/' . 'properties-search/map';
            $result[] = $model->toString();
        }

        /**
         * @var $o PropertyListModel
         */
        foreach ($properties as $o) {


            $model = $this->getModel();

            //$description = $o->office_name . ' - ' . $o->first_name . " " . $o->middle_name . " " . $o->last_name;

            $model->pageDescription = $o->description_text;
            $model->pageTitle = $o->title;
            //$model->relativeUrl = '/' . WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_AGENT)->getJRouteFormattedAgentName($o['id']);
            $model->changeFrequency = 'daily';
            $model->crawlPriority = '0.9';
            $model->level = 2;
            $model->modified = $o->last_update;

            foreach ($langs as $lang => $val) {
                $model->relativeUrl = '/' . $lang . '/' . WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTY)->getJRouteFormattedPropertyRoute($o->property_id);
                $result[] = $model->toString();
            }


        }


        //$result = array();
        return $result;
    }

}