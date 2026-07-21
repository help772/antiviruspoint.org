<?php
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																	 Header  Options
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	
	$header_option = !empty($header_builder['header'])?hexwp_json_decode($header_builder['header']):'';
	
  $item_css.=hexwp_custom_var('--hw-header-wt',hexwp_isset($header_option,'header_width')); 
  $item_css.=hexwp_custom_var_shadow_mini('--hw-header-sd',hexwp_isset($header_option,'header_shadow')); 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																	 Header 
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	
 	
	
	
 
	 
 	$overlab_height=0;
 	$mobile_overlab_height=0;
	$height=0;
 	if(!empty($header_builder['navbar'])){
	foreach(hexwp_header_decode($header_builder['navbar']) as $navbar_key => $navbar_value):
		if(hexwp_has_toolbar($navbar_key,$header_builder)){

		$option = hexwp_json_decode(hexwp_isset($navbar_value,'option'));
 		$height= !empty($option['height'])?$option['height']:'';
		
 
 		
		$css.='body .hw-toolbar-'.$navbar_key.'{';
				
				
	 
				
			$css.= hexwp_custom_var_unit('--hw-nav-ht', hexwp_isset($option,'height'),'px').
			 		hexwp_custom_var_unit('--hw-nav-md-ht', hexwp_isset($option,'height_element'),'px').
					hexwp_custom_var_unit('--hw-sticky-ht', hexwp_isset($option,'sticky_height'),'px').
					hexwp_custom_var_unit('--hw-sticky-md-ht', hexwp_isset($option,'sticky_height'),'px');
					 
	 
						
			$height=intval(hexwp_isset($option,'height'));
			
			
			if($navbar_key=='top'){
				$height=!empty($height)?$height:'50';
			}
			if($navbar_key=='middle'){
				$height=!empty($height)?$height:'100';
			}
			if($navbar_key=='bottom'){
				$height=!empty($height)?$height:'60';
			}
			
			if($navbar_key=='mobile_top' || $navbar_key=='mobile_middle'||$navbar_key=='mobile_bottom'){
				$height=!empty($height)?$height:'60';
			}
			
			 
 					
					
					
			$css.= hexwp_custom_var_gradient_background_color('--hw-nav-bg',hexwp_isset_2($option,'background_color','background'),hexwp_isset_2($option,'background_color','background_2')).
					hexwp_custom_var('--hw-nav-lk',hexwp_isset_2($option,'text_color','text')).
					hexwp_custom_var('--hw-nav-lk-sd',hexwp_hex2rgbacolor(hexwp_isset_2($option,'text_color','text'),0.1)).
					hexwp_custom_var('--hw-nav-lk-icn',hexwp_hex2rgbacolor(hexwp_isset_2($option,'text_color','text'),0.5)).
 					hexwp_custom_var('--hw-nav-hv-lk',hexwp_isset_2($option,'text_color','hover')).
 			 		hexwp_custom_var_border_shadow_mini('--hw-nav',hexwp_isset($option,'border'));
			
				 if(!empty($option['overlap'])  && hexwp_header_overlap()=='enable'){		
					$css.= 	hexwp_custom_var_gradient_background_color('--hw-over-bg',hexwp_isset_2($option,'overlap_background_color','background'),hexwp_isset_2($option,'overlap_background_color','background_2')).
							hexwp_custom_var('--hw-over-lk',hexwp_isset_2($option,'overlap_text_color','text')).
							hexwp_custom_var('--hw-over-hv-lk',hexwp_isset_2($option,'overlap_text_color','hover')).
			 				hexwp_custom_var_border_mini('--hw-over',hexwp_isset($option,'overlap_border'));
					if( $navbar_key=='top'|| $navbar_key=='middle'|| $navbar_key=='bottom'){
						$overlab_height= $height+$overlab_height;
					}
					if($navbar_key=='mobile_top' || $navbar_key=='mobile_middle'||$navbar_key=='mobile_bottom'){
						$mobile_overlab_height= $height+$mobile_overlab_height;
					}
				}
				
				
			$css.=hexwp_custom_var_unit('--hw-nav-fn-sz',hexwp_isset($option,'font_size'),'px').
				hexwp_custom_var('--hw-nav-fn-wt',hexwp_isset($option,'font_weight')).
				hexwp_custom_var('--hw-nav-fn-tr',hexwp_isset($option,'text_transform')).
 				hexwp_custom_var('--hw-nav-icn-sz',hexwp_isset($option,'icon_size'));
				
		$css.='}';
 	 
	 }
  		
	endforeach;
	}
	$item_css.=hexwp_custom_var_unit('--hw-overlab-ht',$overlab_height,'px');  
			$item_css.=hexwp_custom_var_unit('--hw-mobile-overlab-ht',$mobile_overlab_height,'px');  