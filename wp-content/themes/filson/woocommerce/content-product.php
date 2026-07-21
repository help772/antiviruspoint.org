<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
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
	wp_reset_query();

global $product;

// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}

 
$layout =  !empty($_GET['product_layout'])? $_GET['product_layout']:hexwp_option('product_layout');
$list_layout = hexwp_option('product_list_layout');
$grid_layout = hexwp_option('product_grid_layout');
if( hexwp_option('product_excerpt')=='enbale'){
	
	 $product_excerpt = true;
}else{
	 $product_excerpt =false;
}
  $options =array(
	'post_title' => 1,
 	'title_limit' => hexwp_option('product_title_limit'),
	'excerpt' =>    $layout =='list'? true:false,
	'excerpt_limit' =>  hexwp_option('product_excerpt_limit'),
 	'responsive_column' => hexwp_option('product_responsive_column'), 
 	'thumb' => hexwp_option('product_thumb'), 
 	'rating'  => hexwp_option('product_meta_rating'),
 	'addcart'  => hexwp_option('product_addcart',true),
  	'image_size'=>  hexwp_option('product_image_size'),
	'image_width'=>  hexwp_option('product_image_width'),
 	'second_image'=>  hexwp_option('product_second_image',true),
   	'alignment'=> hexwp_option('product_alignment'),
 	'meta_category'  => hexwp_option('product_meta_category',true),
 	'box_layout'=> hexwp_option('product_box_layout'),
 	
  );  
 
if($layout=='list'){			
	hexwp_product_module_1($options);
		 
}else{
	hexwp_product_module_2($options);
  
}

	
	?> 