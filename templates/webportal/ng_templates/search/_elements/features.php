<?php /** @var WebportalViewAddproperty $this */ ?>
<?php
$mergedFeatures = array();

if ($this !== null && $this->addPropertyModel !== null) {
    $selectedFeatures = $this->addPropertyModel->features;
} else
    $selectedFeatures = array();
$features = WFactory::getServices()->getServiceClass(__PROPPERTY_PORTAL_PROPERTIES)->getAllFeatures($selectedFeatures, $mergedFeatures, true);


?>




<div class="features-container">

    <?php foreach ($features as $groupName => $groups) { ?>

        <h2><?php echo $groupName ?></h2>

        <?php foreach ($groups as $feature) {

            ?>



            <lable class="features-container__checkbox-wrapper">
                <input
                    type="checkbox"
                    name="features[]"
                    value="<?php echo $feature['id'] ?>"
                    ng-model="currentProperty.features[<?php echo $feature['id'] ?>]"
                    > <?php echo $feature['name'] ?>
            </lable>

        <?php } ?>
        <br/>
    <?php } ?>

</div>


