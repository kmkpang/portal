<?php
/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

?>

<div class="row small-24 register">


    <div class="row">
        <a name="contact" class="column small-24 heading">
            <?php echo JText::_("REGISTER_FOR_NEWS") ?>
        </a>
    </div>

    <div class="row">
        <div class="column small-24 subheading">
            <?php echo JText::_("PROFILE") ?>
        </div>
    </div>

    <div class="row">

        <div class="column small-24 large-12 input-textbox--wrapper">
            <input type="text" placeholder="<?php echo JText::_("FIRST_NAME") ?> *">
        </div>
        <div class="column small-24 large-12 input-textbox--wrapper">
            <input type="text" placeholder="<?php echo JText::_("LAST_NAME") ?> *">
        </div>

    </div>

    <div class="row">

        <div class="column small-24 large-12 input-textbox--wrapper">
            <input type="text" placeholder="<?php echo JText::_("MOBILE") ?> *">
        </div>
        <div class="column small-24 large-12 input-textbox--wrapper">
            <input type="text" placeholder="<?php echo JText::_("EMAIL") ?> *">
        </div>
    </div>
    <div class="row">
        <div class="column small-24 subheading">
            <?php echo JText::_("ADDRESS") ?>
        </div>
    </div>
    <div class="row">
        <div class="column small-24 large-12 input-textbox--wrapper">
            <?php
            $provinceModel = 'currentProject.register.address.region_id';
            require JPATH_BASE . DS . 'templates/webportal/ng_templates/project/postal_code_select_province.php' ?>
        </div>
        <div class="column small-24 large-12 input-textbox--wrapper">
            <?php
            $districtModel = 'currentProject.register.address.city_town_id';
            $filter = "filterAddressTown";
            ///Applications/MAMP/htdocs/softverk-webportal/templates/webportal/ng_templates/project/postal_code_select_district.php
            require JPATH_BASE . DS . 'templates/webportal/ng_templates/project/postal_code_select_district.php' ?>
        </div>
    </div>
    <div class="row">
        <div class="column small-24 subheading">
            <?php echo JText::_("WORK_ADDRESS") ?>
        </div>
    </div>
    <div class="row">
        <div class="column small-24 large-12 input-textbox--wrapper">
            <?php
            $provinceModel = 'currentProject.register.workAddress.region_id';
            require JPATH_BASE . DS . 'templates/webportal/ng_templates/project/postal_code_select_province.php' ?>
        </div>
        <div class="column small-24 large-12 input-textbox--wrapper">
            <?php
            $districtModel = 'currentProject.register.workAddress.city_town_id';
            $filter = "filterWorkAddressTown";
            require JPATH_BASE . DS . 'templates/webportal/ng_templates/project/postal_code_select_district.php' ?>
        </div>
    </div>
    <div class="row">
        <div class="column small-24 subheading">
            <?php echo JText::_('UNIT_TYPE') ?>

        </div>
    </div>

    <div class="row">

        <div class="column small-24 input-textbox--wrapper">
            <select>

                <option>
                    <?php echo $rs->units_type1_name ?>
                </option>
                <option>
                    <?php echo $rs->units_type2_name ?>
                </option>
                <option>
                    <?php echo $rs->units_type3_name ?>
                </option>
                <option>
                    <?php echo $rs->units_type4_name ?>
                </option>
                <option>
                    <?php echo $rs->units_type5_name ?>
                </option>
                <option>
                    <?php echo $rs->units_type6_name ?>
                </option>
                <option>
                    <?php echo $rs->units_type7_name ?>
                </option>
                <option>
                    <?php echo $rs->units_type8_name ?>
                </option>
                <option>
                    <?php echo $rs->units_type9_name ?>
                </option>
                <option>
                    <?php echo $rs->units_type10_name ?>
                </option>
            </select>
        </div>

    </div>

    <div class="row">
        <div class="column small-24 subheading">
            <?php echo JText::_("BUDGET") ?>
            <select>

                <option>1,000,000 - 2,000,000</option>
                <option>2,000,000 - 2,500,000</option>
                <option>2,500,000 - 3,000,000</option>
                <option>3,000,000 - 3,500,000</option>
                <option>3,500,000 - 4,000,000</option>
                <option>4,000,000 - 4,500,000</option>
                <option>4,500,000 - 5,000,000</option>
                <option>5,000,000 - 5,500,000</option>
                <option>5,500,000 - 6,000,000</option>
                <option>6,000,000 - 6,500,000</option>
                <option>6,500,000 - 7,000,000</option>
                <option>7,000,000 - 7,500,000</option>
                <option>7,500,000 - 8,000,000</option>
                <option>8,000,000 - 8,500,000</option>
                <option>8,500,000 - 9,000,000</option>
                <option>9,000,000 - 9,500,000</option>
                <option>9,500,000 - 10,000,000</option>
            </select>
        </div>
    </div>
    </br>

    <div class="row">
        <div class="column small-24 large-12 input-textbox--wrapper">
        </div>
        <div class="column small-24 large-12 input-textbox--wrapper">
            <a class="input-submit secondary-dark"> <?php echo JText::_("SUBMIT") ?></a>
        </div>
    </div>
</div>
