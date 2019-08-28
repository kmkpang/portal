<?php
/**
 * @package     Joomla.Site
 * @subpackage  wp_articles_news
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$catid = $params->get('viewall_catid');
$app = JFactory::getApplication();
$menu = $app->getMenu();
$menuListItem = $menu->getItems( 'link', 'index.php?option=com_content&view=category&layout=blog&id=' . $catid, true );

$columns = $params->get('count');
?>
<?php if ($module->showtitle == 1) : ?><h3 class="modules__caption"><?php echo $module->title; ?></h3><?php endif; ?>
<ul class="row newsflash-vertical <?php echo $params->get('moduleclass_sfx'); ?>">
<?php for ($i = 0, $n = count($list); $i < $n; $i ++) :	$item = $list[$i]; ?>
	
	<li class="newsflash-item columns small-24 medium-<?php echo 24/$columns ?>">
		<?php require JModuleHelper::getLayoutPath('wp_articles_news', '_item');
			if ($n > 1 && (($i < $n - 1) || $params->get('showLastSeparator'))) : ?>
				<span class="article-separator">&#160;</span>
		<?php endif; ?>
	</li>
	
<?php endfor; ?>
</ul>

<?php if($params->get('viewall') == 'true' ) { ?>
	<div class="newsflash-view-all">
		<a href="<?php echo JRoute::_('index.php?Itemid='.$menuListItem->id); ?>" class="input-submit primary-medium"><?php echo JText::_('VIEW_ALL'); ?></a>
	</div>
<?php } ?>