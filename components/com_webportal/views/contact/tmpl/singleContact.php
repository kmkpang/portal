<?php
/**
 * Created by PhpStorm.
 * User: paisit
 * Date: 3/3/2559
 * Time: 16:05
 */
?>
<div class="columns small-24 large-16 large-centered">

    <div id="contact-form" ng-controller="ContactForm">

        <form name="form" novalidate class="contact-form" method="post" ng-submit="submitContact()" ng-cloak>

            <div ng-show="sent" class="alert-success columns small-24 large-24 text-center">
                <h5><?php echo JText::_('COM_WEBPORTAL_CONTACT_FORM_THANKYOU') ?></h5>
            </div>

            <div class="columns small-24 large-12">

                <div class="contact__form--row">
                    <label for="name" class="contact__textbox-label"><?php echo JText::_("NAME") ?></label>

                    <div class="input-textbox--wrapper">
                        <input id="name" name="contact_name" type="text" ng-model="name" required
                               placeholder="<?php echo JText::_("YOUR_NAME") ?>"/>
                        <div ng-show="form.$submitted">
                            <span class="error" ng-show="form.contact_name.$error.required">
                                <?php echo JText::_("PLEASE_ENTER") . ' ' . JText::_("YOUR_NAME"); ?>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="contact__form--row">
                    <label for="email" class="contact__textbox-label"><?php echo JText::_("EMAIL") ?></label>

                    <div class="input-textbox--wrapper">
                        <input id="email" type="text" name="contact_email" ng-model="email" required
                               placeholder="<?php echo JText::_("YOUR_EMAIL") ?>"/>
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
                        <input id="phone" type="text" name="contact_phone" ng-model="phone" required
                               placeholder="<?php echo JText::_("YOUR_PHONE") ?>"/>
                        <div ng-show="form.$submitted">
                            <span class="error" ng-show="form.contact_phone.$error.required">
                                <?php echo JText::_("PLEASE_ENTER") . ' ' . JText::_("YOUR_PHONE"); ?>
                            </span>
                        </div>
                    </div>
                </div>

            </div>
            <div class="columns small-24 large-12">

                <div class="contact__form--row">
                    <label for="message" class="contact__textbox-label"><?php echo JText::_("SUBJECT") ?></label>

                    <div class="input-textbox--wrapper">
                        <textarea class="contact__textbox-textarea" id="message" name="contact_message" ng-model="message" required
                                  placeholder="<?php echo JText::_("YOUR_QUESTIONS_AND_COMMENTS") ?>"
                                  rows="4"></textarea>
                        <div ng-show="form.$submitted">
                            <span class="error" ng-show="form.contact_message.$error.required">
                                <?php echo JText::_("PLEASE_ENTER") . ' ' . JText::_("YOUR_QUESTIONS_AND_COMMENTS"); ?>
                            </span>
                        </div>
                    </div>

                </div>
            </div>

            <div class="columns small-24 large-12">

                <div class="contact__form--row small-24 large-24">
                    <input id="submit" type="submit"  value="<?php echo JText::_("SEND") ?>"
                           class="input-submit primary-medium"/>
                </div>

            </div>
        </form>
    </div>
</div>
