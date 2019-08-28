<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_news
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


$articleCount = count($list);
$singleArticleWidth = 8;
$largeWidthContainer = $singleArticleWidth * $articleCount;
$largeWidthArticle = 24 / $articleCount;


?>
<div
    class="small-24 large-<?php echo $largeWidthContainer ?> columns newsflash<?php echo $moduleclass_sfx; ?>">
    <?php
    foreach ($list as $item) :
        ?>
        <div class="articles--news__item large-<?php echo $largeWidthArticle ?> column">
            <?php require JModuleHelper::getLayoutPath('mod_articles_news', '_item'); ?>
        </div>
        <?php
    endforeach;
    ?>
</div>
