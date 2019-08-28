<?php defined( '_JEXEC' ) or die; 

// Joomla Variables
$app = JFactory::getApplication();
$doc = JFactory::getDocument(); 
$user = JFactory::getUser();

$menu = $app->getMenu();
$active = $app->getMenu()->getActive();
$params = $app->getParams();
$pageclass = $params->get('pageclass_sfx');
$tpath = $this->baseurl.'/templates/'.$this->template;

// Remove generator tag to prevent bots to know it is made by Joomla.
$this->setGenerator(null);

//Scripts to be excluded from automatically loading
$exclude_scripts = ['/media/jui/js/jquery.min.js',
										'/media/jui/js/jquery-noconflict.js',
										'/media/jui/js/jquery-migrate.min.js',
										'/media/system/js/caption.js',
										];

//Disable autoloading of core Joomla JS files
$this->_scripts = array_diff_key($this->_scripts, array_flip($exclude_scripts)); 
$this->_script = preg_grep("/JCaption/i",$this->_script,PREG_GREP_INVERT);

//Used to disable loading of bootstrap in the frontend until converted to Foundation.
require_once("controllers/overrides/bootstrap_override.php");

//Used to contain logic for all functions used in this view template.
require_once("controllers/helpers.php");

?>
<!doctype html>

<html lang="<?php echo $this->language; ?>" ng-app="webportal">

<head>
	<jdoc:include type="head" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
	<!-- <meta name="viewport" content="width=1080px, user-scalable=yes"> -->
	<link rel="apple-touch-icon-precomposed" href="<?php echo $tpath; ?>/images/apple-touch-icon-57x57-precomposed.png">
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo $tpath; ?>/images/apple-touch-icon-72x72-precomposed.png">
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo $tpath; ?>/images/apple-touch-icon-114x114-precomposed.png">
	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo $tpath; ?>/images/apple-touch-icon-144x144-precomposed.png">
	<link rel="stylesheet" type="text/css" href="assets/css/app.min.css" />

</head>
	
<body class="responsive <?php echo (($menu->getActive() == $menu->getDefault()) ? ('front') : ('site')).' '.$active->alias.' '.$pageclass; ?>">
	<?php //var_dump($this); ?>

<div class="wrapper">

	<?php #This is required for the mobile menu to function ?>
	<input class="hidden" id="nav--mobile__toggle" type="checkbox">
	<label class="nav--mobile__toggle" for="nav--mobile__toggle"></label>

	<header class="header">
		<div class="row">
			<div class="small-24 columns top-logo">
				<a href="<?php echo $this->baseurl; ?>">
					<?php echo getLogo(); ?> 
				</a>
			</div>
		</div>
	</header>

	<?php if ($this->countModules('logo-banner')) : ?>
		<nav class="top-nav">
				<div class="top-nav__wrapper">
					<jdoc:include type="modules" name="logo-banner" style="none" />
				</div>
		</nav>
	<?php endif; ?>

	<?php if ($this->countModules('breadcrumb-row')) : ?>
		<div class="row">
			<jdoc:include type="modules" name="breadcrumb-row" style="none" />
		</div>
	<?php endif; ?>
			
	<jdoc:include type="modules" name="banner" style="none" />

	<div class="main-content row">
		<?php if ($this->countModules('position-8')) : ?>

		<!-- Begin Sidebar -->
		<div id="sidebar" class="span3">
			<div class="sidebar-nav">
				<!-- <jdoc:include type="modules" name="position-8" style="none" /> -->
			</div>
		</div>

		<!-- End Sidebar -->
		<?php endif; ?>
		<main class="columns small-24 end">

			<!-- For errors and alerts -->
			<jdoc:include type="message" />

			<!-- For content -->
			<jdoc:include type="component" /> 

		</main>
		<?php if ($this->countModules('position-7')) : ?>
		<div id="aside" class="span3">

			<!-- Begin Right Sidebar -->
			<jdoc:include type="modules" name="position-7" style="none" />
			<!-- End Right Sidebar -->

		</div>
		<?php endif; ?>
	</div>

<!-- Footer -->
<footer class="row footer collapse">
	<jdoc:include type="modules" name="footer" style="none" />
</footer>

</div>


<jdoc:include type="modules" name="debug" />

<script src="http://localhost:35729/livereload.js"></script>
	
</body>
</html>