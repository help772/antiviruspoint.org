<?php


/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Image Text Start
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$image =hexwp_menu_data($item,'hexwp_menu_image');
$image_url = hexwp_menu_data($item,'hexwp_menu_image_url');
$alignment = hexwp_menu_data($item,'hexwp_menu_alignment','center');
$title = apply_filters( 'the_title', $item->title, $item->ID );
 				
//Main Class

 
   
		ob_start(); 
  
if(!empty($image) && $depth!==0){
	echo '<li class="hw-menu-image-text hw-menu-image-'.esc_attr($alignment ).'  ">';
 	if(!empty($image_url) ){ 
		echo '<a href="'.esc_attr($image_url).'">';
	}
	$src_image= wp_get_attachment_image_src($image,'full');
	if(!empty($src_image[0])){
 	echo '<img src="'.esc_url($src_image[0]).'" width="'.esc_attr($src_image[1]).'" height="'.esc_attr($src_image[2]).'">';
	}
		 echo '<span>'.esc_html($title).'</span>';

	if(!empty($image_url) ){ 
		echo '</a>';
	}
 
	 
	
echo '</li>';
 } 
 $output .=  ob_get_clean();
 