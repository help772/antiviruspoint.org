(function($) {
    $.fn.hexwp_header = function() {


        if ($('[class*="hw-toolbar-"]').hasClass('hw-sticky')) {
            var main_sticky = jQuery('.hw-sticky').offset().top + 100;

            function masthead_sticky(main_sticky) {
				
                var top = jQuery(window).scrollTop();
                var height_sticky = main_sticky;
                if (top > height_sticky) {
                    jQuery('.hw-sticky').addClass('hw-sticky-start');
                    jQuery('.hw-sticky').show(1, function() {
                        jQuery('.hw-sticky').addClass('hw-sticky-enable');

                        var wpadminbar_height = jQuery("#wpadminbar").height();
						 var top_height = $('.hw-toolbar-top.hw-sticky-enable').height();
						 var middle_height = $('.hw-toolbar-middle.hw-sticky-enable').height();
 					
  						if(wpadminbar_height!== undefined){
							wpadminbar_height = wpadminbar_height;
						}else{
							wpadminbar_height=0;
						}
  						if(top_height!== undefined){
							top_height = top_height;
						}else{
							top_height=0;
						}
  						if(middle_height!== undefined){
							middle_height = middle_height;
						}else{
							middle_height=0;
						}
						
						
						
 							jQuery('.hw-toolbar-top.hw-sticky-enable').css({
								"--hw-sticky-mg": wpadminbar_height + "px"
							});
 			 
						
						
 							jQuery('.hw-toolbar-middle.hw-sticky-enable').css({
								"--hw-sticky-mg": (wpadminbar_height +top_height )  + "px"
							});
 					 
							jQuery('.hw-toolbar-bottom.hw-sticky-enable').css({
								"--hw-sticky-mg": (wpadminbar_height +top_height + middle_height)  + "px"
							});
 					 					
						
                    });

                } else {

                    
          

                    jQuery('.hw-sticky').css({
                        "--hw-sticky-mg": ""
                    });
                    jQuery('.hw-sticky').removeClass('hw-sticky-enable');
                    jQuery('.hw-sticky').removeClass('hw-sticky-start');


                }



            }
            $(window).on('scroll', function() {
                masthead_sticky(main_sticky);


            });

        }

function header_width(){
	$('.hw-middle-toolbar').each(function(index, element) {
 		var width = jQuery(this).width();
		  jQuery(this).css({'--hw-header-width':width+'px'});
  	});  
}
	
 
	
        $('[class*="hw-nav"]  .hw-drop').parent().on({
            mouseenter: function() {
                var body_width = $('body').width();
                var boxed_width = $('.hw-body-boxed .hw-body-warp').width();
                var difference = (body_width - boxed_width) / 2;
				
                var ul_first = $(this).find('> .hw-drop');
				var right = $('.hw-body-warp').outerWidth() - ul_first.offset().left - ul_first.outerWidth();
					 
                if (right < 0) {
                    ul_first.addClass('hw-menu-inverse').css({
                        '--hw-menu-rt': right + 'px'
                    });
                }
				ul_first.removeClass('hw-none-pointer');
 				
              },
            mouseleave: function() {
                var ul_first = $(this).find('> .hw-drop');
					ul_first.addClass('hw-not-pointer');
 	
                setTimeout(function() {
				ul_first.removeClass('hw-not-pointer');
 
                    ul_first.removeClass('hw-menu-inverse').css({
                        '--hw-menu-rt': ''
                    });
                }, 350);
             }
        });


        jQuery(document).on("click", ".hw-search-dropdown > li > a", function() {

            if (jQuery(this).parents('.hw-nav-search').hasClass('hw-nav-active')) {
                jQuery(this).parents('.hw-nav-search').removeClass('hw-nav-active');

            } else {
                jQuery(this).parents('.hw-nav-search').addClass('hw-nav-active');


            }
        });




        $(document).on('added_to_wishlist removed_from_wishlist', function() {
            var counter = $('.hw-nav-mobile-wish .hw-count,.hw-nav-wish .hw-count span');

            $.ajax({
                url: yith_wcwl_l10n.ajax_url,
                data: {
                    action: 'yith_wcwl_update_wishlist_count'
                },
                dataType: 'json',
                success: function(data) {
                    counter.html(data.count);
                },
                beforeSend: function() {
                    counter.block();
                },
                complete: function() {
                    counter.unblock();
                }
            })
        });


        jQuery(document).on("click", ".hw-nav-cat-menu > li > a", function() {
            if (jQuery(this).parents('.hw-nav-cat-menu').hasClass('hw-cat-active')) {
                jQuery(this).parents('.hw-nav-cat-menu').removeClass('hw-cat-active');
            } else {
                jQuery(this).parents('.hw-nav-cat-menu').addClass('hw-cat-active');

            }
        });

          jQuery(document).on("keyup", ".hw-search input", function() {
 			var val = $(this).val();
	 		 var data_val= $(this).attr('data-search');
		
			if(val !== data_val){	
				var data_key = Math.floor(Math.random() * 9999999999);
	
				var product_cat = $(this).parent().find('select').val();
				
				var element = $(this).parents('.hw-nav-search');
				var search_ajax = element.find('.hw-search-ajax');
				search_ajax.attr('data-key',data_key);
				search_ajax.find('.hw-el-search-ajax').remove();
				$(this).attr('data-search',val);
	
	
	
				if(val !== ''){
					
					search_ajax.addClass('hw-ajax-loading');
					$('body').addClass('hw-search-ajax-show');
					var dataajax = {};
					dataajax['action'] = 'hexwp_search_ajax';
					dataajax['search'] = val;
					dataajax['cats'] = product_cat;
						 
						 
						 
					setTimeout(function(){  
					if(search_ajax.attr('data-key') ==data_key){
						$.ajax({
							type: "POST",
							dataType: "html",
							url: hexwp_js.ajaxurl,
							data: dataajax,
							success: function(data) {
								var $data = $(data);
								search_ajax.find('.hw-el-search-ajax').remove();
								if ($data.length) {
										 
									element.find('.hw-search-ajax[data-key="'+data_key+'"]').append($data);
								}
								element.find('.hw-search-ajax[data-key="'+data_key+'"]').removeClass('hw-ajax-loading');
				 
							},
							error: function(jqXHR, textStatus, errorThrown) {
									$loader.html(jqXHR + " :: " + textStatus + " :: " + errorThrown);
							}
						});
					}}, 500); 
		
				}else{
					$('body').removeClass('hw-search-ajax-show');
					search_ajax.removeClass('hw-ajax-loading');
		
						
				}
			}

        });
 		
		
        jQuery(document).on("click",function (event) {
	  		if ($(event.target).closest(".hw-search-ajax,.hw-search").length === 0) {
				$('.hw-search-ajax').removeClass('hw-ajax-loading ');
				$('body').removeClass('hw-search-ajax-show');
 				$('.hw-search-ajax').find('.hw-el-search-ajax').remove();
			}
		});

		jQuery(document).on("click",'.hw-search-ajax-close',function () {
			var element = $(this).parents('[class*="-search-fixed-"]');
			var search_ajax = element.find('.hw-search-ajax');
			$('body').removeClass('hw-search-ajax-show');
			search_ajax.removeClass('hw-ajax-loading');
		 });
		
		 		
		
		 
		  $('.hw-element-menu').parents('[class*="vao-col-"],[class*="vao-sc-"],.elementor-element,.elementor-widget-wrap').each(function(index, element) {
               
				$(this).addClass('z-index');
 				
             
           
        });

		
		
		
	$('.hw-element-menu').each(function(index, element) {
 		var number = jQuery(this).attr('data-number');
		var number2 = parseInt(number);
		
  		 if(isNaN(number2)){} else{
				var count = jQuery(this).find('.hw-cat-drop > li').length;
     			if( number2  >= count){
					$(this).find('.hw-element-menu-more').remove();
				}else{
					$(this).addClass('hw-element-menu-has-more');

					$(this).find('.hw-cat-drop > li:nth-child(1n+'+number+')').each(function() {	
						 $(this).hide(0);
					});
				}
  		}
  			
 	});  
		
		
		
		

	jQuery(document).on("click", ".hw-element-menu-more" , function(){
   		var number = jQuery(this).parents('.hw-element-menu').attr('data-number');
		var number2 = parseInt(number);
		if(isNaN(number2)){} else{
			 
			var height = jQuery(this).parents('.hw-element-menu-warp').height();
				
			
			if(jQuery(this).parents('.hw-element-menu').hasClass('hw-element-menu-more-active')){
	 				 
					jQuery(this).parents('.hw-element-menu').find('.hw-cat-drop > li:nth-child(1n+'+number+')').each(function(index, block) {	
						$(this).slideUp( 500, function() {
							 jQuery(this).parents('.hw-element-menu-warp').attr('style','');
							jQuery(this).parents('.hw-element-menu').removeClass('hw-element-menu-more-active');
	
						});
					 });
		 
				
			} else{ 
				jQuery(this).parents('.hw-element-menu').addClass('hw-element-menu-more-active');
				jQuery(this).parents('.hw-element-menu-warp').height(height);
				jQuery(this).parents('.hw-element-menu').find('.hw-cat-drop > li:nth-child(1n+'+number+')').each(function(index, block) {	
		  
						
					$(this).slideDown( 500, function() {
						$(this).show(0);
					 });
				});	
			
			 } 
			 
			
			 
		 }
		 
		
  	});  
	 		
	$('.hw-nav-banner-header').each(function(index, element) {
 		var width = jQuery(this).width();
 		var parents = jQuery(this).parents('.hw-middle-toolbar').width();
	 
  			
 	});  
		
$(window).on('resize', function() {
header_width();
});
header_width();		
		
    };
}(jQuery));