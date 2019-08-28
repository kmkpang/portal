<?php
/**
 * Created by PhpStorm.
 * User: khan
 * Date: 4/20/15
 * Time: 1:40 PM
 */

?>

<input type="text" name="movein"
       ng-model="currentProperty.movein"
       ng-init="createDatePicker()"
       placeholder="<?php echo JText::_("AVAILABLE FROM") ?>">