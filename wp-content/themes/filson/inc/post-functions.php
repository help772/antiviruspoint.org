<?php

include_once hexwp_PATH . '/inc/post-layout/post-type.php';
include_once hexwp_PATH . '/inc/post-layout/post-list.php';
include_once hexwp_PATH . '/inc/post-layout/post-grid.php';
include_once hexwp_PATH . '/inc/post-layout/post-featured.php';
include_once hexwp_PATH . '/inc/post-layout/post-class.php';
include_once hexwp_PATH . '/inc/post-layout/post-carousel-class.php';
include_once hexwp_PATH . '/inc/post-layout/post-masonry-class.php';

 add_filter( 'wp_calculate_image_srcset_meta', '__return_null' ); 
 function the_post_thumbnail_remove_class($output) {
        $output = preg_replace('/class=".*?"/', '', $output);
        return $output;
}
add_filter('post_thumbnail_html', 'the_post_thumbnail_remove_class');

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																Element ID
																	 	
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
function hexwp_el_id($option =false) {
   if(!empty( $option['element_id'])){
		echo ' id="'.esc_attr($option['element_id']).'"  ' ;
   }
} 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																Element Show
																	 	
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
function hexwp_element_show($option,$clsas=false) {
	$display='show';
	if(hexwp_ismobile() && !empty($option['tablet_hide']) && !empty($option['phone_hide'])){
		$display ='hide';
 	}else{
		$display ='show';
	}
 
	if(!empty($clsas)){
		$class_hide='';
		if(!empty($option['hide_desktop'])){
			$class_hide.= 'hw_col_hide ';
		}
		if(!empty($option['hide_tablet'])){
			$class_hide.= 'hw_tab_hide ';
		}
		if(!empty($option['hide_mobile'])){
				$class_hide.= 'hw_mob_hide';
		}
		return $class_hide;
	}else{
		return  $display;
	}
}

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																Data
																	 	
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
function hexwp_data($option=false,$id=false,$def = false){
   	$option_id = !empty( $option[esc_html($id)] ) ? $option[esc_html($id)] : $def;
	$ajax_sao_evalue_id = !empty($_POST[$id])  ? $_POST[$id] : $option_id; 
	return $ajax_sao_evalue_id;
	
}    

 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																Settings
																	 	
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
function hexwp_settings($settings,$id=false){
	$settings_id=!empty( $settings[ $id] )? $settings[$id] : '';
	if($settings_id=='no'){
		$settings_id='';
	}
	return $settings_id;
 }    
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																Title Tabs
																	 	
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
function hexwp_multi_cats_array($array){
	$new_array=array();
	if(!empty($array) && is_array($array)){
	foreach($array as $key => $value){
		$new_array[esc_html($value)]=1;
	}
	}
	return $new_array;
 
}    
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																  El Css Anime
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_el_cssanime($option =false) {
   if(!empty( $option['cssanime'])){
	   	global $sao_aos_script;
		$sao_aos_script++;
		echo ' data-aos="'.esc_attr(hexwp_rtl_has($option['cssanime'])).'" ' ;
   }
} 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																Post title Tabs
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_post_title_tabs($option,$id =false){
	global  $post;
  
	$option['action']= $id;
	$option['post_status']='publish';
 	 
 	$multi_cats = !empty($option['multi_cats'])?$option['multi_cats']:'';
 	$tabs = !empty($option['tabs'])?$option['tabs']:'';
      
	  
   	$title_box_list_all = !empty($option['title_box_list_all'])?$option['title_box_list_all']:'';
	$link = hexwp_query($option,'',true);
	
 	$orderby = !empty($option['orderby'])?'data-orderby="'.$option['orderby'].'"':'';
  	$max_page = 'data-max_page="'.hexwp_query($option)->max_num_pages.'"';	
	
	$element_key = !empty($option['key'])?$option['key']:'123456'; 

	if(!empty($multi_cats)){
		$key_cats = implode(", ", array_keys($multi_cats));
  		$option['cats']=$key_cats;
	} else{
		$key_cats='';
	}
      
	 
 	$arrows = !empty($option['arrows'])?$option['arrows']:'';
 	$title_box_tag = !empty($option['title_box_tag'])?$option['title_box_tag']:'h4';
 	$title_box_type = !empty($option['title_box_type'])?$option['title_box_type']:'main-tabs';
	$title_box_style = !empty($option['title_box_style'])?$option['title_box_style']:hexwp_option('title_box_style');
	$class = ' hw-'.$title_box_type.' hw-tbox-'.$title_box_style.' ';
  
   	/*---------------------------------------------------------------------------------------------------------------------------
	Title Start-------------------------------------------------------------------------------------------------------------------
	----------------------------------------------------------------------------------------------------------------------------*/
 	if(!empty($option['title']) && $title_box_type!=='hide'){
		?>
		
		
	 <div class="<?php echo esc_attr($class);?>"  >
 			<?php echo wp_kses_post("<$title_box_tag class='hw-title-box'>");?>	
			
				<?php
				/*---------------------------------------------------------------------------------------------------------------------------
				Tab Main-------------------------------------------------------------------------------------------------------------------
				----------------------------------------------------------------------------------------------------------------------------*/
				if(	$title_box_type =='main-right' ||
					$title_box_type =='main-center' ||
					$title_box_type =='main-tabs' 
 				){?>
 				 	<div class="hw-tab-main"><span><?php echo wp_kses_post($option['title']);?></span></div>
 		
 
				<?php } ?>
			
            		<?php
 					/*---------------------------------------------------------------------------------------------------------------------------
					Tabs-------------------------------------------------------------------------------------------------------------------
					----------------------------------------------------------------------------------------------------------------------------*/
					if( 
						$title_box_type =='tabs-center' ||
 						$title_box_type =='main-tabs' ||
						!empty($title_box_list_all)
					){
						
						$option['title']= !empty($option['title_box_all'])?$option['title_box_all']:hexwp_t('all');
						 
						if(!empty($tabs)  || $title_box_type =='tabs-center' || !empty($title_box_list_all) || !empty($option['arrows']) ){
						?>
						
						<ul class="hw-tabs">
					 		<?php 
							if(	$title_box_type =='tabs-center' ||
$title_box_type =='main-tabs' ){
							if(!empty($option['tabs']) && is_array($option['tabs']) ):
							
							?><li class="hw-tab-active hw-tab-item"  <?php echo wp_kses_post($orderby);?>  <?php echo wp_kses_post($max_page);?> ><a><?php echo esc_html($option['title']);?></a></li><?php
 							foreach ($option['tabs'] as  $key => $value) : 
			$tab_value = $option;

							$tab_cats = !empty($value['cats']) ? ' data-cats="'.$value['cats'].'" ':'';
							$tab_orderby = !empty($value['orderby']) ? ' data-orderby="'.$value['orderby'].'" ':'';
							
							$tabs_title =  !empty($value['title'])?$value['title']:'';
               				 
							 	$tab_value['cats']= !empty($value['cats'])?$value['cats']:'';
							$tab_value['orderby']= !empty($value['orderby'])?$value['orderby']:'';
 						
							
							$tab_max_page = !empty(hexwp_query($tab_value)->max_num_pages)? ' data-max_page="'.hexwp_query($tab_value)->max_num_pages.'" ':'';

 						 
                 			 	?><li class="hw-tab-item" <?php echo wp_kses_post($tab_cats.$tab_orderby.$tab_max_page);?> ><a><?php echo esc_html($tabs_title);?></a></li><?php
         				
						 
                            endforeach;
                            endif;  
}
                            ?>
                            
                            <?php if(!empty($title_box_list_all)){?>
                            	<li class="hw-tab-item hw-view-all"><a href="<?php echo esc_url($link);?>"><?php echo hexwp_t('full_list');?></a></li>
                            <?php }?>
          						<?php if($arrows=='title-box'  && empty(hexwp_ismobile())){?>
                                <li class="hw-arrow-warp"><a class="hw-arrow-prev"></a><a class="hw-arrow-next"></a></li>
                            <?php }?>
						</ul>
		 
                <?php
  			
				}
				}
				/*---------------------------------------------------------------------------------------------------------------------------
				END Tabs-------------------------------------------------------------------------------------------------------------------
				----------------------------------------------------------------------------------------------------------------------------*/
 				 ?>  
			 
		<?php echo wp_kses_post("</$title_box_tag>");?>			
			
            <div class="hw-data-json"><?php echo json_encode(hexwp_array_filter_recursive($option));?></div>

		</div>
  	<?php
	}
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																Title Box
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_title_box($title =false){
	global  $hexwp_data;
	
	$title_box_type = 'main-tabs';
	$title_box_style = hexwp_option('title_box_style');
	$class = ' hw-'.$title_box_type.' hw-tbox-'.$title_box_style.' ';
	if(!empty($title)){?>
		
  		 <div class="<?php echo esc_attr($class);?>">
		 <h4 class="hw-title-box"><div class="hw-tab-main"><span><?php echo esc_html($title);?></span></div></h4>
         </div>
     <?php    
	}
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																hexwp array Filter Recursive
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 function hexwp_array_filter_recursive($input){
    foreach ($input as &$value)
    {
        if (is_array($value))
        {
            $value = hexwp_array_filter_recursive($value);
        }
    }

    return array_filter($input);
}
 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																Load More
																	 	
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
function hexwp_load_more($option,$id= false){
 	global  $post;
   	$max_page= hexwp_query($option)->max_num_pages;
	
	
	
		if($max_page <= 1){
		$class=' hw-load-more-hide ';
	}else{
		$class=' ';
	}
 		$option['action']=$id;
 
 		$option['post_status']='publish';?>
 		 <div class="hw-load-more <?php echo esc_attr($class);?>" ><a  data-page="2" data-max_page="<?php echo esc_attr($max_page);?>" ><?php echo esc_html(hexwp_t('load_more'));?></a><div class="hw-data-json"><?php echo wp_kses_post(json_encode(hexwp_array_filter_recursive($option)));?></div></div>
 <?php	 
}

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																Query
																	 	
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
function hexwp_query($option =false,$pagenumber= false,$archive_link=false){
  	$qurey = hexwp_data($option,'qurey');

 	$more_posts = hexwp_data($option,'more_posts');
	if(!empty($pagenumber) || $more_posts =="pagenavi" ){
   		$page = is_front_page() ? get_query_var( 'page') :get_query_var( 'paged');
	}else{
   		$page = (isset($_REQUEST['pageNumber'])) ? $_REQUEST['pageNumber'] : 1;
	}
 	$publish = (isset($_REQUEST['post_status'])) ? $_REQUEST['post_status'] : 'publish';
	$number = hexwp_data($option,'number','5');
 	$sliders = hexwp_data($option,'sliders');
	$cats = hexwp_data($option,'cats');
	$post_type = hexwp_data($option,'post_type');
	$multi_cats = hexwp_data($option,'multi_cats');
   	if(!empty($multi_cats)){
	  unset($multi_cats['']);
	}
 	$args = array();
 	$archive_args = array();
	$args['post_type'] = $post_type;
	$archive_args['post_type'] = $post_type;
  	$args['posts_per_page']=$number;
	$args['ignore_sticky_posts'] = 1;
	$archive_args['ignore_sticky_posts'] = 1;
  	if(!empty($qurey)){
		$args =$qurey;
	}
	/*---------------------------------------------------------------------------------------------------------------------------
	Testimonial-------------------------------------------------------------------------------------------------------------------
	----------------------------------------------------------------------------------------------------------------------------*/	
 	elseif($post_type=='testimonial'){
   		 
		
		$args['post_type']= 'testimonial';
 			
 			if(!empty($cats)){	
 
  			$tax_query_category =array(
					'taxonomy' => 'testimonial_category',
					'terms' => $cats,
					'field' => 'slug',
				);		
				$args['tax_query'][0][] =	$tax_query_category;		 
			} 
  
  		 
 		$args['paged']=$page; 
		
 	 
	}	
	/*---------------------------------------------------------------------------------------------------------------------------
	Staff-------------------------------------------------------------------------------------------------------------------
	----------------------------------------------------------------------------------------------------------------------------*/		
 	elseif($post_type=='staff'){
   		 
		
		$args['post_type']= 'staff';
 			
 			if(!empty($cats)){	
 
 				 
 			$tax_query_category =array(
					'taxonomy' => 'staff_category',
					'terms' => $cats,
					'field' => 'slug',
				);		
				$args['tax_query'][0][] =	$tax_query_category;		 
			} 
  
  		 
 		$args['paged']=$page; 
		
 	 
	}	
	/*---------------------------------------------------------------------------------------------------------------------------
	Portfolio-------------------------------------------------------------------------------------------------------------------
	----------------------------------------------------------------------------------------------------------------------------*/	
	elseif($post_type=='portfolio'){
		$orderby = hexwp_orderby($option);
		$date_query = hexwp_date_query($option);
		$meta_key = hexwp_meta_key($option);	
		
		$args['post_type']= 'portfolio';
 	 		$array =array();
			
			
			if(!empty($cats)){	
   
 				$array = $cats;
				}elseif(!empty($multi_cats) && is_array($multi_cats)){
					foreach($multi_cats as $key => $value){
						if(!empty($key)){
						$array[] = $key;
					}
					}
				}
				if(!empty($array)){
  					$tax_query_category =array(
						'taxonomy' => 'portfolio_category',
						'terms' =>  $array,
						'field' => 'slug',
					);		
					$args['tax_query'][0][] =	$tax_query_category;
 				}
   				$archive_args['portfolio_category'] = implode(",", $array);

  		$args['paged']=$page; 
		
 	 
	}
	/*---------------------------------------------------------------------------------------------------------------------------
	Slide-------------------------------------------------------------------------------------------------------------------
	----------------------------------------------------------------------------------------------------------------------------*/		
	elseif($post_type=='sao_slide'){
		
		$args['post_type'] = 'sao_slide';
 		$args['no_found_rows'] = 1;
		if(!empty($sliders)){
			$args['tax_query'] =  array(
				array(
					'taxonomy' => 'sao_sliders',
					'terms' => $sliders,
					'field' => 'slug',
				)
			);
		}
 	}elseif($post_type=='image'){
		$args['post_type'] = 'sao_image';
 		$args['no_found_rows'] = 1;
 		
		
  		if(!empty($cats)){
			$args['tax_query'] =  array(
				array(
					'taxonomy' => 'sao_images',
					'terms' => $cats,
					'field' => 'slug',
				)
			);
		}
	/*---------------------------------------------------------------------------------------------------------------------------
	Product-------------------------------------------------------------------------------------------------------------------
	----------------------------------------------------------------------------------------------------------------------------*/	 		
 	}elseif($post_type=='product'){
		$orderby = hexwp_data($option,'orderby');
		$args['post_type']= 'product';
		$search=hexwp_data('','search');
		if(isset($search)){
		$args['s']= $search;
		}
		if(!empty($cats)){	
			$args['product_cat'] = $cats;
				$archive_args['product_cat'] = $cats;
		}elseif(!empty($multi_cats)){	
			$key_cats = implode(",", array_keys($multi_cats));
			$args['product_cat'] = $key_cats;
				$archive_args['product_cat'] = implode(",", array_keys($multi_cats));
			
		} 
		
 			
 
		if(!empty($orderby)){
			
			if($orderby== 'popularity'){
				$args['orderby'] =   'meta_value_num date';
				$args['meta_key'] =   'total_sales';
				$archive_args['orderby'] = 'popularity';
				
			}elseif($orderby== 'rating'){
				$args['orderby'] =   'meta_value_num date';
				$args['meta_key'] =   '_wc_average_rating';
				$archive_args['orderby'] = 'rating';
			}elseif($orderby== 'price'){
				$args['orderby']= 'meta_value_num';
				$args['meta_key']= '_price';
				$args['order']= 'asc';
				$archive_args['orderby'] = 'price';
				
			}elseif($orderby== 'price_desc'){
				$args['orderby']= 'meta_value_num date';
				$args['meta_key']= '_price';
 				$archive_args['orderby'] = 'price-desc';
 				    
			}elseif($orderby== 'featured'){
				$args['tax_query'] = array(array(
					'taxonomy' => 'product_visibility',
					'field'    => 'name',
					'terms'    => 'featured',
					),);
					
				$args['orderby']='date';
		
			}elseif($orderby== 'onsale'){
					$args['meta_query']	=array(
						'relation' => 'OR',
								array( // Simple products type
									'key'           => '_sale_price',
									'value'         => 0,
									'compare'       => '>',
									'type'          => 'numeric'
								),
								array( // Variable products type
									'key'           => '_min_variation_sale_price',
									'value'         => 0,
									'compare'       => '>',
									'type'          => 'numeric'
								)
							);
				 
					$args['orderby']='date';
					
			}elseif($orderby== 'onsale_variation'){
				
				if(function_exists('is_woocommerce')){
	 				$args['meta_query'] = WC()->query->get_meta_query();
 					$args['post__in'] = array_merge( array( 0 ), wc_get_product_ids_on_sale() );
				}
				$args['orderby']='date';
					
			}elseif($orderby== 'stock'){
				
				$args['meta_query']  = array(
				array(
					'key' => '_stock_status',
					'value' => 'instock'
				),
				array(
					'key' => '_backorders',
					'value' => 'no'
				)
				
				);
				$args['orderby']='date';
				}else{
					$args['orderby']=$orderby;
 					$archive_args['orderby'] = $orderby;
				}
			 
		}
		 
		$args['paged']=$page; 
		 			
		 			
 	}else{
		$orderby = hexwp_orderby($option);
		$date_query = hexwp_date_query($option);
		$meta_key = hexwp_meta_key($option);	
			
			
		if(!empty($cats)){
			$args['category_name']=$cats;
			$archive_args['category_name'] = 'date';

		 }elseif(!empty($multi_cats)){
			$key_multi = implode(", ", array_keys($multi_cats));
 			$args['category_name']=$key_multi;
			$archive_args['category_name'] = implode(", ", array_keys($multi_cats));
		}
		if(!empty($orderby)){
			$args['orderby']=$orderby;
			$archive_args['orderby'] = $orderby;
		}
		
		if( !empty($date_query)){
			$args['date_query']=$date_query;
			$archive_args['date_query'] = $date_query;
		}
		
		if(!empty($meta_key)){
			$args['meta_key']= $meta_key;
			$args['date_query']= $meta_key;
		}
		
			$args['paged']= $page; 

	}
	 
 
	$archive = hexwp_data($option,'archive');
	
	if(!empty($archive_link)){
  		unset($args['number']);
 		unset($args['page']);
  		unset($args['paged']);
 		unset($args['posts_per_page']);
 		unset($args['post_status']);
    	return home_url( '/').''.add_query_arg($archive_args,'','');
		
 	}elseif(!empty($archive)){
		global  $wp_query;
		return  $wp_query;
	}else{
		return new WP_Query($args );
	}
				
} 

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																Date Query
																	 	
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
function hexwp_date_query($option=false,$custom = false){
	if(!empty($custom)){
		$orderby =   isset( $custom['orderby'] ) ? $custom['orderby'] : null;
	} else{
		$orderby = hexwp_data($option,'orderby');
	}
		
	if($orderby =='rand-day' || $orderby =='last-comment-day'||$orderby =='most-comment-day'||$orderby =='views-day'||$orderby =='best-reviews-day'){
			$date_query = array(array('after' => '1 day ago' )) ;
			
	} elseif($orderby =='rand-week'|| $orderby =='last-comment-week'|| $orderby =='most-comment-week'|| $orderby =='views-week'|| $orderby =='best-reviews-day'){
			$date_query = array(array('after' => '1 week ago' )) ;
			
	} elseif( $orderby =='rand-month'|| $orderby =='last-comment-month'|| $orderby =='most-comment-month'|| $orderby =='views-day'|| $orderby =='best-reviews-month'){
			$date_query = array(array('after' => '1 month ago' )) ;
 		
	} elseif( $orderby =='rand-year'|| $orderby =='last-comment-year'|| $orderby =='most-comment-year'|| $orderby =='views-year'|| $orderby =='best-reviews-year'){
			$date_query = array(array('after' => '1 year ago' )) ;
	}else{
 			$date_query='';
	}
	return $date_query;
	
} 

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

															 Query
																	 	
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
function hexwp_orderby($option=false,$custom = false){
	if(!empty($custom)){
		$orderby =   isset( $custom['orderby'] ) ? $custom['orderby'] : null;
	} else{
		$orderby = hexwp_data($option,'orderby');
	}
 	
	if( $orderby == 'rand'|| $orderby =='rand-day'|| $orderby =='rand-week'|| $orderby =='rand-month'|| $orderby =='rand-year'){
		$order='rand';
		
	}elseif( $orderby == 'most-comment'|| $orderby =='most-comment-day'|| $orderby =='most-comment-week'|| $orderby =='most-comment-month'|| $orderby =='most-comment-year'){
		$order='comment_count date';
			
	}elseif( $orderby == 'views'|| $orderby =='views-day'|| $orderby =='views-week'|| $orderby =='views-month'|| $orderby =='views-year'
		|| $orderby == 'best-reviews'|| $orderby =='best-reviews-day'|| $orderby =='best-reviews-week'|| $orderby =='best-reviews-month'|| $orderby =='best-reviews-year'){
		$order='meta_value_num date';					
			
	} else {
 		$order=$orderby;
	}
	
	return $order;
	
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

															 Meta Key
																	 	
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
function hexwp_meta_key($option=false,$custom = false){
	if(!empty($custom)){
		$orderby =   isset( $custom['orderby'] ) ? $custom['orderby'] : null;
	} else{
		$orderby = hexwp_data($option,'orderby');
	}
	
  	if( $orderby == 'views'|| $orderby =='views-day'|| $orderby =='views-week'|| $orderby =='views-month'|| $orderby =='views-year'){
		if(function_exists('the_views')) {
     $meta_key = 'views';
	}else{
  	  $meta_key	 = 'hexwp_views_count';
	}
			
	} elseif($orderby == 'best-reviews'|| $orderby =='best-reviews-day'|| $orderby =='best-reviews-week'|| $orderby =='best-reviews-month'|| $orderby =='best-reviews-year'){
		$meta_key='hexwp_review_score';	
							
	} else{
		$meta_key='';
			
 	}
	return $meta_key;
	
}  
 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

															 Post View
																	 	
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////  
function hexwp_getPostViews($postID,$only_count =false){
 	if(function_exists('the_views')) {

     $count_key = 'views';

	}else{

  	  $count_key = 'hexwp_views_count';

	}
    $count = get_post_meta($postID, $count_key, true);
	
    if($count=='' || $count=='0'){
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
		if(!empty($only_count)){
        	return __("0",'hexwp');
		}else{
        	return __('0','hexwp').' '.hexwp_t('view');
		}
	}
	if(!empty($only_count)){
     	return hexwp_number_replace(round($count));
	}else{
     	return hexwp_number_replace(round($count)).' '. hexwp_t('views') ;
 	}
}
 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

															 light slider
																	 	
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
 function hexwp_lightslider($item,$slider_options=false) {
	if(empty(hexwp_ismobile())){

		$number = isset( $sao_evalue['number'] ) ? $sao_evalue['number'] : '5'; 
 		wp_enqueue_script('sao_lightslider_script',hexwp_DIR. '/js/lib/lightslider.min.js','',1); 
 	
		$slider_options["item"] = (int)$item;
		$slider_options["slideMove"] = 1;
		$slider_options["loop"] = false;
 		$slider_options["slideMargin"] = 0;
 		?>
		<div class="hw-slider-options" ><?php echo wp_kses_post(json_encode($slider_options));?></div>
		<?php
		 

	}
} 


/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

															 Get Time
																	 	
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
function hexwp_get_time(){
  	if(  hexwp_option('time_format') == 'morden'  ){	
			echo hexwp_number_replace(hexwp_time_elapsed_string());
   	}else{
		echo  esc_html(get_the_time(get_option('date_format')));
 	}
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

															   Time Elased String
																	 	
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
 function hexwp_time_elapsed_string( )
{
		$to = current_time('timestamp'); //time();
		$from = get_the_time('U') ;
		
		$diff = (int) abs($to - $from);
		$etime=$diff ;
   	 if ($etime < 1)
    {
        return __('0 Second','hexwp');
    }

    $a = array( 365 * 24 * 60 * 60  => 'year' ,
                 30 * 24 * 60 * 60  => 'month' ,
                      24 * 60 * 60  => 'day' ,
                           60 * 60  => 'hour' ,
                                60  => 'minute' ,
                                 1  => 'second' ,
                );
    $a_dates = array( 'year'   => esc_html(hexwp_t('years')),
                       'month'  => esc_html(hexwp_t('months')),
                       'day'    => esc_html(hexwp_t('days')),
                       'hour'   => esc_html(hexwp_t('hours')),
                       'minute' => esc_html(hexwp_t('minutes' )),
                       'second' => esc_html(hexwp_t('seconds')),
                );
    $a_date = array( 'year'   => esc_html(hexwp_t('year')),
                       'month'  => esc_html(hexwp_t('month')),
                       'day'    => esc_html(hexwp_t('day')),
                       'hour'   => esc_html(hexwp_t('hour')),
                       'minute' => esc_html(hexwp_t('minute' )),
                       'second' => esc_html(hexwp_t('second')),
                );

    foreach ($a as $secs => $str)
    {
        $d = $etime / $secs;
        if ($d >= 1)
        {
            $r = round($d);
            return $r . ' ' . ($r > 1 ? $a_dates[$str] : $a_date[$str]) . " ". esc_html(hexwp_t('ago'));
        }
    }
}

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

															   Post Hover Link
																	 	
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
function hexwp_post_hover_link($link=false){
	global $post;
 	$img_link =get_the_post_thumbnail_url($post->ID,'full');
 	$the_permalink = hexwp_meta('staff_external',hexwp_meta('portfolio_external', get_permalink()));
  	?>
    <div class="hw-hover-icon">
		<a class="hw-hover-img" href="<?php echo esc_url($img_link);?>"></a><a class="hw-hover-post"  href="<?php echo esc_url($the_permalink);?>"></a>
	</div>
	<?php
}

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

															Caption Effect
																	 	
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
 function hexwp_image_caption_effect($option =false){ 
  	$image_effect= !empty($option['image_effect']) ? $option['image_effect'] :hexwp_option('image_effect');;
 	$image_effect_class = !empty($image_effect)? 'hw-hover-'.$image_effect:'';	 
	$caption_effect =!empty($option['caption_effect'])? $option['caption_effect']: hexwp_option('caption_effect');
 	$caption_effect_class = $caption_effect!== 'imghvr-fade'?'hw-'.$caption_effect:'';
 	
	return $image_effect_class.' '.$caption_effect_class;
 }
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		Thumbnail Image
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_remove_rel_attr($content) {
    return preg_replace('/\s+rel="attachment wp-att-[0-9]+"/i', '', $content);
}
add_filter('the_content', 'hexwp_remove_rel_attr');
function hexwp_filter_image_sizes( $sizes) {
    unset( $sizes['yith-woocompare-image']);
    return $sizes;
}

add_filter('intermediate_image_sizes_advanced', 'hexwp_filter_image_sizes');	
function hexwp_filter_post_thumbnail_html( $html ) {
 
    if ( '' == $html ) {
        return '<img src="' . hexwp_DIR . '/images/default-thumbnail.jpg" width="640px" height="384px" class="image-size-name" />';
    } 
    return $html;
}
add_filter( 'post_thumbnail_html', 'hexwp_filter_post_thumbnail_html' );
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

															Get Search Form
																	 	
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
function hexwp_get_search_form( $form ) {
  	$searchform_post_type= function_exists ( "is_woocommerce" ) ? hexwp_option('searchform_post_type') :hexwp_option('searchform_post_type');
	$classes = array(
  		'hw-search-button-icon',
		'hw-searchfield',
		'hw-aw',
		'hw-align-center',
    	);
 	ob_start();
	
	?>
	<aside  class="<?php echo esc_attr(join( ' ', $classes ));?> " >
        <form method="get" class="hw-search" action="<?php echo esc_url(home_url( '/') );?>">
            <input type="text" name="s"  value="" placeholder="<?php echo esc_attr(hexwp_t('searchitem'));?>" />
            <button type="submit" name="btnSubmit" ></button>
          
            <?php if(!empty($searchform_post_type)){?>
                <input type="hidden" name="post_type" value="<?php echo esc_attr($searchform_post_type);?>">
            <?php }?>
                         
		</form>
     </aside>
	<?php
	return ob_get_clean();

}
add_filter( 'get_search_form', 'hexwp_get_search_form' );

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

															Caption Effect
																	 	
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
 function hexwp_between_border($option =false,$box_layout=false){ 
 		$between_border =!empty($option['between_border'])?$option['between_border']:hexwp_option('between_border');
		$border_class =$between_border !=='none' ? 'hw-bw-'.$between_border:'';
		if($box_layout=='none' || $box_layout=='boxed-all'|| $box_layout=='boxed-content'){
			return $border_class;
		}else{
			return '';
		}
	 
 }