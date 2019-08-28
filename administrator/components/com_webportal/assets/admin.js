/**
 * Created by khan on 9/6/15.
 */

//make sure the menus are always enabled!

jQuery(document).ready(function () {

    jQuery('.dropdown-toggle').dropdown();
    jQuery('.collapse').collapse('show');
    jQuery('#myModal').modal('hide');
    jQuery('.tabs').button();
    jQuery('.tip').tooltip();
    jQuery(".alert-message").alert();
});

