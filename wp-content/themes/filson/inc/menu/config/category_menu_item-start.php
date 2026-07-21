<?php
  /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Menu Item Start
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 	
	
 

$classes = empty( $item->classes ) ? array() : (array) $item->classes;
$li_classes=!empty($classes[0])?$classes[0]:null;
$args = apply_filters( 'nav_menu_item_args', $args, $item, $depth );

$fallback= $args->fallback_cb;
 
$current='current';
$li_classes.= !empty($item->$current)  ?' hw-menu-current':'';

if($depth == 0){
$li_classes.= !empty($fallback['class_inner'])?$fallback['class_inner']:'';
}
$menu_location = !empty($fallback['menu_location'])?$fallback['menu_location']:'';
$layout = !empty($fallback['layout'])?$fallback['layout']:'';
  

 $icon_type =hexwp_menu_data($item,'hexwp_menu_icon_type'); 		 
 $image =hexwp_menu_data($item,'hexwp_menu_image'); 		 
 $icon_size =hexwp_menu_data($item,'hexwp_menu_icon_size'); 		 


  $sub_width =hexwp_menu_data($item,'hexwp_menu_sub_width'); 		 
  $li_classes.= $sub_width =='full-width' && $depth == 0 ? ' hw-menu-full-width':'';

		
		
		 
 
$bg_args=array();

$menu_bg_image=$slug.'_menu_background_image';	

 
 $bg_args['image']= hexwp_menu_data($item,'hexwp_menu_background_image'); 	

 $bg_args['size'] = hexwp_menu_data($item,'hexwp_menu_background_size'); 	
 
 $bg_args['position'] = hexwp_menu_data($item,'hexwp_menu_background_position'); 
 

 $bg_args['opacity'] = hexwp_menu_data($item,'hexwp_menu_background_opacity'); 
 
  
  
 

 $item_css= hexwp_nav_menu_background($bg_args);
$item_css.= hexwp_nav_menu_width($sub_width);
if($depth  == 0){
$item_css.=hexwp_nav_menu_icon_size('--hw-nav-icn-sz',$icon_size);
}

 $inline_style = !empty($item_css)?' style="'.$item_css.'"':''; 
$class =!empty($li_classes)?' class="'.esc_attr($li_classes).'"':'';
 //**********************************************************************************************
/*************************************** START List *************************************************
**********************************************************************************************************/
 
 
$output .='<li  '.$class.'  '.$inline_style.'>';

 


		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );
		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}
		 
		$title = apply_filters( 'the_title', $item->title, $item->ID );
		$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );
		
		 
		 
 		$featured =hexwp_menu_data($item,'hexwp_menu_featured'); 
		
  		$icon = $icon_type !=='image' ? hexwp_menu_data($item,'hexwp_menu_icon'):'';
 
		 

		  
	 

 		
		
		ob_start(); 

  
		//**********************************************************************************************
		/*************************************** START A Herf *************************************************
		**********************************************************************************************************/
 		$a_item_css='';
		if($depth !== 0 ){
 			$a_item_css.=hexwp_nav_menu_icon_size('--hw-drp-icn-sz',$icon_size);
		}
		
		
		$link_class='';
  		 
		if($icon_type =='image'){
			$link_class.=!empty($image)?'  hw-menu-icon-image ' :'';
			$a_item_css.=  hexwp_nav_menu_image($image);
		}else{
			$link_class.=!empty($icon)?$icon.' hw-menu-icon-icon' :'';
 			
		}
  		
 		$a_item_css_inline=!empty($a_item_css)?'style="'.$a_item_css.'"':'';
 	 
		$a_class =!empty($link_class)?' class="'.esc_attr($link_class).'"':'';
	 
	 
  		echo '<a '.$attributes.' '.$a_class.' '.$a_item_css_inline.' >';
			if(  (!empty($icon) || !empty($image))) {echo  '<span>';} 
			echo wp_kses_post($title);
			 
			
			if( (!empty($icon) || !empty($image))){ echo '</span>';}
		 
  
		
		 hexwp_nav_menu_featured($featured);
 				if(!empty($args->walker->has_children)){
					echo '<i></i>';
 				}
			
					
		 
			
		echo '</a>';

 	 	 hexwp_icon_fontfamily($icon);



$output .= apply_filters( 'walker_nav_menu_start_el', ob_get_clean(), $item, $depth, $args );
 