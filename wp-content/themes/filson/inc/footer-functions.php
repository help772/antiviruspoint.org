<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																Footer Content
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_footer_content() {
	  $column = hexwp_option('footer_column');
   	if($column == '1'){
		$class_footer= 'hw_col_1_1';
	}elseif($column == '2'){
		$class_footer= 'hw_col_1_2 hw_tab_1_2 hw_mob_1_1';
	}elseif($column == '3'){
		$class_footer= 'hw_col_1_3 hw_tab_1_1 hw_mob_1_1';
	}elseif($column == '4'){
 		$class_footer= 'hw_col_1_4 hw_tab_1_2 hw_mob_1_1';
	}elseif($column == '5'){
 		$class_footer= 'hw_col_1_5 hw_tab_1_2 hw_mob_1_1';
	}else{
		$class_footer= 'hw_col_1_6 hw_tab_1_2 hw_mob_1_1';
	};
	?>
	<?php  
	if( is_active_sidebar( 'sidebar_footer_1' ) ||
		is_active_sidebar( 'sidebar_footer_2' ) || 
		is_active_sidebar( 'sidebar_footer_3' ) || 
		is_active_sidebar( 'sidebar_footer_4' ) || 
		is_active_sidebar( 'sidebar_footer_5' ) || 
		is_active_sidebar( 'sidebar_footer_6' ) ){?>
 			
            <div class="hw-footer-content hw-flex <?php echo esc_attr($class_footer);?>" > 
        
			<section class="hw-column">
				<?php if(is_active_sidebar( 'sidebar_footer_1' ) ){ dynamic_sidebar('sidebar_footer_1') ; } ?>
			</section> 
            
            
 			<?php if($column=='2' || $column=='3' || $column=='4' ||$column=='5' ||$column=='6' ){ ?>
				<section class="hw-column">
					<?php if(is_active_sidebar( 'sidebar_footer_2' ) ){dynamic_sidebar('sidebar_footer_2');}?>
				</section> 
            <?php }?>
            
 			<?php if($column=='3' || $column=='4' ||$column=='5' ||$column=='6' ){ ?>
				<section class="hw-column">
					<?php if(is_active_sidebar( 'sidebar_footer_3' ) ){dynamic_sidebar('sidebar_footer_3');}?>
				</section> 
            <?php }?>

 			<?php if($column=='4' ||$column=='5' ||$column=='6' ){ ?>
                <section class="hw-column">
                   <?php if(is_active_sidebar( 'sidebar_footer_4' ) ){dynamic_sidebar('sidebar_footer_4');}?>
                 </section> 
            <?php }?>


 			<?php if( $column=='5' || $column=='6' ){ ?>
                <section class="hw-column">
                   <?php if(is_active_sidebar( 'sidebar_footer_5' ) ){dynamic_sidebar('sidebar_footer_5');}?>
                </section> 
            <?php }?>
            
			<?php if( $column=='6'  ){ ?>
                <section class="hw-column">
                   <?php if(is_active_sidebar( 'sidebar_footer_6' ) ){dynamic_sidebar('sidebar_footer_6');}?>
                 </section> 
            <?php }?>
 	</div> 
	<?php 
	}
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																Footer Bottom
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_footer_bottom() {

	if(hexwp_option('footer_bottom_code',true)|| hexwp_option('footer_social') == 'show' ) {
		$footer_center='';
			
		if(hexwp_option('footer_bottom_code',true) && hexwp_option('footer_social') == 'show' ){
			$footer_center="hw-footer-not-center";
		}
		?>
            
		<div class="hw-footer-bottom <?php echo wp_kses_post($footer_center);?>">
			<div class="hw-middle-footer">
				<?php hexwp_footer_code();?>
				<?php hexwp_footer_social();?>
			</div>
		</div>
            
		<?php 
	} 
		
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																Footer Code
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_footer_code() {
	if(hexwp_option('footer_bottom_code',true)){
		?>
 		<div class="hw-footer-code"><?php echo wp_kses_post(hexwp_option('footer_bottom_code',true));?></div>
 		<?php 
	}
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																Footer Social
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_footer_social() {
  	if(hexwp_option('footer_social') == 'show' ){
   	global $smof_data;
	?>
 		<div class="hw-footer-social hw-social-icon-<?php echo hexwp_option('footer_icon_style');?>">
			<?php hexwp_social_content(hexwp_option('footer_icon_style'),$smof_data,'social_');?>
		</div>
   	<?php
	}
}

  

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																Page Builder Footer
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_page_footer($out =false) {
	$page_footer = hexwp_option('page_footer' );
							$output='';

	if(!empty($page_footer)){
	if($page_footer !='hide'){
		$post_name = get_page_by_path($page_footer);
		if(!empty($post_name->ID)){
			   
			   
			   
			 if(function_exists( "sao_builder_meta") && !empty(get_post_meta($post_name->ID,'sao_show_page_builder',false))){
  				$section = sao_section_config($post_name->ID) ;
						$output='';
 					if(!empty($section['output'])){
						$output.= '<div class="hw-footer-page-builder">'.$section['output'].'</div>';
						ob_start(); 
						sao_enqueue();
 						$output.=ob_get_clean();
					}
 					if(!empty($section['css'])){
						$output.='<style>'.$section['css'].'</style>';
					}
					return $output;
				 
				 
	 
			}else{
				 
		wp_reset_query();
				wp_reset_postdata();
				
				$args = array(
					'p' => $post_name->ID, 
					'post_type' => 'page',
					'post_status' => 'publish',
					'number' => 1,
					'posts_per_page' => 1,
				);
				ob_start(); 
				$query = new WP_Query($args);
				if( $query->have_posts() ) : 
				while ( $query->have_posts() ) : $query->the_post(); 	
				
						 echo '<div class="hw-footer-page-builder">';
							 the_content();
							 echo '</div>';
 				endwhile; 
				endif; 
				wp_reset_postdata();
				return ob_get_clean();
				
				
			}
		   }else{
			 return '';
		   }
	} 
	}
}

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																Before Page Builder Footer
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_page_top_footer($out =false) {
	$page_footer = hexwp_option('page_top_footer' );
	$output='';

	if(!empty($page_footer)){
	if($page_footer !='hide'){
		$post_name = get_page_by_path($page_footer);
		if(!empty($post_name->ID)){
			   
			   
			   
			 if(function_exists( "sao_builder_meta") && !empty(get_post_meta($post_name->ID,'sao_show_page_builder',false))){
  				$section = sao_section_config($post_name->ID) ;
						$output='';
 					if(!empty($section['output'])){
						$output.= '<div class="hw-page-top-builder">'.$section['output'].'</div>';
						ob_start(); 
						sao_enqueue();
 						$output.=ob_get_clean();
					}
 					if(!empty($section['css'])){
						$output.='<style>'.$section['css'].'</style>';
					}
					return $output;
				 
				 
	 
			}else{
 
 
		wp_reset_query();
				wp_reset_postdata();
				
				$args = array(
					'p' => $post_name->ID, 
					'post_type' => 'page',
					'post_status' => 'publish',
					'number' => 1,
					'posts_per_page' => 1,
				);
				ob_start(); 
				$query = new WP_Query($args);
				if( $query->have_posts() ) : 
				while ( $query->have_posts() ) : $query->the_post(); 		
						echo '<div class="hw-page-top-builder">';
							 the_content();
					echo '</div>';
			  	endwhile; 
				endif; 
				wp_reset_postdata();
				return ob_get_clean();
 
 			}
		   }else{
			 return '';
		   }
	} 
	}
}