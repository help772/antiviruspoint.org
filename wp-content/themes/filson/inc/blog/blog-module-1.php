<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Blog Module 1
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_blog_module_1($option,$class ='',$excerpt_2 =false,$ratio_2=false,$image_size_2=false){
	global $post;
  	$excerpt =!empty($excerpt_2) ? hexwp_data($option,'excerpt_2'):hexwp_data($option,'excerpt');	
	
	if(!empty($ratio_2) && hexwp_data($option,'ratio_2')){
 		$option['ratio'] = hexwp_data($option,'ratio_2');
	}
	if(!empty($image_size_2) && hexwp_data($option,'image_size_2')){
 		$option['image_size'] = hexwp_data($option,'image_size_2');
	}
	 
   	$location =hexwp_option_2('blog_meta_layout','location'); 
   	$readmore =hexwp_data($option,'readmore'); 
	
	$thumbnail= wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), hexwp_data($option,'image_size','full') );
  	$post_thumbnail =!empty($thumbnail[0])?'':'hw-not-thumb';
	$is_sticky= is_sticky()? ' sticky ':'';
	
 	?>
    
    <div class="hw-item hw-module-1 <?php echo esc_attr($class.' '.$post_thumbnail.' '.$is_sticky);?>">
 	<div class="hw-post-blog" >
   					
		<?php hexwp_blog_thumb ($option,true);?>
                      
 		<div class="hw-details">
		        
			<?php
 			if($location == 'title-top' ) hexwp_blog_meta($option);  
			hexwp_blog_post_title($option);   
 			if($location == 'title-bottom' ) hexwp_blog_meta($option); 
			 if(!empty($excerpt)) hexwp_blog_excerpt($option); 
			if($location == 'details-bottom' ) hexwp_blog_meta($option); 
 			if(!empty($readmore)) hexwp_readmore(); 
			?>
              
		</div>
	</div> 
	</div>

<?php  
} 