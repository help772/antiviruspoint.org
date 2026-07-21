(function($) {
    $.fn.hexwp_widget = function() {

        function social_font_size() {
            $('.hw-el-widget_social').each(function(index, element) {
                var width = $(this).find('a').width().toFixed(0);
                if ($(this).find('.hw-item-list').hasClass('hw-social-icon-style-1')) {
                    var width_size = width * 0.7;
                } else {
                    var width_size = width;
                }
                $(this).attr('style', '--hw-scl-sz:' + width_size + 'px;');

            });

        }
        social_font_size();
 
        $(window).on('resize', function() {
            social_font_size();
        });
    };

})(jQuery);