<?php
  /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Page Builder Start
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 $item_output='';
	wp_reset_query();
	wp_reset_postdata();
 $page_builder=hexwp_menu_data($item,'hexwp_menu_page_builder');
  
ob_start(); 
		echo '<li class="hw-menu-builder" >';
  	if(!empty($page_builder)){
		$post_name = get_page_by_path($page_builder);
				if(!empty($post_name->ID)){

		   if(function_exists( "sao_builder_meta") &&    !empty(get_post_meta($post_name->ID,'sao_show_page_builder',false))){
 
				$out = sao_section_config($post_name->ID,'output');
				$css = sao_section_config($post_name->ID,'css');
				$script = sao_section_config($post_name->ID,'script');
				if(!empty($out)){
 				echo '<div class="hw-menu-page-builder">'.$out.'</div>';
 				sao_enqueue();
 				}
				if(!empty($css)){
					echo '<style>'.$css.'</style>';  	
				}
				
		   }else {
				wp_reset_query();
				wp_reset_postdata();
				
				$argss = array(
					'p' => $post_name->ID,  
					'post_type' => 'page',
					'post_status' => 'publish',
					'posts_per_page' => 1,
					'number' => 1,
			
				);
  				$query = new WP_Query($argss);
				if( $query->have_posts() ) : 
				while ( $query->have_posts() ) : $query->the_post(); 		
							 echo '<div class="hw-footer-page-builder">';
							 the_content();
							 echo '</div>';
			  	endwhile; 
				endif; 
 				
				
				wp_reset_postdata();
			   
 			} 
		}
 			  
  }
  		echo '</li>';
 	
 
$output .= ob_get_clean();  	
 
 	wp_reset_query();
	wp_reset_postdata();
 