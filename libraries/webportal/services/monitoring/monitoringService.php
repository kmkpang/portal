<?php

/**
 * Created by JetBrains PhpStorm.
 * User: khan
 * Date: 3/12/13
 * Time: 2:42 PM
 * To change this template use File | Settings | File Templates.
 */


require_once JPATH_BASE . '/libraries/webportal/services/monitoring/Config.php';

class MonitoringService
{

    public function getStatus()
    {

        //get number of office , agent , property ,

        // https://github.com/Softverk/softverk-webportal/tree/generic-dev
        // https://api.github.com/repos/:owner/:repo/git/refs/heads/:branch
        //https://api.github.com/repos/Softverk/softverk-webportal/git/refs/heads/generic-dev

        //  curl -i https://api.github.com/repos/Softverk/softverk-webportal/git/refs/heads/generic-dev -u softverk:xx
        //  curl -i https://api.github.com/repos/Softverk/softverk-webportal/commits/38f2c38cf0e00442f9750c400db744b2fab88cd3 -u softverk:xx

        $config = WFactory::getConfig()->getWebportalConfigurationArray();

        $info = array();
        $gitInfo = WFactory::getHelper()->getVersionInfo();
        $info[] = "{$gitInfo['branch']} -v{$gitInfo['version']} ({$gitInfo['commit']})";

        $info [] = "site: " . __SITEURL;
        $info [] = "template: " . __TEMPLATE;
        $info [] = "cust: " . __CUSTOMER_ID;
        $info [] = "country: " . __COUNTRY;
        $info [] = "bucket: " . $config['s3']['awsEndpoint'] . "/" . $config['s3']['s3BucketName'];

        if ($config['s3']['useproxy'] === true)
            $info [] = "s3proxy: YES";
        else
            $info [] = "s3proxy: NO";

        $conf = new Config();

         $apacheConfig = $conf->parseConfig("/etc/apache2/sites-available/000-default.conf", "apache");

        ///home/khan/www/softverk-webportal-generic/libraries/webportal/services/monitoring/testConfig.conf
        //$apacheConfig = $conf->parseConfig("/home/khan/www/softverk-webportal-generic/libraries/webportal/services/monitoring/testConfig.conf", "apache");
        $children = $apacheConfig->children;
        $pathBase = explode('/', JPATH_BASE);
        $pathBase = array_filter($pathBase);
        $pathBase = implode('/', $pathBase);
        $publicIp = WFactory::getHelper()->getPublicIp();
        $siteLive = "NO";

        /**
         * @var $child Config_Container
         */
        foreach ($children as $child) {

            $type = strtolower($child->type);
            $name = strtolower($child->name);

            if ($type == 'section' && $name == 'virtualhost') {

                //echo "Got virtual host!";

                $serverName = "";
                $serverAlias = "";
                $documentRoot = "";

                foreach ($child->children as $c) {
                    if ($c->name == "Documentroot") {
                        $documentRoot = $c->content;
                    }
                    if ($c->name == "ServerName") {
                        $serverName = $c->content;
                    }
                    if ($c->name == "ServerAlias") {
                        $serverAlias = $c->content;
                    }
                }

                $documentRoot = array_filter(explode('/', $documentRoot));
                $documentRoot = implode('/', $documentRoot);


                if ($documentRoot === $pathBase) {

                    $siteAddress = array();
                    $siteAddress[] = trim($serverName);
                    $serverAlias = explode(' ', $serverAlias);
                    $serverAlias = array_filter($serverAlias);
                    $siteAddress = array_merge($siteAddress, $serverAlias);
                    $siteAddress = array_unique($siteAddress);

                    foreach ($siteAddress as $address) {
                        if (strpos($address, "softverk") === false) {
                            $ip = gethostbyname($address);
                            if ($ip === $publicIp) {
                                $siteLive = "YES";
                                break;
                            }
                        }


                    }

                    break;
                    //$x = $documentRoot;
                }

            }

        }

        $info[] = "Live:$siteLive";

        $healthMessage = array();
        $healthOk = WFactory::getHelper()->doHealthCheck($healthMessage);

        $info=array_merge($info,$healthMessage);

        $result=array("health"=>$healthOk,"message"=>$info);

        echo json_encode($result);
        exit(0);

    }


}
