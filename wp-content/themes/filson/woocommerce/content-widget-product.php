<?php
/**
 * The template for displaying product widget entries.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-widget-product.php.
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 10003.5.5
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

if ( ! is_a( $product, 'WC_Product' ) ) {
	return;
}
?>
<div class="hw-item hw-module-1">
<div class="hw-post-product">
		<?php  hexwp_product_thumb();?>

 		<div class="hw-details">
 			<?php
  			hexwp_product_post_title();   
			hexwp_price();  
 			hexwp_product_rating(); 
  			?>
    
		</div>
  		 
 </div>
  </div>
 