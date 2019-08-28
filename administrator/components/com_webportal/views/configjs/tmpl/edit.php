<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_webportal
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
//C:\xampp\htdocs\softverk-webportal-remaxth\administrator\components\com_webportal\views\Configjs\tmpl\edit.php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.formvalidation');


$filePath = JPATH_ROOT . "/webportal.configuration.js";
$content = file_get_contents($filePath);

$rows = count(explode("\n", $content));

$config = base64_encode($content);


?>
<script type="text/javascript">
    window.js = '<?php echo $config?>';
</script>

<div id="j-sidebar-container" class="span2">
    <?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">

    <form ng-controller="ConfigAdminCtrl"
          ng-init='initJs()'
          action="<?php echo JUri::root() . 'administrator/index.php?option=com_webportal&view=Configjs&layout=edit&task=edit' ?>"
          method="post" name="adminForm" id="adminForm" class="form-validate" ng-app="webportal">
        <div class="form-horizontal">

            <fieldset class="adminform">
                <legend>Edit Javascript Configuration <br/>
                    1. DO NOT USE IF YOU ARE NOT FAMILIAR WITH PROGRAMMING<br/>
                    2. VALIDATE CODE IN AN IDE BEFORE SAVING</legend>
                <div class="row-fluid">


                    <div class="span12">
                        <legend><?php echo $filePath ?></legend>
                        <div class="control-group">
                            <div class="control-label">webportal.configuration.js</div>
                            <div class="controls">
                                <textarea rows="<?php echo $rows ?>"
                                          style="width: 1000px;font-size: 14px;" ng-model="js.config"></textarea>
                            </div>
                        </div>


                    </div>

                    <div class="span12">
                        <legend>Actions</legend>
                        <div class="control-group">

                            <a class="btn btn-small" title="Save Configjs"
                               ng-click="saveJs()"
                                >
                                <span class="icon-save"></span>
                                Save Js Config
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
