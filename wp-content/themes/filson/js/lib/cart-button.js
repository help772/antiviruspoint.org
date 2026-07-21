 
(function($) {
    $.fn.hexwp_cart_button = function() {
 
        $(".hw-product-button .add_to_cart_button,.hw-product-button .add_to_wishlist,.hw-product-button .yith-wcwl-wishlistexistsbrowse a,.hw-product-button .compare-button a").on({
            mouseenter: function() {
                var self = this;
                // تنظیم یک تایم‌اوت برای اجرای دستورات پس از 350 میلی‌ثانیه
                timeout = setTimeout(function() {
                    var text = $(self).text();
                    $('body').append('<div class="hw-btn-text-hover"><span>' + text + '</span></div>');
                    var wpadminbar = $('#wpadminbar').height();
                    if ($.isNumeric(wpadminbar)) {
                        var header_sticky_top = wpadminbar;
                    } else {
                        var header_sticky_top = 0;
                    }
                    var btn_item = $(self).parents('.hw-product-button > div');
                    var btn_height = btn_item.height();
                    var btn_width = btn_item.width() / 2;
                    var width = $('.hw-btn-text-hover').width() / 2;
                    var left = btn_item.offset().left - width + btn_width;
                    var top = btn_item.offset().top - 35;
                    $('.hw-btn-text-hover').css({
                        'top': top.toFixed(0) + 'px',
                        'left': left.toFixed(0) + 'px'
                    });
                }, 350);
            },
            mouseleave: function() {
                 clearTimeout(timeout);
                $('.hw-btn-text-hover').remove();
            },
        });
    };
}(jQuery));