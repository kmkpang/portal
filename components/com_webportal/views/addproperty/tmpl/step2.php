<?php
/**
 * Created by PhpStorm.
 * User: khan
 * Date: 3/26/15
 * Time: 9:16 AM
 */

/** @var WebportalViewAddproperty $this */
if ($this->addPropertyModel !== null && intval($this->addPropertyModel->property_id) > 0)
    $this->_setUpStep(2, "property-id={$this->addPropertyModel->property_id}");
else
    $this->_setUpStep(2);

?>

<?php $this->_insertHead(); ?>

<div class="add-property-step2">
    <div class="row">
        <div class="column small-6 text-center">
            <div class="circle-step active">1</div>
            <p><?php echo JText::_("STEP 1 PERSONAL AND CONTACT INFORMATION") ?></p>
        </div>

        <div class="column small-6 text-center">
            <div class="circle-step active">2</div>
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
        <div class="small-24 large-10 columns ">

            <?php
            $provinceModel = 'currentProperty.region_id';
            require_once JPATH_BASE . DS . 'templates/webportal/ng_templates/search/addproperty/postal_code_select_province.php' ?>


            <?php require_once JPATH_BASE . DS . 'templates/webportal/ng_templates/search/addproperty/postal_code_select_district.php' ?>

            <div class="input-textbox--wrapper">
                <input type="text" name="name"
                       ng-model="currentProperty.address"
                       placeholder="<?php echo JText::_("ADDRESS") ?>">
            </div>


            <?php require_once JPATH_BASE . DS . 'templates/webportal/ng_templates/search/addproperty/office_select_frontpage.php' ?>

            <div ng-show="noofficefound">
                <label><?php echo JText::_("NO OFFICE FOUND AT THAT LOCALITY SELECT ANY FROM LIST") ?></label>
            </div>
        </div>
        <div class="small-24 large-12 columns">
            <div class="pindrop-map--wrapper">
                <pindrop-map></pindrop-map>
            </div>
        </div>
    </div>


    <div class="row">

        <div class="small-24 large-8 columns ">
            <div class="input-textbox--wrapper">
                <input type="text" name="price" ng-model="currentProperty.price_formatted" ng-blur="reformatPrice()"
                       placeholder="<?php echo JText::_("PRICE") ?>">
            </div>
        </div>
        <div class="small-24 large-8 columns">
            <div class="category-select--wrapper">
                <?php require_once JPATH_BASE . DS . 'templates/webportal/ng_templates/search/addproperty/property_category_select_frontpage.html' ?>
            </div>
        </div>

        <div class="small-24 large-8 columns">

            <div class="sale-rent-select--wrapper">
                <a class="sale-rent-select"
                   ng-class="currentProperty.type_id == 2 ? 'active' : ''"
                   ng-click="currentProperty.type_id = 2"
                   ng-init="currentProperty.type_id = (currentProperty.type_id == 0 ? 2 : currentProperty.type_id)"><?php echo JText::_('SALE') ?></a>
            </div>

            <div class="sale-rent-select--wrapper">
                <a class="sale-rent-select "
                   ng-class="currentProperty.type_id == 3 ? 'active' : ''"
                   ng-click="currentProperty.search_bts = false; currentProperty.type_id = 3"><?php echo JText::_('RENT') ?></a>
            </div>

        </div>

    </div>

    <div class="row">

        <div class="small-24 large-8 columns ">
            <div class="input-textbox--wrapper">
                <input type="text" name="size"
                       ng-model="currentProperty.size"
                       placeholder="<?php echo JText::_("SIZE IN SQ METER") ?>">
            </div>
        </div>
        <div class="small-24 large-8 columns input-textbox--floor">
            <div class="input-textbox--wrapper">
                <input type="text" name="floor"
                       ng-model="currentProperty.floor_level"
                       placeholder="<?php echo JText::_("FLOOR LEVEL") ?>">
            </div>
        </div>

        <!--    <div class="small-24 large-6 columns">-->
        <!--        <div class="input-textbox--wrapper">-->
        <!--            <input type="text" name="unit"-->
        <!--                   ng-model="currentProperty.unit"-->
        <!--                   placeholder="--><?php //echo JText::_("UNIT") ?><!--">-->
        <!--        </div>-->
        <!--    </div>-->

        <!--    <div class="small-24 large-5 columns">-->
        <!--        <div class="input-textbox--wrapper">-->
        <!--            <input type="text" name="noof"-->
        <!--                   ng-model="currentProperty.noof"-->
        <!--                   ng-init="currentProperty.noof = '-->
        <?php //echo $this->addPropertyModel->noof ?><!--'"-->
        <!--                   placeholder="--><?php //echo JText::_("NO OF") ?><!--">-->
        <!--        </div>-->
        <!--    </div>-->

        <div class="small-24 large-8 columns">
            <div class="input-textbox--wrapper">
                <filter-date-picker></filter-date-picker>
            </div>
        </div>

    </div>

    <div class="row">

        <?php require_once JPATH_BASE . "/templates/webportal/ng_templates/search/_elements/features.php" ?>

    </div>


    <br/>

    <div class="row">
        <div class="small-24 large-offset-14 large-5 columns" style="padding-right: 0">
            <a type="submit" class="input-submit secondary-medium"
               href="<?php echo $this->previous_step_link ?>" value="<?php echo JText::_("PREVIOUS STEP")?>"><?php echo JText::_("PREVIOUS STEP")?></a>
        </div>
        <div class="small-24 large-5 columns" style="padding-right: 0">
            <input type="submit" class="input-submit secondary-medium" value="<?php echo JText::_("SUBMIT")?>">
        </div>
    </div>


</div>

<?php $this->_insertTail(); ?>
