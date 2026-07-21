<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Nav Cart
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_filter('hexwp_header_builder_cart', 'hexwp_nav_cart');
add_filter('hexwp_header_builder_mobile_cart', 'hexwp_nav_cart');
function hexwp_nav_cart($opt=array()){
 	if ( function_exists ( "is_woocommerce" )) {
	$layout =  hexwp_isset($opt,'layout',hexwp_nav_default('cart_layout'));
	$class = hexwp_isset($opt,'boxed_layout')?' hw-nav-boxed ':'';
	$class.= hexwp_isset($opt,'icon_layout')?' hw-nav-icn-boxed ':'';;
 		
   	$classes = array(
 		'hw-nav-cart',
		'hw-nav-layout-'.$layout,
 		$class,
  		'hw-nav-'.hexwp_isset($opt,'key'),
		hexwp_isset($opt,'side'),
  	);
 	?>
		<div class="<?php echo esc_attr(join( ' ', $classes ));?>"  >	
 			<div class="widget_shopping_cart_content hw-middle">
			<?php woocommerce_mini_cart();?>
            </div>
 		</div>
	<?php 
	}
}

			 
 
 
/******************************************************************************************************************************************************
 
																	Nav Mini Cart
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 function hexwp_mini_cart() {
	 
	if ( function_exists ( "is_woocommerce" )) {
		
 		do_action( 'woocommerce_before_mini_cart' );  
		$item_count = WC()->cart->cart_contents_count; 
		
			echo '<a class="hw-link">';
			  echo'<div class="hw-count"><span>'.hexwp_number_replace(esc_html($item_count)).'</span></div>';
		  
 					  echo '<span class="hw-twoline">';
					  echo '<span>'.hexwp_t('mycart').'</span>';
					  echo '<span>'.hexwp_number_replace(WC()->cart->get_cart_subtotal()).'</span>';
					  echo '</span>';
				   
					 
			 echo '</a>';
			 
			 ?>
			 
			<ul class="hw-cart-warp hw-drop">
            
				<?php if ( ! WC()->cart->is_empty() ){?>
				<?php hexwp_mini_cart_product();?>
                
				<div class="hw-cart-stats"> 
                
                
					<div class="hw-cart-total"><strong><?php echo hexwp_t( 'subtotal').':';?></strong><span><?php echo hexwp_number_replace(WC()->cart->get_cart_subtotal());?></span></div>
                    
					<?php do_action( 'woocommerce_widget_shopping_cart_before_buttons' );?>
                    
					<div class="hw-cart-buttons"><?php do_action( 'woocommerce_widget_shopping_cart_buttons');?></div>
                    
					<?php do_action( 'woocommerce_widget_shopping_cart_after_buttons' );?>
                    
                    
				</div>
				<?php }else { ?>
					<div class="hw-cart-stats"><?php echo hexwp_t( 'noproducts' ); ?></div>
				<?php } ?>
		 
			</ul>
	  
		<?php
	}
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Nav Mini Cart Product
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_mini_cart_product(){?>
 
	<ul class="hw-cart-list">
    
		<?php
		do_action( 'woocommerce_before_mini_cart_contents' );

		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
				$product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

				if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
					$product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
					$thumbnail         = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
 					$product_price     = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
					$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
					?>
					<li class="woocommerce-mini-cart-item <?php echo esc_attr( apply_filters( 'woocommerce_mini_cart_item_class', 'mini_cart_item', $cart_item, $cart_item_key ) ); ?>">
						<?php
						echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf(
							'<a href="%s" class="remove remove_from_cart_button" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s">&times;</a>',
							esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
							hexwp_t( 'removethisitem'),
							esc_attr( $product_id ),
							esc_attr( $cart_item_key ),
							esc_attr( $_product->get_sku() )
						), $cart_item_key );
						?>
 						<?php if ( empty( $product_permalink ) ) : ?>
							<?php echo wp_kses_post($thumbnail . $product_name . '&nbsp;'); ?>
						<?php else : ?>
							<div class="hw-cart-thumb"> 
                            	 <a href="<?php echo esc_url( $product_permalink ); ?>" >
									<?php echo wp_kses_post($thumbnail); ?>
                                  </a>
                              </div>
                          
						<?php endif; ?>
                        <div class="hw-cart-details">
                        
                        	<a href="<?php echo esc_url( $product_permalink ); ?>"><?php echo wp_kses_post($product_name); ?></a>
 							<div class="hw-cart-price">
                            
								<?php echo wc_get_formatted_cart_item_data( $cart_item ); ?>
                                <?php echo hexwp_number_replace(apply_filters( 'woocommerce_widget_cart_item_quantity', '<span class="quantity">' . sprintf( '%s &times; %s', $cart_item['quantity'], $product_price ) . '</span>', $cart_item, $cart_item_key )); ?>
                                
							</div>
                            
						</div>
                         
 					</li>
 					<?php
				}
			}

		do_action( 'woocommerce_mini_cart_contents' );
		?>
	</ul>
 <?php
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Woccomrce ajax add to cart
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_action('wp_ajax_woocommerce_ajax_add_to_cart', 'hexwp_woocommerce_ajax_add_to_cart');
add_action('wp_ajax_nopriv_woocommerce_ajax_add_to_cart', 'hexwp_woocommerce_ajax_add_to_cart');
function hexwp_woocommerce_ajax_add_to_cart() {

	$product_id = apply_filters('woocommerce_add_to_cart_product_id', absint($_POST['product_id']));
	$quantity = empty($_POST['quantity']) ? 1 : wc_stock_amount($_POST['quantity']);
	$variation_id = absint($_POST['variation_id']);
	$passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity);
	$product_status = get_post_status($product_id);
	
	if ($passed_validation && WC()->cart->add_to_cart($product_id, $quantity, $variation_id) && 'publish' === $product_status) {

		do_action('woocommerce_ajax_added_to_cart', $product_id);

		if ('yes' === get_option('woocommerce_cart_redirect_after_add')) {
			wc_add_to_cart_message(array($product_id => $quantity), true);
		}

		WC_AJAX :: get_refreshed_fragments();
	} else {
		
 		$data = array(
		'error' => true,
		'product_url' => apply_filters('woocommerce_cart_redirect_after_error', get_permalink($product_id), $product_id));
 		echo wp_send_json($data);
	}

	wp_die();
} 