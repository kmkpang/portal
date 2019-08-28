<?php
/**
 * Created by PhpStorm.
 * User: khan
 * Date: 3/26/15
 * Time: 9:16 AM
 */

/** @var WebportalViewAddproperty $this */

JPluginHelper::importPlugin('captcha');

WFactory::getHelper()->insertReCaptcha('add-property-captcha');


$this->_setUpStep(1);
?>



<?php $this->_insertHead(); ?>

    <div class="add-property-step1">

        <div class="row">
            <div class="column small-6 text-center">
                <div class="circle-step active">1</div>
                <p><?php echo JText::_("STEP 1 PERSONAL AND CONTACT INFORMATION") ?></p>
            </div>

            <div class="column small-6 text-center">
                <div class="circle-step">2</div>
                <p><?php echo JText::_("STEP 2 PROPERTY INFORMATION") ?></p>
            </div>

            <div class="column small-6 text-center">
                 <div class="circle-step">3</div>
                <p><?php echo JText::_("STEP 3 DESCRIPTION AND PHOTOS") ?></p>
            </div>
            <div class="column small-6 text-center">
                <div class="circle-step">4</div>
                <p><?php echo JText::_("STEP 4 YOUR PROPERTY IS READY") ?></p>
            </div>
        </div>

        <hr />
        <div class="row">
            <div class="small-24 large-24 columns">
                <div class="input-textbox--wrapper">
                    <input type="text" name="name"
                           ng-model="currentProperty.name"
                           placeholder="<?php echo JText::_("NAME") ?>" required/>

                </div>
            </div>
        </div>
        <div class="row">
            <div class="small-24 large-12 columns ">
                <div class="input-textbox--wrapper">
                    <input type="text" name="phone"
                           ng-model="currentProperty.phone"
                           placeholder="<?php echo JText::_("PHONE NUMBER") ?>" required/>
                </div>
            </div>
            <div class="small-24 large-12 columns">
                <div class="">
                    <?php
                    $provinceModel = 'currentProperty.user_region_id';
                    require_once JPATH_BASE . DS . 'templates/webportal/ng_templates/search/addproperty/postal_code_select_province.php' ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="small-24 large-24 columns ">
                <div class="input-textbox--wrapper">
                    <input name="email" type="email"
                           ng-model="currentProperty.email"
                           placeholder="<?php echo JText::_("EMAIL ADDRESS") ?>" required/>
                </div>
            </div>
        </div>

        <!------------------------ NO USER ACCOUNT FOR NOW -------------------------->


        <!--<div class="row">-->
        <!--    <div class="small-24 large-12 columns ">-->
        <!--        <div class="input-textbox--wrapper">-->
        <!--            <input type="password" name="password1"-->
        <!--                   ng-model="currentProperty.password1"-->
        <!--                   placeholder="--><?php //echo JText::_("PASSWORD") ?><!--" required/>-->
        <!--        </div>-->
        <!--    </div>-->
        <!--    <div class="small-24 large-12 columns ">-->
        <!--        <div class="input-textbox--wrapper">-->
        <!--            <input type="password" name="password2"-->
        <!--                   ng-model="currentProperty.password2"-->
        <!--                   placeholder="--><?php //echo JText::_("RE-ENTER PASSWORD") ?><!--" required/>-->
        <!---->
        <!--            <div ng-if="add_property_form.password2.$invalid"-->
        <!--                 class="">--><?php //echo JText::_("PASSWORDS DONT MATCH") ?><!--</div>-->
        <!---->
        <!--        </div>-->
        <!--    </div>-->
        <!--</div>-->

        <!------------------------ NO USER ACCOUNT FOR NOW -------------------------->


        <div class="row">
            <div class="small-24 large-12 columns ">

                <div id="add-property-captcha"></div>

            </div>
            <div class="small-24 large-12 columns">
                <input type="submit" class="input-submit secondary-medium" value="<?php echo JText::_("SUBMIT")?>">
            </div>
        </div>


    </div>

<?php $this->_insertTail(); ?>