<?php
  /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Image Start
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

  $image = hexwp_menu_data($item,'hexwp_menu_image' );
  $image_url = hexwp_menu_data($item,'hexwp_menu_image_url' );
 
 $full_width= ! empty(  hexwp_menu_data($item,'hexwp_menu_full_width' )) ?  'hw-menu-image-full-width' : '';
 $full_height = ! empty(  hexwp_menu_data($item,'hexwp_menu_full_height' )) ?  'hw-menu-image-full-height' : '';
 
//Main Class
 
		ob_start(); 
  
if(!empty($image) && $depth!==0){
	echo '<li class="hw-menu-image '.esc_attr($full_width).' '.esc_attr($full_height).'">';
 		echo '<a '.wp_kses_post($image_url).'>';
 	$src_image= wp_get_attachment_image_src($image,'full');
	if(!empty($src_image[0])){
 	echo '<img src="'.esc_url($src_image[0]).'" width="'.esc_attr($src_image[1]).'" height="'.esc_attr($src_image[2]).'">';
	}
 		echo '</a>';
 echo '</li>';
 } 
 $output .=  ob_get_clean();
