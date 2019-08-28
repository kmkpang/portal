<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_webportal
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

JHtml::_('formbehavior.chosen', 'select');

$listOrder = $this->escape($this->filter_order);
$listDirn = $this->escape($this->filter_order_Dir);
?>

<div id="j-sidebar-container" class="span2">
    <?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
    <form action="index.php?option=com_webportal&view=webportals" method="post" id="adminForm" name="adminForm">
        <div class="row-fluid">
            <div class="span6">
                <?php echo JText::_('COM_WEBPORTAL_WEBPORTALS_FILTER'); ?>
                <?php
                echo JLayoutHelper::render(
                    'joomla.searchtools.default',
                    array('view' => $this)
                );
                ?>
            </div>
        </div>
        <table class="table table-striped table-hover">
            <thead>
            <tr>
                <th width="1%">#</th>
                <th width="2%">
                    <?php echo JHtml::_('grid.checkall'); ?>
                </th>
                <th width="10%">
                    <?php echo JHtml::_('grid.sort', 'Image', '', $listDirn, $listOrder); ?>
                </th>
                <th width="25%">
                    <?php echo JHtml::_('grid.sort', 'Name', 'name', $listDirn, $listOrder); ?>
                </th>
                <th width="20%">
                    <?php echo JHtml::_('grid.sort', 'Category', 'category', $listDirn, $listOrder); ?>
                </th>
                <th width="25%">
                    <?php echo JHtml::_('grid.sort', 'Link', '', $listDirn, $listOrder); ?>
                </th>
                <th width="5%">
                    <?php echo JHtml::_('grid.sort', 'Deleted', 'is_deleted', $listDirn, $listOrder); ?>
                </th>
                <th width="2%">
                    <?php echo JHtml::_('grid.sort', 'Id', 'id', $listDirn, $listOrder); ?>
                </th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <td colspan="5">
                    <?php echo $this->pagination->getListFooter(); ?>
                </td>
            </tr>
            </tfoot>
            <tbody>
            <?php if (!empty($this->items)) : ?>
                <?php foreach ($this->items as $i => $row) :
                    $link = JRoute::_('index.php?option=com_webportal&task=webportal.edit&id=' . $row->id);
                    ?>
                    <tr>
                        <td><?php echo $this->pagination->getRowOffset($i); ?></td>
                        <td>
                            <?php echo JHtml::_('grid.id', $i, $row->id); ?>
                        </td>

                        <!-- ----------------------------- stuff ------------------------------------------- -->


                        <th>
                            <img src="<?php echo $row->image?>" style="width: 75px">
                        </th>
                        <th width="20%">
                            <?php echo $row->name?>
                        </th>
                        <th width="20%">
                            <?php echo $row->category?>
                        </th>
                        <th width="25%">
                            <a href="<?php echo $row->link?>"><?php echo urldecode($row->link)?></a>
                        </th>


                        <!-- ----------------------------- stuff ------------------------------------------- -->

                        <td align="center">
                            <?php echo JHtml::_('jgrid.published', $row->published, $i, 'webportals.', true, 'cb'); ?>
                        </td>
                        <td align="center">
                            <?php echo $row->id; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
        <input type="hidden" name="task" value=""/>
        <input type="hidden" name="boxchecked" value="0"/>
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
        <?php echo JHtml::_('form.token'); ?>
    </form>
</div>


