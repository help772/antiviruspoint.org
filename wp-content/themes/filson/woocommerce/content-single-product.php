<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 10003.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked wc_print_notices - 10
 */
do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
}
 global $product;
 
$class= '';
$attachment_ids = version_compare(WC()->version, '3.0.0', '<') ? $product->get_gallery_image_ids() : $product->get_gallery_image_ids();

if(!empty($attachment_ids)){
	$class.=' hw-has-gallery';
}
 
$class.=' hw_img_width_'.hexwp_option('single_product_image_width');
?>

<aside class="hw-el-single-product hw-aw <?php echo hexwp_box_layout_single('product');?>">
	<div id="product-<?php the_ID(); ?>" <?php wc_product_class($class, $product); ?>>
 
	<?php do_action( 'woocommerce_before_single_product_summary' ); ?>

	<div class="hw-single-summary  hw-aw">
		<?php do_action( 'woocommerce_single_product_summary' );?>
	</div>

 
 	</div>
</aside>

<?php do_action( 'woocommerce_after_single_product_summary' ); ?>

<?php do_action( 'woocommerce_after_single_product' ); ?>
