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

$officeService = WFactory::getServices()->getSqlService()->getSqlServiceClass(__PROPPERTY_PORTAL_OFFICE);
$offices = $officeService->getOffices();

?>
<div id="j-sidebar-container" class="span2">
    <?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
    <form action="index.php?option=com_webportal&view=office&layout=edit" method="post" id="adminForm" name="adminForm">
    <div id="editcell">
        <table class="table table-striped">
            <tr>
                <th>ID</th>
                <th><?php echo JHtml::_('grid.checkall'); ?></th>
                <th><?php echo JText::_('COM_WEBPORTAL_OFFICES_MANAGER_OFFICE_NAME'); ?></th>
                <th><?php echo JText::_('COM_WEBPORTAL_OFFICES_MANAGER_EMAIL'); ?></th>
                <th><?php echo JText::_('COM_WEBPORTAL_OFFICES_MANAGER_FAX'); ?></th>
                <th><?php echo JText::_('COM_WEBPORTAL_OFFICES_MANAGER_PHONE'); ?></th>
                <th><?php echo JText::_('COM_WEBPORTAL_OFFICES_MANAGER_WEBSITE'); ?></th>
                <th><?php echo JText::_('COM_WEBPORTAL_OFFICES_MANAGER_IMAGE'); ?></th>
                <th><?php echo JText::_('COM_WEBPORTAL_OFFICES_MANAGER_DATE_ENTER'); ?></th>
                <th><?php echo JText::_('COM_WEBPORTAL_OFFICES_MANAGER_DATE_MODIFIED'); ?></th>
            </tr>
            <tbody>
            <?php
            foreach($offices as $o)
            {
            $link = JRoute::_('index.php?option=com_webportal&view=office&layout=edit&office_id=' . $o->id);
            ?>

            <tr>
                <td width="2%">
                        <?php echo $o->id; ?>
                </td>
                <td width="2%">
                    <?php echo JHtml::_('grid.id', $i, $o->id); ?>
                </td>
                <td>
                    <a href="<?php echo $link; ?>">
                        <?php echo $o->office_name; ?>
                    </a>
                </td>
                <td>
                        <?php echo $o->email; ?>
                </td>
                <td>
                        <?php echo $o->fax; ?>
                </td>
                <td>
                        <?php echo $o->phone; ?>
                </td>
                <td>
                    <?php echo $o->url_to_private_page; ?>
                </td>
                <td>
                     <img src="<?php echo $o->image_file_path; ?>" width="60">
                </td>
                <td width="7%">
                    <?php echo $o->date_entered; ?>
                </td>
                <td width="7%">
                    <?php echo $o->date_modified; ?>
                </td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <input type="hidden" name="task" value="edit"/>
    <input type="hidden" name="boxchecked" value="0"/>
    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
    <?php echo JHtml::_('form.token'); ?>
    </form>
</div>

                        