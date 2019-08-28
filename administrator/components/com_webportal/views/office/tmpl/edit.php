<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_webportal
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
//C:\xampp\htdocs\softverk-webportal-remaxth\administrator\components\com_webportal\views\office\tmpl\edit.php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.formvalidation');

$officeId = JFactory::getApplication()->input->getInt("office_id");

/**
 * @var $office OfficeModel
 */
$office = WFactory::getServices()->getSqlService()->getSqlServiceClass(__PROPPERTY_PORTAL_OFFICE)->getOffice($officeId, true);
$office->agents = WFactory::getServices()->getSqlService()->getSqlServiceClass(__PROPPERTY_PORTAL_AGENTS)->getAgents($office->id, true);
$postalCodeTree = WFactory::getServices()->getSqlService()->getSqlServiceClass(__PROPPERTY_PORTAL_ADDRESS)->postalCodeTree();


//test

//$office->address->region_id = "5";
//$office->address->town_id = "35";
//$office->address->postal_code_id = "63";
//$office->show_on_web = "0";


?>
<script type="text/javascript">
    window.office =<?php echo json_encode($office)?>;
    window.postal_code_tree =<?php echo json_encode($postalCodeTree)?>;
</script>
<form ng-controller="OfficeAdminCtrl"
      ng-init='initOffice()'
      action="<?php echo JUri::root() . 'administrator/index.php?option=com_webportal&view=office&layout=edit&task=edit&office_id=' . $office->id ?>"
      method="post" name="adminForm" id="adminForm" class="form-validate" ng-app="webportal">
    <div class="form-horizontal">

        <fieldset class="adminform">
            <legend>Edit Office</legend>
            <div class="row-fluid">

                <div class="span6">

                    <legend>Images and Logos</legend>
                    <div class="control-group">
                        <div class="control-label">
                            Office Image
                        </div>
                        <div class="controls">
                            <div class="editofficeimage">
                                <img ng-src="{{office.image_file_path}}">
                                <input type="file" name="file"
                                       onchange="angular.element(this).scope().processNewFiles(this.files,'office_image')">
                            </div>
                        </div>
                        <div class="control-label">
                            Office Logo
                        </div>
                        <div class="controls">
                            <div class="editofficeimage">
                                <img ng-src="{{office.logo}}">
                                <input type="file" name="file"
                                       onchange="angular.element(this).scope().processNewFiles(this.files,'office_logo')">
                            </div>
                        </div>
                    </div>

                </div>


                <div class="span6">
                    <legend>Details</legend>
                    <div class="control-group">
                        <div class="control-label">Unique Id</div>
                        <div class="controls">
                            <input type="text" name="unique_id" id="unique_id" size="32" maxlength="250"
                                   ng-model="office.unique_id">
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Office Name</div>
                        <div class="controls">
                            <input type="text" name="office_name" id="office_name" size="32" maxlength="250"
                                   ng-model="office.office_name">
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Office Email</div>
                        <div class="controls">

                            <input class="text_area" type="text" name="email" id="email" size="32" maxlength="250"
                                   ng-model="office.email"/>

                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Fax</div>
                        <div class="controls">

                            <input type="text" name="fax" size="32" maxlength="250" ng-model="office.fax">

                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Phone</div>
                        <div class="controls">

                            <input type="text" name="phone" size="32" maxlength="250"
                                   ng-model="office.phone">

                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Office Manager</div>
                        <div class="controls">

                            <select ng-model="office.manager_id">
                                <option value="" selected default>Select Manager</option>
                                <option ng-selected="{{office.manager_id == agent.id}}"
                                        ng-repeat="agent in office.agents"
                                        value="{{agent.id}}">
                                    {{agent.full_name}}
                                </option>
                            </select>

                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Certified Agent</div>
                        <div class="controls">
                            <select ng-model="office.certified_agent_id">
                                <option value="" selected default>Select Manager</option>
                                <option ng-selected="{{office.certified_agent_id == agent.id}}"
                                        ng-repeat="agent in office.agents"
                                        value="{{agent.id}}">
                                    {{agent.full_name}}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Website</div>
                        <div class="controls">
                            <input type="text" name="url_to_private_page" size="32" maxlength="250"
                                   ng-model="office.url_to_private_page">
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Date Entered</div>
                        <div class="controls">
                            <label>{{office.date_entered}}</label>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Date Modified</div>
                        <div class="controls">
                            <label>{{office.date_modified}}</label>
                        </div>
                    </div>
                </div>

                <div class="span6">
                    <legend>Marketing Info</legend>
                    <div class="control-group">
                        <div class="control-label">Slogan</div>
                        <div class="controls">
                            <textarea ng-model="office.marketingInfo.slogan"></textarea>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Closer</div>
                        <div class="controls">
                            <textarea ng-model="office.marketingInfo.closer"></textarea>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Bullet Point 1</div>
                        <div class="controls">
                            <textarea ng-model="office.marketingInfo.bullet_point1">{{office.marketingInfo.bullet_point1}}</textarea>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Bullet Point 2</div>
                        <div class="controls">
                            <textarea ng-model="office.marketingInfo.bullet_point2"></textarea>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Bullet Point 3</div>
                        <div class="controls">
                            <textarea ng-model="office.marketingInfo.bullet_point3"></textarea>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Description</div>
                        <div class="controls">
                            <textarea ng-model="office.marketingInfo.description"></textarea>
                        </div>
                    </div>
                </div>

                <div class="span6">
                    <legend>Address</legend>
                    <div class="control-group">
                        <div class="control-label">Province</div>
                        <div class="controls">
                            <select name="province" ng-model="office.address.region_id" id="province"
                                    ng-change="updateGoogleMapCenter('regions');"
                                    ng-options="region.id as region.name for region in postal_code_tree.regions">
                                <option value="" selected default>Province</option>
                            </select>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">City/Town</div>
                        <div class="controls">
                            <select name="city_town" ng-model="office.address.town_id" id="city_town"
                                    ng-change="updateGoogleMapCenter('towns');"
                                    ng-options="town.id as town.name for town in postal_code_tree.towns | filter:filterTown">
                                <option value="" selected default>City/Town</option>
                            </select>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Postal Code</div>
                        <div class="controls">
                            <select name="postal" id="postal" ng-model="office.address.postal_code_id"
                                    ng-change="updateGoogleMapCenter('postal_codes');"
                                    ng-options="postals.id as postals.name for postals in postal_code_tree.postals | filter:filterPostal">
                                <option value="" selected default>Postal Code</option>
                            </select>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Address</div>
                        <div class="controls">

                            <input type="text" name="" size="32" maxlength="250" ng-model="office.address.address">

                        </div>
                    </div>


                    <div class="control-group">
                        <div class="control-label">Latitude</div>
                        <div class="controls">
                            <input type="text" size="32" maxlength="250"
                                   ng-model="office.address.latitude">
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Longitude</div>
                        <div class="controls">
                            <input type="text" size="32" maxlength="250"
                                   ng-model="office.address.longitude">
                        </div>
                    </div>


                    <div class="control-group">
                        <div class="control-label">Map Location</div>
                        <div class="controls">
                            <pindrop-map title="<?php echo $office->office_name ?>"
                                         lat="<?php echo $office->address->latitude ?>"
                                         lng="<?php echo $office->address->longitude ?>"></pindrop-map>
                        </div>
                    </div>
                </div>
            </div>


            <div class="span12">
                <legend>Actions</legend>
                <div class="control-group">

                    <a class="btn btn-small" title="Save Agent"
                       href="<?php echo JUri::root() . "administrator/index.php?option=com_webportal&view=offices" ?>">
                        <span class="icon-back"></span>
                        Back
                    </a>

                    <a class="btn btn-small" confirmed-click="togglePublish(true)"
                       ng-confirm-click="Publish Office to Web ? "
                       title="Show or Hide from web ( office list ) , Agents and Properties left untouched"
                       ng-show="office.show_on_web === '0'">
                        <span class="icon-publish"></span>
                        Publish on web
                    </a>
                    <a class="btn btn-small" confirmed-click="togglePublish(false)"
                       ng-confirm-click="Unpublish Office from Web ? "
                       title="Show or Hide from web  ( office list ) , Agents and Properties left untouched"
                       ng-show="office.show_on_web === '1'">
                        <span class="icon-unpublish"></span>
                        Unpublish from web
                    </a>

                    <a class="btn btn-small" confirmed-click="deleteOffice()"
                       ng-confirm-click="Delete Office? All its agents and properties will also be marked as deleted ! This action can NOT be reversed"
                       title="Delete current office and all its Agents and Properties">
                        <span class="icon-delete"></span>
                        Delete Office
                    </a>


                    <a class="btn btn-small" title="Save Office"
                       confirmed-click="saveOffice()"
                       ng-confirm-click="Save Office ? ">
                        <span class="icon-save"></span>
                        Save Office
                    </a>

                </div>
                <div class="control-group">
                    <label>{{processing_msg}}</label>
                </div>
            </div>

        </fieldset>

    </div>

</form>
