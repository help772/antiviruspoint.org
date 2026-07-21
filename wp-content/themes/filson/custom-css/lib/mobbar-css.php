<?php
	
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Dropdown Css
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
$mobbar_option = !empty($header_builder['mobbar'])?hexwp_json_decode($header_builder['mobbar']):'';
   
$mobbar_css=
	hexwp_custom_var('--hw-mobbar-bg',hexwp_isset($mobbar_option,'background_color')).
	hexwp_custom_var('--hw-mobbar-lk',hexwp_isset_2($mobbar_option,'link_color','link')).
 	hexwp_custom_var('--hw-mobbar-hv-lk',hexwp_isset_2($mobbar_option,'link_color','hover')).	
	hexwp_custom_var('--hw-mobbar-txt',hexwp_isset($mobbar_option,'text_color')).
 	hexwp_custom_var('--hw-mobbar-br-cr',hexwp_isset($mobbar_option,'border_color')).
	hexwp_custom_var('--hw-mobbar-hl',hexwp_isset($mobbar_option,'highlight_color')).	
	hexwp_custom_var('--hw-mobbar-gry',hexwp_isset($mobbar_option,'grey_color')).	
  	hexwp_custom_var_unit('--hw-mobbar-fn-sz',hexwp_isset($mobbar_option,'font_size'),'px').
	hexwp_custom_var('--hw-mobbar-fn-wt',hexwp_isset($mobbar_option,'font_weight')).
	hexwp_custom_var('--hw-mobbar-fn-tr',hexwp_isset($mobbar_option,'text_transform'));
 	 			$css.=hexwp_item_css($mobbar_css,'body .hw-mobbar');
