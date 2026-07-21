(function($) {
    $.fn.hexwp_slider = function() {
        $(this).find('.hw-slider-options').each(function(index, block) {

            var data_slider = jQuery.parseJSON($(this).html());

             data_slider['onSliderLoad'] = function($el, scene) {

                if (jQuery().hexwp_auto_width) {
                    $(this).hexwp_auto_width();
                }
            };
            data_slider['onBeforeStart'] = function($el) {

                if (jQuery().hexwp_auto_width) {
                    $(this).hexwp_auto_width();
                }
            }
            if ($(this).parents('.hw-slider').find('.hw-item-list').hasClass('lightSlider')) {} else {
                if (jQuery().hexwp_lightSlider) {

                    $(this).parents('.hw-slider').find('.hw-item-list').hexwp_lightSlider(data_slider);
                }
            }
            if (jQuery().hexwp_auto_width) {
                $(this).hexwp_auto_width();
            }

        });
    }
}(jQuery));