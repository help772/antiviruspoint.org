(function($) {
    $.fn.hexwp_mobbar = function() {

		 jQuery(document).on("click", ".hw-mobbar-tabs a", function() {
			var data_id = $(this).attr('data-id');
			$(this).parents('.hw-mobbar-warp').find('.hw-mobbar-tab-active').removeClass('hw-mobbar-tab-active');
			 $(this).addClass('hw-mobbar-tab-active');
			
			$(this).parents('.hw-mobbar-warp').find('.hw-mobbar-content-active').removeClass('hw-mobbar-content-active');
		  $(this).parents('.hw-mobbar-warp').find('.hw-mobbar-content[data-id='+data_id+']').addClass('hw-mobbar-content-active');
			
			
		
		});
	/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																Loading Mobilebar
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        function mobbar_loading(main) {
            if (main !== undefined) {
                $('.hw-mobbar').addClass('hw-mobbar-active');
		 
                var out = '';
                out += main;
	 
                $('.hw-mobbar-middle').append(out);
				
				
				jQuery('.hw-mobbar').find('.hw-nav-search select,.hw-nav-search  > li > a').remove();
				jQuery('.hw-mobbar').find('.hw-nav-search').removeClass('hw-search-woo');
				
				
            }
        }
        jQuery(document).on("click", ".hw-mobbar-close", function() {
            $('.hw-mobbar').removeClass('hw-mobbar-active');
            $('[class*="hw-nav-"]').removeClass('hw-nav-active');
            $('.hw-mobbar-middle').html('');
        });
			/*****************************************************************************************************************************************************
		******************************************************************************************************************************************************
		 
																		Login
		 
		*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		        function mobile_menu_account($class) {
						
 						
						var account_attr = $class.parents('.hw-nav-mobile-menu').attr('data-account');		
  						var account_html = $('.hw-nav-account').html();	
 											
  						if ( account_attr==='show' && account_html !== undefined) {
							return '<ul class="hw-nav-mobbar-account">' + account_html + '</div>';
						}else{
							return '';
						}
				}
	/*****************************************************************************************************************************************************
	******************************************************************************************************************************************************
	 
																	Search
	 
	*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		        function search_loading() {

					var classes = $('.hw-nav-search').attr('class');
					var search_html = $('.hw-nav-search .hw-search').parent().html();
    
					if (search_html !== undefined) {
						return '<div class="' + classes + '">' + search_html + '</div>';
					}else{
						return '';
					}
				}
				
				
		/*****************************************************************************************************************************************************
		******************************************************************************************************************************************************
		 
																		Wish
		 
		*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				
				function wish_loading() {

					var classes = $('.hw-nav-wish').attr('class');
					var wish_html = $('.hw-nav-wish').html();
    
					if (wish_html !== undefined) {
						return '<div class="' + classes + '">' + wish_html + '</div>';
					}else{
						return '';
					}
				}			
				
	/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																Menu
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        jQuery(document).on("click", '[class*="hw-toolbar-mobile"] .hw-nav-mobile-menu a,[class*="hw-toolbar-mobile"] .hw-nav-mobile-category-menu a,[class*="hw-toolbar-mobile"] .hw-nav-account.hw-logged a,[class*="hw-toolbar-mobile"] [class*="hw-search-dropdown"] a', function() {

 			var html = $(this).parents('[class*="hw-nav-"]').find('.hw-mobile-content').html();
			var out='';
			
			 
            
             if (html !== undefined) {
                out += html;
            }
 
			 
   
            mobbar_loading(out);
			
 
        });
	
        jQuery(document).on("click", '[class*="hw-toolbar-mobile"] .hw-nav-cart a', function() {
            var html = $('header .widget_shopping_cart_content').html();
            mobbar_loading('<div class="hw-mobbar-cart"><div class="widget_shopping_cart_content">' + html + '</div></div>');
        }); 
        jQuery(document).on("click", ".hw-mobbar-menu i,.hw-mob-drop i", function() {
            if ($(this).parent().hasClass('hw-mob-drop-active')) {
                $(this).parent().parent().find('.hw-mob-drop-active').each(function(index, element) {
                    $(this).removeClass('hw-mob-drop-active');
                });
            } else {
                $(this).parent().parent().find('li').each(function(index, element) {
                    $(this).removeClass('hw-mob-drop-active');
                });
                $(this).parent().addClass('hw-mob-drop-active');
                $(this).parent().find('> .hw-mob-drop > .hw-mob-drop').parent().addClass('hw-mob-drop-active');
            }
        });

 
    }
})(jQuery);