<?php
	
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Dropdown Css
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
$dropdown_option = !empty($header_builder['dropdown'])?hexwp_json_decode($header_builder['dropdown']):'';
   
$dropdown_css=
	hexwp_custom_var('--hw-drp-bx-bg',hexwp_isset($dropdown_option,'background_color')).
	hexwp_custom_var('--hw-drp-tbox',hexwp_isset_2($dropdown_option,'link_color','link')).
	hexwp_custom_var('--hw-drp-lk',hexwp_hex2rgbacolor(hexwp_isset_2($dropdown_option,'link_color','link'),0.9)).
	hexwp_custom_var('--hw-drp-hv-lk',hexwp_isset_2($dropdown_option,'link_color','hover')).	
	hexwp_custom_var('--hw-drp-txt',hexwp_isset($dropdown_option,'text_color')).
	hexwp_custom_var('--hw-drp-meta',hexwp_hex2rgbacolor(hexwp_isset($dropdown_option,'text_color'),0.7)).
	hexwp_custom_var('--hw-drp-sd',hexwp_hex2rgbacolor(hexwp_isset($dropdown_option,'text_color'),0.15)).
	hexwp_custom_var('--hw-drp-hv-sd',hexwp_hex2rgbacolor(hexwp_isset($dropdown_option,'text_color'),0.25)).
	hexwp_custom_var('--hw-drp-br-cr',hexwp_isset($dropdown_option,'border_color')).
	hexwp_custom_var('--hw-drp-hl',hexwp_isset($dropdown_option,'highlight_color')).	
	hexwp_custom_var('--hw-drp-gry',hexwp_isset($dropdown_option,'grey_color')).	
	hexwp_custom_var_shadow_mini('--hw-drp-bx-sd',hexwp_isset($dropdown_option,'shadow')).
	hexwp_custom_var_border_mini('--hw-drp-bx',hexwp_isset($dropdown_option,'border')).
	hexwp_custom_var_radius_mini('--hw-drp-rd',hexwp_isset($dropdown_option,'radius')).
 	hexwp_custom_var_unit('--hw-drp-fn-sz',hexwp_isset($dropdown_option,'font_size'),'px').
	hexwp_custom_var('--hw-drp-fn-wt',hexwp_isset($dropdown_option,'font_weight')).
	hexwp_custom_var('--hw-drp-fn-tr',hexwp_isset($dropdown_option,'text_transform'));
	$css.=hexwp_item_css($dropdown_css,'body header.hw-bar');

	
 