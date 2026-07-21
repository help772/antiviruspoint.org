(function($) {
    $.fn.hexwp_load_more = function() {

        $(".hw-load-more a").on("click", function() {
            var gets = $(this).attr('hw-id');
            var element = $(this).parents('[class*="hw-el"]');
            var masonry = $(this).parents('.macy-masonry');
            $(this).parent().addClass('hw-loading');
            var hw_this = $(this);
            var active_filter = element.find('.hw-tab-active').attr('data-filter');
            var hw_list = $(this).parents('[class*="hw-el"]').find('.hw-item-list');
            var data_option = $(this).next().html();
            var dataajax = jQuery.parseJSON(data_option);
            dataajax['count'] = true;
            dataajax['ajaxcount'] = '10';

if($(this).attr('data-cats') !== undefined) {
            dataajax['cats'] = $(this).attr('data-cats');
			}
			if($(this).attr('data-orderby') !== undefined) {
				dataajax['orderby'] = $(this).attr('data-orderby');
			}
			if($(this).attr('data-platform') !== undefined) {
            	dataajax['platform'] = $(this).attr('data-platform');
			}
            dataajax['max_page'] = $(this).attr('data-max_page');
            var max_pages = $(this).attr('data-max_page');
            var pageNumber = rd_this.attr('data-page');
            dataajax['pageNumber'] = pageNumber;
 


            $.ajax({
                type: "POST",
                dataType: "html",
                url: hexwp_js.ajaxurl,
                data: dataajax,
                success: function(data) {
                    var datanew = data.replaceAll("hw-post-", "hw-new-post hw-post-");
                    var $data = $(datanew);


                    if ($data.length) {
                        hw_list.append($data);
                        hw_this.parent().addClass('hw-loading');
                        hw_this.parent().removeClass('hw-loading');

                    }
                    pageNumber++;
                    hw_this.attr('data-page', pageNumber);
                    if (pageNumber > dataajax['max_page']) {
                        rd_this.parent().addClass('hw-load-more-hide');
                    }
                    if (jQuery().hexwp_masonry) {
                        setTimeout(function() {
                            element.hexwp_masonry();
                        }, 1);
                    }
                    setTimeout(function() {
                        element.find('.hw-new-post').each(function(index, element) {
                            $(this).removeClass('hw-new-post');
                        });

                    }, 1);



                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $loader.html(jqXHR + " :: " + textStatus + " :: " + errorThrown);
                }
            });

        });

 $('.hw-load-more a').each(function(index, element) {
            var max_page = $(this).data('max_page');
            if (max_page > 1) {
                $(this).parent().removeClass('hw-load-more-hide');
            }

        });

    };

})(jQuery);