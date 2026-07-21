<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Title Box 
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	$item_css.=
hexwp_custom_var('--hw-tbox-main-bg',hexwp_option_2('title_box_main_color','background')). 
hexwp_custom_var('--hw-tbox-main-txt',hexwp_option_2('title_box_main_color','text')).
hexwp_custom_var_font_typo('--hw-tbox-main','title_box_main_typo').   
hexwp_custom_var('--hw-tbox-tab-bg',hexwp_option_2('title_box_tab_color','background')).
hexwp_custom_var('--hw-tbox-tab-txt',hexwp_option_2('title_box_tab_color','text')).
hexwp_custom_var_font_typo('--hw-tbox-tab','title_box_tab_typo').   	
hexwp_custom_var('--hw-tbox-atv-bg',hexwp_option_2('title_box_active_color','background')).
hexwp_custom_var('--hw-tbox-atv-txt',hexwp_option_2('title_box_active_color','text')).
hexwp_custom_var('--hw-tbox-br-cr',hexwp_option('title_box_border_color')).  
hexwp_custom_var('--hw-tbox-rd',hexwp_option('title_box_radius'));
    	
 
 
 	 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Title Box 
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	 
 
 
 
 
 
	$item_css.=
			hexwp_custom_var('--hw-post-bg',hexwp_option('post_background_color')).
 			hexwp_custom_var('--hw-post-tl-lk',hexwp_option_2('post_title_color','link')).
			hexwp_custom_var('--hw-post-tl-hv-lk',hexwp_option_2('post_title_color','hover')).
			
			hexwp_custom_var('--hw-price-ma',hexwp_option_2('price_color','main')).
			hexwp_custom_var('--hw-price-sa',hexwp_option_2('price_color','sale')).
			hexwp_custom_var('--hw-price-re',hexwp_option_2('price_color','regular')).
			
			hexwp_custom_var('--hw-expt-txt',hexwp_option('post_excerpt_color')).
			hexwp_custom_var('--hw-meta-txt',hexwp_option('post_meta_color')).
			hexwp_custom_var('--hw-aw-txt',hexwp_option_2('arrow_color','arrow')).
			hexwp_custom_var('--hw-aw-bg',hexwp_option_2('arrow_color','background')).
			hexwp_custom_var('--hw-cd-bg',hexwp_option_2('countdown_color','background')).
			hexwp_custom_var('--hw-cd-num',hexwp_option_2('countdown_color','number')).
			hexwp_custom_var('--hw-cd-txt',hexwp_option_2('countdown_color','text')).
 			hexwp_custom_var_gradient_background_color('--hw-featured-bg',hexwp_option_2('featured_color','background'),hexwp_option_2('featured_color','background_2'),'225deg').
  			hexwp_custom_var('--hw-featured-txt',hexwp_option_2('featured_color','text')).
			hexwp_custom_var('--hw-rat-rat-cr',hexwp_option_2('rating_color','rating')).
			hexwp_custom_var('--hw-rat-no-cr',hexwp_option_2('rating_color','none')).
			hexwp_custom_var_shadow_size('--hw-post-sd-sz',hexwp_option('box_border_size')).
			hexwp_custom_var('--hw-post-sd',hexwp_option('box_border_color')).
   			hexwp_custom_var('--hw-post-hv-sd',hexwp_rgba2hex(hexwp_option('box_border_color'))).

 
  			hexwp_custom_var_font_typo_mini('--hw-post-tl','post_title_typo').
  			hexwp_custom_var_font_typo_mini('--hw-product-tl','product_title_typo').
  			hexwp_custom_var_font_typo_mini('--hw-price','price_typo').
			 
 			hexwp_custom_var_font_typo_mini('--hw-expt','post_excerpt_typo').
 			hexwp_custom_var_font_typo_mini('--hw-meta','post_meta_typo').
			hexwp_custom_var_font_typo_mini('--hw-read','read_more_typo').  	
			hexwp_custom_var_font_typo_mini('--hw-more','more_posts_typo').  	
			hexwp_custom_var_font_typo_size('--hw-article','article_typo');  	
	 
	
	 
	$item_css.=
			hexwp_custom_var('--hw-cap-bg',hexwp_option('caption_background_color')).
			hexwp_custom_var('--hw-cap-txt',hexwp_option('caption_color')).
			hexwp_custom_var('--hw-cap-expt-txt',hexwp_hex2rgbacolor(hexwp_option('caption_color'),0.7)).
			hexwp_custom_var('--hw-cap-meta-txt',hexwp_hex2rgbacolor(hexwp_option('caption_color'),0.5)).
			hexwp_custom_var('--hw-cap-br-cr',hexwp_hex2rgbacolor(hexwp_option('caption_color'),0.25));
			
			
	$blog_css =hexwp_custom_var_meta('blog');
	$single_css =hexwp_custom_var_meta('single');
 		
	$price_pos = !empty(get_option( 'woocommerce_currency_pos'))?get_option( 'woocommerce_currency_pos'):'';	
			
	$item_css.=hexwp_custom_var('--hw-gl-item',hexwp_option('single_product_gallery_item'));		
			
	if($price_pos =='left'){
		$item_css.=  '--hw-price-pos:left;';
	}elseif($price_pos =='right'){
		$item_css.=  '--hw-price-pos:right;';
	}elseif($price_pos =='left_space'){
		$item_css.=  '--hw-price-pos:left;--hw-price-sp:0px 5px 0px 0px;';
	}elseif($price_pos =='right_space'){
		$item_css.=  '--hw-price-pos:right;--hw-price-sp: 0px 0px 0px 5px;';
	} 		