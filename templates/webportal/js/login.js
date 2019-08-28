jQuery(document).ready(function() {
// login popup
    if (jQuery('a.login-popup').length && jQuery('#login-popup').length) {
        var popup = jQuery('#login-popup');
        var overlay = jQuery('#login-popup-overlay');
        var close = jQuery('#login-popup-close');

        jQuery('a.login-popup').click(function (e) {
            e.preventDefault();

            overlay.css('display', 'block');
            popup.css('display', 'block');

            setTimeout(function () {
                overlay.addClass('active');
                popup.addClass('active');
            }, 50);
        });

        jQuery(overlay).add(close).click(function () {
            overlay.removeClass('active');
            popup.removeClass('active');

            setTimeout(function () {
                overlay.css('display', 'none');
                popup.css('display', 'none');
            }, 650);
        });
    }
});
