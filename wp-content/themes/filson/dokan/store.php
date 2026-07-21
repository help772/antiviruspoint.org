<?php
/**
 * The Template for displaying all single posts.
 *
 * @package dokan
 * @package dokan - 2014 1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$store_user   = dokan()->vendor->get( get_query_var( 'author' ) );
$store_info   = $store_user->get_shop_info();
$map_location = $store_user->get_location();

get_header( 'shop' );

?>

<div class="hw-middle-content hw-dokan-store">
  
	<div class="hw-content hw-main">
 
 		<div class="hw-column-main">
 
		<div id="dokan-primary" class="dokan-single-store">
        <div id="dokan-content" class="store-page-wrap woocommerce" role="main">

			<div class="hw-el-product">
            <?php dokan_get_template_part( 'store-header' ); ?>
            </div>
             
            <?php
			$title_class= hexwp_option('title_box_style');
			$column=hexwp_option('related_product_column')?hexwp_option('related_product_column'):'4'; 

			$option = array(
				'layout'=>  'grid',
				'grid_layout'=>  'grid_4',
				'box_layout' => hexwp_option('product_box_layout'),
				'post_title' => 1,
				'title_limit' => hexwp_option('product_title_limit'),
				'excerpt' =>  0,
				'excerpt_limit' => 0,
				'list_layout' 		=>  'list_'.$column,
				'grid_layout'	 	=>  'grid_'.$column,				
  				'responsive_column' => hexwp_option('product_responsive_column'), 
				'thumb' => hexwp_option('product_thumb'), 
				'rating'  => hexwp_option('product_meta_rating',true),
				'addcart'  => hexwp_option('product_addcart',true),			
				'image_size'=>  hexwp_option('product_image_size'),
				'image_width'=>  hexwp_option('product_image_width',true),
				'image_alignment'=>  hexwp_option('product_image_alignment' ),
				'second_image'=>  hexwp_option('product_second_image' ),
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
				$second_image_class,
						hexwp_between_border($option,$box_layout ),
	
				'hw-align-'.hexwp_alignment($alignment_class),
				$box_layout_class,
			);
		?> 
    				 
 
			<section class="<?php echo esc_attr(join( ' ', $classes ));?>" data-key="shop">
                 <div class="hw-tbox-<?php echo esc_attr($title_class);?>">
                    <?php do_action( 'dokan_store_profile_frame_after', $store_user->data, $store_info ); ?>
                 </div>		

				<?php if ( have_posts() ) { ?>
						<div class="hw-gap-content">
                 	<div class="hw-gap-warp" >
						<div class="hw-item-list hw-aw hw-flex <?php echo hexwp_post_class($option);?> products"  >
          
                        <?php while ( have_posts() ) : the_post(); ?>
                            <?php wc_get_template_part( 'content', 'product' ); ?>
                        <?php endwhile; ?>
                        
						</div> 
                		<?php dokan_content_nav( 'nav-below' ); ?>
					</div>
					</div>
            	<?php } else { ?>

                	<p class="dokan-info"><?php esc_html_e( 'No products were found of this vendor!', 'hexwp' ); ?></p>

            	<?php } ?>

			</section>
 		</div>   
		</div> 
		</div>   
	</div>   
</div> 
<?php get_footer( 'shop' ); ?>
