<?php
/**
 * Single Product Thumbnails
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-thumbnails.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce/Templates
 * @version 10003.5.1
 */

defined( 'ABSPATH' ) || exit;

// Note: `wc_get_gallery_image_html` was added in WC 3.3.2 and did not exist prior. This check protects against theme overrides being used on older versions of WC.
if ( ! function_exists( 'wc_get_gallery_image_html' ) ) {
	return;
}
  		wp_enqueue_script( 'hexwp_product_gallery' , hexwp_DIR.'/js/lib/product-gallery.js');


global $product;
$count=0;
 $attachment_ids = version_compare(WC()->version, '3.0.0', '<') ? $product->get_gallery_image_ids() : $product->get_gallery_image_ids();
 
 if ( !empty($attachment_ids) ) {

$count_attachment_ids = count( $attachment_ids ) +1;
  
$attachment_ids = $product->get_gallery_image_ids();
$count++;
?>
<div class="hw-has-stick-arrow  hw-product-thumbnails-warp" data-item="<?php echo hexwp_option('single_product_gallery_item');?>">
 
 		<div class="hw-slider-prev"></div>
 	
    <div class="hw-product-thumbnails-list">
  		<?php 
		if($count==1){
			$lighbox='hw-slick-current';
		}else{
			$lighbox='';
		}?>

 		<?php $first_attributes = wp_get_attachment_image_src( get_post_thumbnail_id(),'full'); ?>
     		<?php $first_image = wp_get_attachment_image_src( get_post_thumbnail_id(),'thumbnail'); 
 			if(!empty($first_image[0])){?>
                <a class="hw-product-lightbox <?php echo esc_attr($lighbox);?>" href="<?php echo esc_url($first_attributes[0]);?>"    >
              	  <img src="<?php echo esc_url($first_image[0]);?>" width="<?php echo esc_attr($first_image[1]);?>" height="<?php echo esc_attr($first_image[2]);?>" >
					<figure style="--hw-bg:url('<?php echo esc_url($first_image[0]);?>');"></figure>

                </a>
            <?php } else{?>
                <a class="hw-product-lightbox <?php echo $lighbox;?>" href="<?php echo esc_url( wc_placeholder_img_src( 'woocommerce_single' ) );?>" >
					<img src="<?php echo esc_url( wc_placeholder_img_src( 'woocommerce_single' ) );?>"  >
					<figure style="--hw-bg:url('<?php echo esc_url( wc_placeholder_img_src( 'woocommerce_single' ) );?>');"></figure>
   				</a>
                
			<?php } ?>
 

		<?php foreach ( $attachment_ids as $attachment_id ) {?>
            
            <?php $image_attributes = wp_get_attachment_image_src( $attachment_id,'full');
			if(!empty($image_attributes[0])){
			?>
            <a class="hw-product-lightbox" href="<?php echo esc_url($image_attributes[0]);?>">
                <?php $image_attributes = wp_get_attachment_image_src( $attachment_id,'thumbnail');?>
                <img src="<?php echo esc_url($image_attributes[0]);?>"  width="<?php echo esc_attr($image_attributes[1]);?>" height="<?php echo esc_attr($image_attributes[2]);?>"   >
				<figure style="--hw-bg:url('<?php echo esc_url($image_attributes[0]);?>');"></figure>

            </a>
        <?php }} ?>
 	</div> 
         <div class="hw-slider-next"></div>
  
</div>
<?php }?>