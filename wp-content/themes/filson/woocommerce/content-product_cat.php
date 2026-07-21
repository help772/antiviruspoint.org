<?php
/**
 * The template for displaying product category thumbnails within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product_cat.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 10004.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

	
global $product,$post;
?>
 <div class="hw-item hw-module-2"> 
	<div class="hw-post-categories" >
        <div class="hw-thumb"><a href="<?php echo esc_url( get_term_link( $category, 'product_cat' ) ) ;?>"><?php hexwp_subcategory_thumbnail($category) ;?></a></div>
        
		<div class="hw-details">
            <h3 class="hw-title woocommerce-loop-category__title">
                <a href="<?php echo esc_url( get_term_link( $category, 'product_cat' ) ) ;?>"><?php echo  esc_html( $category->name );?><?php
                    if ( $category->count > 0 ) {
                        echo apply_filters( 'woocommerce_subcategory_count_html', ' <div class="count">(<span> ' . esc_html( $category->count ) . ' </span>)</div>', $category );
                    }
                ?></a>
            </h3>
		</div>
	</div>
</div>
