<?php
/**
 * Single Product Image
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 10009.0.0
 */

defined( 'ABSPATH' ) || exit;

// Note: `wc_get_gallery_image_html` was added in WC 3.3.2 and did not exist prior. This check protects against theme overrides being used on older versions of WC.
if ( ! function_exists( 'wc_get_gallery_image_html' ) ) {
	return;
}
 wp_enqueue_script( 'hexwp_zoomove' , hexwp_DIR.'/js/lib/zoomove.js');

global $product;

$columns           = apply_filters( 'woocommerce_product_thumbnails_columns', 4 );
$post_thumbnail_id = $product->get_image_id();
$wrapper_classes   = apply_filters(
	'woocommerce_single_product_image_gallery_classes',
	array(
		'hw-product-gallery',
		'hw-product-gallery--' . ( $product->get_image_id() ? 'with-images' : 'without-images' ),
		'hw-product-gallery--columns-' . absint( $columns ),
		'images',
	)
);
 $attachment_ids = version_compare(WC()->version, '3.0.0', '<') ? $product->get_gallery_image_ids() : $product->get_gallery_image_ids();
 
 

 ?>
 
<div class="hw-single-product-thumb" data-columns="<?php echo esc_attr( $columns ); ?>" >
		<?php  	$thumbnail= wp_get_attachment_image_src( $post_thumbnail_id, 'full' );
	
 		do_action( 'woocommerce_product_thumbnails' );

		if ( $product->get_image_id() ) {
			
			$html = wc_get_gallery_image_html( $post_thumbnail_id, true );?>
            
			<div class="hw-single-product-image">
            
				<?php echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, $post_thumbnail_id ); ?>
                  <div class="hw-product-thumb-resize"> <a class=" hw-product-lightbox" href="<?php echo esc_url($thumbnail[0]);?>" ></a></div>
 				<?php  woocommerce_show_product_sale_flash();?>

			</div>
             
             
        <?php }else {?> 
 			 
            <div class="hw-single-product-image">
     
                <div data-thumb="<?php echo esc_url( wc_placeholder_img_src( 'woocommerce_single' ) );?>" class="woocommerce-product-gallery__image">
                <a href="<?php echo esc_url( wc_placeholder_img_src( 'woocommerce_single' ) )?>"><img src="<?php echo esc_url( wc_placeholder_img_src( 'woocommerce_single' ) )?>"  ></a>
                </div>
                <div class="hw-product-thumb-resize"> <a class="hw-product-lightbox hw-singleimg-lightbox" href="<?php echo esc_url( wc_placeholder_img_src( 'woocommerce_single' ) )?>"></a></div>
                <?php  woocommerce_show_product_sale_flash();?>
            </div> 
  
	  <?php }?>

 </div>
