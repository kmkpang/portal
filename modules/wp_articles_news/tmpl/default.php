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

?>
<div class="newsflash <?php echo $params->get('moduleclass_sfx'); ?>">
<?php if ($module->showtitle == 1) : ?><h3 class="modules__caption"><?php echo $module->title; ?></h3><?php endif; ?>
<?php foreach ($list as $item) :
	require JModuleHelper::getLayoutPath('wp_articles_news', '_item');
endforeach; ?>

	<?php if($params->get('viewall') == 'true' ) { ?>
		<div class="newsflash-view-all">
			<a href="<?php echo JRoute::_('index.php?Itemid='.$menuListItem->id); ?>" class="input-submit primary-medium"><?php echo JText::_('VIEW_ALL'); ?></a>
		</div>
	<?php } ?>
</div>
