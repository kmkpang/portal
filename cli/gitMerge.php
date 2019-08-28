<?php
/**
 * @package        Joomla.Site
 * @copyright    Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */
if (!defined('_JEXEC'))
    define('_JEXEC', 1);

define('__ISUNITTEST', false);

error_reporting(1);


define('DS', DIRECTORY_SEPARATOR);

ini_set('display_errors', '1'); // only for xampp , because it screws up the display
ini_set('max_execution_time', '0');
ini_set('memory_limit', '2048M'); // required only on linux and ubuntu !

// We are a valid entry point.
const _JEXEC = 1;

// Load system defines
if (file_exists(dirname(__DIR__) . '/defines.php')) {
    require_once dirname(__DIR__) . '/defines.php';
}

if (!defined('_JDEFINES')) {
    define('JPATH_BASE', dirname(__DIR__));
    require_once JPATH_BASE . '/includes/defines.php';
}

/**
 * Constant that is checked in included files to prevent direct access.
 * define() is used in the installation folder rather than "const" to not error for PHP 5.2 and lower
 */
define('_JEXEC', 1);

if (file_exists(__DIR__ . '/defines.php')) {
    include_once __DIR__ . '/defines.php';
}

if (!defined('_JDEFINES')) {
    define('JPATH_BASE', __DIR__);
    require_once JPATH_BASE . '/includes/defines.php';
}
// Get the framework.
require_once JPATH_LIBRARIES . '/import.legacy.php';

// Bootstrap the CMS libraries.
require_once JPATH_LIBRARIES . '/cms.php';
// Framework import
require_once JPATH_BASE . '/includes/framework.php';
require_once JPATH_BASE . '/cliParser.php';

///var/www/softverk-webportal-remaxth/libraries/webportal/services/webservice/websending/websendingBase.php

require_once JPATH_BASE . '/libraries/webportal/services/webservice/websending/websendingBase.php';

// Instantiate the application.
$app = JFactory::getApplication('site');

ob_start(); // Start output buffering

// Execute the application.
$app->execute();

$list = ob_get_contents(); // Store buffer in variable

ob_end_clean(); // End buffering and clean up

/**
 * This script will fetch the update information for all extensions and store
 * them in the database, speeding up your administrator.
 *
 * @package  Joomla.Cli
 * @since    2.5
 */
class Gitmerge extends JApplicationCli
{
    /**
     * Entry point for the script
     *
     * @return  void
     *
     * @since   2.5
     */

    private $conflictBracnhes = array();
    var $files_to_bkup = array(
        "webportal.configuration.js",
        "webportal.configuration.php",
        "configuration.php"
    );

    public function doExecute()
    {


        $this->sleepWait(10, "WARNING: Make sure you are on generic-production and commited/pushed your changed.if not..abort NOW!");

        /**
         *              MERGE STRATEGY
         *  -  Merge generic dev to ALL branches
         *  -  checkout from github
         *  -  merge
         *  -  push , if it conflicts...abort and allow for manual fix
         *

         */

        $this->backupFiles();


        $branches2Merge2 = [
            
            "arsalir",
            "atrius",
            "atv-is",
            "eign-net",
            "eignin",
            "fannberg",
            "fasteignsnae",
            "fastlind",
            "framtidareign",
            "fsv",
            "heimaey",
            "hofdaberg",
            "hollfast",
            "hvammur",
            "inni",
            "jaspis",
            "jofur-is",
            "kaupstadur",
            "lit",

            "bs-estate",
            "forbest-th",
            "darvid-property-th",

            "domus-realty-ph",
            "remax-united",
            "remax-beachtown-th",
            "remax-philippine",
            "remax-th-top-properties",
            "remax-trp",


        ];

        $this->fetch();
        $this->checkoutBranch("generic-production");

        foreach ($branches2Merge2 as $branch) {

            WFactory::getLogger()->debug("======== merging generic-production to $branch ===========");
            WFactory::getLogger()->debug("step 1: test merging branch $branch");
            $tstResult = $this->testMerge($branch);
            if ($tstResult === true) {
                WFactory::getLogger()->debug("step 2: test successful,merging $branch");
                $this->doMergeBranch();
                WFactory::getLogger()->debug("step 3: merge complete,pushing to origin/$branch");
                $this->push($branch);
            }
            WFactory::getLogger()->debug("====== done with $branch======");


        }

        $this->checkoutBranch("generic-production");
        $this->restoreFiles();


    }

    private function checkoutBranch($branchName)
    {
        $command = "git checkout $branchName";
        $retCode = 0;
        $result = $this->executeShellCommand($command, $retCode);
        return strpos($result, $command) !== false;
    }

    private function resetBranch()
    {
        $command = "git reset --hard";
        $retCode = 0;
        $result = $this->executeShellCommand($command, $retCode);
    }

    private function doMergeBranch()
    {
        $this->resetBranch();
        $command = "git merge generic-production";
        $retCode = 0;
        $result = $this->executeShellCommand($command, $retCode);


    }

    private function push($branchName)
    {
        $command = "git push origin $branchName";
        $retCode = 0;
        $result = $this->executeShellCommand($command, $retCode);
    }

    private function fetch()
    {
        $command = "git fetch -a";
        $retCode = 0;
        $result = $this->executeShellCommand($command, $retCode);
    }

    public function testMerge($targetBranch)
    {
        $this->checkoutBranch($targetBranch);
        $this->resetBranch();
        $retCode = 0;


        $command = "git pull origin $targetBranch";
        $result = $this->executeShellCommand($command, $retCode);

        $command = "git merge --no-commit generic-production";
        $result = $this->executeShellCommand($command, $retCode, false);


        if ($retCode !== 0) {
            WFactory::getLogger()->warn("Merge conflict in $targetBranch,resetting...");

            //$attempting to fix this

            $command = "git diff --name-only --diff-filter=U";
            $result = $this->executeShellCommand($command, $retCode, false);
            $result = explode("\n", $result);

            $oursList = ["Grunt-settings.json", "favicon.ico", "images/logo.png", "images/favicon.ico", "README.txt"];
            $theirsList = ["cli/gitMerge.php",
                "webportal.configuration.js",
                ".gitignore",
                ".gitattributes",
                "assets/css/app.min.css", "index.php"];

            foreach ($result as $r) {
                if (in_array($r, $oursList)) {
                    $checkoutCommand = "git checkout --ours $r";
                    $result = $this->executeShellCommand($command, $retCode);
                } else if (in_array($r, $theirsList)) {
                    $checkoutCommand = "git checkout --theirs $r";
                    $result = $this->executeShellCommand($command, $retCode);

                } else {
                    WFactory::getLogger()->fatal("File $r is not in allowed list , not auto merging..exiting...");
                    exit(1);
                }

            }///

            $command = "git add -u";
            $result = $this->executeShellCommand($command, $retCode, false);

            $command = "git commit -m \"Automatic commit by gitMerge.php script\"";
            $result = $this->executeShellCommand($command, $retCode, false);

            if ($result === 0) {
                return true;
            }

            $this->resetBranch();
            return false;
        }
        $this->resetBranch();
        return true;


    }


    public function backupFiles()
    {
        foreach ($this->files_to_bkup as $f) {
            $fileName = explode("/", $f);
            $fileName = $fileName[count($fileName) - 1];
            $result = copy(JPATH_ROOT . "/$f ", JPATH_BASE . "/tmp/$fileName");
//            if (!$result) {
//                WFactory::getLogger()->warn("Failed to backup file from " . JPATH_ROOT . "/$f ==> " . JPATH_BASE . "/tmp/$fileName" . " , exiting..");
//                exit(1);
//            }

            $command = "sudo cp -rf " . JPATH_ROOT . "/$f " . JPATH_BASE . "/tmp/$fileName";
            $retCode = 0;
            $this->executeShellCommand($command, $retCode, false);
        }

    }

    public function restoreFiles()
    {
        foreach ($this->files_to_bkup as $f) {
            $fileName = explode("/", $f);
            $fileName = $fileName[count($fileName) - 1];

            $result = copy(JPATH_BASE . "/tmp/$fileName", JPATH_ROOT . "/$f ");
//            if (!$result) {
//                WFactory::getLogger()->warn("Failed to backup file from : " . JPATH_BASE . "/tmp/$fileName" . " ==>  " . JPATH_ROOT . "/$f exiting..");
//                exit(1);
//            }
//
            $command = "sudo cp -rf " . JPATH_BASE . "/tmp/$fileName " . JPATH_ROOT . "/$f ";
            $retCode = 0;
            $this->executeShellCommand($command, $retCode, false);
        }
    }

    private function sleepWait($waitTime, $msg)
    {
        WFactory::getLogger()->warn($msg);
        for ($i = 0; $i < $waitTime; $i++) {
            sleep(1);
            echo " . ";
        }
        echo "\n";
    }

    private function executeShellCommand($command, &$returnValue, $exit = true)
    {
        $output = array();

        exec($command, $output, $returnValue);
        WFactory::getLogger()->debug("$command ($returnValue)-> " . implode("\n", $output));


        if ($returnValue !== 0) {
            WFactory::getLogger()->fatal("Last command : $command has return value : $returnValue , aborting..! fix this manually and start over!");

            if ($exit)
                exit(1);
        }

        return implode("\n", $output);
    }
}


JApplicationCli::getInstance('Gitmerge')->execute();
