<?php
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Custom Var
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_custom_var( $name=false,$option=false) {
	$css=""; 
   if(!empty($option)){
 		$css.= $name.':'.$option.';';
	 }
 	return $css;	
}
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Custom Var Unit
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 function hexwp_custom_var_unit($name=false,$option=false,$amount='px'  ) {
	$css=""; 
  	if(!empty($option)){
		$css.=  $name.':'.$option.$amount.';';
	}
 	return $css;	
 
}
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Custom Var 2 Unit
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 function hexwp_custom_var_2_unit($name=false,$option=false,$id=false,$amount='px'  ) {
	$css=""; 
  	if(!empty($option[$id])){
		$css.=  $name.':'.$option[$id].$amount.';';
	}
 	return $css;	
 
}
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Custom Var Background
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_custom_var_background() {
	global $smof_data,$post;
 	if ( is_category() ) {
		$meta = get_term_meta(get_query_var( 'cat'  ));
  	}
 	elseif ( (  is_page() || is_single())) {
		$meta = get_post_meta( $post->ID );
 	}  
	$css='';
	
	if ( !empty($meta['body_background_type'][0])){
		
		$body_background_type = $meta['body_background_type'][0]; 
		$body_background_pattern = !empty( $meta['body_background_pattern'][0]) ?  $meta['body_background_pattern'][0] : '';	
		$body_background_image = !empty( $meta['body_background_image'][0]) ?  $meta['body_background_image'][0] : '';	

	} else{
		$body_background_type = !empty($smof_data['body_background_type']) ? $smof_data['body_background_type']: hexwp_option_default('body_background_type');  
  		$body_background_pattern= !empty($smof_data['body_background_pattern']) ? $smof_data['body_background_pattern']: hexwp_option_default('body_background_pattern');	
  		$body_background_image= !empty($smof_data['body_background_image']) ? $smof_data['body_background_image']: hexwp_option_default('body_background_image');	
  	}	
	
	
  	  if($body_background_type=='custom'){
		$css.= '--hw-body-bg-img:url('.$body_background_image.');';
		$css.= '--hw-body-bg-rp:no-repeat;';
		$css.= '--hw-body-bg-sz:cover;';
 	  }elseif($body_background_type=='pattern'){
 
 
		$css.= '--hw-body-bg-img:url('.esc_url(hexwp_DIR.'/images/bg/'.$body_background_pattern.'.png').');';
		$css.= '--hw-body-bg-rp:repeat;';
		$css.= '--hw-body-bg-sz:auto;';
 
	}
  
	
	return $css;
	 
}

 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Custom Var Shadow
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Custom Var Shadow
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_custom_var_shadow($name=false,$shadow =false) {
		$css="";
	$shadow_option_size  = hexwp_option_2($shadow,'size');
 
 	$shadow_option_color  = hexwp_option_2($shadow,'color');
		if(!empty($shadow_option_size)){
  $size = str_replace("-", " ", $shadow_option_size);
   			$shadow_color = !empty($shadow_option_color) ? ' '.$shadow_option_color.' ' : '';		
 			$css.=  $name.'-sd: 0 0 '.$size.$shadow_color.' ;';
		}
	return $css;	
		
}

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Shdaow
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 function hexwp_custom_var_shadow_mini($name=false, $option=false) {
		$css="";

		if( isset($option['blur']) || isset($option['spread'])){
			$shadow_blur = intval(isset($option['blur'])) ?  $option['blur'].'px '  : '0 ';
			$shadow_spread = intval(isset($option['spread'])) ?  $option['spread'].'px '  : '0 ';		
			$shadow_color = !empty($option['color']) ? $option['color'].' ' : '';		
		
			$css.= $name.': 0 0 '.$shadow_blur.$shadow_spread.$shadow_color.';';
		}
	return $css;	
 
}
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Custom Var Border
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_custom_var_border( $name=false,$border =false) {
	$css="";
 
	$border_option_size  = hexwp_option_2($border,'size');
	$border_option_color  = hexwp_option_2($border,'color');
		if(!empty($border_option_size)){
 			$size = intval($border_option_size);
			$position = preg_replace('/\d+/', '',$size  );
			
 				if($position == 'px-top'){
					$border_width = $name.'-br-wt:'.$size.'px 0 0 0;';
				}
				elseif($position == 'px-bottom'){
					$border_width = $name.'-br-wt:0 0 '.$size.'px 0;';
				}	
				elseif($position == 'px-left'){
					if(is_rtl()){
					$border_width =   $name.'-br-wt:0 '.$size.' 0 0px ;' ;
					}else{
					$border_width =   $name.'-br-wt:0 0 0 '.$size.'px ;' ;
					}
				}
				elseif($position == 'px-right'){
					if(is_rtl()){
					$border_width =   $name.'-br-wt:0 0 0 '.$size.'px ;' ;
 					}else{
					$border_width =   $name.'-br-wt:0 '.$size.' 0 0px ;' ;
 					}
				}	
				elseif($position == 'px-top-bottom'){
					$border_width = $name.'-br-wt: '.$size.'px 0  '.$size.'px 0;';		
				}else{ 
					$border_width =  $name.'-br-wt:'.$size.'px;'; 	
				}
 			$border_color =  !empty($border_option_color) ? $name.'-br-cr:'.$border_option_color.';' : '';	
			
	 
 			$css.= $border_width.$border_color;
 		} 
		 
	
	return $css;	
}
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Custom Var Border
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_custom_var_border_mini( $name=false,$option =false) {
  
	$css="";
 

		if(!empty($option['size'])){
			 
 			$position = !empty($option['position'])?$option['position']:'round';
 				if($position == 'top'){
					$border_width = intval(isset($option['size'])) ?$name.'-br-wt:'.$option['size'].'px 0 0;' : '';		
				}
				elseif($position == 'bottom'){
					$border_width = intval(isset($option['size'])) ? $name.'-br-wt: 0 0  '.$option['size'].'px ;' : '';		
				}	
				elseif($position == 'left'){
  					$border_width = intval(isset($option['size'])) ?$name.'-br-wt: 0 '.$option['size'].'px 0 0;' : '';	

 				}
				elseif($position == 'right'){
  					$border_width = intval(isset($option['size'])) ? $name.'-br-wt: 0 0 0 '.$option['size'].'px;' : '';	
 				}	
				elseif($position == 'top-bottom'){
					$border_width = intval(isset($option['size'])) ? $name.'-br-wt:'.$option['size'].'px 0 ;' : '';		
				}else{
					$border_width = intval(isset($option['size'])) ? $name.'-br-wt:'.$option['size'].'px;' : '';		
				}
				
 				$border_color =  isset($option['color']) ? $name.'-br-cr:'.$option['color'].';' : '';	
			
	 
 			$css.= ''.$border_width.$border_color.';';
 		} 
		 
	
	return $css;	
}

 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Custom Var Border
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_custom_var_border_shadow_mini( $name=false,$option =false) {
  
	$css="";
 

		if(isset($option['size'])){
			 	if($option['size'] !==''){
 				$position = !empty($option['position'])?$option['position']:'top';
 	 
				$shadow_color =  isset($option['color']) ?  $option['color'] : '';	
				$shadow_size =  intval(isset($option['size'])) ?  $option['size'] : '';
    			if($position == 'top'){
					$css =  $name.'-sd:0 '.$shadow_size.'px 0 0 '.$shadow_color.' inset;';		
				}
				elseif($position == 'bottom'){
					$css =  $name.'-sd:0 '.$shadow_size.'px 0 0 '.$shadow_color.' inset;';		
				 
				}elseif($position == 'top-bottom'){
					$css =  $name.'-sd:0 '.$shadow_size.'px 0 0 '.$shadow_color.' inset,0 -'.$shadow_size.'px 0 0 '.$shadow_color.' inset;';		
				} 
 			}
	 
 			  
 		} 
		 
	
	return $css;	
}
 function hexwp_custom_var_radius_mini( $name=false,$option=false ) {
		$css="";
 		if(!empty($option['size'])){
				$radius_unit = !empty($option['unit']) ?$option['unit'] : 'px';
   				$radius_size = intval(isset($option['size'])) ? $option['size'].$radius_unit.' ' : '0 ';
			
				$position =!empty($option['position'])? $option['position']:'round';
 				 
				if($position == 'top'){
					$raduis =  $radius_size.$radius_size .' 0px 0px ';		
				
				}elseif($position == 'bottom'){
					$raduis =  '0px 0px '.$radius_size.$radius_size .' ';
				 
				}elseif($position == 'top-left-bottom-right'){
					if(is_rtl()){
					$raduis =  '  0px '.$radius_size.' 0px '.$radius_size.'  ';
 					}else{
						$raduis = $radius_size.' 0px '.$radius_size.' 0px ';
 					}
					
 				}	
				elseif($position == 'top-right-bottom-left'){
					if(is_rtl()){
						$raduis = $radius_size.' 0px '.$radius_size.' 0px ';
 					}else{
						$raduis =  '  0px '.$radius_size.' 0px '.$radius_size.'  ';
 					}
 				}else{
					$raduis = $radius_size;		
				} 
	  
			$css.=$name.':'.$raduis.';';
		}
	return $css;	
}

 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Custom Var Meta
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_custom_var_meta( $name='blog') {
	$item_css='';
	if(hexwp_option_2($name.'_meta_layout','location')=='title-top'){
		$item_css.= hexwp_custom_var('--hw-meta-loc','0 0 var(--hw-mg-tp) 0');
	}
	
	if(hexwp_option_2($name.'_meta_layout','between')=='between-1'){
		$item_css.= hexwp_custom_var('--hw-meta-bet','" "');
		
	}elseif(hexwp_option_2($name.'_meta_layout','between')=='between-2'){
		$item_css.= hexwp_custom_var('--hw-meta-bet','"-"');
	
	}elseif(hexwp_option_2($name.'_meta_layout','between')=='between-3'){
		$item_css.= hexwp_custom_var('--hw-meta-bet','"|"');
	}elseif(hexwp_option_2($name.'_meta_layout','between')=='between-4'){
		$item_css.= hexwp_custom_var('--hw-meta-bet','"/"');
	}
	
	if(hexwp_option_2($name.'_meta_layout','layout')=='layout-1' || hexwp_option_2($name.'_meta_layout','layout')=='layout-2'){
		$item_css.= hexwp_custom_var('--hw-meta-dis','none');
	 } 
	if( hexwp_option_2($name.'_meta_layout','layout')=='layout-2' || hexwp_option_2($name.'_meta_layout','layout')=='layout-5'){
		$item_css.= hexwp_custom_var('--hw-meta-ht','2em');
	 }
	if( hexwp_option_2($name.'_meta_layout','layout')=='layout-4' ||  hexwp_option_2($name.'_meta_layout','layout')=='layout-5'){
		$item_css.= hexwp_custom_var('--hw-meta-aut','none');
	 }
	 return $item_css;
 
}
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Custom Var Font Typo
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function hexwp_custom_var_font_typo( $name=false,$option=false) {
	$item_css =
 		hexwp_custom_var_unit($name.'-fn-sz',hexwp_option_2($option,'font_size'),'px').   	
		hexwp_custom_var($name.'-fn-wt',hexwp_option_2($option,'font_weight')).   	
		hexwp_custom_var($name.'-fn-tr',hexwp_option_2($option,'text_transform')). 	
 		hexwp_custom_var($name.'-icn-sz',hexwp_option_2($option,'icon_size'));   	
	
	 return $item_css;

}
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Custom Var Font Typo Mini
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_custom_var_font_typo_mini( $name=false,$option=false) {
	$item_css =
 		hexwp_custom_var($name.'-fn-wt',hexwp_option_2($option,'font_weight')).   	
		hexwp_custom_var($name.'-fn-tr',hexwp_option_2($option,'text_transform')); 	
	
	 return $item_css;

}

function hexwp_custom_var_column( $name=false,$column =false) {
	$css='';
	if($column =='1_3'){
		$css.= $name.':33.331%;';
	}elseif($column =='1_4'){
		$css.= $name.':25%;';
	}elseif($column =='1_5'){
		$css.= $name.':20%;';
	}elseif($column =='2_5'){
		$css.= $name.':40%;';
	}elseif($column =='1_6'){
		$css.= $name.':16.661%;';
	}elseif($column =='2_7'){
		$css.= $name.':28.57%';
	}if($column =='3_7'){
		$css.= $name.':42.85%';
	}
	return $css;
	
}

function hexwp_custom_var_gradient_background_color( $name=false,$option=false,$option_2=false,$deg='to Right') {
	$css='';
  		if(!empty($option)){
 		 
  			$css = $name.': '.esc_html($option).';';
   				if(!empty($option_2)){
 				$css =  $name.': linear-gradient('.$deg.', '.$option.' 0%,'.$option_2.' 100%) ;';
 			} 
	}
		
	return $css;
 
}
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Custom Var Shadow
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_custom_var_shadow_size($name=false,$option =false) {
		$css="";
	  	if(!empty($option)){
  $size = str_replace("-", " ", $option);
 			$css.=  $name.':'.$size.' ;';
		}
	return $css;	
		
} 
function hexwp_custom_var_font_typo_size( $name=false,$option=false) {
	$item_css =
 		hexwp_custom_var_unit($name.'-fn-sz',hexwp_option_2($option,'font_size'),'px').   	
		hexwp_custom_var($name.'-li-ht',hexwp_option_2($option,'line_height')) ;   	
	
	 return $item_css;

}