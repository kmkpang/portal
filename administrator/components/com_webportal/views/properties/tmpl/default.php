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

$propertiesService = WFactory::getServices()->getSqlService()->getSqlServiceClass(__PROPPERTY_PORTAL_PROPERTIES);
$properties = $propertiesService->getProperties();

$officeService = WFactory::getServices()->getSqlService()->getSqlServiceClass(__PROPPERTY_PORTAL_OFFICE);
$offices = $officeService->getOffices();

$agentsService = WFactory::getServices()->getSqlService()->getSqlServiceClass(__PROPPERTY_PORTAL_AGENTS);
$agents = $agentsService->getAgents(null, true);

$searchModel = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_SEARCH)->getSearchModel();
$search_result = null;
?>
<div id="j-sidebar-container" class="span2">
    <?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">

    <form ng-controller="PropertiesAdminCtrl" action="index.php?option=com_webportal&view=properties&layout=default"
          method="post" id="adminForm" name="adminForm">

        <div>

            <table class="js-stools clearfix">
                <tr>

                    <td>
                        <input type="text" placeholder="Property id or unique id"
                               name="propertyUniqueId">
                    </td>

                </tr>
                <tr>
                    <td>
                        <select name="agentUniqueId">
                            <option value="">Select agents</option>
                            <?php foreach ($agents as $a) { ?>
                                <option value="<?php echo $a->unique_id; ?>"><?php echo $a->full_name; ?></option>
                            <?php } ?>
                        </select>
                    </td>
                    <td>
                        <select name="officeUniqueId">
                            <option value="">Select offices</option>
                            <?php foreach ($offices as $o) { ?>
                                <option value="<?php echo $o->unique_id; ?>"><?php echo $o->office_name; ?></option>
                            <?php } ?>
                        </select>
                    </td>

                    <td>
                        <button type="submit" value="search" class="btn btn-small btn-success pull-right">

                        <span class="icon-search icon-white">
                        </span>
                            Search
                        </button>
                    </td>

                </tr>
            </table>

        </div>

        <div id="editcell">
            <table class="table table-striped">
                <tr>
                    <th>ID</th>
                    <th><?php echo JHtml::_('grid.checkall'); ?></th>
                    <th>Image</th>
                    <th>Address</th>
                    <th>Property type</th>
                    <th>Price</th>
                    <th>Office</th>
                    <th>Agent</th>
                    <th>Date enter</th>
                    <th>Date modified</th>
                </tr>
                <tbody>
                <?php
                foreach ($properties as $p) {
                    $link = JRoute::_('index.php?option=com_webportal&view=property&layout=edit&property_id=' . $p->property_id);
                    ?>

                    <tr>
                        <td width="2%">
                            <?php echo $p->property_id; ?>
                        </td>
                        <td width="2%">
                            <?php echo JHtml::_('grid.id', $i, $p->property_id); ?>
                        </td>
                        <td>
                            <a href="<?php echo $link; ?>">
                                <img src="<?php echo $p->list_page_thumb_path; ?>" width="60px">
                            </a>
                        </td>
                        <td>
                            <a href="<?php echo $link; ?>">
                                <?php echo $p->address; ?>
                            </a>
                        </td>
                        <td>
                            <?php echo $p->category_name; ?>
                        </td>
                        <td>
                            <?php echo number_format($p->current_listing_price); ?>
                        </td>
                        <td>
                            <?php echo $p->office_name; ?>
                        </td>
                        <td>
                            <?php echo $p->sales_agent_full_name; ?>
                        </td>
                        <td width="7%">
                            <?php echo $p->created_date; ?>
                        </td>
                        <td width="7%">
                            <?php echo $p->last_update; ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
        <input type="hidden" name="task" value="search"/>
        <input type="hidden" name="boxchecked" value="0"/>
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
        <?php echo JHtml::_('form.token'); ?>
    </form>
</div>

                        