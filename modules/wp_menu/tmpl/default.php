<?php
/**
 * @package     Joomla.Site
 * @subpackage  wp_menu
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Note. It is important to remove spaces between elements.
if ($responsive) $m_menu = "mobile-menu_activate";
?>

<ul class="nav--main nav<?php echo $m_menu . ' ' . $class_sfx; ?>
<?php if (getParam('menuPosition') == 'left' && getParam('menuLayout') == 'm2')
	{
		echo 'block large-text-left';
	} else if (getParam('menuPosition') == 'right' && getParam('menuLayout') == 'm2') {
		echo 'block';
	} else {} ?>"
	<?php
$tag = '';
if ($params->get('tag_id') != null) {
	$tag = $params->get('tag_id') . '';
	echo ' id="' . $tag . '"';
}
?>>
	<?php

	foreach ($list as $i => &$item) :
		// $class = 'item-'.$item->id;
		$class = "nav__item ";
		$class .= $item->anchor_css ? $item->anchor_css : '';
		if ($item->id == $active_id) {
			$class .= ' current';
		}

		if (in_array($item->id, $path)) {
			$class .= ' active';
		} elseif ($item->type == 'alias') {
			$aliasToId = $item->params->get('aliasoptions');
			if (count($path) > 0 && $aliasToId == $path[count($path) - 1]) {
				$class .= ' active';
			} elseif (in_array($aliasToId, $path)) {
				$class .= ' alias-parent-active';
			}
		}

		if ($item->type == 'separator') {
			$class .= ' divider';
		}

		if ($item->deeper) {
			$class .= ' deeper';
		}

		if ($item->parent) {
			$class .= ' parent';
		}

		if (!empty($class)) {
			$class = ' class="' . trim($class) . '"';
		}

		echo '<li' . $class . '>';
		if ($item->deeper) {
			echo '<span class="child--toggle"><i class="fa fa-angle-left"></i></span>';
		}
		// Render the menu item.
		switch ($item->type) :
			case 'separator':
			case 'url':
			case 'component':
			case 'heading':
				require JModuleHelper::getLayoutPath('wp_menu', $item->type);
				break;

			default:
				require JModuleHelper::getLayoutPath('wp_menu', 'url');
				break;
		endswitch;

		// The next item is deeper.
		if ($item->deeper) {
			echo '<ul class="nav--child">';

		} // The next item is shallower.
		elseif ($item->shallower) {
			echo '</li>';
			echo str_repeat('</ul></li>', $item->level_diff);
		} // The next item is on the same level.
		else {
			echo '</li>';
		}
	endforeach;

	if (getParam('languageEnable') == 'true') {
		$app = JFactory::getApplication();
		$menu = $app->getMenu();

		$langs = $otherLangs = JFactory::getLanguage()->getKnownLanguages();

		$isHome = false;


		foreach ($langs as $l) {

			if ($menu->getActive() == $menu->getDefault($l['tag'])) {
				$isHome = true;
				break;
			}
		}


		$path = WFactory::getHelper()->getCurrentUrlPathWithOutLanguage();


		// $menu_contents =  ob_get_flush();
		// Very Hacky Language Position - Use modules instead later on
		echo "<li class='nav__item lang'>";

		$langTag = WFactory::getHelper()->getCurrentlySelectedLanguage();
		$languages = WFactory::getHelper()->getAllLang()
		?>

		<a href="<?php echo JUri::base() ?><?php echo $langTag ?>/"><span
				class="language__flag--<?php echo $langTag ?>"></span></a>

		<ul class="nav--child">

			<?php

			foreach ($languages as $l => $fullTag) {

				$langPath = JUri::base() . $l;

				if (!WFactory::getHelper()->isNullOrEmptyString($path))
					$langPath .= "/$path";

				?>

                <!--Menu Language-->
                <li class="nav__item">
                    <a href="<?php echo $langPath ?>/" class="lang__flag--container">
                        <span class="language__flag--<?php echo $l ?>">
                        </span>
                        <!--Menu Language Add text-->
                        <span class="language--text"><?php if ($l == 'en') echo "English";
                            elseif ($l == 'th') echo "ภาษาไทย";
                            else echo $l; ?></span>
                    </a>
                </li>
				<?php
			}
			?>

		</ul>

		<?php
		echo "</li>";

	}


	?></ul>

