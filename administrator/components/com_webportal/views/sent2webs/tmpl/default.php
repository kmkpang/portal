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

//JFactory::getDocument()->addScript();

?>
<script src="https://cdn.rawgit.com/google/code-prettify/master/loader/run_prettify.js?skin=desert"></script>
<style>
    .INCOMING {
        background: lightsalmon !important;
    }

    .OUTGOING {
        background: lightgreen !important;
    }
</style>

<div id="j-sidebar-container" class="span2">
    <?php echo $this->sidebar; ?>
</div>

<div id="j-main-container" class="span10" ng-app="webportal" ng-controller="XmlSearchCtrl" ng-init="filterXml()">

    <div id="xmlSearchForm"
         name="xmlSearchForm">

        <table class="js-stools clearfix">
            <tr>
                <td>
                    <input type="text" placeholder="Property unique id" ng-model="xmlSearchModel.propertyUniqueId"
                           name="propertyUniqueId">
                </td>

                <td>
                    <input type="text" placeholder="Agent unique id" ng-model="xmlSearchModel.agentUniqueId"
                           name="agentUniqueId">
                </td>

                <td>
                    <input type="text" placeholder="Office unique id" ng-model="xmlSearchModel.officeUniqueId"
                           name="officeUniqueId">
                </td>
            </tr>

            <tr>
                <td>
                    <select name="direction" ng-model="xmlSearchModel.direction">
                        <option value="">Direction</option>
                        <option value="INCOMING">Incoming ( SAGA &#x2192; Portal )</option>
                        <option value="OUTGOING">Outgoing ( Portal &#x2192; SAGA )</option>
                    </select>
                </td>

                <td>
                    <select name="command" ng-model="xmlSearchModel.command">
                        <option value="">Command</option>
                        <option value="CREATE">Create</option>
                        <option value="UPDATE">Update</option>
                        <option value="DELETE">Delete</option>
                    </select>
                </td>

                <td>
                    <select name="type" ng-model="xmlSearchModel.type">
                        <option value="">Type</option>
                        <option value="OFFICE">Office</option>
                        <option value="AGENT">Agent</option>
                        <option value="PROPERTY">Property</option>
                        <option value="PROJECT">Project</option>
                    </select>
                </td>
            </tr>

            <tr>
                <td>
                    <input type="text" name="date"
                           ng-model="xmlSearchModel.date"
                           ng-init="createDatePicker()"
                           style="display: none"
                        >
                </td>

                <td>
                    <label>Show Command/Response <input type="checkbox" name="getAssociated"
                                                        ng-model="xmlSearchModel.getAssociated"></label>

                </td>

                <td>
                    <button class="btn btn-small btn-success pull-right"
                            ng-click="filterXml()"
                            ng-model="xmlSearchModel.direction">

                        <span class="icon-search icon-white">
                        </span>
                        {{searchText}}
                    </button>
                </td>
            </tr>


        </table>

    </div>

    <div id="editcell">
        <table class="table table-striped">
            <tr>
                <th>ID</th>
                <th>Timestamp</th>
                <th>Link</th>
                <th>Direction</th>
                <th>Type</th>
                <th>Command</th>
                <th>Details</th>
                <th>Resend Xml</th>
            </tr>
            <tbody>

            <tr ng-repeat="xml in xmlData">

                <td class="{{xml.direction}}">
                    {{xml.id}}
                </td>
                <td class="{{xml.direction}}">
                    {{xml.date}}
                </td>
                <td class="{{xml.direction}}">
                    <a ng-show="xml.officeAgentPropertyId > 0 " href="{{xml.officeAgentPropertyLink}}" target="_blank">{{xml.officeAgentPropertyId}}</a>
                </td>
                <td class="{{xml.direction}}">
                    {{xml.direction}}
                </td>
                <td class="{{xml.direction}}">
                    {{xml.type}}
                </td>
                <td class="{{xml.direction}}">
                    {{xml.command}}
                </td>
                <td class="{{xml.direction}}">
                    <a href="<?php echo JUri::base() . 'index.php?option=com_webportal&controller=sent2webs&task=getFile&file=/administrator/components/com_webportal/views/sent2webs/tmpl/xmlFile.php&xmlId=' ?>{{xml.id}}"
                       target="_blank">Show Data</a>

                </td>
                <td class="{{xml.direction}}">



                    <span style="cursor: pointer;text-decoration: underline"
                          title="Resend this xml to emulate a saga incoming xml,DON'T USE FOR COMMAND CREATE"

                          ng-if="xml.direction=='INCOMING'"
                          confirmed-click="resendXml(xml.id)"
                          ng-confirm-click="Are you sure?"
                          href="#"
                        >Execute</span>


                </td>

            </tr>

            </tbody>
        </table>

    </div>

    <label>{{executeState}}</label>

</div>

                        
