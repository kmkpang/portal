<?php
/**
 * @package     Joomla.Site
 * @subpackage  wp_articles_news
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$item_heading = $params->get('item_heading', 'h4');
?>

<?php if ($params->get('image')) : ?>
	<?php if ($params->get('link_titles') && $item->link != '') : ?>
		<a href="<?php echo $item->link;?>">
			<div class="newsflash-image">
				<img src="<?php echo JUri::base() .json_decode($item->images)->image_intro ;?>" alt="<?php echo $item->title; ?>"/>
			</div>
		</a>
	<?php else : ?>
		<div class="newsflash-image">
			<img src="<?php echo JUri::base() .json_decode($item->images)->image_intro ;?>" alt="<?php echo $item->title; ?>"/>
		</div>
	<?php endif; ?>
<?php endif; ?>

<?php if ($params->get('item_title')) : ?>

	<<?php echo $item_heading; ?> class="newsflash-title">
	<?php if ($params->get('link_titles') && $item->link != '') : ?>
		<a href="<?php echo $item->link;?>">
			<?php echo $item->title;?></a>
	<?php else : ?>
		<?php echo $item->title; ?>
	<?php endif; ?>
	</<?php echo $item_heading; ?>>

<?php endif; ?>

<?php if (!$params->get('intro_only')) :
	echo $item->afterDisplayTitle;
endif; ?>

<?php echo $item->beforeDisplayContent; ?>

<?php if ($params->get('item_introtext')) :
	echo $item->introtext;
endif; ?>

<?php if (isset($item->link) && $params->get('readmore')) :
	echo '<a class="readmore" href="'.$item->link.'">'.$item->linkText.'</a>';
endif; ?>
