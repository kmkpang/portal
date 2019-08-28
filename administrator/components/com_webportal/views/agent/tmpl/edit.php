<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_webportal
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
//C:\xampp\htdocs\softverk-webportal-remaxth\administrator\components\com_webportal\views\agent\tmpl\edit.php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.formvalidation');


//$agentId=JFactory::getApplication()->input->getInt("agent_id");
//$agentService = WFactory::getServices()->getSqlService()->getSqlServiceClass(__PROPPERTY_PORTAL_AGENT);
//$agent = $agentService->getAgent($agentId,true);

$agentId = JFactory::getApplication()->input->getInt("agent_id");
$agentService = WFactory::getServices()->getSqlService()->getSqlServiceClass(__PROPPERTY_PORTAL_AGENT);
$agent = $agentService->getAgent($agentId, true);

?>
<script type="text/javascript">
    window.agent =<?php echo json_encode($agent)?>;
</script>
<form ng-controller="AgentAdminCtrl"
      ng-init='initAgent()'
      action="<?php echo JUri::root() . 'administrator/index.php?option=com_webportal&view=agent&layout=edit&task=edit&agent_id=' . $agent->id ?>"
      method="post" name="adminForm" id="adminForm" class="form-validate" ng-app="webportal">
    <div class="form-horizontal">

        <fieldset class="adminform">
            <legend>Edit Agent</legend>
            <div class="row-fluid">

                <div class="span6">

                    <legend>Images</legend>
                    <div class="control-group">
                        <div class="control-label">
                            Agent Image
                        </div>
                        <div class="controls">
                            <div class="editagentimage">
                                <img ng-src="{{agent.image_file_path}}">

                            </div>
                        </div>
                        <div class="control-label">
                            &nbsp;
                        </div>
                        <div class="controls">
                            <input type="file" name="file"
                                   onchange="angular.element(this).scope().processNewFiles(this.files,'agent_image')">
                        </div>

                    </div>

                </div>


                <div class="span6">
                    <legend>Details</legend>
                    <div class="control-group">
                        <div class="control-label">Unique Id</div>
                        <div class="controls">
                            <input type="text" name="unique_id" id="unique_id" size="32" maxlength="250"
                                   ng-model="agent.unique_id">
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Title</div>
                        <div class="controls">
                            <input type="text" name="title" id="title" size="32" maxlength="250"
                                   ng-model="agent.title">
                        </div>
                    </div>


                    <div class="control-group">
                        <div class="control-label">First Name</div>
                        <div class="controls">
                            <input type="text" name="first_name" id="first_name" size="32" maxlength="250"
                                   ng-model="agent.first_name">
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">MIddle Name</div>
                        <div class="controls">
                            <input type="text" name="middle_name" id="middle_name" size="32" maxlength="250"
                                   ng-model="agent.middle_name">
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Last Name</div>
                        <div class="controls">
                            <input type="text" name="last_name" id="last_name" size="32" maxlength="250"
                                   ng-model="agent.last_name">
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Email</div>
                        <div class="controls">

                            <input class="text_area" type="text" name="email" id="email" size="32" maxlength="250"
                                   ng-model="agent.email"/>

                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Fax</div>
                        <div class="controls">

                            <input type="text" name="fax" size="32" maxlength="250" ng-model="agent.fax">

                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Office Phone</div>
                        <div class="controls">

                            <input type="text" name="phone" size="32" maxlength="250"
                                   ng-model="agent.phone">

                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Mobile Phone</div>
                        <div class="controls">

                            <input type="text" name="mobile" size="32" maxlength="250"
                                   ng-model="agent.mobile">

                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">DOB</div>
                        <div class="controls">

                            <input type="text" name="dob" size="32" maxlength="250"
                                   ng-model="agent.DOB">

                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">SIN</div>
                        <div class="controls">

                            <input type="text" name="dob" size="32" maxlength="250"
                                   ng-model="agent.SIN">

                        </div>
                    </div>


                    <div class="control-group">
                        <div class="control-label">Gender</div>
                        <div class="controls">

                            <select ng-model="agent.gender">
                                <option value="" selected default>Select Gender</option>
                                <option value="1">Male</option>
                                <option value="2">Female</option>
                            </select>

                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Date Entered</div>
                        <div class="controls">
                            <label>{{agent.date_entered}}</label>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Date Modified</div>
                        <div class="controls">
                            <label>{{agent.date_modified}}</label>
                        </div>
                    </div>
                </div>

                <div class="span6">
                    <legend>Marketing Info</legend>
                    <div class="control-group">
                        <div class="control-label">Slogan</div>
                        <div class="controls">
                            <textarea ng-model="agent.marketing_info.slogan"></textarea>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Closer</div>
                        <div class="controls">
                            <textarea ng-model="agent.marketing_info.closer"></textarea>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Bullet Point 1</div>
                        <div class="controls">
                            <textarea
                                ng-model="agent.marketing_info.bullet_point1">{{agent.marketing_info.bullet_point1}}</textarea>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Bullet Point 2</div>
                        <div class="controls">
                            <textarea ng-model="agent.marketing_info.bullet_point2"></textarea>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Bullet Point 3</div>
                        <div class="controls">
                            <textarea ng-model="agent.marketing_info.bullet_point3"></textarea>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Description</div>
                        <div class="controls">
                            <textarea ng-model="agent.marketing_info.description"></textarea>
                        </div>
                    </div>

                </div>
                <div class="span6">
                    <legend>&nbsp;</legend>
                    <div class="control-group">
                        <div class="control-label">Language Spoken 1</div>
                        <div class="controls">
                            <textarea ng-model="agent.language_spoken1"></textarea>
                        </div>
                    </div>


                    <div class="control-group">
                        <div class="control-label">Language Spoken 2</div>
                        <div class="controls">
                            <textarea ng-model="agent.language_spoken2"></textarea>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Language Spoken 3</div>
                        <div class="controls">
                            <textarea ng-model="agent.language_spoken3"></textarea>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Language Spoken 4</div>
                        <div class="controls">
                            <textarea ng-model="agent.language_spoken4"></textarea>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Language Spoken 5</div>
                        <div class="controls">
                            <textarea ng-model="agent.language_spoken5"></textarea>
                        </div>
                    </div>


                </div>


            </div>


            <div class="span12">
                <legend>Actions</legend>
                <div class="control-group">

                    <a class="btn btn-small" title="Save Agent"
                       href="<?php echo JUri::root() . "administrator/index.php?option=com_webportal&view=agents" ?>">
                        <span class="icon-back"></span>
                        Back
                    </a>

                    <a class="btn btn-small" confirmed-click="togglePublish(true)"
                       ng-confirm-click="Publish Agent to Web ? "
                       title="Show or Hide from web ( agent list ) ,  Properties left untouched"
                       ng-show="agent.show_on_web === '0'">
                        <span class="icon-publish"></span>
                        Publish on web
                    </a>
                    <a class="btn btn-small" confirmed-click="togglePublish(false)"
                       ng-confirm-click="Unpublish Agent from Web ? "
                       title="Show or Hide from web  ( agent list ) ,  Properties left untouched"
                       ng-show="agent.show_on_web === '1'">
                        <span class="icon-unpublish"></span>
                        Unpublish from web
                    </a>

                    <a class="btn btn-small" confirmed-click="deleteAgent()"
                       ng-confirm-click="Delete Agent? All its properties will also be marked as deleted ! This action can NOT be reversed"
                       title="Delete current agent and all its Properties">
                        <span class="icon-delete"></span>
                        Delete Agent
                    </a>


                    <a class="btn btn-small" title="Save Agent"
                       confirmed-click="saveAgent()"
                       ng-confirm-click="Save Agent ? ">
                        <span class="icon-save"></span>
                        Save Agent
                    </a>



                </div>
                <div class="control-group">
                    <label>{{processing_msg}}</label>
                </div>
            </div>

        </fieldset>

    </div>

</form>
