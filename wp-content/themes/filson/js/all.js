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
}(jQuery));(function($) {
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
})(jQuery);(function($) {


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

})(jQuery);(function($) {
 
    $.fn.showclock = function() {

        var currentDate = new Date();
        var fieldDate = $(this).data('date').split('-');
        var text_days = $(this).data('days');
        var text_hours = $(this).data('hours');
        var text_minutes = $(this).data('minutes');
        var text_seconds = $(this).data('seconds');
        var fieldTime = [0, 0];
        if ($(this).data('time') != undefined)
            fieldTime = $(this).data('time').split(':');
        var futureDate = new Date(fieldDate[0], fieldDate[1] - 1, fieldDate[2], fieldTime[0], fieldTime[1]);
        var seconds = futureDate.getTime() / 1000 - currentDate.getTime() / 1000;

        if (seconds <= 0 || isNaN(seconds)) {
            this.hide();
            return this;
        }

        var days = Math.floor(seconds / 86400);
        seconds = seconds % 86400;

        var hours = Math.floor(seconds / 3600);
        seconds = seconds % 3600;

        var minutes = Math.floor(seconds / 60);
        seconds = Math.floor(seconds % 60);

        var html = "";
        if (days !== 0) {
            html += '<div class="hw-cd-item">';
            html += '<span>' + days + '</span>';
            html += '<span>' + text_days + '</span>';
            html += "</div>";

        }

        html += '<div class="hw-cd-item">';
        html += '<span>' + hours + '</span>';
        html += '<span>' + text_hours + '</span>';
        html += "</div>";		
		
        html += '<div class="hw-cd-item">';
        html += '<span>' + minutes + '</span>';
        html += '<span>' + text_minutes + '</span>';
        html += "</div>";

        html += '<div class="hw-cd-item">';
        html += '<span>' + seconds + '</span>';
        html += '<span>' + text_seconds + '</span>';
        html += "</div>";






        this.html(html);
    };

    $.fn.hexwp_countdown = function() {
        $(this).find(".hw-countdown").each(function() {
            var el = $(this);
            el.showclock();

            setInterval(function() {
                 el.showclock();
            }, 1000);
        });

    };
}(jQuery)); 
(function($) {
    $.fn.hexwp_cart_button = function() {
 
        $(".hw-product-button .add_to_cart_button,.hw-product-button .add_to_wishlist,.hw-product-button .yith-wcwl-wishlistexistsbrowse a,.hw-product-button .compare-button a").on({
            mouseenter: function() {
                var self = this;
                // تنظیم یک تایم‌اوت برای اجرای دستورات پس از 350 میلی‌ثانیه
                timeout = setTimeout(function() {
                    var text = $(self).text();
                    $('body').append('<div class="hw-btn-text-hover"><span>' + text + '</span></div>');
                    var wpadminbar = $('#wpadminbar').height();
                    if ($.isNumeric(wpadminbar)) {
                        var header_sticky_top = wpadminbar;
                    } else {
                        var header_sticky_top = 0;
                    }
                    var btn_item = $(self).parents('.hw-product-button > div');
                    var btn_height = btn_item.height();
                    var btn_width = btn_item.width() / 2;
                    var width = $('.hw-btn-text-hover').width() / 2;
                    var left = btn_item.offset().left - width + btn_width;
                    var top = btn_item.offset().top - 35;
                    $('.hw-btn-text-hover').css({
                        'top': top.toFixed(0) + 'px',
                        'left': left.toFixed(0) + 'px'
                    });
                }, 350);
            },
            mouseleave: function() {
                 clearTimeout(timeout);
                $('.hw-btn-text-hover').remove();
            },
        });
    };
}(jQuery));(function($) {
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
}(jQuery));!(function(e, n) {
    "function" == typeof define && define.amd ? define(["exports"], n) : "undefined" != typeof exports ? n(exports) : n((e.dragscroll = {}));
})(this, function(e) {
    var n,
        t,
        o = window,
        r = document,
        i = [],
        l = function(e, l) {
            for (e = 0; e < i.length;)(l = (l = i[e++]).container || l).removeEventListener("mousedown", l.md, 0), o.removeEventListener("mouseup", l.mu, 0), o.removeEventListener("mousemove", l.mm, 0);
            for (i = [].slice.call(r.getElementsByClassName("dragscroll")), e = 0; e < i.length;)
                !(function(e, i, l, s, d, m) {
                    (m = e.container || e).addEventListener(
                            "mousedown",
                            (m.md = function(n) {
                                (e.hasAttribute("nochilddrag") && r.elementFromPoint(n.pageX, n.pageY) != m) || ((s = 1), (i = n.clientX), (l = n.clientY), n.preventDefault());
                            }),
                            0
                        ),
                        o.addEventListener(
                            "mouseup",
                            (m.mu = function() {
                                s = 0;
                            }),
                            0
                        ),
                        o.addEventListener(
                            "mousemove",
                            (m.mm = function(o) {
                                s && (((d = e.scroller || e).scrollLeft -= n = -i + (i = o.clientX)), (d.scrollTop -= t = -l + (l = o.clientY)), e == r.body && (((d = r.documentElement).scrollLeft -= n), (d.scrollTop -= t)));
                            }),
                            0
                        );
                })(i[e++]);
        };
    "complete" == r.readyState ? l() : o.addEventListener("load", l, 0), (e.reset = l);
});(function($) {
    $.fn.hexwp_figcaption_hover = function() {
        $(this).find('.hw-module-1,.hw-module-2,.hw-cap-hover-middle .hw-glider').on({
            mouseenter: function() {
                var element = $(this);
                element.find('figcaption').addClass('hw-aw');
                element.hexwp_auto_width();

            },
            mouseleave: function() {
                var element = $(this);
                setTimeout(function() {
                    element.find('figcaption').hexwp_remove_auto_width_warp();
                    element.find('figcaption').removeClass('hw-aw');
                }, 350);
            }
        });
    };

})(jQuery);(function($) {
    $.fn.hexwp_lightbox = function() {



	 $('.single .hw-single-content img').parent('a').each(function(i, el) {
 	var hexwp_href_value = this.href;
  	if (/\.(jpg|jpeg|png|gif|jpeg|bmp|webp)$/.test(hexwp_href_value)) {
		$(this).addClass('hw-singleimg-lightbox');
  	}  
	});

        if (jQuery('.hw-lightbox').hasClass('hw-lightbox-active')) {
            $('.hw-singleimg-lightbox').on('click', function(event) {
                $('.hw-lightbox').addClass('hw-lightbox-post-content')

                event.stopPropagation();
                event.preventDefault();
                var images = $('.hw-singleimg-lightbox');
                rd_lightboxActual = this;

				var href = this.getAttribute('href');
				if(href){
					var href_image= href;
				}else{
					var href_image= $(this).find('img').attr('src');
				}
                $('.hw-lightbox-targetimg').css('display', 'none').attr('src', href_image);
                $('.hw-lightbox-targetimg').one('load', function() {
                    $('.hw-lightbox-loading').css('display', 'none');
                    $(this).fadeIn();
                });

                var text = $(this).parent().find('.wp-caption-text').text();
                var display = 'block';
				/*
                if (text == null) {
                    text = '';
                    display = 'none';
                }
*/
                $('.hw-lightbox h3').text(text);
                $('.hw-lightbox').addClass('hw-multi-lightbox').slideToggle('fast');
                var actualId;
                $.each(images, function(index) {
                    if (rd_lightboxActual === images[index]) {
                        actualId = index + 1;
                    }
                });
                if (images.length == 1) {
                    $('.hw-lightbox-moreitems').css('display', 'none');
                }

                $('.hw-lightbox span').text($.rd_lightboxMessage(actualId, images.length));


            });
        }
		
		
		
		
		
        $.rd_lightboxMove = function(direction, allImages) {
            console.log(allImages);
            direction = (direction == 'next') ? 'next' : 'prev';
            var actualId;
            $.each(allImages, function(index) {
                if (allImages[index] === rd_lightboxActual) {
                    actualId = index;
                }
            });

            var iterator;
            if (direction == 'next') {
                iterator = actualId + 1;
                if (actualId == allImages.length - 1) {
                    iterator = 0;
                }
            } else if (direction == 'prev') {
                iterator = actualId - 1;
                if (actualId == 0) {
                    iterator = allImages.length - 1;
                }
            }

            var newImage = allImages[iterator];
			
			
				var href = newImage.getAttribute('href');
				if(href){
					var href_image= href;
				}else{
 					var href_image= $(newImage).find('img').attr('src');
				}
			
            $('.hw-lightbox-targetimg').css('display', 'none').attr('src',href_image);
            $('.hw-lightbox-loading').css('display', 'block');

            $('.hw-lightbox-targetimg').one('load', function() {
                $('.hw-lightbox-loading').css('display', 'none');
                $(this).css('display', 'inline-block');
            });

            var text = $(newImage).parent().find('.wp-caption-text').text();
            var display = 'block';

            if (text == null) {
                text = '';
                display = 'none';
            }
            $('.hw-lightbox h3').text(text).css('display', display);
            $('.hw-lightbox span').text($.rd_lightboxMessage(iterator + 1, allImages.length));
            rd_lightboxActual = newImage;
        };


        $(document).on('click', '.hw-lightbox-post-content.hw-multi-lightbox .hw-lightbox-prevbig', function() {
            $.rd_lightboxMove('prev', $('.hw-singleimg-lightbox'));
        });

        $(document).on('click', '.hw-lightbox-outer,.hw-lightbox-close', function() {
            $('.hw-lightbox').slideToggle('fast');
        });

        $(document).on('click', '.hw-lightbox-close,.hw-lightbox-outer', function() {
            $('.hw-lightbox').removeClass('hw-lightbox-singleimg').removeClass('hw-multi-lightbox');
            $('.hw-lightbox').removeClass('hw-lightbox-post-content').removeClass('hw-lightbox-gallery');
      

        });

        $(document).on('click', '.hw-multi-lightbox.hw-lightbox-post-content .hw-lightbox-nextbig,.hw-multi-lightbox.hw-lightbox-post-content .hw-lightbox-targetimg', function() {
            $.rd_lightboxMove('next', $('.hw-singleimg-lightbox'));
        });



        var rd_lightboxActual = null;

        $.rd_lightboxMessage = function(actual, last) {
            return '' + $('body').hexwp_translate(actual + ' / ' + last);
        };



        $(document).on('keydown', function(event) {
            if (event.keyCode == 37) {


                if ($('.hw-lightbox').hasClass('hw-multi-lightbox')) {
                    if ($('.hw-lightbox').hasClass('hw-lightbox-post-content')) {

                        $.rd_lightboxMove('prev', $('.hw-single-content img').parent('a'));
                    }
                }
            }
        });
        $(document).on('keydown', function(event) {

            if ($('.hw-lightbox').hasClass('hw-multi-lightbox')) {
                if ($('.hw-lightbox').hasClass('hw-lightbox-post-content')) {
                    if (event.keyCode == 39) {
                        $.rd_lightboxMove('next', $('.hw-single-content img').parent('a'));
                    }
                }
            }

        });






        if (jQuery('.hw-lightbox').hasClass('hw-lightbox-active')) {
            $('.hw-hover-img,.hw-single-product-image .hw-product-thumb-resize .hw-product-lightbox').on('click', function(event) {
                $('.hw-hover-img,.hw-product-lightbox:not(.hw-slick-current)').each(function(i, el) {
					
                    var hexwp_href_value = this.href;
 					if(hexwp_href_value){
						var href_image=  this.href;
					}else{
						var href_image= $(this).find('img').attr('src');
					}					
					
                     if (/\.(jpg|jpeg|png|gif|jpeg|bmp|webp)$/.test(href_image)) {
                        $(this).addClass('hw-singleimg-lightbox');
                    }
                });

                event.stopPropagation();
                event.preventDefault();
                var rand = Math.floor((Math.random() * 10000000) + 1);

                $(this).parents('[class*="hw-el-"],.hw-product-thumbnails-warp').attr('data-lightbox', rand);


                $('.hw-lightbox').addClass('hw-lightbox-gallery').attr('data-rand', rand);
                var images = $(this).parents('[class*="hw-el-"],.hw-product-thumbnails-warp').find('.hw-singleimg-lightbox');
                rd_lightboxActual = this;
	
				var href = this.getAttribute('href');
				if(href){
					var href_image= href;
				}else{
					var href_image= $(this).find('img').attr('src');
				}
			
                $('.hw-lightbox-targetimg').css('display', 'none').attr('src', href_image);
                $('.hw-lightbox-targetimg').one('load', function() {
                    $('.hw-lightbox-loading').css('display', 'none');
                    $(this).slideToggle(0);
                });

                var text = $(this).next().html();
                var display = 'block';
                if (text == null) {
                    text = '';
                    display = 'none';
                }

                $('.hw-lightbox-title').text(text).css('display', display);
                $('.hw-lightbox').addClass('hw-multi-lightbox').slideToggle('fast');
                var actualId;
                $.each(images, function(index) {
                    if (rd_lightboxActual === images[index]) {
                        actualId = index + 1;
                    }
                });
                if (images.length == 1) {
                    $('.hw-lightbox-moreitems').css('display', 'none');


                }
                $('.hw-lightbox').attr('data-item', images.length);
                $('.hw-lightbox span').text($.rd_lightboxMessage(actualId, images.length));



            });
        }





        $(document).on('click', '.hw-multi-lightbox.hw-lightbox-gallery .hw-lightbox-prevbig', function() {
            var rand = $('.hw-lightbox').attr('data-rand');
            $.rd_lightboxMove('prev', $('[data-lightbox="' + rand + '"]').find('.hw-hover-img,.hw-product-lightbox:not(.hw-slick-current)'));
        });



        $(document).on('click', '.hw-multi-lightbox.hw-lightbox-gallery .hw-lightbox-nextbig,.hw-multi-lightbox.hw-lightbox-gallery .hw-lightbox-targetimg', function() {
            var rand = $('.hw-lightbox').attr('data-rand');
            $.rd_lightboxMove('next', $('[data-lightbox="' + rand + '"]').find('.hw-hover-img,.hw-product-lightbox:not(.hw-slick-current)'));
        });




        $(document).on('keydown', function(event) {
            if (event.keyCode == 39) {

                if ($('.hw-lightbox').hasClass('hw-multi-lightbox')) {
                    if ($('.hw-lightbox').hasClass('hw-lightbox-gallery')) {
                        var rand = $('.hw-lightbox').attr('data-rand');
                        $.rd_lightboxMove('next', $('[data-lightbox="' + rand + '"]').find('.hw-hover-img,.hw-product-lightbox:not(.hw-slick-current)'));
                    }
                }
            }
        });
        $(document).on('keydown', function(event) {
            if (event.keyCode == 37) {

                if ($('.hw-lightbox').hasClass('hw-multi-lightbox')) {
                    if ($('.hw-lightbox').hasClass('hw-lightbox-gallery')) {


                        var rand = $('.hw-lightbox').attr('data-rand');
                        $.rd_lightboxMove('prev', $('[data-lightbox="' + rand + '"]').find('.hw-hover-img,.hw-product-lightbox:not(.hw-slick-current)'));

                    }
                }

            }
        });



    };
}(jQuery));(function($) {
    $.fn.hexwp_translate = function(input) {
       
        return input;
    }

    $.fn.hexwp_translatepad = function(n) {
        return $('body').hexwp_translate((n < 10) ? (n) : n);
    }


}(jQuery));(function($) {
    $.fn.hexwp_tabs_transition = function() {

        $('.rez-transition').on('click', '.hw-tab-item:not(.hw-view-all)', function(index, element) {
            var transition = $(this).parents('.rez-transition');
            transition.addClass('rez-all-transition-active');

            transition.find('.hw-tab-active').removeClass('hw-tab-active');
            $(this).addClass('hw-tab-active');

            var data_filter = $(this).attr('data-filter');

            var list_html = transition.find('.hw-gap-warp').html();
            transition.find('.hw-gap-warp').append('<div class="hw-append">' + list_html + '</div>');
            var append = transition.find('.hw-append .hw-item-list');

            var list = transition.find('.hw-gap-warp > .hw-item-list');
            var old_list_height = list.height().toFixed(0);
            list.css({
                "--hw-old-list-ht": old_list_height + 'px'
            });
            list.find('.hw-item').each(function(i) {
                var count = i + 1;
                $(this).attr('data-count', count);

                var width = $(this).width();
                var height = $(this).height();
                if (transition.hasClass('macy-masonry')) {
                    var horizontal = $(this).position().left;
                    var vertical = $(this).position().top;

                } else {
                    var horizontal = $(this).offset().left - (window.scrollX);
                    var vertical = $(this).offset().top - (window.scrollY);
                }
                $(this).css({
                    "--hw-old-wt": width.toFixed(0) + 'px',
                    "--hw-old-ht": height.toFixed(0) + 'px',
                    "--hw-old-hor": horizontal.toFixed(0) + 'px',
                    "--hw-old-ver": vertical.toFixed(0) + 'px'
                });

            });


            setTimeout(function() {
                if (data_filter !== 'all') {
                    transition.addClass('hw-filter-not-all');
                }
                if (data_filter === 'all') {
                    transition.removeClass('hw-filter-not-all');
                }

                transition.addClass('rez-transition-active');

                append.find('.hw-item').each(function(i) {
                    var count = i + 1;

                    var main_post_item = transition.find('.hw-gap-warp > .hw-item-list .hw-item[data-count="' + count + '"]');
                    var append_item = $(this);

                    if (append_item.hasClass(data_filter) || data_filter === 'all') {
                        if (append_item.hasClass('rez-has-hide')) {
                            main_post_item.addClass('rez-to-show');
                        } else {
                            main_post_item.addClass('rez-to-move');
                        }
                        main_post_item.removeClass('rez-has-hide');
                        append_item.removeClass('rez-has-hide');
                    } else {
                        if (!append_item.hasClass('rez-has-hide')) {
                            main_post_item.addClass('rez-to-hide');
                        }
                        append_item.addClass('rez-has-hide');
                    }

                });
            }, 1);


            if (jQuery().hexwp_macy) {
                setTimeout(function() {
                    append.hexwp_macy();
                }, 2);
            }
            setTimeout(function() {
                append.find('.hw-item').each(function(i) {
                    var count = i + 1;
                    var main_post_item = transition.find('.hw-gap-warp > .hw-item-list .hw-item[data-count="' + count + '"]');
                    var append_item = $(this);
                    var width = append_item.width();
                    var height = append_item.height();

                    if (transition.hasClass('macy-masonry')) {
                        var horizontal = append_item.position().left;
                        var vertical = append_item.position().top;

                    } else {
                        var horizontal = append_item.offset().left - (window.scrollX);
                        var vertical = append_item.offset().top - (window.scrollY);

                    }
                    main_post_item.css({
                        "--hw-wt": width.toFixed(0) + 'px',
                        "--hw-ht": height.toFixed(0) + 'px',
                        "--hw-hor": horizontal.toFixed(0) + 'px',
                        "--hw-ver": vertical.toFixed(0) + 'px'
                    });


                });



                var list_height = append.height().toFixed(0);
                list.css({
                    "--hw-list-ht": list_height + 'px'
                });
                if (list_height < old_list_height) {
                    transition.addClass('hw-old-list-height');
                }

            }, 10);

            setTimeout(function() {
                transition.addClass('rez-animte');
            }, 15);

            setTimeout(function() {

                transition.find('.hw-gap-warp > .hw-item-list .hw-item').each(function(index, element) {
                    if ($(this).hasClass('rez-to-hide')) {

                        $(this).addClass('rez-has-hide');
                    }
                    $(this).removeClass('rez-to-move rez-to-show rez-to-hide');
                    $(this).removeAttr('data-count');
                    $(this).css({
                        '--hw-wt': '',
                        '--hw-ht': '',
                        '--hw-hor': '',
                        '--hw-ver': '',
                        '--hw-old-wt': '',
                        '--hw-old-ht': '',
                        '--hw-old-hor': '',
                        '--hw-old-ver': ''
                    });

                });
                transition.removeClass('rez-animte rez-transition-active rez-all-transition-active hw-old-list-height');
                $(this).css({
                    '--hw-old-list-ht': '',
                    '--hw-list-ht': ''
                });
                if (jQuery().hexwp_macy) {
                    list.hexwp_macy();
                }
                transition.find('.hw-item-list').removeAttr('style');
                transition.find('.hw-append').remove();

            }, 400);

        });

    };

})(jQuery);(function($) {
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

})(jQuery);(function($) {
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

})(jQuery);(function($) {
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
}(jQuery));(function($) {
    $.fn.hexwp_widget = function() {

        function social_font_size() {
            $('.hw-el-widget_social').each(function(index, element) {
                var width = $(this).find('a').width().toFixed(0);
                if ($(this).find('.hw-item-list').hasClass('hw-social-icon-style-1')) {
                    var width_size = width * 0.7;
                } else {
                    var width_size = width;
                }
                $(this).attr('style', '--hw-scl-sz:' + width_size + 'px;');

            });

        }
        social_font_size();
 
        $(window).on('resize', function() {
            social_font_size();
        });
    };

})(jQuery);	// Generated by CoffeeScript 1.9.2
	/**
		@license Sticky-kit v1.1.2 | WTFPL | Leaf Corcoran 2015 |  
		 */


	(function() {
	    var $, win;

	    $ = this.jQuery || window.jQuery;

	    win = $(window);
		function hexwp_margin_top(offset = 0){
				var header_sticky_top = 0;
	                                    var header_sticky = $('.hw-sticky-enable').height();
	                                    var wpadminbar = $('#wpadminbar').height();
	                                    if ($.isNumeric(header_sticky)) {
	                                        header_sticky_top = header_sticky;
	                                    }
	                                    if ($.isNumeric(wpadminbar)) {
	                                        header_sticky_top = header_sticky_top + wpadminbar;
	                                    }
	                                    if (offset === 0) {
	                                        return offset + header_sticky_top;
	                                    } else {
	                                        return offset;
	                                    }
										
		}
		
	    $.fn.stick_in_parent = function(opts) {
	        var doc, elm, enable_bottoming, fn, i, inner_scrolling, len, manual_spacer, offset_top, parent_selector, recalc_every, sticky_class;
	        if (opts == null) {
	            opts = {};
	        }
	        sticky_class = opts.sticky_class, inner_scrolling = opts.inner_scrolling, recalc_every = opts.recalc_every, parent_selector = opts.parent, offset_top = opts.offset_top, manual_spacer = opts.spacer, enable_bottoming = opts.bottoming;
	        if (offset_top == null) {
	            offset_top = 0;
	        }
	        if (parent_selector == null) {
	            parent_selector = void 0;
	        }
	        if (inner_scrolling == null) {
	            inner_scrolling = true;
	        }
	        if (sticky_class == null) {
	            sticky_class = "is_stuck";
	        }
	        doc = $(document);
	        if (enable_bottoming == null) {
	            enable_bottoming = true;
	        }
	        fn = function(elm, padding_bottom, parent_top, parent_height, top, height, el_float, detached) {
	            var bottomed, detach, fixed, last_pos, last_scroll_height, offset, parent, recalc, recalc_and_tick, recalc_counter, spacer, tick;
	            if (elm.data("sticky_kit")) {
	                return;
	            }
	            elm.data("sticky_kit", true);
	            last_scroll_height = doc.height();
	            parent = elm.parent();
	            if (parent_selector != null) {
	                parent = parent.closest(parent_selector);
	            }
	            if (!parent.length) {
	                throw "failed to find stick parent";
	            }
	            fixed = false;
	            bottomed = false;
	            spacer = manual_spacer != null ? manual_spacer && elm.closest(manual_spacer) : $("<div />");
	            if (spacer) {
	                spacer.css('position', elm.css('position'));
	            }
	            recalc = function() {
	                var border_top, padding_top, restore;
	                if (detached) {
	                    return;
	                }
	                last_scroll_height = doc.height();
	                border_top = parseInt(parent.css("border-top-width"), 10);
	                padding_top = parseInt(parent.css("padding-top"), 10);
	                padding_bottom = parseInt(parent.css("padding-bottom"), 10);
	                parent_top = parent.offset().top + border_top + padding_top;
	                parent_height = parent.height();
	                if (fixed) {
	                    fixed = false;
	                    bottomed = false;
	                    if (manual_spacer == null) {
	                        elm.insertAfter(spacer);
	                        spacer.detach();
	                    }
	                    elm.css({
	                        position: "",
	                        top: "",
	                        width: "",
	                        bottom: ""
	                    }).removeClass(sticky_class);
	                    restore = true;
	                }
	                top = elm.offset().top - (parseInt(elm.css("margin-top"), 10) || 0) - offset_top;
	                height = elm.outerHeight(true);
	                el_float = elm.css("float");
	                if (spacer) {
	                    spacer.css({
	                        width: elm.outerWidth(true),
	                        height: height,
	                        display: elm.css("display"),
	                        "vertical-align": elm.css("vertical-align"),
	                        "float": el_float
	                    });
	                }
	                if (restore) {
	                    return tick();
	                }
	            };
	            recalc();
	            if (height === parent_height) {
	                return;
	            }
	            last_pos = void 0;
	            offset = offset_top;
	            recalc_counter = recalc_every;
	            tick = function() {
	                var css, delta, recalced, scroll, will_bottom, win_height;
	                if (detached) {
	                    return;
	                }
	                recalced = false;
	                if (recalc_counter != null) {
	                    recalc_counter -= 1;
	                    if (recalc_counter <= 0) {
	                        recalc_counter = recalc_every;
	                        recalc();
	                        recalced = true;
	                    }
	                }
	                if (!recalced && doc.height() !== last_scroll_height) {
	                    recalc();
	                    recalced = true;
	                }
	                scroll = win.scrollTop();
	                if (last_pos != null) {
	                    delta = scroll - last_pos;
	                }
	                last_pos = scroll;
	                if (fixed) {
	                    if (enable_bottoming) {
	                        will_bottom = scroll + height + offset > parent_height + parent_top;
							 		 
							
							
	                        if (bottomed && !will_bottom) {
							 
								
	                            bottomed = false;
	                            elm.css({
	                                position: "fixed",
	                                bottom: "",
	                                top: hexwp_margin_top(offset),  
	                            }).trigger("sticky_kit:unbottom");
	                        }
	                    }
	                    if (scroll < top) {
	                        fixed = false;
	                        offset = offset_top;
	                        if (manual_spacer == null) {
	                            if (el_float === "left" || el_float === "right") {
	                                elm.insertAfter(spacer);
	                            }
	                            spacer.detach();
	                        }
	                        css = {
	                            position: "",
	                            width: "",
	                            top: ""
	                        };
	                        elm.css(css).removeClass(sticky_class).trigger("sticky_kit:unstick");
	                    }
	                    if (inner_scrolling) {
	                        win_height = win.height();
	                        if (height + offset_top > win_height) {
	                            if (!bottomed) {
	                                offset -= delta;
	                                offset = Math.max(win_height - height, offset);
	                                offset = Math.min(offset_top, offset);


	                                if (fixed) {

	                                   
	                                    elm.css({
	                                        top: hexwp_margin_top(offset) + "px"
	                                    });
	                                }
	                            }
	                        }
	                    }
	                } else {
	                    if (scroll > top) {

	                        fixed = true;
							
						 
							
	                        css = {
	                            position: "fixed",
	                            top: hexwp_margin_top(offset) + 'px',
	                        };
	                        css.width = elm.css("box-sizing") === "border-box" ? elm.outerWidth() + "px" : elm.width() + "px";
	                        elm.css(css).addClass(sticky_class);
	                        if (manual_spacer == null) {
	                            elm.after(spacer);
	                            if (el_float === "left" || el_float === "right") {
	                                spacer.append(elm);
	                            }
	                        }
	                        elm.trigger("sticky_kit:stick");
	                    }
	                }
	                if (fixed && enable_bottoming) {
	                    if (will_bottom == null) {
	                        will_bottom = scroll + height + offset > parent_height + parent_top;
	                    }
	                    if (!bottomed && will_bottom) {
	                        bottomed = true;
	                        if (parent.css("position") === "static") {
	                            parent.css({
	                                position: "relative"
	                            });
	                        }
	                        return elm.css({
	                            position: "absolute",
	                            bottom: padding_bottom,
	                            top: "auto"
	                        }).trigger("sticky_kit:bottom");
	                    }
	                }
	            };
	            recalc_and_tick = function() {
	                recalc();
	                return tick();
	            };
	            detach = function() {
	                detached = true;
	                win.off("touchmove", tick);
	                win.off("scroll", tick);
	                win.off("resize", recalc_and_tick);
	                $(document.body).off("sticky_kit:recalc", recalc_and_tick);
	                elm.off("sticky_kit:detach", detach);
	                elm.removeData("sticky_kit");
	                elm.css({
	                    position: "",
	                    bottom: "",
	                    top: "",
	                    width: ""
	                });
	                parent.position("position", "");
	                if (fixed) {
	                    if (manual_spacer == null) {
	                        if (el_float === "left" || el_float === "right") {
	                            elm.insertAfter(spacer);
	                        }
	                        spacer.remove();
	                    }
	                    return elm.removeClass(sticky_class);
	                }
	            };
	            win.on("touchmove", tick);
	            win.on("scroll", tick);
	            win.on("resize", recalc_and_tick);
	            $(document.body).on("sticky_kit:recalc", recalc_and_tick);
	            elm.on("sticky_kit:detach", detach);
	            return setTimeout(tick, 0);
	        };
	        for (i = 0, len = this.length; i < len; i++) {
	            elm = this[i];
	            fn($(elm));
	        }
	        return this;
	    };

	}).call(this);


	(function($) {
	    $.fn.hexwp_sidebar_sticky = function() {
	        $('.hw-column-sidebar').each(function(index, element) {
	            $(this).find(".hw-sticky-sidebar").stick_in_parent();

	        });
	    };
	}(jQuery));(function($) {
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
 
}(jQuery));(function($) {
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
})(jQuery); (function($) {
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