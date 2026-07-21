(function($) {
    $.fn.hexwp_page_number = function() {
 
    
        jQuery(document).on('click', ".hw-page-ajax a", function(e) {
            var element = $(this).parents('[class*="hw-el"]').addClass('ygygy');;
            var key = element.attr('data-key');
            var x = element.offset().top;
            window.scrollTo(0, x - 60);
            e.preventDefault();
            var link = jQuery(this).attr('href');
            element.addClass('hw-ajax-loading');

            jQuery('.hw-pagenavi').load(link + ' .hw-wrapper .hw-el-' + key + '  .hw-pagenavi', function() {
                element.find('.hw-pagenavi').fadeIn(0);
                element.find(".hw-pagenavi").children('.hw-pagenavi').unwrap();
            });

            element.find('.hw-item-list').load(link + ' .hw-wrapper  .hw-el-' + key + '  .hw-item-list', function() {

                element.find('[class*="hw-post-"]').each(function(index, element) {
                    $(this).addClass('hw-new-post');
                });
                element.removeClass('hw-ajax-loading');

                window.history.pushState("object or string", "Title", link);

                element.find(".hw-item-list").children('.hw-item-list').unwrap();

                if (jQuery().hexwp_auto_width) {
                    element.hexwp_auto_width();
                }

                setTimeout(function() {
                    element.find('.hw-new-post').each(function(index, element) {
                        $(this).removeClass('hw-new-post');
                    });
                    hexwp_page_translate();
                }, 1);
                setTimeout(function() {
                    hexwp_page_translate();
                }, 5);


            });
        });



    };
}(jQuery));