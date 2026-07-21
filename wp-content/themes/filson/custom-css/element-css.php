<?php
  /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Font Family
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_element_padding( $option=false,$item = false) {
	$css='';
	 
	  
	$css.= hexwp_var_padding('--hw-el-pd',$option,'padding');
 		
	$css.= hexwp_var_padding('--hw-el-tab-pd',$option,'tablet_padding');
	$css.= hexwp_var_padding('--hw-el-mob-pd',$option,'mobile_padding');
 		
	 
 	return $css;
	
}

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Box Css
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Title Box css
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
function hexwp_title_box_css( $option,$items = false) {
   	$css=
		hexwp_var_2('--hw-tbox-main-bg',$option,'title_box_main_color','background'). 
		hexwp_var_2('--hw-tbox-main-txt',$option,'title_box_main_color','text').
		hexwp_var_font_typo('--hw-tbox-main',$option,'title_box_main_typo').   
		hexwp_var_2('--hw-tbox-tab-bg',$option,'title_box_tab_color','background').
		hexwp_var_2('--hw-tbox-tab-txt',$option,'title_box_tab_color','text').
		hexwp_var_font_typo('--hw-tbox-tab',$option,'title_box_tab_typo').   	
		hexwp_var_2('--hw-tbox-atv-bg',$option,'title_box_active_color','background').
		hexwp_var_2('--hw-tbox-atv-txt',$option,'title_box_active_color','text').
		hexwp_var('--hw-tbox-br-cr',$option,'title_box_border_color').  
		hexwp_var('--hw-tbox-rd',$option,'title_box_radius');
  	return $css;
} 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Post Title css
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
function hexwp_post_css( $option,$item = false) {
  	 
 
 	$css=
		hexwp_var('--hw-post-bg',$option,'background_color').
		hexwp_var_2('--hw-post-tl-lk',$option,'title_color','link').
		hexwp_var_2('--hw-post-tl-hv-lk',$option,'title_color','hover').
		hexwp_var_font_typo('--hw-post-tl',$option,'post_title_typo').   			
		hexwp_var('--hw-expt-txt',$option,'excerpt_color'). 	
		hexwp_var_font_typo('--hw-expt',$option,'excerpt_typo').   	
		hexwp_var('--hw-meta-txt',$option,'meta_color').
		hexwp_var_font_typo('--hw-meta',$option,'meta_typo').   	
		hexwp_var('--hw-main-rd',$option,'radius'). 
		hexwp_var('--hw-main-br-cr',$option,'border_color').
		hexwp_var('--hw-post-sd',$option,'box_border_color').
		hexwp_var_dark('--hw-post-hv-sd',$option,'box_border_color');
		
	 
	 
 		
	return $css;
  		
}
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Post Title css
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
function hexwp_product_css( $option,$item = false) {
  	 
 
 	$css=
		hexwp_var_font_typo('--hw-product-tl',$option,'post_title_typo').   			
  		hexwp_var_2('--hw-price-ma',$option,'price_color','main').
 		hexwp_var_2('--hw-price-sa',$option,'price_color','sale').
 		hexwp_var_2('--hw-price-re',$option,'price_color','regular').
		hexwp_var_font_typo('--hw-price',$option,'price_typo').   			
   		hexwp_var_2('--hw-rat-rat-cr',$option,'rating_color','rating').
 		hexwp_var_2('--hw-rat-no-cr',$option,'rating_color','none').
		hexwp_var_2('--hw-cd-bg',$option,'countdown_color','background').
		hexwp_var_2('--hw-cd-txt',$option,'countdown_color','text').
 		hexwp_var_2('--hw-cd-num',$option,'countdown_color','number');		
  	return $css;
  		
}
 
 
 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	details mode 3 css
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_caption_css( $option,$item = false) {
	$css='';
	if(!empty($option['caption_background_color'])){
		$css.=
			'--hw-cap-bg:'.$option['caption_background_color'].';';
		 
	}
	
	if(!empty($option['caption_color'])){
		$css.=
			'--hw-cap-txt:'.$option['caption_color'].';'.
			'--hw-cap-expt-txt:'.hexwp_hex2rgbacolor($option['caption_color'],0.8).';'.
			'--hw-cap-meta-txt:'.hexwp_hex2rgbacolor($option['caption_color'],0.6).';'.
			'--hw-cap-br-cr:'.hexwp_hex2rgbacolor($option['caption_color'],0.25).';';
	} 

 	return $css;

} 
 
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																Arrow Layout Css
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 
function hexwp_arrow_layout_css( $option=false) {
  	$arrow_location = !empty($option['arrow_layout']['location'])?  $option['arrow_layout']['location'] :'';
  	$arrow_size = !empty($option['arrow_layout']['size'])?  $option['arrow_layout']['size'] :'';
  	$arrow_layout = !empty($option['arrow_layout']['layout'])?  $option['arrow_layout']['layout'] :'';
	$css ='';
	if($arrow_size == 'small'){
		$size='30px';
		$css.='--hw-aw-sz:30px;';	
	}elseif($arrow_size == 'large'){
		$css.='--hw-aw-sz:60px;';
		$size='60px';
 	}else{
		$size='45px';
	}
	
 	if($arrow_location == 'slider-2'){
		$css.='--hw-aw-lc:20px;';	
	}elseif($arrow_location == 'slider-1'){
		$css.='--hw-aw-lc:0px;';
	}
	
	if($arrow_layout == 'fixed'){
		$css.='--hw-aw-ly:1;';	
	} 
	if(!empty($option['arrow_radius'])){
		$css.='--hw-aw-rd:'.$option['arrow_radius'].'px;';
 	}
	$css.=hexwp_var_2('--hw-aw-txt',$option,'arrow_color','text');
	$css.=hexwp_var_2('--hw-aw-bg',$option,'arrow_color','background');
 	 
	return $css;
   
}
 
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																Elementor Typo
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 function hexwp_elmentor_typo_css( $option,$id) {
	$array=array();
	if(!empty($option[$id.'_font_size'])){
	$array['font_size']=$option[$id.'_font_size'];
  	}
	
	if(!empty($option[$id.'_font_weight'])){
	$array['font_weight']=$option[$id.'_font_weight'];
  	}
	
	if(!empty($option[$id.'_font_style'])){
	$array['font_style']=$option[$id.'_font_style'];
  	}	
 
 	if(!empty($option[$id.'_text_transform'])){
	$array['text_transform']=$option[$id.'_text_transform'];
  	}		 
 	return $array;
}
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																Elementor Padding
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 function hexwp_elmentor_padding( $option,$default=false,$responsive=false) {
 	
	if(!empty($responsive)){
	$padding= !empty($option['elementor_padding_'.$responsive])?$option['elementor_padding_'.$responsive]:$default;
	}else{
	$padding= !empty($option['elementor_padding'])?$option['elementor_padding']:$default;
	}
	$ver='';
	$hor='';
	if($padding=='1px'){				$ver='1';		$hor='1';
 	}elseif($padding=='10px'){			$ver='10';		$hor='10';
	}elseif($padding=='10px 15px'){		$ver='10';		$hor='15';
	}elseif($padding=='10px 20px'){		$ver='15';		$hor='20';
	}elseif($padding=='15px'){			$ver='15';		$hor='15';
	}elseif($padding=='15px 10px'){		$ver='15';		$hor='10';
	}elseif($padding=='15px 20px'){		$ver='15';		$hor='20';
	}elseif($padding=='15px 30px'){		$ver='15';		$hor='30';
	}elseif($padding=='20px'){			$ver='20';		$hor='20';
 	}elseif($padding=='20px 10px'){		$ver='20';		$hor='10';
	}elseif($padding=='20px 15px'){		$ver='20';		$hor='15';
 	}elseif($padding=='20px 30px'){		$ver='20';		$hor='30';
	}elseif($padding=='20px 40px'){		$ver='20';		$hor='40';
	}elseif($padding=='30px'){			$ver='30';		$hor='30';
	}elseif($padding=='30px 10px'){		$ver='30';		$hor='15';
	}elseif($padding=='30px 15px'){		$ver='30';		$hor='20';
	}elseif($padding=='30px 20px'){		$ver='30';		$hor='20';
	}elseif($padding=='30px 40px'){		$ver='30';		$hor='40';
	}elseif($padding=='40px'){			$ver='40';		$hor='40';
	}elseif($padding=='40px 20px'){		$ver='40';		$hor='20';
	}elseif($padding=='50px'){			$ver='50';		$hor='50';
	}elseif($padding=='50px 25px'){		$ver='50';		$hor='25';
	}
	if(!empty($padding)){
   	return array('top'=>$ver,'bottom'=>$ver,'left'=>$hor,'right'=>$hor);
	}
}
 