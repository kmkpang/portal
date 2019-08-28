<?php
/**
 * @var $office OfficeModel
 * */

?>

<div class="large-24 office-details-frontpage--wrapper">
        <div class="row small-24 large-24 office-details-frontpage">
            <h1><a href="<?php echo JRoute::_('index.php') . $office->office_name ?>"><?php echo $office->office_name ?></a></h1>

            <p><?php echo $office->marketingInfo->slogan ?></p>
            <p><?php echo $office->marketingInfo->bullet_point1 ?>
               <?php echo $office->marketingInfo->bullet_point2 ?>
               <?php echo $office->marketingInfo->bullet_point3 ?>
            </p>
        </div>
</div>

