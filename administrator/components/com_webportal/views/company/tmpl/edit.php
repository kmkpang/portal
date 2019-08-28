<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_webportal
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
//C:\xampp\htdocs\softverk-webportal-remaxth\administrator\components\com_webportal\views\company\tmpl\edit.php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.formvalidation');

$companyId = JFactory::getApplication()->input->getInt("company_id");

/**
 * @var $company CompanyModel
 */
$company = WFactory::getServices()->getSqlService()->getSqlServiceClass(__PROPPERTY_PORTAL_COMPANY)->getCompany($companyId, true);


//test

//$company->address->region_id = "5";
//$company->address->town_id = "35";
//$company->address->postal_code_id = "63";
//$company->show_on_web = "0";


?>
<script type="text/javascript">
    window.company =<?php echo json_encode($company)?>;
    window.postal_code_tree =<?php echo json_encode($postalCodeTree)?>;
</script>

<div id="j-sidebar-container" class="span2">
    <?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">

    <form ng-controller="CompanyAdminCtrl"
          ng-init='initCompany()'
          action="<?php echo JUri::root() . 'administrator/index.php?option=com_webportal&view=company&layout=edit&task=edit&company_id=' . $company->id ?>"
          method="post" name="adminForm" id="adminForm" class="form-validate" ng-app="webportal">
        <div class="form-horizontal">

            <fieldset class="adminform">
                <legend>Edit Company</legend>
                <div class="row-fluid">


                    <div class="span12">
                        <legend>Details</legend>
                        <div class="control-group">
                            <div class="control-label">Company Id</div>
                            <div class="controls">
                                <label id="id" name="id">{{company.id}}</label>
                            </div>
                        </div>

                        <div class="control-group">
                            <div class="control-label">Company Name</div>
                            <div class="controls">
                                <input type="text" name="company_name" id="company_name" size="32" maxlength="250"
                                       ng-model="company.company_name">
                            </div>
                        </div>

                        <div class="control-group">
                            <div class="control-label">Company Address</div>
                            <div class="controls">
                                <input type="text" name="company_address" id="company_address" size="32" maxlength="250"
                                       ng-model="company.company_address">
                            </div>
                        </div>

                        <div class="control-group">
                            <div class="control-label">Postal Address</div>
                            <div class="controls">
                                <input type="text" name="postal_address" id="postal_address" size="32" maxlength="250"
                                       ng-model="company.postal_address">
                            </div>
                        </div>

                        <div class="control-group">
                            <div class="control-label" style="color: red;font-weight: bolder">Company Email *
                            </div>
                            <div class="controls">

                                <input class="text_area" type="text" name="email" id="email" size="32" maxlength="250"
                                       ng-model="company.email"/>

                            </div>
                        </div>


                        <div class="control-group">
                            <div class="control-label">Fax</div>
                            <div class="controls">

                                <input type="text" name="fax" size="32" maxlength="250" ng-model="company.fax">

                            </div>
                        </div>

                        <div class="control-group">
                            <div class="control-label">Phone</div>
                            <div class="controls">

                                <input type="text" name="telephone" size="32" maxlength="250"
                                       ng-model="company.telephone">

                            </div>
                        </div>

                        <div class="control-group">
                            <div class="control-label">SSN</div>
                            <div class="controls">

                                <input type="text" name="ssn" size="32" maxlength="250"
                                       ng-model="company.ssn">

                            </div>
                        </div>

                        <div class="control-group">
                            <div class="control-label">Legal Info</div>
                            <div class="controls">
                                <textarea ng-model="company.legal"></textarea>
                            </div>
                        </div>

                        <div class="control-group">
                            <div class="control-label">Description</div>
                            <div class="controls">
                                <textarea ng-model="company.description"></textarea>
                            </div>
                        </div>


                        <div class="control-group">
                            <div class="control-label">Date Entered</div>
                            <div class="controls">
                                <label>{{company.date_entered}}</label>
                            </div>
                        </div>

                        <div class="control-group">
                            <div class="control-label">Date Modified</div>
                            <div class="controls">
                                <label>{{company.date_modified}}</label>
                            </div>
                        </div>
                    </div>

                    <div class="span12">
                        <legend>Actions</legend>
                        <div class="control-group">

                            <a class="btn btn-small" title="Save Company"
                               ng-click="saveCompany()"
                                >
                                <span class="icon-save"></span>
                                Save Company
                            </a>

                        </div>
                        <div class="control-group">
                            <label>{{processing_msg}}</label>
                        </div>
                    </div>


            </fieldset>

        </div>

    </form>

</div>
