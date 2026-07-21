<?php
   /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Widget Start
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$menu_widget=$slug.'_menu_widget';	
 $page_builder=hexwp_menu_data($item,'hexwp_menu_widget');

ob_start(); 

   	$widgets = !empty( $widget ) ? $widget : '';
	
	if ( is_active_sidebar( $widgets ) ) : 
		echo '<li class="hw-menu-widget" >';
		dynamic_sidebar( sanitize_title($widgets) ); 
		echo '</li>';
	endif;
	wp_reset_query();	
 			
		
   
$output .= ob_get_clean();  	
 