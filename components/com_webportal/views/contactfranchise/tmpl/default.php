<?php
/**
 * Created by PhpStorm.
 * User: Lian
 * Date: 7/2/14
 * Time: 4:40 AM
 */

$doc = JFactory::getDocument();
$app = JFactory::getApplication();
require_once(JPATH_BASE . DS . 'templates' . DS . 'generic' . DS . 'controllers' . DS . 'helpers.php');

$office_id = getParam('office_id');
$offices = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_OFFICE)->getOffice($office_id);
$officeAddress = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_ADDRESS)->getAddress($offices['address']['id']);
if (intval($office_id) == 0) {
    //get default office..
    $office_id = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_OFFICE)->getDefaultOfficeId();
    
}
?>
<div class="franchise-bg">
    <div class="row franchise-contact">
        <div class="columns small-24 medium-8 franchise-contact-form">
    
            <div id="contact-form" ng-controller="ContactForm">
    
                <form name="form" novalidate class="contact-form" method="post" ng-submit="submitContactFranchise()" ng-cloak>
    
                    <div ng-show="sent" class="alert-success columns small-24 large-24 text-center">
                        <h5><?php echo JText::_('COM_WEBPORTAL_CONTACT_FORM_THANKYOU') ?></h5>
                    </div>

                        <div class="contact__form--row">
                            <label for="name" class="contact__textbox-label"><?php echo JText::_("FIRSTNAME") ?></label>
    
                            <div class="input-textbox--wrapper">
                                <input id="firstname" name="contact_firstname" type="text" ng-model="firstname" required/>
    
                                <div ng-show="form.$submitted">
                                <span class="error" ng-show="form.contact_firstname.$error.required">
                                    <?php echo JText::_("PLEASE_ENTER") . ' ' . JText::_("YOUR_FIRST_NAME"); ?>
                                </span>
                                </div>
                            </div>
                        </div>

                    <div class="contact__form--row">
                        <label for="name" class="contact__textbox-label"><?php echo JText::_("LASTNAME") ?></label>

                        <div class="input-textbox--wrapper">
                            <input id="lastname" name="contact_lastname" type="text" ng-model="lastname" required/>

                            <div ng-show="form.$submitted">
                                <span class="error" ng-show="form.contact_lastname.$error.required">
                                    <?php echo JText::_("PLEASE_ENTER") . ' ' . JText::_("YOUR_LAST_NAME"); ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="contact__form--row">
                        <label for="email" class="contact__textbox-label"><?php echo JText::_("EMAIL") ?></label>

                        <div class="input-textbox--wrapper">
                            <input id="email" type="text" name="contact_email" ng-model="email" required/>

                            <div ng-show="form.$submitted">
                            <span class="error" ng-show="form.contact_email.$error.required">
                                <?php echo JText::_("PLEASE_ENTER") . ' ' . JText::_("YOUR_EMAIL"); ?>
                            </span>
                            </div>
                        </div>
                    </div>

                    <div class="contact__form--row">
                        <label for="phone" class="contact__textbox-label"><?php echo JText::_("PHONE") ?></label>

                        <div class="input-textbox--wrapper">
                            <input id="phone" type="text" name="contact_phone" ng-model="phone" required/>

                            <div ng-show="form.$submitted">
                            <span class="error" ng-show="form.contact_phone.$error.required">
                                <?php echo JText::_("PLEASE_ENTER") . ' ' . JText::_("YOUR_PHONE"); ?>
                            </span>
                            </div>
                        </div>
                    </div>

                    <div class="contact__form--row">
                        <label for="phone" class="contact__textbox-label"><?php echo JText::_("ADDRESS") ?></label>

                        <div class="input-textbox--wrapper">
                            <input id="address" type="text" name="contact_address" ng-model="address" required/>

                            <div ng-show="form.$submitted">
                                <span class="error" ng-show="form.contact_address.$error.required">
                                    <?php echo JText::_("PLEASE_ENTER") . ' ' . JText::_("YOUR_ADDRESS"); ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="contact__form--row">
                        <label for="phone" class="contact__textbox-label"><?php echo JText::_("PROVINCE") ?></label>

                        <div class="input-textbox--wrapper">
                            <input id="province" type="text" name="contact_province" ng-model="province" required/>

                            <div ng-show="form.$submitted">
                            <span class="error" ng-show="form.contact_province.$error.required">
                                <?php echo JText::_("PLEASE_ENTER") . ' ' . JText::_("PROVINCE"); ?>
                            </span>
                            </div>
                        </div>
                    </div>

                    <div class="contact__form--row">
                        <label for="phone" class="contact__textbox-label"><?php echo JText::_("DISTRICT") ?></label>

                        <div class="input-textbox--wrapper">
                            <input id="district" type="text" name="contact_district" ng-model="district" required/>

                            <div ng-show="form.$submitted">
                            <span class="error" ng-show="form.contact_district.$error.required">
                                <?php echo JText::_("PLEASE_ENTER") . ' ' . JText::_("DISTRICT"); ?>
                            </span>
                            </div>
                        </div>
                    </div>

                    <div class="contact__form--row">
                        <label for="phone" class="contact__textbox-label"><?php echo JText::_("PROVINCE_AREA") ?></label>

                        <div class="input-textbox--wrapper">
                            <input id="province_area" type="text" name="contact_province_area" ng-model="province_area" />
                        </div>
                    </div>

                    <div class="contact__form--row">
                        <label for="message" class="contact__textbox-label"><?php echo JText::_("SUBJECT") ?></label>

                        <div class="input-textbox--wrapper">
                        <textarea class="contact__textbox-textarea" id="message" name="contact_message"
                                  ng-model="message" required
                                  placeholder="<?php echo JText::_("YOUR_QUESTIONS_AND_COMMENTS") ?>"
                                  rows="4"></textarea>

                            <div ng-show="form.$submitted">
                            <span class="error" ng-show="form.contact_message.$error.required">
                                <?php echo JText::_("PLEASE_ENTER") . ' ' . JText::_("YOUR_QUESTIONS_AND_COMMENTS"); ?>
                            </span>
                            </div>
                        </div>

                    </div>
    
                        <div class="contact__form--row small-24 large-24">
                            <input id="submit" type="submit" value="<?php echo JText::_("SUBMIT FRANCHISE INQUIRY") ?>"
                                   class="input-submit primary-medium"/>
                        </div>

                </form>
            </div>
        </div>
        <div class="columns small-24 large-16">
            <div class="office-details__office-name"><?php echo JText::_("FRANCHISE OPPORTUNITIES"); ?></div>
            <div class="office-details__office-description">
                <p><?php echo JText::_("FRANCHISE_OPPORTUNITIES_TEXT"); ?></p>
            </div>
        </div>
    </div>
</div>