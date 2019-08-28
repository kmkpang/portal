<?php
/**
 * Created by PhpStorm.
 * User: khan
 * Date: 10/7/14
 * Time: 2:57 PM
 */

require_once JPATH_LIBRARY_WEBPORTAL_SERVICES . DS . 'modelbase.php';


class SitemapModel extends ModelBase
{
    ///var/www/softverk-webportal-remaxth/libraries/webportal/services/sitemap/sitemapModel.php
    # Column 1: Relative URL, e.g. '/what-is-new.htm?page=17&itemsPerPage=10'
    #           NO session IDS! NO DOS-BACKSLASHES!
    #           Only content pages, no images, movies, CSS files or such
    #           Mandatory
    var $relativeUrl = '/';
    # Column 2: Date/Time of last modification, e.g. '2005-07-25 12:13:41'
    #           You can omit the time, but keep the format CCYY-MM-DD
    #           Optional
    var $modified;
    # Column 3: Change Frequency, valid values are:
    #           'always'  - page content changes significantly with every page view
    #           'hourly'
    #           'daily'
    #           'weekly'
    #           'monthly'
    #           'yearly'
    #           'never'   - archived pages which aren't changed any more, PDF documents and such
    #           Optional
    var $changeFrequency = 'daily';
    # Column 4: Crawling priority 0.0 - 1.0
    #           Optional
    var $crawlPriority = 0.5;
    # Column 5: Level 0 - n, example:
    #           Root index page     = 0
    #           Category index page = 1
    #           Content page        = 2
    #           Mandatory for HTML sitemap
    var $level = 2;
    # Column 6: Page title, don't use '#' or '\' or '|' or crlf (new line)
    #           in this text field
    #           Mandatory for HTML sitemap and RSS feed
    var $pageTitle = '';
    # Column 7: Page description, don't use hard line breaks
    #           Optional for HTML sitemap (used as tooltip in links)
    #           Mandatory for RSS feed
    var $pageDescription = '';
    # Column 8: RSS Categories
    #           A comma delimited list of max. 5 categories applicable for the page
    #           Optional but recommended for RSS feed
    var $rssCategories = '';

    private function removeUnwantedChars($text)
    {
        // Remove the HTML tags
        $text = mb_substr(strip_tags($text), 0, 250) ;
        $text = str_replace('/', '', $text);
        $text = str_replace('#', '', $text);
        $text = str_replace('\\', '', $text);
        $text = str_replace('|', '', $text);
        $text = str_replace("\n", '', $text);
        $text = str_replace("\r", '', $text);



        return trim($text);

    }

    public function toString()
    {
        $this->pageTitle = $this->removeUnwantedChars($this->pageTitle);
        $this->pageDescription = $this->removeUnwantedChars($this->pageDescription);

        return "{$this->relativeUrl}|{$this->modified}|{$this->changeFrequency}|{$this->crawlPriority}|{$this->level}|{$this->pageTitle}|{$this->pageDescription}|{$this->rssCategories}";
    }

}