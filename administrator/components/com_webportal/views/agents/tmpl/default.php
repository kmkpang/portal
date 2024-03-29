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

//$officeService = WFactory::getServices()->getSqlService()->getSqlServiceClass(__PROPPERTY_PORTAL_OFFICE);
//$offices = $officeService->getOffices();

$agentsService = WFactory::getServices()->getSqlService()->getSqlServiceClass(__PROPPERTY_PORTAL_AGENTS);
$agents = $agentsService->getAgents(null, true);

?>
<div id="j-sidebar-container" class="span2">
    <?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
    <form action="index.php?option=com_webportal&view=agent&layout=edit" method="post" id="adminForm" name="adminForm">
        <div id="editcell">
            <table class="table table-striped">
                <tr>
                    <th>ID</th>
                    <th><?php echo JHtml::_('grid.checkall'); ?></th>
                    <th>Office Name</th>
                    <th>Agent Name</th>
                    <th>Email</th>
                    <th>Created</th>
                    <th>Last Updated</th>
                    <th>Image</th>
                </tr>
                <tbody>
                <?php
                /**
                 * @var $a AgentsModel
                 */
                foreach ($agents as $a) {
                    $agentLink = JRoute::_('index.php?option=com_webportal&view=agent&layout=edit&agent_id=' . $a->id);
                    $officeLink = JRoute::_('index.php?option=com_webportal&view=office&layout=edit&office_id=' . $a->office_id);
                    ?>

                    <tr>
                        <td width="2%">
                            <?php echo $a->id; ?>
                        </td>
                        <td width="2%">
                            <?php echo JHtml::_('grid.id', $i, $a->id); ?>
                        </td>
                        <td>
                            <a href="<?php echo $officeLink; ?>">
                                <?php echo $a->office_name; ?>
                            </a>
                        </td>
                        <td>
                            <a href="<?php echo $agentLink; ?>">
                                <?php echo "{$a->first_name} {$a->last_name}"; ?>
                            </a>
                        </td>
                        <td>
                            <?php echo $a->email; ?>
                        </td>
                        <td width="7%">
                            <?php echo $a->date_entered; ?>
                        </td>
                        <td width="7%">
                            <?php echo $a->date_modified; ?>
                        </td>
                        <td>
                            <img src="<?php echo $a->image_file_path; ?>" width="40">
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