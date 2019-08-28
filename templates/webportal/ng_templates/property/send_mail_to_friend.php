<?php
/**
 * Created by PhpStorm.
 * User: khan
 * Date: 9/27/15
 * Time: 10:47 PM
 */

?>

<div class="property-details__friend__emailform">

    <form method="post" ng-submit="submitSend2Friend()" class="ng-cloak">
        <!--  -->
        <div ng-show="!sent">

            <div class="property-details__agentpanel__emailform--label">
                <span><?php echo JText::_("SHARE THIS PROPERTY WITH FRIEND") ?></span>
            </div>

            <div class="form-field--row">

                <div class="input-textbox--wrapper">
                    <input type="text" ng-model="from_name"
                           placeholder="<?php echo JText::_('YOUR NAME') ?>"/>
                </div>
            </div>

            <div class="form-field--row">

                <div class="input-textbox--wrapper">
                    <input type="email" ng-model="from_email"
                           placeholder="<?php echo JText::_('YOUR EMAIL') ?>"/>
                </div>
            </div>

            <div class="form-field--row">

                <div class="input-textbox--wrapper">
                    <input type="text" ng-model="to_email" required
                           placeholder="<?php echo JText::_('FRIENDS EMAIL COMMA SEPARATED') ?>"/>
                </div>
            </div>

            <div class="form-field--row">

                <div class="input-textbox--wrapper">
                                <textarea ng-model="message"
                                          placeholder="<?php echo JText::_('MESSAGE') ?>"></textarea>
                </div>
            </div>

            <div ng-show="error.length > 0" class="form-field--row">
                <span class="error">{{error}}</span>
            </div>

            <div class="large-18 large-centered large-offset-3">
                <input class="input-submit primary-medium" ng-show="!sending" type="submit"
                       value="<?php echo JText::_('SEND EMAIL') ?>"/>
                <input ng-show="sending"
                       class="input-submit primary-medium"
                       type="submit" disabled="disabled"
                       value="<?php echo JText::_('SENDING') ?>..."/>
            </div>
        </div>

        <div ng-show="sent">
            <div class="large-offset-3 large-18 large-centered">
                <span><?php echo JText::_('EMAIL SENT') ?></span><br/>
            </div>
        </div>

    </form>


</div>
