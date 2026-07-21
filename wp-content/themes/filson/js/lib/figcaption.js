(function($) {
    $.fn.hexwp_figcaption_hover = function() {
        $(this).find('.hw-module-1,.hw-module-2,.hw-cap-hover-middle .hw-glider').on({
            mouseenter: function() {
                var element = $(this);
                element.find('figcaption').addClass('hw-aw');
                element.hexwp_auto_width();

            },
            mouseleave: function() {
                var element = $(this);
                setTimeout(function() {
                    element.find('figcaption').hexwp_remove_auto_width_warp();
                    element.find('figcaption').removeClass('hw-aw');
                }, 350);
            }
        });
    };

})(jQuery);