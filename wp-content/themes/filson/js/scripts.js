 (function($) {
     'use strict';
     jQuery(document).ready(function() {


          if (jQuery().hexwp_mobbar) {
             $('body').hexwp_mobbar();
         }


         if (jQuery().hexwp_auto_width) {
             $('body').hexwp_auto_width();
 
 				var prevWidth = window.width;
				var observer = new ResizeObserver(function(entries) {
				const width = entries[0].borderBoxSize?.[0].inlineSize;
				if (typeof width === 'number' && width !== prevWidth ) {
					prevWidth = width;
					$('body').hexwp_auto_width(); 
			
				}
			});
			observer.observe(window.document.body);
         }
         if (jQuery().hexwp_countdown) {
             $('body').hexwp_countdown();
         }
         if (jQuery().hexwp_cart_button) {
             $('body').hexwp_cart_button();
         }

         if (jQuery().hexwp_slider) {
             $('body').hexwp_slider();
         }
         if (jQuery().hexwp_masonry) {
             $('body').hexwp_masonry();
             $(window).on('resize', function() {
                 $('body').hexwp_masonry();
             });
         }
 
         if (jQuery().hexwp_tabs_transition) {
             $('body').hexwp_tabs_transition();
         }
         if (jQuery().hexwp_tabs_ajax) {
             $('body').hexwp_tabs_ajax();
         }

         if (jQuery().hexwp_load_more) {
             $('body').hexwp_load_more();
         }

         if (jQuery().hexwp_page_number) {
             $('body').hexwp_page_number();
         }

         if (jQuery().hexwp_counter) {

             $('body').hexwp_counter();
         }

         if (jQuery().hexwp_lightbox) {
             $('body').hexwp_lightbox();
         }
         if (jQuery().hexwp_product_gallery) {
             $('body').hexwp_product_gallery();
         }

         if (jQuery().hexwp_zoomove) {
             $('body').hexwp_zoomove();
         }
         if (jQuery().hexwp_widget) {
             $('body').hexwp_widget();
         }
         if (jQuery().hexwp_sidebar_sticky) {

             $('body').hexwp_sidebar_sticky();
         }
         if (jQuery().hexwp_woocommerce) {

             $('body').hexwp_woocommerce();
         }
         if (jQuery().hexwp_counter) {
             $('body').hexwp_counter();
         }

         if (jQuery().hexwp_header) {
             $('body').hexwp_header();
         }
         if (jQuery().hexwp_figcaption_hover) {
             $('body').hexwp_figcaption_hover();
         }
		 
		 	 if (jQuery().slick_responsive) {
             $('body').slick_responsive();
              $(window).on('resize', function() {
             $('body').slick_responsive();
             });
         }
		 
         if (jQuery().hexwp_title_box) {
             $('body').hexwp_title_box();
             $(window).on('resize', function() {
                 $('body').hexwp_title_box();
                 $('body').hexwp_auto_width();
             });
         }

     });
 })(jQuery);