/**
 * Created by Admin on 22/6/2559.
 */


jQuery("#province").children().each(function (i, e) {
    var id = jQuery(e).val();
    var name = jQuery(e).text();

    console.log(id + "->" + name)

    jQuery('#province').val(id);

    jQuery("#amphoe").children().each(function (i, e) {
        var id = jQuery(e).val();
        var name = jQuery(e).text();
        console.log('\t'+id + "->" + name)
    });


});