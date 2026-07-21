(function($) {
    $.fn.hexwp_woocommerce = function() {
		
		function qty_max_hide($class){
            var val =  parseInt($class.find('.qty').val());
            var max_qty =  parseFloat($class.find('.qty').attr('max'));
            var min_qty =  parseFloat($class.find('.qty').attr('min'));
			 if (isNaN(min_qty)) {
				min_qty =0;
			  }
			   if (isNaN(max_qty)) {
					max_qty =99999999;
			  }
				if (isNaN(val)) {
					val =0;
			  }
			if(val === max_qty){
 				$class.find('.hw-qty-plus').addClass('hw-qty-hide');
			}else{
 				$class.find('.hw-qty-plus').removeClass('hw-qty-hide');
			}
 			if(val === min_qty){
 				$class.find('.hw-qty-minus').addClass('hw-qty-hide');
 			}else{
 				$class.find('.hw-qty-minus').removeClass('hw-qty-hide');
			}
		}
		$('.quantity').each(function(index, element) {
            qty_max_hide($(this));
        });
        $(document).on('click', '.hw-qty-plus,.hw-qty-minus', function() {
            var qty = $(this).closest('.quantity').find('.qty');
            if (qty.val() == '') {
                var val = 0;
            } else {
                var val = parseInt(qty.val());
            }
            var max = parseFloat(qty.attr('max'));
            var min = parseFloat(qty.attr('min'));
            var step = parseFloat(qty.attr('step'));


              
            if ($(this).is('.hw-qty-plus')) {
                if (max && (max <= val)) {
                    qty.val(max);
                  } else {
                      qty.val(val + step);
                }
				
			 
            } else {

                if (min && (min >= val)) {
                    qty.val(min);

                } else if (val >= 1) {
                    qty.val(val - step);
                }
            }
			
			 qty_max_hide($(this).parent('.quantity'));
			
			
			$('button[name="update_cart"]').attr('aria-disabled','false').removeAttr('disabled');
			
        });

  
			
		$.fn.wc_variations_image_update = null;
		$.fn.wc_variations_image_reset = null;

		$.fn.wc_variations_image_update = function( variation ) {
  		 	var $form             = this,
			$product          = $form.closest( '.product' ),
 			$single_image      = $product.find( '.hw-single-product-image' ),
 			$gallery_image      = $product.find( '.hw-product-thumbnails-list .slick-slide:eq(0)' );
  			
			 
		if ( variation && variation.image && variation.image.src && variation.image.src.length > 1 ) {
	  
	  
			var $has_gallery_image = $product.find( '.hw-product-thumbnails-list a[href="'+variation.image.full_src+'"]' ).parents(".slick-slide");
 			$single_image.find('.woocommerce-product-gallery__image > a').wc_set_variation_attr( 'href', variation.image.full_src );
			$single_image.find('.woocommerce-product-gallery__image > a > img').wc_set_variation_attr( 'src', variation.image.full_src );
			$single_image.find('.hw-product-thumb-resize > a').wc_set_variation_attr( 'href', variation.image.full_src );
			
			if($has_gallery_image.length > 0 ){
 				$has_gallery_image.trigger( 'click' );
 			}else{
				$gallery_image.find('a').wc_set_variation_attr( 'href', variation.image.full_src );
				$gallery_image.find('a > img').wc_set_variation_attr( 'src',variation.image.gallery_thumbnail_src );
				$gallery_image.find('figure').wc_set_variation_attr( 'style','--hw-bg:url("'+ variation.image.gallery_thumbnail_src+'");');
				$gallery_image.trigger( 'click' );
 			}
 
 
  
		} else {
			$form.wc_variations_image_reset();
 		} 
		
    };
	
	$.fn.wc_variations_image_reset = function() {
	 		var $form             = this,
			$product          = $form.closest( '.product' ),
 			$single_image      = $product.find( '.hw-single-product-image' ),
 			
 			$gallery_image      = $product.find( '.hw-product-thumbnails-list .slick-slide:eq(0)' );
  			$single_image.find('.woocommerce-product-gallery__image > a').wc_reset_variation_attr( 'href'  );
			$single_image.find('.woocommerce-product-gallery__image > a > img').wc_reset_variation_attr( 'src');
			$single_image.find('.hw-product-thumb-resize > a').wc_reset_variation_attr( 'href' );
			
 				$gallery_image.find('a').wc_reset_variation_attr( 'href');
				$gallery_image.find('a > img').wc_reset_variation_attr( 'src' );
				$gallery_image.find('figure').wc_reset_variation_attr( 'style');
				$gallery_image.trigger( 'click' );
		};
 

     };
 
}(jQuery));