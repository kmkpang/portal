<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_breadcrumbs
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>

<ul class="breadcrumbs <?php echo $moduleclass_sfx; ?>" itemprop="breadcrumb">
<?php if ($params->get('showHere', 1))
	{
		echo '<span class="showHere"><i class="fa fa-home"></i></span>';
	}
?>
<?php for ($i = 0; $i < $count; $i ++) :

	// If not the last item in the breadcrumbs add the separator
	if ($i < $count -1) {
		if (!empty($list[$i]->link)) {
			echo '<li><a href="'.$list[$i]->link.'" class="pathway">'.$list[$i]->name.'</a></li>';
		} else {
			echo '<li>';
			echo $list[$i]->name;
			echo '</li>';
		}
	}  elseif ($params->get('showLast', 1)) { // when $i == $count -1 and 'showLast' is true
		 echo '<li class="last">';
		echo $list[$i]->name;
		  echo '</li>';
	}
endfor; ?>
</ul>
