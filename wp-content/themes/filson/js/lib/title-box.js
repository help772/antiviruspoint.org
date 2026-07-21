(function($) {
    $.fn.hexwp_title_box = function() {
        $(this).find('.hw-boxed-all [class*="hw-tbox-"]').each(function(index, block) {
            $(this).parents('.hw-boxed-all').addClass('hw-has-tbox');

        });
        $(this).find('[class*="hw-tbox-"]').each(function(index, block) {
            $(this).removeClass('hw-tbox-full-width');
            $(this).find('[class*="hw-tborder"]').remove();
            var width = $(this).outerWidth(true);
            var main_width = $(this).find('.hw-tab-main').outerWidth(true);
            var tab_width = $(this).find('.hw-tabs').outerWidth(true);

            var rs_tab = width - main_width;

            if (rs_tab < tab_width) {
                $(this).addClass('hw-tbox-full-width');
            } else {
                $(this).removeClass('hw-tbox-full-width');
            }

            if ($(this).hasClass('hw-tbox-style-5')) {

                var children_width = 0;
                $(this).find('.hw-title-box').children().each(function(index, block) {
                    children_width += parseInt($(this).outerWidth(true), 10);
                });
                if ($(this).hasClass('hw-main-center') || $(this).hasClass('hw-tabs-center')) {
                    var bagho = width - children_width;
                    var half = bagho / 2;
                    $(this).find('.hw-title-box').append('<i class="hw-tborder-before" style="width:' + half + 'px;"></i><i class="hw-tborder-after" style="width:' + half + 'px;"></i>');
                } else {
                    $(this).find('.hw-title-box').append('<i class="hw-tborder" style="width:' + (width - children_width) + 'px; left:' + main_width + 'px;"></i>');
                }

            }

        });
    };
})(jQuery);