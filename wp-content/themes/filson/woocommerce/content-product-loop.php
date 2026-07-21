<?php
$layout = !empty($_GET['product_layout'])? $_GET['product_layout']:hexwp_option('product_layout');
$key = 'product';
$model = $layout == 'list_1'? 'list':'grid';
$list_layout = hexwp_option('product_list_layout');
$grid_layout = hexwp_option('product_grid_layout');
$between_class =hexwp_option('product_between');
$ratio_class =hexwp_option('product_ratio');
$alignment_class =hexwp_option('product_alignment');
$second_image_class =hexwp_option('product_second_image',true)?'hw-has-second':'';
$box_layout =hexwp_option('product_box_layout');
$box_layout_class = 'hw-'.$box_layout;

$option=array(
	'layout'=>!empty($_GET['product_layout'])? $_GET['product_layout']:hexwp_option('product_layout'),
	'list_layout'=>hexwp_option('product_list_layout'),
	'grid_layout'=>hexwp_option('product_grid_layout'),
);
	
if($layout=='grid'){
	$option['responsive_column']=hexwp_option('product_responsive_column');
}
	
$layout_class='';
if($layout=='list'){
	$layout_class ='hw_img_width_'.hexwp_option('product_image_width');
} 
$classes = array(
	'hw-el-shop',
	'woocommerce',
	'hw-gap-'.$between_class,
	$ratio_class,$layout_class,
			hexwp_between_border($option,$box_layout ),
	
	$second_image_class,
	'hw-align-'.hexwp_alignment($alignment_class),
	$box_layout_class,
);
		 
?> 
    				 
<section class="hw-el-product <?php echo esc_attr(join( ' ', $classes ));?>" data-key="shop">

 	<?php   
  	$woocommerce_archive_description =get_the_archive_description();
	$class_woocommerce_archive_description =!empty($woocommerce_archive_description)?'hw-has-archive':'';
	$title_class= hexwp_option('title_box_style');?>
    
	 <div class="hw-tbox-<?php echo esc_attr($title_class).' '.esc_attr($class_woocommerce_archive_description);?> hw-has-orderby">
		
         <h1 class="hw-title-box"> 
 			<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
				<div class="hw-tab-main">
				<span><?php woocommerce_page_title();?></span></div>
			<?php endif; ?> 
 		</h1>
        
        
		<?php if(is_product_category()||is_product_tag() || is_product_taxonomy() && !empty($woocommerce_archive_description)){?>
			<div class="hw-archive-description">
				<?php echo wp_kses_post($woocommerce_archive_description);?>
			</div>
		<?php } ?>
        
 		 <div class="hw-orderby">
			<?php do_action( 'woocommerce_before_shop_loop' );?>
		 </div>
	
	</div>
    
						<div class="hw-gap-content">
	<div class="hw-gap-warp" >
		<?php if ( woocommerce_product_loop() ) : ?>
			<div class="hw-item-list hw-aw hw-flex <?php echo hexwp_post_class($option);?> products "  >
  
				<?php woocommerce_product_loop_start(); ?>
    
                <?php
                if ( wc_get_loop_prop( 'total' ) ) : 
                while ( have_posts() ) :  the_post();
					wc_get_template_part( 'content', 'product' ); 
                endwhile;  
                endif; 			
				woocommerce_product_loop_end(); 
                ?>
            
			</div> 
                 
			<?php do_action( 'woocommerce_after_shop_loop' ); ?>
 
		<?php else :?> 

			<?php do_action( 'woocommerce_no_products_found' ); ?>

		<?php endif;?>		 
 	</div> 
 	</div> 
    
</section>