<?php
	
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	footer Css
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
 
$item_css.=
	hexwp_custom_var('--hw-fot-bg',hexwp_option('footer_background_color')).
	hexwp_custom_var('--hw-fot-tbox',hexwp_option_2('footer_link_color','link')).
	hexwp_custom_var('--hw-fot-lk',hexwp_hex2rgbacolor(hexwp_option_2('footer_link_color','link'),0.9)).
	hexwp_custom_var('--hw-fot-hv-lk',hexwp_option_2('footer_link_color','hover')).	
	hexwp_custom_var('--hw-fot-txt',hexwp_option('footer_text_color')).
	hexwp_custom_var('--hw-fot-meta',hexwp_hex2rgbacolor(hexwp_option('footer_text_color'),0.7)).
	hexwp_custom_var('--hw-fot-sd',hexwp_hex2rgbacolor(hexwp_option('footer_text_color'),0.15)).
	hexwp_custom_var('--hw-fot-hv-sd',hexwp_hex2rgbacolor(hexwp_option('footer_text_color'),0.25)).
	hexwp_custom_var('--hw-fot-br-cr',hexwp_option('footer_border_color')).
	hexwp_custom_var('--hw-fot-hl',hexwp_option('footer_highlight_color')).	
	hexwp_custom_var('--hw-fot-gry',hexwp_option('footer_grey_color')).	
	hexwp_custom_var('--hw-fot-br-cr',hexwp_option('footer_border'));	
 
	 
	
 	$item_css.= hexwp_custom_var_unit('--hw-fot-logo-wt',hexwp_option('footer_logo_width'),'px');
 	
	
	
 	if(hexwp_option('footer_logo_type')=='title' || hexwp_option('footer_logo_type')=='description'){
		
	
		$item_css.=
			hexwp_custom_var_unit('--hw-fot-logo-tl-fn-sz',hexwp_option('footer_logo_title_font_size'),'px').
			hexwp_custom_var('--hw-fot-logo-tl-cr',hexwp_option('footer_logo_title_color'));
		 
		if(hexwp_option('footer_logo_type')=='description'){
 		
			$item_css.=
		  		hexwp_custom_var_unit('--hw-fot-logo-des-fn-sz',hexwp_option('footer_logo_description_font_size'),'px').
 				hexwp_custom_var('--hw-fot-logo-des-cr',hexwp_option('footer_logo_description_color'));
 		
		}
  		
	}