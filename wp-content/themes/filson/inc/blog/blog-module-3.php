<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Blog Module 3
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_blog_module_3($option,$class='',$excerpt_2 =false,$ratio_2=false,$image_size_2=false){
	global $post;
 	$excerpt =!empty($excerpt_2) ? hexwp_data($option,'excerpt_2'):hexwp_data($option,'excerpt');		
	
 	$ratio_class='';
	if(!empty($ratio_2) && hexwp_data($option,'ratio_2')){
 		$option['ratio'] = hexwp_data($option,'ratio_2');
	}
	
 	if(!empty($image_size_2) && hexwp_data($option,'image_size_2')){
 		$option['image_size'] = hexwp_data($option,'image_size_2');
	}
	
    $location =hexwp_option_2('blog_meta_layout','location'); 
   	$readmore =hexwp_data($option,'readmore'); 
	$hover_post_icon = hexwp_data($option,'hover_post_icon',hexwp_option('blog_hover_post_icon')); 
	$caption_layout =!empty($option['caption_layout'])? $option['caption_layout']: hexwp_option('blog_caption_layout' );
   	
	$thumbnail= wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), hexwp_data($option,'image_size','full') );
  	$post_thumbnail =!empty($thumbnail[0])?'':'hw-not-thumb';
	$is_sticky= is_sticky()? ' sticky ':'';	 
	?>
    <div class="hw-item hw-glider <?php echo esc_attr($class);?>">
	<div class="hw-post-blog <?php echo esc_attr(' '.$post_thumbnail.' '.$is_sticky);?>" >
  		<?php hexwp_blog_thumb ($option,false,$caption_layout);?>
 		  
  		<figcaption>
        <div class="hw-details">
         
			<?php
			 
			if($hover_post_icon=='show'){
			hexwp_post_hover_link();  
			}
			 
  			if($location == 'title-top' ) hexwp_blog_meta($option);  
 			hexwp_blog_post_title($option);  
           	if($location == 'title-bottom' ) hexwp_blog_meta($option); 
 			if(!empty($excerpt)){hexwp_blog_excerpt($option);} 
			if($location == 'details-bottom' ) hexwp_blog_meta($option);  
			if(!empty($readmore)) hexwp_readmore();

  			?>
            
		</div>
 		</figcaption>
  	 
	</div> 
	</div> 
    
	<?php
}