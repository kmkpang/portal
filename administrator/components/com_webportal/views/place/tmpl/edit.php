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

$config = WFactory::getConfig()->getWebportalConfigurationArray();

$placeTypes = $config['externalApis']['pointsOfInterests'];

?>
<form
    action="<?php echo JRoute::_('index.php?option=com_webportal&view=place&layout=edit&id=' . (int)$this->item->id); ?>"
    method="post" name="adminForm" id="adminForm" class="form-validate" ng-app="webportal">
    <div class="form-horizontal">

        <fieldset class="adminform">
            <legend><?php echo ((int)$this->item->id > 0) ? "Edit Place " : "Add new Place" ?></legend>
            <div class="row-fluid">
                <div class="span6">

                    <div class="control-group">
                        <div class="control-label">Name</div>
                        <div class="controls">

                            <input type="text" name="name">

                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Category</div>
                        <div class="controls">

                            <select name="category">

                                <?php foreach ($placeTypes as $p) { ?>

                                    <option name="<?php echo $p ?>" value="<?php echo $p ?>"><?php echo $p ?></option>

                                <?php } ?>
                            </select>

                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">Link</div>
                        <div class="controls">

                            <input type="text" name="link">

                        </div>
                    </div>

                    <!--                    <div class="control-group">-->
                    <!--                        <div class="control-label">Zipcode</div>-->
                    <!--                        <div class="controls">-->
                    <!---->
                    <!--                            <input type="text" name="zip">-->
                    <!---->
                    <!--                        </div>-->
                    <!--                    </div>-->

                    <div class="control-group">
                        <div class="control-label">Location</div>
                        <div class="controls">

                            <div>
                                <input type="hidden" name="pindrop_latitude" value="0">
                                <input type="hidden" name="pindrop_longitude" value="0">
                                <pindrop-map></pindrop-map>
                            </div>

                        </div>
                    </div>


                </div>
            </div>
        </fieldset>

    </div>
    <input type="hidden" name="task" value="webportal.edit"/>
    <?php echo JHtml::_('form.token'); ?>
</form>
