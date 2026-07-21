<?php
/**
 * Related Products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/related.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version 10003.9.0
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
 
$key = 'product';
$between =  hexwp_option('product_between');
$column=hexwp_option('related_product_column')?hexwp_option('related_product_column'):'4'; 
  
$option = array(
 	'layout'=>  'grid',
  	'grid_layout'=>  'grid_'.$column,
	'box_layout' => hexwp_option('product_box_layout'),
 	'post_title' => 1,
  	'title_limit' => hexwp_option('product_title_limit'),
	'excerpt' =>  0,
	'excerpt_limit' => 0,
 	'thumb' => hexwp_option('product_thumb'), 
 	'rating'  => hexwp_option('product_meta_rating'),
 	'addcart'  => hexwp_option('product_addcart',true),

 	'image_size'=>  hexwp_option('product_image_size'),
	'image_width'=>  hexwp_option('product_image_width'),
	'image_alignment'=>  hexwp_option('product_image_alignment' ),
	'second_image'=>  hexwp_option('product_second_image',true),
  	'alignment'=> hexwp_option('product_alignment'),
 	'meta_category'  => hexwp_option('product_meta_category',true),
   	
  ); 
  
$between_class =hexwp_option('product_between');
$ratio_class =hexwp_option('product_ratio');
$alignment_class =hexwp_option('product_alignment');
$second_image_class =hexwp_option('product_second_image')?'hw-has-second':'';
$box_layout =hexwp_option('product_box_layout');
$box_layout_class = 'hw-'.$box_layout;
	
 
$classes = array(
  	'hw-el-product',
	'hw-gap-'.$between_class,
	$ratio_class,
	hexwp_between_border($option,$box_layout ),
	$second_image_class,
	'hw-align-'.hexwp_alignment($alignment_class),
	$box_layout_class,
);
	
	
if ( $related_products ) :	?>

<section class="<?php echo esc_attr(join( ' ', $classes ));?>" >
	 
	<?php $title_class= hexwp_option('title_box_style');?>
    
	 <div class="hw-tbox-<?php echo esc_attr($title_class);?>">		
 		<h4 class="hw-title-box"><div class="hw-tab-main"><span><?php echo hexwp_t( 'relatedproducts' );?></span></div></h4>
	</div>
	<div class="hw-gap-content">
	<div class="hw-gap-warp" >
		<div class="hw-item-list hw-aw hw-flex <?php echo hexwp_post_class($option);?> products"  >

 			<?php foreach ( $related_products as $related_product ) :  ?>
 				 
				<?php $post_object = get_post( $related_product->get_id() );?>
 				<?php setup_postdata( $GLOBALS['post'] =& $post_object );?>
				<?php hexwp_product_module_2($option);?>
 
			<?php endforeach; ?>
		
        </div>
	
    </div>
    </div>
 
</section>
<?php
endif;

wp_reset_postdata();
