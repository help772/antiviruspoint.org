<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Product Module 1
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_product_module_1($option=false,$class ='',$excerpt_2 =false,$ratio_2=false,$image_size_2=false){
	global $post;
	$excerpt =!empty($excerpt_2) ? hexwp_data($option,'excerpt_2'):hexwp_data($option,'excerpt');	
	
	if(!empty($ratio_2) && hexwp_data($option,'ratio_2')){
 		$option['ratio'] = hexwp_data($option,'ratio_2');
	}
	if(!empty($image_size_2) && hexwp_data($option,'image_size_2')){
 		$option['image_size'] = hexwp_data($option,'image_size_2');
	}
	
 	$countdown = hexwp_data($option,'countdown');
   	$meta_category = hexwp_data($option,'meta_category');    
 	$rating = hexwp_data($option,'rating');    
 	$addcart = hexwp_data($option,'addcart');    
 
	$thumbnail= wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), hexwp_data($option,'image_size','full') );
  	$post_thumbnail =!empty($thumbnail[0])?'':'hw-not-thumb';
  

  ?>
	<div class="hw-item hw-module-1 <?php echo esc_attr($class.' '.$post_thumbnail);?>">

	<div class="hw-post-product product" >
       
  					
		<?php  hexwp_product_thumb($option);?>
                     
                     
 		<div class="hw-details">
		
                             
			<?php
			 do_action( 'woocommerce_before_shop_loop_item_title' );

			if(!empty($meta_category)) hexwp_product_category($option);
 			hexwp_product_post_title($option);   
						if(!empty($rating)) hexwp_product_rating(); 

			hexwp_price();  

 				if(!empty($excerpt)) hexwp_product_excerpt($option);
 				if(!empty($countdown)) hexwp_product_countdown();
 				if(!empty($addcart)) hexwp_product_button();
		 
			do_action( 'woocommerce_after_shop_loop_item_title' );
 			?>
             
   
		</div>
        <div class="hw-product-tags">
        		<?php  hexwp_product_sale();?>
				<?php hexwp_product_featured()?>
  		</div>
        
 	</div> 
 	</div> 
    
<?php  
}