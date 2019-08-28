<?php defined('_JEXEC') or die;

// Joomla Variables
$app = JFactory::getApplication();
$doc = JFactory::getDocument();
$user = JFactory::getUser();
$userID    = $user->get('id');

$menu = $app->getMenu();
$active = $app->getMenu()->getActive();
$params = $app->getParams();
$pageclass = $params->get('pageclass_sfx');
$tpath = $this->baseurl . '/templates/' . $this->template;

$redirectToFrontPageInsteadSamePage = false;

define('GK_COM_USERS', $option == 'com_users' && ($view == 'login' || $view == 'registration'));
$btn_login_text = ($userID == 0) ? '<i class="fa fa-lock" aria-hidden="true"></i> ' . JText::_('LOGIN') : '<i class="fa fa-unlock-alt" aria-hidden="true"></i> ' . JText::_('LOGOUT');

// Remove generator tag to prevent bots to know it is made by Joomla.
$this->setGenerator(null);

//Scripts to be excluded from automatically loading
$exclude_scripts = ['/media/jui/js/jquery.min.js',
    '/media/jui/js/jquery-noconflict.js',
    '/media/jui/js/jquery-migrate.min.js',
    '/media/jui/js/bootstrap.min.js',
    '/media/system/js/caption.js',
    '/media/system/js/mootools-core.js',
    '/media/system/js/core.js',
    '/media/system/js/punycode.js',
    '/media/system/js/html5fallback.js',
    '/media/system/js/validate.js',
];

//Disable autoloading of core Joomla JS files
$this->_scripts = array_diff_key($this->_scripts, array_flip($exclude_scripts));
$this->_script = preg_grep("/JCaption/i", $this->_script, PREG_GREP_INVERT);
$this->_script = preg_grep("/JQuery/i", $this->_script, PREG_GREP_INVERT);


// echo __FILE__;
//Used to disable loading of bootstrap in the frontend until converted to Foundation.
require_once(__DIR__."/../generic/controllers/overrides/bootstrap_override.php");

//Used to contain logic for all functions used in this view template.
require_once(__DIR__."/../generic/controllers/helpers.php");
require_once(__DIR__.'/../generic/controllers/temp_jscript.php');
?>
<!doctype html>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>"
      lang="<?php echo $this->language; ?>" ng-app="webportal">

<head>
    <meta name="google-site-verification" content="teay-3UdiPGbASkLEneI930MFELXwOcj5QhzA10IFwM"/>
    <jdoc:include type="head"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
    <!-- <meta name="viewport" content="width=1080px, user-scalable=yes"> -->
    <link rel="apple-touch-icon-precomposed" href="<?php echo $tpath; ?>/images/apple-touch-icon-57x57-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72"
          href="<?php echo $tpath; ?>/images/apple-touch-icon-72x72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114"
          href="<?php echo $tpath; ?>/images/apple-touch-icon-114x114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="144x144"
          href="<?php echo $tpath; ?>/images/apple-touch-icon-144x144-precomposed.png">
    <link rel="shortcut icon" href="<?php echo $tpath; ?>/favicon.ico"/>

    <link rel="stylesheet" type="text/css" href="<?php echo getStyle(getParam('templateStyle')); ?>"/>
    <link href='https://fonts.googleapis.com/css?family=Roboto:400,300,700' rel='stylesheet' type='text/css'>

    <meta property="og:site_name" content="<?php echo getParam('sitetitle'); ?>"/>


    <script type="text/javascript">

        <?php

         $doc =& JFactory::getDocument();
         $uri = JUri::getInstance();
         $baseWithLang = $uri->toString(array('scheme', 'user', 'pass', 'host','port', 'path'));


         $doc->addScriptDeclaration("var documentRoot = \"" . $baseWithLang . "\";");
         $doc->addScriptDeclaration("var documentRootRaw = \"" . JUri::base() . "\";");
       //  $doc->addScriptDeclaration("var documentUrl = \"" . WFactory::getHelper()->getCurrentUrl() . "\";");
         $doc->addScriptDeclaration("var lang = \"" . JFactory::getLanguage()->getTag() . "\";");
         $doc->addScriptDeclaration("var langHalf = \"" . WFactory::getHelper()->getCurrentlySelectedLanguage() . "\";");


         $doc->addScriptDeclaration("
            angular.module('webportal').factory('uri', function() {
                return {
                    getBase: function() {
                        return '" . JUri::base() . "';
                    }
                }
            });
            ");

        ?>

        $(document).foundation({
            equalizer: {
                // Specify if Equalizer should make elements equal height once they become stacked.
                equalize_on_stack: false
            }
        });

    </script>
</head>

<body
    class="responsive <?php echo (($menu->getActive() == $menu->getDefault()) ? ('front') : ('site')) . ' ' . $active->alias . ' ' . $pageclass; ?>">
<?php //var_dump($this); ?>

<div class="wrapper--main wrapper">

    <?php #This is required for the mobile menu to function since it's based on CSS and not JS ?>
    <input class="hidden" id="nav--mobile__toggle" type="checkbox">

    <div class="nav--top__faux <?php if (getParam('menuLayout') == 'm2'){echo 'block';} else {}?>"></div>

    <!-- Logo Header Block -->
    <div class="logo-header-wrapper">
        <div class="row">
            <div class="small-24 <?php if (getParam('headerLogo') == 'left'){echo 'large-3';} else {echo 'large-24';}?> top-logo <?php if (getParam('menuLayout') == 'm2'){echo 'block';} else {}?>">
                <a href="<?php echo $this->baseurl; ?>">
                    <?php echo getLogo(); ?>
                </a>
            </div>

            <?php if ($this->countModules('top-phone')) : ?>
                <div class="top-menu right large-only phone">
                    <jdoc:include type="modules" name="top-phone" style="none"/>
                </div>
            <?php endif; ?>

            <?php if ($this->countModules('login') && getParam('menuLayout') == 'm2') : ?>
                <div class="top-menu right">
                    <a class="login-popup large-only" href="#" ><?php echo $btn_login_text; ?></a>
                    <label class="nav--mobile__toggle__login">
                        <?php if($userID == 0) : ?>
                            <a class="login-popup" href="#"><i class="fa fa-lock" aria-hidden="true"></i></a>
                        <?php endif; ?>
                        <?php if($userID > 0) : ?>
                            <a class="login-popup" href="#"><i class="fa fa-unlock-alt" aria-hidden="true"></i></a>
                        <?php endif; ?>
                    </label>
                    <?php if($userID == 0) : ?>
                        <a class="large-only" href="<?php echo JRoute::_('index.php?option=com_users&view=registration') ?>" ><?php echo ' | ' . JText::_('REGISTER'); ?></a>
                    <?php endif; ?>
                    <?php if($userID > 0) : ?>
                        <a class="large-only" href="<?php echo JRoute::_('index.php?option=com_users&view=profile') ?>" ><?php echo ' | ' . JText::_('MYACCOUNT'); ?></a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($this->countModules('top-menu')) : ?>
                <div class="top-menu right large-only">
                    <jdoc:include type="modules" name="top-menu" style="none"/>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <!-- Menu Block -->
    <?php if ($this->countModules('mainmenu-row')) :
        ?>

        <nav class="nav--top shadow <?php if (getParam('menuLayout') == 'm2'){echo 'block';} else {}?>">
            <div class="nav--top__wrapper">
                <jdoc:include type="modules" name="mainmenu-row" style="none"/>
            </div>
        </nav>

    <?php endif; ?>

    <label class="nav--mobile__toggle" for="nav--mobile__toggle">
        <span></span>
    </label>

    <main class="columns small-24 end">

        <!-- For errors and alerts -->
        <jdoc:include type="message"/>

        <?php if ($this->countModules('breadcrumb-row')) : ?>
            <div class="row">
                <jdoc:include type="modules" name="breadcrumb-row" style="none"/>
            </div>
        <?php endif; ?>

        <!-- For content -->
        <jdoc:include type="component"/>

    </main>
</div>

<!-- Begin Social Footer -->
<?php if ($this->countModules('social-footer')) : ?>
    <div id="svSocialFooter" class="large-24 large-centered light shadow">
        <div class="row">
            <jdoc:include type="modules" name="social-footer" style="none"/>
        </div>
    </div>
<?php endif; ?>

<?php if ($this->countModules('social-frame')) : ?>
    <div id="svSocialFrame" class="large-24 large-centered light shadow">
        <div class="row">
            <jdoc:include type="modules" name="social-frame" style="none"/>
        </div>
    </div>
<?php endif; ?>
<!-- End Social Footer -->

<?php if ($this->countModules('bottom-1')) : ?>
    <div id="svBottom1" class="light shadow">
        <div class="row">
            <jdoc:include type="modules" name="bottom-1" style="none" />
        </div>
    </div>
<?php endif; ?>

<!-- Footer -->
<div class="wrapper--footer <?php if (getParam('footerStyle') == 'dark'){echo 'dark';} else {}?>">

    <footer class="row footer__site collapse">

        <div class="large-24 clearfix">
            <div class="logo-footer">
                <?php if (getParam('showLogoCustomer') == 'true') : ?>
                    <img src="<?php echo JUri::root() . getParam('logoFile'); ?>">
                <?php else : ?>
                    <img src="<?php echo JText::_('COM_WEBPORTAL_SOFTVERK_LOGO'); ?>">
                <?php endif; ?>
            </div>
            <?php if (getParam('showLogoCustomer') == 'true') : ?>
                <em class="footer-text center"><?php echo getParam('copyrightFooter') . ' ' . date("Y") . ' ' . JText::_('COM_WEBPORTAL_COPYRIGHT_RESERVED'); ?></em>
            <?php else : ?>
                <em class="footer-text center"><?php echo JText::_('COM_WEBPORTAL_COPYRIGHT') . ' ' . date("Y") . ' ' . JText::_('COM_WEBPORTAL_COPYRIGHT_RESERVED'); ?></em>
            <?php endif; ?>
            <br/>
            <em class="version-text center <?php if (getParam('versionControl') == 'false'){echo 'hidden';} else {}?>"><?php echo WFactory::getHelper()->getVersionInfoFormatted() ?></em>

        </div>

    </footer>
</div>

<?php if ($this->countModules('login')) : ?>
    <div id="login-popup">
        <a href="#" id="login-popup-close">&times;</a>
        <jdoc:include type="modules" name="login" style="none" />
    </div>
    <div id="login-popup-overlay"></div>
<?php endif; ?>

<a href="#" id="back-to-top" title="Back to top"><i class="fa fa-chevron-up" aria-hidden="true"></i></a>

<jdoc:include type="modules" name="debug"/>

<script src="http://localhost:35729/livereload.js"></script>

<?php if (!empty(getParam('googleAnalytic'))) : ?>
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

        ga('create', '<?php echo getParam('googleAnalytic');?>', 'auto');
        ga('send', 'pageview');
    </script>
<?php endif; ?>

</body>
</html>