<?php
include_once hexwp_PATH . '/inc/product/product-module-1.php';  
include_once hexwp_PATH . '/inc/product/product-module-2.php';   
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Action
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 
add_action( 'wp_enqueue_scripts', 'hexwp_woocommerce_cart_fragments', 11); 
function hexwp_woocommerce_cart_fragments() {
	if (is_front_page()) wp_dequeue_script('wc-cart-fragments');
}
 
remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
add_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
if ( ! function_exists( 'woocommerce_template_loop_product_link_open' ) ) {
    function woocommerce_template_loop_product_link_open() {} 
} 




remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
if ( ! function_exists( 'woocommerce_template_loop_product_link_close' ) ) {
    function woocommerce_template_loop_product_link_close() {} 
} 



//***woocommerce_template_loop_price

remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price');
add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
if ( ! function_exists( 'woocommerce_template_loop_price' ) ) {
    function woocommerce_template_loop_price() {} 
} 



remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash');
add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
if ( ! function_exists( 'woocommerce_show_product_loop_sale_flash' ) ) {
    function woocommerce_show_product_loop_sale_flash() {} 
} 



remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail');
add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
if ( ! function_exists( 'woocommerce_template_loop_product_thumbnail' ) ) {
    function woocommerce_template_loop_product_thumbnail() {} 
} 

 
remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart');
add_action( 'woocommerce_after_shop_loop_item', 'hexwp_woocommerce_template_loop_add_to_cart', 10 );
     function hexwp_woocommerce_template_loop_add_to_cart() { } 
 
remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash');

 

remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating');

 add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 10 );
if ( ! function_exists( 'woocommerce_template_loop_rating' ) ) {
    function woocommerce_template_loop_rating() { } 
} 
 



remove_action('woocommerce_shop_loop_subcategory_title', 'woocommerce_template_loop_category_title');
 add_action( 'woocommerce_shop_loop_subcategory_title', 'woocommerce_template_loop_category_title', 10 );
if ( ! function_exists( 'woocommerce_template_loop_category_title' ) ) {
    function woocommerce_template_loop_category_title() { } 
} 




remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
 add_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_category_title', 10 );
 if ( ! function_exists( 'woocommerce_template_loop_product_title' ) ) {
    function woocommerce_template_loop_product_title() { } 
} 


 


remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
 
if ( ! function_exists( 'woocommerce_template_loop_product_thumbnail' ) ) {
    function woocommerce_template_loop_product_thumbnail() {} 
} 
 

 remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display', 10);
add_action( 'woocommerce_after_cart', 'woocommerce_cross_sell_display', 10);


/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Loop columns
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_filter('loop_shop_columns', 'hexwp_loop_columns');
if (!function_exists('hexwp_loop_columns')) {
	function hexwp_loop_columns() {
		return 4; // 4 products per row
	}
}  

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	loop shop per page
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_loop_shop_per_page( $cols ) {
  global $smof_data  ;
   if(!empty($_GET['product_number'])){
   $cols = $_GET['product_number'];
    }else{
   $cols = hexwp_option('product_number','12' );
   }
  return $cols;
}
add_filter( 'loop_shop_per_page', 'hexwp_loop_shop_per_page', 10 );
 
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Product Line
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_filter('woocommerce_single_product_summary','hexwp_product_line',21);
add_filter('woocommerce_single_product_summary','hexwp_product_line',39);
function hexwp_product_line( ) { 
	echo '<div class="hw-el-line"></div>';
 	 
}
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Single Product Summary
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_filter('woocommerce_single_product_summary','hexwp_single_product_countdown',20);
function hexwp_single_product_countdown( ) {
	if( hexwp_option('single_product_countdown')=='enbale'){
		hexwp_product_countdown();
	}
} 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Product Countdown
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 
function hexwp_product_countdown() {
    if ( function_exists( "is_woocommerce" ) ) {
        global $product;

        if ( $product->is_type( 'variable' ) ) {
            $variation_dates = array();

            // Get variations of the product
            $variations = $product->get_available_variations();

            // Loop through each variation
            foreach ( $variations as $variation ) {
                // Get sale dates for this variation
                $sale_from = get_post_meta( $variation['variation_id'], '_sale_price_dates_from', true );
                $sale_to = get_post_meta( $variation['variation_id'], '_sale_price_dates_to', true );

                // Add sale dates to the list
                if ( !empty( $sale_from ) && !empty( $sale_to ) ) {
                    $variation_dates[] = array(
                        'sale_from' => $sale_from,
                        'sale_to' => $sale_to
                    );
                }
            }

            // Find the latest sale end date
            $latest_end_date = '';
            foreach ( $variation_dates as $date ) {
                if ( $date['sale_to'] > $latest_end_date ) {
                    $latest_end_date = $date['sale_to'];
                }
            }

            // Display countdown if there's a sale end date
            if ( !empty( $latest_end_date ) ) {
                echo '<div class="hw-countdown" data-days="' . hexwp_t( 'days' ) . '" data-hours="' . hexwp_t( 'hours' ) . '" data-minutes="' . hexwp_t( 'minutes' ) . '" data-seconds="' . hexwp_t( 'seconds' ) . '" data-date="' . esc_attr( date( "Y-m-d", $latest_end_date ) ) . '"></div>';
            }
        } else {
            // For simple products
            $sales_price_to = get_post_meta( $product->get_id(), '_sale_price_dates_to', true );
            $sales_price_from = get_post_meta( $product->get_id(), '_sale_price_dates_from', true );
            $diff = 0;

            if ( !empty( $sales_price_from ) ) {
                $diff = current_time( 'timestamp' ) - 12600 - $sales_price_from;
            }

            if ( !empty( $sales_price_to ) && ( $diff >= 0 ) ) {
                echo '<div class="hw-countdown" data-days="' . hexwp_t( 'days' ) . '" data-hours="' . hexwp_t( 'hours' ) . '" data-minutes="' . hexwp_t( 'minutes' ) . '" data-seconds="' . hexwp_t( 'seconds' ) . '" data-date="' . esc_attr( date( "Y-m-d", $sales_price_to ) ) . '"></div>';
            }
        }
    }
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Product Countdown
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_product_sale(){
	global $product,$post;
	if ( $product->is_on_sale() ) :
	if(hexwp_option('product_onsale') =='percentage'){
		if( $product->is_type('variable')){
			$percentages = array();
			$prices = $product->get_variation_prices();
			foreach( $prices['price'] as $key => $price ){
				if( $prices['regular_price'][$key] !== $price ){
					$percentages[] = '-'.round(100 - ($prices['sale_price'][$key] / $prices['regular_price'][$key] * 100));
				}
			}
			$percentage = max($percentages) . '%';
		} else {
			$regular_price = (float) $product->get_regular_price();
			$sale_price    = (float) $product->get_sale_price();
			if(!empty($regular_price) && !empty($sale_price )){
			$percentage    = '-'.round(100 - ($sale_price / $regular_price * 100)) . '%';
			}else{
				$percentage='';
			}
		}
		$sale =hexwp_number_replace($percentage);
	}else if (hexwp_option('product_onsale') =='sale'){
		$sale =hexwp_t( 'salet');
		
	}else{
		$sale ='';
	}
  
	if(!empty($sale)){
	 
		echo apply_filters( 'woocommerce_sale_flash', '<span class="onsale">' .$sale . '</span>', $post, $product ); 
	}
	endif; 	
} 
 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Product Category
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function hexwp_product_category($category_layout=false) {
	$terms = wp_get_post_terms(get_the_ID(),"product_cat");
 	
	$class='';
    
 	
 	$count=0;
 	if(!empty($terms)){
	?>
 	<div class="hw-product-category">
	<?php foreach ( $terms as $term ) { $count++; ?>
		<a href="<?php echo esc_url(get_term_link($term->term_id)); ?>"><?php echo esc_html( $term->name);?></a>
			<?php 
 			if($count == 1) { 
			break;
			}
			
			} ?>
	</div>
  	
<?php  }
}
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Wishlist
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_wishlist() {
	if(  function_exists( 'yith_wishlist_constructor' ) ) {
 		echo do_shortcode( "[yith_wcwl_add_to_wishlist]" );
 	}
}
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Compare Button
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_compare_button() {
	if(  function_exists( 'yith_woocompare_constructor' ) ) {
  		global $post;
		$home = get_home_url();
 		echo '<div class=" compare-button"><a  class="compare button" href="'.esc_url($home.'?action=yith-woocompare-view-table&amp;iframe=yes').'"  data-product_id="'.esc_attr($post->ID).'" rel="nofollow"><div class="hw-text-hover">'.hexwp_t('compare').'</div></a></div>';
		
 	}
} 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Product Thumb
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_product_thumb( $option =false,$cart=false) { 
 	
	if(has_post_thumbnail()){
 		global $post,$product;
		$meta = get_post_meta( $post->ID );
		$thumb =  hexwp_data($option,'image_size','full');   
 		
		$style='';
 
 		$thumbnail= wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), $thumb );
 		$attachment_ids = version_compare(WC()->version, '3.0.0', '<') ? $product->get_gallery_image_ids() : $product->get_gallery_image_ids();
		$second_image =	hexwp_data($option,'second_image');   
		
		$has_second= '';	
 		if(!empty($second_image) && !empty($attachment_ids[0])){ 
            $second_thumbnail= wp_get_attachment_image_src($attachment_ids[0],$thumb );
			if(!empty($second_thumbnail[0])){
				$has_second='hw-thumb-second ';
 			}
		}
 		$the_permalink = get_permalink();
		if(!empty($thumbnail[0])){
  		?>
			<div class="hw-thumb <?php echo esc_attr($has_second);?>"> 
                
                <a <?php if(!empty($the_permalink)){?> href="<?php echo esc_url($the_permalink) ?>"  <?php } ?>  >
                
					<?php the_post_thumbnail($thumb,array('alt'=>esc_attr(get_the_title())));?>
                     
                    <?php if(hexwp_data($option,'ratio',hexwp_option('product_ratio'))!=='hw-ratio-auto'){?>
                        <figure class="hw-first-img" style="background-image:url('<?php echo get_the_post_thumbnail_url($post->ID, $thumb);?>');"></figure>
                    <?php }?>
                     
                     
                   <?php 
				   // second_image
				   if(!empty($second_image) && !empty($attachment_ids[0])){ 
						$second_thumbnail= wp_get_attachment_image_src($attachment_ids[0],$thumb );
                        
                        if(!empty($second_thumbnail[0])){
                            echo wp_get_attachment_image($attachment_ids[0],$thumb,'',array('class'=>'hw-second-img','alt'=>esc_attr(get_the_title())));
							
                            if(hexwp_data($option,'ratio',hexwp_option('product_ratio'))!=='hw-ratio-auto'){?>
                                <figure class="hw-second-img" style="background-image:url('<?php echo $second_thumbnail[0];?>');"></figure>
                            <?php
                            }
                                     
                        }
                    }
                	?>
                    
                </a>
			
			<?php 
            $stock = !empty($product->get_stock_status())?$product->get_stock_status():'';
            if($stock =='outofstock'){ ?>
            <div class="hw-outofstock"><?php echo hexwp_t('outofstock');?></div>
            <?php 
            }?>
 		 
            
            <?php
            if(!empty($cart)){
				$addcart = hexwp_data($option,'addcart');    
				if(!empty($addcart)) hexwp_product_button();
			}
			?>
 		</div>
	<?php
	}
	}

}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Product Post Title
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_product_post_title($option= false,$font=false) {
	 	$limit = hexwp_data($option,'title_limit');    

	$the_title = strip_tags(get_the_title());
  	if ( !empty($limit) && strlen($the_title) > $limit){
 		 $content= mb_substr($the_title, 0,$limit).'...';
		 
	} else {
		$content= $the_title;
		$dots='';
	}
  	?>
	<h3 class="hw-title"><a href="<?php the_permalink(); ?>"><?php echo esc_html($content);?></a></h3>
 	<?php 
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Product Price
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_product_price( $price ) {
	$out='';
	if(!empty($price)){
		$out='<div class="hw-price">';
		$out.='<span>';
		$out.=hexwp_number_replace($price);
		$out.='</span>';
		$out.='</div>';
	}
 	return $out;
} 
add_filter( 'woocommerce_get_price_html', 'hexwp_product_price' );
add_filter( 'woocommerce_cart_item_price', 'hexwp_product_price' );
add_filter( 'woocommerce_cart_item_subtotal', 'hexwp_product_price' ); // added
add_filter( 'woocommerce_cart_subtotal', 'hexwp_product_price' ); // added
add_filter( 'woocommerce_cart_total', 'hexwp_product_price' );
 function hexwp_price() {
 	global $product;
	echo hexwp_number_replace($product->get_price_html(true));  
 
}   
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Product Rating
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 function hexwp_product_rating() {
 	global $product;
 	$rating_review =  $product->get_average_rating()  ;
	?>
  
 	<div class="hw-rating">
		<?php if(!empty($rating_review)){?>
  		<?php echo wc_get_rating_html( $product->get_average_rating() );?>
		<?php }else{?>
        <div class="star-rating"><span> <strong class="rating"></strong> </span></div>
    <?php }?>
	</div>
 <?php
 } 
 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Product Excerpt
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_product_excerpt($option=false) {
 
	global $post;
	$limit = hexwp_data($option,'excerpt_limit');    
 	$the_excerpt = strip_tags(apply_filters( 'woocommerce_short_description', $post->post_excerpt ));
  	if ( !empty($limit) && strlen($the_excerpt) > $limit){
 		 $content= mb_substr($the_excerpt, 0,$limit).'...';
		 
	}else{
		$content=apply_filters( 'woocommerce_short_description', $post->post_excerpt );
 	}
 	if(!empty($content)){
 
	?>
 	<div class="hw-excerpt"><?php echo wp_kses_post($content); // WPCS: XSS ok. ?></div>
  
     <?php
	}
} 
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Button Add to Cart
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_button_add_to_cart() {
	?><div class="hw-button-cart"><?php woocommerce_template_loop_add_to_cart(); ?></div><?php
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Product Button
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_product_button() {
	?>
	<div class="hw-product-button">
		<?php 
        hexwp_button_add_to_cart();
        hexwp_wishlist();
        hexwp_compare_button();
        ?>
	</div>
	<?php   
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Product Button
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 
function hexwp_product_featured() {
		global $post,$product;
	if(!empty($product->get_featured())){
	?>
	<span class="hw-product-featured"><?php echo hexwp_t('hot');?></span>
	<?php   
	}
}
if( defined( 'YITH_WCWL' ) && ! function_exists( 'yith_wcwl_ajax_update_count' ) ){
function hexwp_yith_wcwl_ajax_update_count(){
	wp_send_json( array('count' => hexwp_number_replace(yith_wcwl_count_all_products())) );
}
add_action( 'wp_ajax_yith_wcwl_update_wishlist_count', 'hexwp_yith_wcwl_ajax_update_count' );
add_action( 'wp_ajax_nopriv_yith_wcwl_update_wishlist_count', 'hexwp_yith_wcwl_ajax_update_count' );
}
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	change number related products
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_filter( 'woocommerce_output_related_products_args', 'hexwp_change_number_related_products', 9999 );
function hexwp_change_number_related_products( $args ) {
	 $column=hexwp_option('related_product_column')?hexwp_option('related_product_column'):'4';
 $args['posts_per_page'] = $column; 
 $args['columns'] = $column; 
 return $args;
}
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	hexwp quantity  
 
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
add_action( 'woocommerce_after_quantity_input_field', 'hexwp_quantity_plus_sign' );
function hexwp_quantity_plus_sign() {
   echo '<div class="hw-qty-plus" >+</div>';
}
add_action( 'woocommerce_before_quantity_input_field', 'hexwp_quantity_minus_sign' );
function hexwp_quantity_minus_sign() {
   echo '<div  class="hw-qty-minus" >-</div>';
}
 
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Before Widget product list
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 
add_filter('woocommerce_before_widget_product_list','hexwp_before_widget_product_list',1);
add_filter('woocommerce_before_widget_product_review_list','hexwp_before_widget_product_list',1);
function hexwp_before_widget_product_list() {
	 return '<div class="hw-gap-20px hw-ratio-auto hw-gap-warp"><div class="hw-item-list hw-aw hw_img_width_25">';
 	 
 }
 add_filter('woocommerce_after_widget_product_list','hexwp_after_widget_product_list',1);
 add_filter('woocommerce_after_widget_product_review_list','hexwp_after_widget_product_list',1);
function hexwp_after_widget_product_list() {
	 return '</div></div>';
	 
 }
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Before Subcategory Thumbnail
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_subcategory_thumbnail( $category ) { 
            $small_thumbnail_size = apply_filters( 'subcategory_archive_thumbnail_size', 'shop_catalog' ); 
            $dimensions = wc_get_image_size( $small_thumbnail_size ); 
            $thumbnail_id = get_term_meta( $category->term_id, 'thumbnail_id', true ); 
         
            if ( $thumbnail_id ) { 
                $image = wp_get_attachment_image_src( $thumbnail_id, $small_thumbnail_size ); 
                $image = $image[0]; 
                $image_srcset = function_exists( 'wp_get_attachment_image_srcset' ) ? wp_get_attachment_image_srcset( $thumbnail_id, $small_thumbnail_size ) : false; 
                $image_sizes = function_exists( 'wp_get_attachment_image_sizes' ) ? wp_get_attachment_image_sizes( $thumbnail_id, $small_thumbnail_size ) : false; 
            } else { 
                $image = wc_placeholder_img_src(); 
                $image_srcset = $image_sizes = false; 
            } 
         
            if ( $image ) { 
                  $image = str_replace( ' ', '%20', $image ); 
         
                 if ( $image_srcset && $image_sizes ) {?>
                 
                   <img src="<?php echo esc_url( $image );?>" alt="<?php echo esc_attr( $category->name );?>" width="<?php echo esc_attr( $dimensions['width'] );?>" height="<?php echo esc_attr( $dimensions['height'] );?>" />
					<figure style="background-image:url('<?php  echo esc_url( $image );?>');"></figure>
               <?php } else { ?>
					<img src="<?php echo esc_url( $image );?>" alt="<?php echo esc_attr( $category->name );?>" width="<?php echo esc_attr( $dimensions['width'] );?>" height="<?php echo esc_attr( $dimensions['height'] );?>" />
					<figure style="background-image:url('<?php  echo esc_url( $image );?>');"></figure>
                    
                 <?php
                } 
            } 
} 