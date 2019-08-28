<?php
defined('_JEXEC') or die;

$sitename = JFactory::getApplication()->getCfg('sitename');
?>

<div class="footer__site small-centered large-uncentered small-24 large-16 columns <?php echo $moduleclass_sfx ?>">
	<span><?php
        echo "<div class='sitelogo'>" . getLogo() . "</div>";
        echo "<span class='sitename'>$sitename | </span>";
        echo strip_tags($module->content, '<div><span><b><strong><i><em><u><a>');
        ?></span>
</div>

<div class="footer__softverk small-centered large-uncentered small-24 large-8 columns <?php echo $moduleclass_sfx ?>">
    <img height="50" alt="Softverk Real Estate Management Software Development" src="<?php echo JUri::base() ?>images/softverk.png"/> <span>&copy;
        Softverk <?php echo date('Y'); ?> All rights reserved.</span>
</div>
