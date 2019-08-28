<?php
/**
 * @package     Joomla.Site
 * @subpackage  wp_menu
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once __DIR__ . '/helper.php';

$list		= WpMenuHelper::getList($params);
$base		= WpMenuHelper::getBase($params);
$active		= WpMenuHelper::getActive($params);
$active_id 	= $active->id;
$path		= $base->tree;

$showAll	= $params->get('showAllChildren');
$responsive = $params->get('responsiveMenu');
$class_sfx	= htmlspecialchars($params->get('class_sfx'));

if (count($list))
{
	require JModuleHelper::getLayoutPath('wp_menu', $params->get('layout', 'default'));
}
