<?php
  /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Menu Item Start
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 	
	$slug=hexwp_slug();
 

$classes = empty( $item->classes ) ? array() : (array) $item->classes;
$li_classes=!empty($classes[0])?$classes[0]:null;
$args = apply_filters( 'nav_menu_item_args', $args, $item, $depth );
  
 
$current='current';
$li_classes.= !empty($item->$current)  ?' hw-menu-current':'';
		 
 
$menu_icon_size=$slug.'_menu_icon_size';	
$icon_size = ! empty( $item->$menu_icon_size ) ? $item->$menu_icon_size : ''; 
		 


 
$bg_args=array();

$menu_bg_image=$slug.'_menu_background_image';	
 $bg_args['image']= ! empty( $item->$menu_bg_image ) ? $item->$menu_bg_image : '';	

$menu_bg_size=$slug.'_menu_background_size';	
$bg_args['size'] = isset( $item->$menu_bg_size ) ? $item->$menu_bg_size : '';	
 
 $menu_bg_position=$slug.'_menu_background_position';	
$bg_args['position'] =  isset( $item->$menu_bg_position ) ? $item->$menu_bg_position : '';	
 

$menu_bg_opacity=$slug.'_menu_background_opacity';	
$bg_args['opacity'] = isset( $item->$menu_bg_opacity ) ? $item->$menu_bg_opacity : '';	
 
  
  
 

$item_css=hexwp_nav_menu_icon_size('--hw-nav-icn-sz',$icon_size);
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
		
		 
		 
		$menu_featured=$slug.'_menu_featured';	
		$featured = ! empty( $item->$menu_featured ) ? $item->$menu_featured : '';
		
		$menu_icon=$slug.'_menu_icon';	
		$icon = ! empty( $item->$menu_icon ) ?  $item->$menu_icon : '';
 
		 

		  
	 

 		
		
		ob_start(); 

 			
 
		//**********************************************************************************************
		/*************************************** START A Herf *************************************************
		**********************************************************************************************************/
		$a_class=!empty($icon)?' class="'.$icon.'"' :'';
		
		echo '<a '.$attributes.' '.$a_class.' >';
	 
		echo  wp_kses_post($title);
		
	 	
 	 
		
		 hexwp_nav_menu_featured($featured);

		
		echo '</a>';
	if(!empty($args->walker->has_children)){
			echo '<i></i>';
		}
 	 	 hexwp_icon_fontfamily($icon);



$output .= apply_filters( 'walker_nav_menu_start_el', ob_get_clean(), $item, $depth, $args );
 