(function($) {
    $.fn.hexwp_tabs_ajax = function() {

        $(this).find(".hw-ajax-tab [class*='hw-tbox'] .hw-tabs .hw-tab-item:not(.hw-view-all)").on("click", function() {

            var hw_this = $(this);
            var tab_jax = $(this).parents('.hw-ajax-tabs');

            hw_this.parent().find('.hw-tab-active').removeClass('hw-tab-active');

            hw_this.addClass('hw-tab-active');
            var element = $(this).parents('[class*="hw-el"]');
            element.find('.hw-load-more').removeClass('hw-loading');

            var data_option = $(this).parents('[class*="hw-tbox"]').find('.hw-data-json').html();

            var dataajax = jQuery.parseJSON(data_option);


			if($(this).data('cats') !== undefined) {
				dataajax['cats'] = $(this).data('cats');
				element.find('.hw-load-more span,.hw-load-more a').attr('data-cats', dataajax['cats']);
  			}
			if($(this).data('orderby') !== undefined) {
            	 dataajax['orderby'] = $(this).data('orderby');
				element.find('.hw-load-more span,.hw-load-more a').attr('data-orderby', dataajax['orderby']);
 			}
			if($(this).data('max_page') !== undefined) {
            	 dataajax['max_page'] = $(this).data('max_page');
				element.find('.hw-load-more span,.hw-load-more a').attr('data-max_page', dataajax['max_page']);
 			}
	 
 
            element.addClass('hw-ajax-loading');
            element.find('.hw-load-more span,.hw-load-more a').attr('data-page', '2');
			
            element.find('.hw-load-more').removeClass('hw-load-more-hide');
		 
		 	 
			if(dataajax['max_page'] <= 1 ){
            element.find('.hw-load-more').addClass('hw-load-more-hide');
			} 
		 
            if (element.hasClass('hw-slider')) {
                var slider = true;

            } else {
                var slider = false;
            }

            $.ajax({
                type: "POST",
                dataType: "html",
                url: hexwp_js.ajaxurl,
                data: dataajax,
                success: function(data) {
                    var datanew = data.replaceAll("hw-post-", "hw-new-post hw-post-");
                    var $data = $(datanew);
                    if ($data.length) {
                        if (slider === true) {
                            element.find('.lSSlideOuter').remove();
							if(element.find('.hw-slider-list-warp .hw-gap-container').hasClass('hw-image-featured')){
                            element.find('.hw-slider-list-warp .hw-gap-container').append('<div class="hw-item-list hw-aw hw-item-carousel"></div>');
							}else{
                            element.find('.hw-slider-list-warp').append('<div class="hw-item-list hw-aw hw-item-carousel"></div>');
							}
                        }

                        element.find('.hw-item-list').children().remove();
                        element.find('.hw-item-list').append($data);
                        if (jQuery().hexwp_auto_width) {
                            element.hexwp_auto_width();
                        }
                        element.removeClass('hw-ajax-loading');
                    }

                    setTimeout(function() {
                        if (slider === true && jQuery().hexwp_slider) {
                            element.hexwp_slider();
                        }
                        if (jQuery().hexwp_masonry) {
                            element.hexwp_masonry();
                        }

                    }, 1);

                    setTimeout(function() {
                        element.find('.hw-new-post').each(function(index, element) {
                            $(this).removeClass('hw-new-post');
                        });
                    }, 2);


                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $loader.html(jqXHR + " :: " + textStatus + " :: " + errorThrown);
                }
            });

        });

    };

})(jQuery);