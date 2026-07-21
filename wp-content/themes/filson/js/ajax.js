(function($) {
    wp.customize('my_background_color', function(value) {
        value.bind(function(newval) {
            $('.hw-bar').addClass(newval);
        });
    });
})(jQuery);