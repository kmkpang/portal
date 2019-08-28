<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_webportal
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.formvalidation');

$propertyService = WFactory::getServices()->getSqlService()->getSqlServiceClass(__PROPPERTY_PORTAL_PROPERTY);
$propertyId = JFactory::getApplication()->input->getInt("property_id");
$property = WFactory::getServices()->getSqlService()->getSqlServiceClass(__PROPPERTY_PORTAL_PROPERTY)->getDetail($propertyId);
$property->office = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_OFFICE)->getOffice($officeId, true);

$categoryId = JFactory::getApplication()->input->getInt("category_id");
$categoryName = WFactory::getServices()->getSqlService()->getSqlServiceClass(__PROPPERTY_PORTAL_PROPERTY)->getCategoryName($categoryId);

$offices = WFactory::getServices()->getSqlService()->getSqlServiceClass(__PROPPERTY_PORTAL_OFFICE)->getOffices();

$officeId = $property->office_id;
$office = WFactory::getServices()->getSqlService()->getSqlServiceClass(__PROPPERTY_PORTAL_OFFICE)->getOffice($officeId, true);
$office->agents = WFactory::getServices()->getSqlService()->getSqlServiceClass(__PROPPERTY_PORTAL_AGENTS)->getAgents($office->id, true);

$postalCodeTree = WFactory::getServices()->getSqlService()->getSqlServiceClass(__PROPPERTY_PORTAL_ADDRESS)->postalCodeTree();

?>
<script type="text/javascript">
    window.property =<?php echo json_encode($property)?>;
    window.offices =<?php echo json_encode($offices)?>;
    window.office =<?php echo json_encode($office)?>;
    window.postal_code_tree =<?php echo json_encode($postalCodeTree)?>;
</script>
<?php //echo print_r($property->office); ?>
<form ng-controller="PropertyAdminCtrl"
      ng-init='initProperty()'
      action="<?php echo JUri::root() . 'administrator/index.php?option=com_webportal&view=property&layout=edit&task=edit&property_id=' . $property->id ?>"
      method="post" name="adminForm" id="adminForm" class="form-validate" ng-app="webportal">
    <div class="form-horizontal">
        <fieldset class="adminform">
            <legend>Edit Property</legend>
            <div class="row-fluid">

                <div class="span6">
                    <legend>Images</legend>
                    <div class="control-label">
                        Main Image
                    </div>
                    <div class="controls">
                        <div class="editpropertyimage">
                            <img ng-src="{{property.list_page_thumb_path}}"><br />
                            <input type="file" name="file"
                                   onchange="angular.element(this).scope().processNewFiles(this.files,'property_image')">
                        </div>
                    </div>
                    <div class="control-label" style="color:#f00;">
                        Image Uploader
                    </div>
                    <div class="controls">
                        <div class="editpropertyimage">
                            <input type="file" name="file"
                                   onchange="angular.element(this).scope().processNewFiles(this.files,'office_logo')">
                        </div>
                    </div>

                    <div class="control-group">
                        <legend>Images</legend>
                        <div class="span12" ng-repeat="image in property.images">
                            <div class="span3"><img ng-src="{{image}}"></div>
                            <div class="span9"><p>Image url : {{image}}</p></div>
                        </div>
                    </div>

                </div>

                <div class="span6">

                    <legend>Office and Agent</legend>
                    <div class="control-group">
                        <div class="control-label">Office</div>
                        <div class="controls">

                            <select ng-model="property.office_id">
                                <option value="" selected default>Select Office</option>
                                <option ng-selected="{{property.office_id == office.id}}"
                                        ng-repeat="office in offices"
                                        value="{{office.id}}">
                                    {{office.office_name}}
                                </option>
                            </select>

                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Agent</div>
                        <div class="controls">
                            <select ng-model="property.sale_id">
                                <option value="" selected default>Select Agent</option>
                                <option ng-selected="{{property.sale_id == agent.id}}"
                                        ng-repeat="agent in office.agents"
                                        value="{{agent.id}}">
                                    {{agent.full_name}}
                                </option>
                            </select>
                        </div>
                    </div>

                </div>

                <div class="span6">
                    <legend>Address</legend>
                    <div class="control-group">
                        <div class="control-label" style="color:#f00;">Region</div>
                        <div class="controls">
                            <select name="province" ng-model="property.address.region_id" id="province"
                                    ng-change="updateGoogleMapCenter('regions');"
                                    ng-options="region.id as region.name for region in postal_code_tree.regions">
                                <option value="" selected default>Province</option>
                            </select>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label" style="color:#f00;">City/Town</div>
                        <div class="controls">
                            <select name="city_town" ng-model="property.address.town_id" id="city_town"
                                    ng-change="updateGoogleMapCenter('towns');"
                                    ng-options="town.id as town.name for town in postal_code_tree.towns | filter:filterTown">
                                <option value="" selected default>City/Town</option>
                            </select>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label" style="color:#f00;">Postal Code</div>
                        <div class="controls">
                            <select name="postal" id="postal" ng-model="property.address.postal_code_id"
                                    ng-change="updateGoogleMapCenter('postal_codes');"
                                    ng-options="postals.id as postals.name for postals in postal_code_tree.postals | filter:filterPostal">
                                <option value="" selected default>Postal Code</option>
                            </select>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Address</div>
                        <div class="controls">
                            <input type="text" name="" size="32" maxlength="250" ng-model="property.address">
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label" style="color:#f00;">Latitude</div>
                        <div class="controls">
                            <input type="text" size="32" maxlength="250"
                                   ng-model="property.latitude">
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label" style="color:#f00;">Longitude</div>
                        <div class="controls">
                            <input type="text" size="32" maxlength="250"
                                   ng-model="property.longitude">
                        </div>
                    </div>


                    <div class="control-group">
                        <div class="control-label" style="color:#f00;">Map Location</div>
                        <div class="controls">
                            <pindrop-map title="<?php echo $property->address ?>"
                                         lat="<?php echo $property->latitude ?>"
                                         lng="<?php echo $property->longitude ?>"></pindrop-map>
                        </div>
                    </div>
                </div>

                <div class="span6">
                    <legend>Property Details</legend>
                    <div class="control-group">
                        <div class="control-label" style="color:#f00;">Categories</div>
                        <div class="controls">

                            <select ng-model="property.category_id">
                                <option value="" selected default>Select Category</option>
                                <option ng-selected="{{property.category_id == category_id}}"
                                        ng-repeat="category_id in category_id"
                                        value="{{category_id}}">
                                    {{category_id}}
                                </option>
                            </select>

                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label" style="color:#f00;">Property type</div>
                        <div class="controls">

                            <select ng-model="property.manager_id">
                                <option value="" selected default>Select Property Type</option>
                                <option ng-selected="{{property.manager_id == agent.id}}"
                                        ng-repeat="agent in property.agents"
                                        value="{{agent.id}}">
                                    {{agent.full_name}}
                                </option>
                            </select>

                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label">Current listing price</div>
                        <div class="controls">
                            <input type="text" name="current_listing_price" id="current_listing_price" size="32" maxlength="250"
                                   ng-model="property.current_listing_price">
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label" style="color:#f00;">Current_listing currency</div>
                        <div class="controls">
                            <input type="text" name="office_name" id="office_name" size="32" maxlength="250"
                                   ng-model="property.office_name">
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label" style="color:#f00;">Language code</div>
                        <div class="controls">

                            <input class="text_area" type="text" name="email" id="email" size="32" maxlength="250"
                                   ng-model="property.email"/>

                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label" style="color:#f00;">Rental price granularity</div>
                        <div class="controls">

                            <input type="text" name="fax" size="32" maxlength="250" ng-model="property.fax">

                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Property status</div>
                        <div class="controls">

                            <input type="text" name="property_status" size="32" maxlength="250"
                                   ng-model="property.property_status">

                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Floor level</div>
                        <div class="controls">
                            <input type="text" name="floor_level" size="32" maxlength="250"
                                   ng-model="property.floor_level">
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Total area</div>
                        <div class="controls">
                            <input type="text" name="url_to_private_page" size="32" maxlength="250"
                                   ng-model="property.url_to_private_page">
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Living area</div>
                        <div class="controls">
                            <input type="text" name="total_area" size="32" maxlength="250"
                                   ng-model="property.total_area">
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Cubic volume</div>
                        <div class="controls">
                            <input type="text" name="cubic_volume" size="32" maxlength="250"
                                   ng-model="property.cubic_volume">
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Total no. of rooms</div>
                        <div class="controls">
                            <input type="text" name="total_number_of_rooms" size="32" maxlength="250"
                                   ng-model="property.total_number_of_rooms">
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">No. of bathrooms</div>
                        <div class="controls">
                            <input type="text" name="number_of_bathrooms" size="32" maxlength="250"
                                   ng-model="property.number_of_bathrooms">
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">No. of bedrooms</div>
                        <div class="controls">
                            <input type="text" name="number_of_bedrooms" size="32" maxlength="250"
                                   ng-model="property.number_of_bedrooms">
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">No. toilet rooms</div>
                        <div class="controls">
                            <input type="text" name="number_of_toilet_rooms" size="32" maxlength="250"
                                   ng-model="property.number_of_toilet_rooms">
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">No. of floors</div>
                        <div class="controls">
                            <input type="text" name="number_of_floors" size="32" maxlength="250"
                                   ng-model="property.number_of_floors">
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Year build</div>
                        <div class="controls">
                            <input type="text" name="year_build" size="32" maxlength="250"
                                   ng-model="property.year_build">
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label" style="color:#f00;">Unit of measure</div>
                        <div class="controls">
                            <input type="text" name="url_to_private_page" size="32" maxlength="250"
                                   ng-model="property.url_to_private_page">
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Possession date</div>
                        <div class="controls">
                            <input type="text" name="possession_date" size="32" maxlength="250"
                                   ng-model="property.possession_date">
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Availability date</div>
                        <div class="controls">
                            <input type="text" name="availability_date" size="32" maxlength="250"
                                   ng-model="property.availability_date">
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Original listing date</div>
                        <div class="controls">
                            <input type="text" name="original_listing_date" size="32" maxlength="250"
                                   ng-model="property.original_listing_date">
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Expiry date</div>
                        <div class="controls">
                            <input type="text" name="expiry_date" size="32" maxlength="250"
                                   ng-model="property.expiry_date">
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label" style="color:#f00;">Alternate url</div>
                        <div class="controls">
                            <input type="text" name="url_to_private_page" size="32" maxlength="250"
                                   ng-model="property.url_to_private_page">
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Virtual tour</div>
                        <div class="controls">
                            <input type="text" name="virtual_tour" size="32" maxlength="250"
                                   ng-model="property.virtual_tour">
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Open house start</div>
                        <div class="controls">
                            <input type="text" name="open_house_start" size="32" maxlength="250"
                                   ng-model="property.open_house_start">
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Open house end</div>
                        <div class="controls">
                            <input type="text" name="open_house_end" size="32" maxlength="250"
                                   ng-model="property.open_house_end">
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label" style="color:#f00;">Register date </div>
                        <div class="controls">
                            <input type="text" name="url_to_private_page" size="32" maxlength="250"
                                   ng-model="property.url_to_private_page">
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label" style="color:#f00;">requested </div>
                        <div class="controls">
                            <input type="text" name="url_to_private_page" size="32" maxlength="250"
                                   ng-model="property.url_to_private_page">
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Description</div>
                        <div class="controls">
                            <textarea default="default" ng-model="property.description_text"></textarea>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Date Entered</div>
                        <div class="controls">
                            <label>{{property.created_date}}</label>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Date Modified</div>
                        <div class="controls">
                            <label>{{property.last_update}}</label>
                        </div>
                    </div>
                </div>
            </div>


            <div class="span12">
                <legend>Actions</legend>
                <div class="control-group">

                    <a class="btn btn-small" title="Back"
                       href="<?php echo JUri::root() . "administrator/index.php?option=com_webportal&view=properties" ?>">
                        <span class="icon-back"></span>
                        Back
                    </a>

                    <a class="btn btn-small" confirmed-click="togglePublish(true)"
                       ng-confirm-click="Publish Property to Web ? "
                       title="Show or Hide from web ( property list ) , Offices and Agents left untouched"
                       ng-show="property.sent_to_web === '0'">
                        <span class="icon-publish"></span>
                        Publish on web
                    </a>
                    <a class="btn btn-small" confirmed-click="togglePublish(false)"
                       ng-confirm-click="Unpublish Property from Web ? "
                       title="Show or Hide from web ( property list ) , Offices and Agents left untouched"
                       ng-show="property.sent_to_web === '1'">
                        <span class="icon-unpublish"></span>
                        Unpublish from web
                    </a>

                    <a class="btn btn-small" confirmed-click="deleteProperty()"
                       ng-confirm-click="Delete Property? All its offices and agents will also be marked as deleted ! This action can NOT be reversed"
                       title="Delete current property and all its Offices and Agents">
                        <span class="icon-delete"></span>
                        Delete Property
                    </a>


                    <a class="btn btn-small" title="Save Property"
                       confirmed-click="saveProperty()"
                       ng-confirm-click="Save Property ? ">
                        <span class="icon-save"></span>
                        Save Property
                    </a>

                </div>
                <div class="control-group">
                    <label>{{processing_msg}}</label>
                </div>
            </div>

        </fieldset>

    </div>

</form>