<?php
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Header Logo
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
 	$overlab_height=0;
	$height=0;
	if(!empty($header_builder['element'])){
	foreach(hexwp_header_decode($header_builder['element']) as $element_key => $element_value):
	$element_id = !empty($element_value['id'])?$element_value['id']:'';
	$opt = !empty($element_value['option'])?hexwp_json_decode($element_value['option']):'';

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Header Logo
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
		if($element_id=='logo'){
			$logo_css= hexwp_custom_var_unit('--hw-logo-wt',hexwp_isset($opt,'logo_width'),'px');
 			$css.=hexwp_item_css($logo_css,'body .hw-nav-'.$element_key);
		}
	   /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Menu Padding
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
 
 			 
		
		if($element_id=='menu'){
  				$menu_css =hexwp_custom_var_unit('--hw-nav-menu-pd',hexwp_isset($opt,'menu_padding'),'px');
	 			$css.=hexwp_item_css($menu_css,'body .hw-nav-'.$element_key);
		}
 	
		if($element_id=='category_menu'){
 			$category_menu_css = 
 				hexwp_custom_var_unit('--hw-cat-menu-mg-tp',hexwp_isset($opt,'menu_margin_top')).
				hexwp_custom_var_unit('--hw-cat-menu-pd',hexwp_isset($opt,'menu_padding')).
 				hexwp_custom_var('--hw-cat-menu-bg',hexwp_isset($opt,'menu_background_color')).
				hexwp_custom_var('--hw-cat-menu-txt',hexwp_isset_2($opt,'menu_text_color','text')).
				hexwp_custom_var('--hw-cat-menu-hv-txt',hexwp_isset_2($opt,'menu_text_color','hover')).
			hexwp_custom_var('--hw-cat-menu-icn-cr',hexwp_isset_2($opt,'menu_icon_color','text')).
				hexwp_custom_var('--hw-cat-menu-icn-hv-cr',hexwp_isset_2($opt,'menu_icon_color','hover')).
					hexwp_custom_var('--hw-cat-menu-icn-sz',hexwp_isset($opt,'menu_icon_size')).
				hexwp_custom_var('--hw-cat-menu-br-cr',hexwp_isset($opt,'menu_border_color')).
				hexwp_custom_var('--hw-cat-menu-rd',hexwp_isset($opt,'menu_radius')).
				hexwp_custom_var_shadow_size('--hw-cat-menu-sd-sz',hexwp_isset_2($opt,'menu_shadow','size')).
				hexwp_custom_var('--hw-cat-menu-sd',hexwp_isset_2($opt,'menu_shadow','color')).
							hexwp_custom_var_unit('--hw-cat-menu-fn-sz',hexwp_isset($opt,'menu_font_size'),'px').
					hexwp_custom_var('--hw-cat-menu-fn-wt',hexwp_isset($opt,'menu_font_weight')).
					hexwp_custom_var('--hw-cat-menu-fn-tr',hexwp_isset($opt,'menu_text_transform')).
					hexwp_custom_var('--hw-cat-menu-icn-sz',hexwp_isset($opt,'menu_icon_size'));
			 	
				$css.=hexwp_item_css($category_menu_css,'body [class*="hw-nav"].hw-nav-'.$element_key);

		}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Searchform

 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/

		if($element_id=='search'){
			$search_css=
			hexwp_custom_var('--hw-srh-txt-bg',hexwp_isset_2($opt,'search_style','background')).
			hexwp_custom_var('--hw-srh-txt-txt',hexwp_isset_2($opt,'search_style','text')).
			hexwp_custom_var('--hw-srh-cat-bg',hexwp_isset_2($opt,'search_category_style','background')).
			hexwp_custom_var('--hw-srh-cat-txt',hexwp_isset_2($opt,'search_category_style','text')).
			hexwp_custom_var('--hw-srh-btn-bg',hexwp_isset_2($opt,'search_button_style','background')).
			hexwp_custom_var('--hw-srh-btn-txt',hexwp_isset_2($opt,'search_button_style','text')).
				hexwp_custom_var('--hw-srh-rd',hexwp_isset($opt,'search_radius'));
 
  			$css.=hexwp_item_css($search_css,'body .hw-nav-'.$element_key);
 	 
	  
		
}

		if($element_id=='social'){
  				$social_css =hexwp_custom_var_unit('--hw-scl-pd',hexwp_isset($opt,'social_padding'),'px').
					hexwp_custom_var('--hw-scl-sz',hexwp_isset($opt,'social_size')) ;				
	 			$css.=hexwp_item_css($social_css,'body .hw-nav-'.$element_key);
		}
		
		if($element_id=='text_html'){
				$text_html_css=hexwp_custom_var('--hw-nav-lk',hexwp_isset_2($opt,'text_color','text')).
   					hexwp_custom_var('--hw-nav-hv-lk',hexwp_isset_2($opt,'text_color','hover')).
					hexwp_custom_var_unit('--hw-nav-fn-sz',hexwp_isset($opt,'font_size'),'px').
					hexwp_custom_var('--hw-nav-fn-wt',hexwp_isset($opt,'font_weight')).
					hexwp_custom_var('--hw-nav-fn-tr',hexwp_isset($opt,'text_transform'));
	 			$css.=hexwp_item_css($text_html_css,'body .hw-nav-'.$element_key);
 				
		}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Socail
 
*///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*

 

			
			$element_style = hexwp_custom_var_gradient_background_color('--hw-nav-item-bg',hexwp_isset_2($opt,'background_color','background'),hexwp_isset_2($opt,'background_color','background_2'),'225deg').
					hexwp_custom_var('--hw-nav-lk',hexwp_isset_2($opt,'text_color','text')).
					hexwp_custom_var('--hw-nav-lk-sd',hexwp_hex2rgbacolor(hexwp_isset_2($opt,'text_color','text'),0.1)).
 					hexwp_custom_var('--hw-nav-hv-lk',hexwp_isset_2($opt,'text_color','hover')).
 					hexwp_custom_var('--hw-nav-hv-lk-sd',hexwp_hex2rgbacolor(hexwp_isset_2($opt,'text_color','hover'),0.2)).
				 	hexwp_custom_var('--hw-nav-item-rd',hexwp_isset($opt,'border_radius')).
					hexwp_custom_var_unit('--hw-nav-fn-sz',hexwp_isset($opt,'font_size'),'px').
					hexwp_custom_var('--hw-nav-fn-wt',hexwp_isset($opt,'font_weight')).
					hexwp_custom_var('--hw-nav-fn-tr',hexwp_isset($opt,'text_transform')).
					hexwp_custom_var('--hw-nav-icn-sz',hexwp_isset($opt,'icon_size')).
					hexwp_custom_var('--hw-nav-icn-cr',hexwp_isset_2($opt,'icon_color','text')).
					hexwp_custom_var('--hw-nav-icn-hv-cr',hexwp_isset_2($opt,'icon_color','hover')).
					hexwp_custom_var_gradient_background_color('--hw-nav-icn-bg',hexwp_isset_2($opt,'icon_background_color','background'),hexwp_isset_2($opt,'icon_background_color','background_2'),'225deg').
									 	hexwp_custom_var('--hw-nav-icn-rd',hexwp_isset($opt,'icon_radius'));
 			
 
	 			$css.=hexwp_item_css($element_style,'body [class*="hw-nav"].hw-nav-'.$element_key);
		 






/*
if(	hexwp_isset('social') !=='hide'){ 
$item_css.= hexwp_custom_var('--hw-nav-scl-sz',hexwp_isset('social_size'));
}
 */
 endforeach;
	}