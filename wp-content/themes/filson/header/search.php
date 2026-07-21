<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		 Nav Search
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_filter('hexwp_header_builder_search', 'hexwp_nav_search');
function hexwp_nav_search($opt=array()) {
	$search_position= hexwp_isset($opt,'search_position',hexwp_nav_default('search_position'));
	$search_button_layout= hexwp_isset($opt,'search_button_layout',hexwp_nav_default('search_button_layout'));
	$search_layout= hexwp_isset($opt,'search_layout',hexwp_nav_default('search_layout'));
	
    	if($search_position=='fixed'){
  		$class = 'hw_col_'.hexwp_isset($opt,'search_width',hexwp_nav_default('search_width'));
	} else{ 		
	$class= 'hw-nav-layout-'.$search_layout.' ';
		$class.= hexwp_isset($opt,'boxed_layout')?' hw-nav-boxed ':'';
 		$class.=hexwp_isset($opt,'icon_layout')?' hw-nav-icn-boxed ':'';


 	}
 	$class_woo= function_exists ( "is_woocommerce" ) && hexwp_isset($opt,'search_category') ? 'hw-search-woo':'';
   	$classes = array(
 		'hw-nav-search',
		'hw-search-'.$search_position,
		'hw-search-button-'.$search_button_layout,
 		$class ,
  		$class_woo,
 		'hw-nav-'.hexwp_isset($opt,'key'),
		hexwp_isset($opt,'side'),

   	);
	  
   ?>
 
	 
	<div class="<?php echo esc_attr(join( ' ', $classes ));?>">   
    
		<li class="hw-middle">
				 


		<?php if( $search_position=='dropdown' ){ ?>
        
			<a class="hw-link"><?php  
		 		if( $search_layout=='text-right' || $search_layout=='text-bottom' ){
						echo '<span>'.esc_html(hexwp_t('search')).'</span>';
				}?></a>
          
			<ul class="hw-drop">
			<div class="hw-search-warp">
		<?php } ?>
        
			<form id="<?php echo esc_attr('hw-search-'.hexwp_isset($opt,'key'));?>" method="get" class="hw-search" action="<?php echo esc_url(home_url( '/') );?>">
            
				<?php 
 				if ( function_exists ( "is_woocommerce" ) && hexwp_isset($opt,'search_category')){
					$product_cat =hexwp_nav_search_product_cat();
					?>
                    
					<select name="product_cat" >
						<?php if(!empty($product_cat) && is_array($product_cat) ){?>
						<?php foreach($product_cat as $key=> $value){ ?>
                                     <option  value="<?php echo esc_attr($key);?>" ><?php echo esc_html($value);?></option>
						<?php } ?>
						<?php } ?>
					</select>
					<input type="hidden" name="post_type" value="product">
				<?php } ?>
                          
                           
 				<input type="text" name="s"  value=""autocapitalize="none" autocomplete="off" autocorrect="off"   placeholder="<?php echo hexwp_t('searchitem');?>" />
 						
				<?php if($search_button_layout=='text'){?>
					<button type="submit" name="btnSubmit"  ><?php echo hexwp_t('search');?></button>
				<?php }else{?>
					<button type="submit" name="btnSubmit" ></button>
				<?php } ?>
                
				
				<?php if ( function_exists ( "is_woocommerce" ) && hexwp_isset($opt,'search_ajax')) {?><div class="hw-search-ajax-close"></div><?php }?>

                 	
			</form>
 
			
			<?php if ( function_exists ( "is_woocommerce" ) && hexwp_isset($opt,'search_ajax') ) {?><div class="hw-search-ajax hw-search-drop"></div><?php }?>

                
		<?php if( $search_position=='dropdown' ){ ?>
			</div>	 
			</ul>	 
		<?php }?>
        
        
            
		</li> 
	</div>
<?php   

}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		 Mobile Search
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_filter('hexwp_header_builder_mobile_search', 'hexwp_nav_mobile_search');
function hexwp_nav_mobile_search($opt=array()) {
	$search_position= hexwp_isset($opt,'search_position',hexwp_nav_default('search_position'));
 	$search_layout= hexwp_isset($opt,'search_layout',hexwp_nav_default('search_layout'));
 
 	$class='';
 
 	if($search_position=='dropdown'){
	$class= 'hw-nav-layout-'.$search_layout.' ';
		$class.= hexwp_isset($opt,'boxed_layout')?' hw-nav-boxed ':'';
 		$class.=hexwp_isset($opt,'icon_layout')?' hw-nav-icn-boxed ':'';
	} 
 	$class_woo= function_exists ( "is_woocommerce" ) && hexwp_isset($opt,'search_category') ? 'hw-search-woo':'';
   	$classes = array(
		'hw-nav-search',
		'hw-search-'.$search_position,
   		$class_woo,
		hexwp_isset($opt,'side'),
 		$class,
 		'hw-nav-'.hexwp_isset($opt,'key'),

   	);
	  
   ?>
 
	 
	<div class="<?php echo esc_attr(join( ' ', $classes ));?>">   
    
		<li  class="hw-middle">
		<?php  
		 if( $search_position=='dropdown'     ){?>
			
            <a class="hw-link"><?php 
 		 		if( $search_layout=='text-right' || $search_layout=='text-bottom' ){
						echo '<span>'.esc_html(hexwp_t('search')).'</span>';
				}
			?></a>
            
            
		<ul class="hw-mobile-content">
            <div class="hw-nav-search hw-search-button-icon hw-search-fixed">
            <?php } ?>
        
			<form method="get" class="hw-search" action="<?php echo esc_url(home_url( '/') );?>">
            
				<?php 
 				if ( function_exists ( "is_woocommerce" ) && hexwp_isset($opt,'search_category')){
					$product_cat =hexwp_nav_search_product_cat();
					?>
                    
					<select name="product_cat" >
						<?php if(!empty($product_cat) && is_array($product_cat) ){?>
						<?php foreach($product_cat as $key=> $value){ ?>
                                     <option  value="<?php echo esc_attr($key);?>" ><?php echo esc_html($value);?></option>
						<?php } ?>
						<?php } ?>
					</select>
							
				<?php } ?>
                          
				<input type="hidden" name="post_type" value="product">
                          
 				<input type="text" name="s"  value=""autocapitalize="none" autocomplete="off" autocorrect="off"   placeholder="<?php echo hexwp_t('searchitem');?>" />
 						
	 
					<button type="submit" name="btnSubmit" ></button>
                 
				
				<?php if ( function_exists ( "is_woocommerce" ) && hexwp_isset($opt,'search_ajax') ) {?><div class="hw-search-ajax-close"></div><?php }?>

                 	
			</form>
 
			
			<?php if ( function_exists ( "is_woocommerce" ) && hexwp_isset($opt,'search_ajax')) {?><div class="hw-search-ajax hw-search-drop"></div><?php }?>

                
		<?php if( $search_position=='dropdown'   ){ ?>
			</div>	 
			</ul>	 
		<?php }?>
        
        
            
		</li> 
	</div>
<?php   

}
 
 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		  Mobile Search
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function hexwp_nav_mobile_menu_search($opt=array()) {
	$search_position= hexwp_isset($opt,'search_position',hexwp_nav_default('search_position'));
 	$class_layout='hw-nav-search-'.$search_position;
  	?>
   
              <div class="hw-nav-search hw-search-fixed   hw-search-button-icon ">
        
                <form method="get" class="hw-search" action="<?php echo esc_url(home_url( '/') );?>">
                             
                    <input type="text" name="s"  value="" autocapitalize="none" autocomplete="off" autocorrect="off"  placeholder="<?php echo hexwp_t('searchitem');?>" />
                               
					<input type="hidden" name="post_type" value="product">
                              
                       <button type="submit"   name="btnSubmit" ></button>
 				
					
					<?php if ( function_exists ( "is_woocommerce" )) {?><div class="hw-search-ajax-close"></div><?php }?>
                 	
			</form>
			
			<?php if ( function_exists ( "is_woocommerce" ) ) {?><div class="hw-search-ajax hw-search-drop"></div><?php }?>
           
           
            </div>
  <?php
}


/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		 Nav Search Product Cat
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_nav_search_product_cat() {

 	$options_product = array();
	$options_product_obj = get_categories('taxonomy=product_cat&type=product&hide_empty=0');
	$options_product['']=  hexwp_t('category');  
	if(!empty($options_product_obj) && is_array($options_product_obj) ){
 	foreach ($options_product_obj as $product) {
    	$options_product[$product->slug] = $product->cat_name;
	}
	}
	return $options_product;
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		  Search Ajax
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_action('wp_ajax_nopriv_hexwp_search_ajax', 'hexwp_search_ajax');
add_action('wp_ajax_hexwp_search_ajax', 'hexwp_search_ajax');
function hexwp_search_ajax() {
	if ( function_exists ( "is_woocommerce" ) ) {
		$option=array();
		$option['post_type']='product';
		$option['image_size']='hexwp_thumbnail';
		$option['search']=hexwp_data('','search');
		$option['number']='8';
		$layout = 'list';
		$classes = array(
			'hw-el-search-ajax',
  			'woocommerce',
 			'hw-gap-20px',
 			'hw-'.hexwp_option('product_ratio'),
 			'hw-none',
			'hw_img_width_10',
  	
		);
 		ob_start();
		?>
		
		 <aside  class="<?php echo esc_attr(join( ' ', $classes ));?> "  >
   			<div class="hw-gap-warp">
                <div class="hw-item-list">
				 
					<?php
					$query = hexwp_query($option);
					if( $query->have_posts() ) : 
					while ( $query->have_posts() ) : $query->the_post();
					global $post;
						  
						  
						  
						$thumbnail= wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ),'hexwp_thumbnail' );
						$post_thumbnail =!empty($thumbnail[0])?'':'hw-not-thumb';
				
				  		?>
                       
                        	<div class="hw-item hw-module-1 <?php echo esc_attr($post_thumbnail);?>">
                        	<div class="hw-post-product product" >
					   
									
								<?php  hexwp_product_thumb($option);?>
									 
									 
                                <div class="hw-details">
                                    <?php
									hexwp_product_post_title($option);   
                                    hexwp_product_category_search($option);
                                     ?>
                                </div>
        
                            </div> 
                            </div>   
						  
						  
					<?php	  
					endwhile; 
					else:?>
					<div class="hw-no-results">
                	<?php echo hexwp_t('noresults');	?>
                    </div>
					
					<?php
					
					endif;
					?>
					
				</div>
  				 
 			</div>
		</aside> 
  <?php
	}
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Product Category
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function hexwp_product_category_search($category_layout=false) {
	$terms = wp_get_post_terms(get_the_ID(),"product_cat");
 	
	$class='';
 
   
	
	
 	$count=0;
 	if(!empty($terms)){
	?>
 	<div class="hw-product-category">
	<?php foreach ( $terms as $term ) { $count++; ?>
		<a href="<?php echo esc_url(get_term_link($term->term_id)); ?>"><?php echo esc_html( $term->name);?></a>
			<?php 
 			if($count == 5) { 
			break;
			}
			
			} ?>
	</div>
  	
<?php  }
}