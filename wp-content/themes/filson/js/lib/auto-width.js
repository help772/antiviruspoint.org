(function($) {


    function first_auto_width(id) {
        var flex = id;
        if (window.matchMedia('(max-width: 1024px) and (min-width: 359px)').matches) {
            if (flex.hasClass('hw_first_full')) {
                var first = id.find('.hw-item:first-child');
                if (first.hasClass('hw-aw')) {} else {
                    first.addClass('hw-aw');
                    first.hexwp_auto_width_warp();
                }
            }
        }
    }



    function auto_width_flex(width, id, res) {
        var flex = id;

        var width_return = width;

        if (flex.hasClass('hw-flex') || flex.hasClass('hw-group') || flex.hasClass('dragscroll')|| id.hasClass('hw-item-carousel')  ||  flex.hasClass('hw-masonry')) {
            if (flex.hasClass('hw_' + res + '_1_2')) {
                width_return = width * 0.5;

            } else if (flex.hasClass('hw_' + res + '_1_3')) {
                width_return = width * 0.33;

            } else if (flex.hasClass('hw_' + res + '_1_4')) {
                width_return = width * 0.25;

            } else if (flex.hasClass('hw_' + res + '_1_5')) {
                width_return = width * 0.2;

            } else if (flex.hasClass('hw_' + res + '_2_5')) {
                width_return = width * 0.4;

            } else if (flex.hasClass('hw_' + res + '_3_5')) {
                width_return = width * 0.6;

            } else if (flex.hasClass('hw_' + res + '_4_5')) {
                width_return = width * 0.8;

            } else if (flex.hasClass('hw_' + res + '_1_6')) {
                width_return = width * 0.16;

            } else if (flex.hasClass('hw_' + res + '_5_6')) {
                width_return = width * 0.83;
            } else if (flex.hasClass('hw_' + res + '_1_7')) {
                width_return = width / 7;

            } else if (flex.hasClass('hw_' + res + '_1_8')) {
                width_return = width / 8;
            }
        }
        return width_return;
    }





    function auto_width_post(width, id) {
        var width_return = width;

        if (id.hasClass('hw-flex') || id.hasClass('hw-group') || id.hasClass('hw-item') || id.hasClass('dragscroll') || id.hasClass('hw-item-carousel') || id.hasClass('hw-single-summary') ) {

            var width_return = width;
            if (id.hasClass('hw-module-1')) {
                var module_1 = id;
            } else {
                var module_1 = id.find('.hw-item.hw-module-1:not(.hw-aw)');
            }
			 
			
            var module_1_details = module_1.find('.hw-details');
            var module_1_width = module_1_details.width();
			
            if (!isNaN(module_1_width)) {
                width_return = module_1_width;
            }

            if (id.hasClass('hw-module-2')) {
                var module_2 = id;
            } else {
                var module_2 = id.find('.hw-item.hw-module-2:not(.hw-aw)');
            }

            var module_2_details = module_2.find('.hw-details');
            var module_2_width = module_2_details.width();

            if (!isNaN(module_2_width)) {
                width_return = module_2_width;
            }




            if (id.hasClass('hw-glider')) {
                var glider = id;
            } else {
                var glider = id.find('.hw-item.hw-glider:not(.hw-aw)');
            }

				var glider_thumb = glider.find('.hw-thumb');
				var glider_thumb_ratio = (glider_thumb.height() / glider_thumb.width()) + 0.25;
 				if (!isNaN(glider_thumb_ratio)) {
					if(glider_thumb_ratio > 1){
	
					width_return = (glider_thumb.width() * glider_thumb_ratio) - 75;
 					
					}else{
			
					width_return = (glider_thumb.width() * glider_thumb_ratio) - 40;
		
					}
				 } 
				
 
        }
		if(id.hasClass('hw-single-summary')){
			var summary_height = id.prev().find('.woocommerce-product-gallery__image').height();
  				width_return = (width_return + summary_height) / 2;
  		}
		
        return width_return;

    }
	
	
	
	
	
	
    $.fn.hexwp_auto_width_warp = function() {

        first_auto_width($(this));

        var widths = $(this).width();
        var body_width = $('body').width();

        if (window.matchMedia('(max-width: 1024px) and (min-width: 768px)').matches) {
            widths = auto_width_flex(widths, $(this), 'tab') - 40;
        } else if (window.matchMedia('(max-width: 767px) and (min-width: 360px)').matches) {
            widths = auto_width_flex(widths, $(this), 'mob') - 40;

        } else if (window.matchMedia('(max-width: 359px) and (min-width: 1px)').matches) {
            widths = widths - 40;
        } else {
            widths = auto_width_flex(widths, $(this), 'col') - 40;
        }

        var new_widths = auto_width_post(widths, $(this));


        widths = new_widths;


        if (900 < widths) {
            $(this).addClass('hw-1200');


        } else if (700 < widths && widths <= 900) {
            $(this).addClass('hw-900');

        } else if (500 < widths && widths <= 700) {
            $(this).addClass('hw-700');

        } else if (400 < widths && widths <= 500) {
            $(this).addClass('hw-500');

        } else if (300 < widths && widths <= 400) {
            $(this).addClass('hw-400');

        } else if (250 < widths && widths <= 300) {
            $(this).addClass('hw-300');

        } else if (190 < widths && widths <= 250) {
            $(this).addClass('hw-250');

		} else if (150 < widths && widths <= 190) {
            $(this).addClass('hw-200');
		
        } else if (100 < widths && widths <= 150) {
            $(this).addClass('hw-150');

        } else if (0 < widths && widths <= 100) {
            $(this).addClass('hw-100');

        }
    }











    $.fn.hexwp_remove_auto_width_warp = function() {
        var widths = $(this).width();
        $(this).removeClass('hw-1200').removeClass('hw-900').removeClass('hw-700').removeClass('hw-500').removeClass('hw-400').removeClass('hw-300').removeClass('hw-200').removeClass('hw-250').removeClass('hw-150').removeClass('hw-100').removeClass('hw-000');
    }








    $.fn.hexwp_auto_width = function() {
        $(this).find('.hw-aw').each(function() {
            $(this).hexwp_remove_auto_width_warp();
            $(this).hexwp_auto_width_warp();
        });
        $(this).find('.hw-icon-video').each(function() {
            var size = ($(this).parent().width() / 10) + 10;
            $(this).attr('style', '--hw-icn-sz:' + size + 'px;');
        });
		
		

		
  $(this).find('.hw-module-1 .hw-product-tags').each(function() {
          		  var thumb_size = $(this).parent().find('.hw-thumb').width();
				var size=60;  
 				if (size > thumb_size) {
					$(this).find('.onsale').addClass('hw-product-tag-hide');
 				 }else{
					$(this).find('.onsale').removeClass('hw-product-tag-hide');
 				 }
				 if($(this).children('.onsale').hasClass('onsale')){
				  size=120;  
 				 }
				 
				if (size > thumb_size) {
					$(this).find('.hw-product-featured').addClass('hw-product-tag-hide');
				 }else{
					$(this).find('.hw-product-featured').removeClass('hw-product-tag-hide');
				 }
			 
			 
 
			 
         });

    };

})(jQuery);