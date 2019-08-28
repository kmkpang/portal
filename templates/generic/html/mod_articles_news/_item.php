<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_news
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$images = json_decode($item->images);
$item_heading = $params->get('item_heading', 'h4');
?>
<?php if ($params->get('item_title')) : ?>

	<<?php echo $item_heading; ?> class="news__title<?php echo $params->get('moduleclass_sfx'); ?>">
	<?php if ($params->get('link_titles') && $item->link != '') : ?>
		<a href="<?php echo $item->link;?>">
			<?php echo $item->title;?></a>
	<?php else : ?>
		<?php echo $item->title; ?>
	<?php endif; ?>
	</<?php echo $item_heading; ?>>

<?php endif; ?>

<img class="news__intro-image" src="<?php echo htmlspecialchars($images->image_intro); ?>"
     alt="<?php echo htmlspecialchars($images->image_intro_alt); ?>"/>

<?php if (!$params->get('intro_only')) :
    echo $item->afterDisplayTitle;
endif; ?>

<?php echo $item->beforeDisplayContent; ?>

<?php if (isset($item->link) && $item->readmore != 0 && $params->get('readmore')) :
    echo '<a class="readmore" href="' . $item->link . '">' . $item->linkText . '</a>';
endif; ?>


<span style="color: #52616a; font-family: Arial, Helvetica, sans-serif; font-size: 15px;">

                <?php echo strip_tags($item->introtext); ?>

            </span>




