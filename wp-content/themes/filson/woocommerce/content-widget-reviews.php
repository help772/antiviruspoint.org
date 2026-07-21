<?php
/**
 * The template for displaying product widget entries.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-widget-reviews.php
 * 
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 10003.4.0
 */

defined( 'ABSPATH' ) || exit;

?>
 
 
<div class="hw-item hw-module-1" >
<div class="hw-post-product" >

     
	<div class="hw-thumb">
		<a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>"><?php echo wp_kses_post($product->get_image()); ?></a>
	</div>
		<div class="hw-details">
		<h3 class="hw-title"><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>"><?php echo wp_kses_post($product->get_name()); ?></a></h3>
         
		<div class="hw-rating"><?php echo wc_get_rating_html( intval( get_comment_meta( $comment->comment_ID, 'rating', true ) ) );?></div>
 
		<span class="hw-meta"><?php echo sprintf( esc_html__( 'by %s', 'hexwp' ), get_comment_author( $comment->comment_ID ) ); ?></span>
	</div>
      
</div>
</div>
 

