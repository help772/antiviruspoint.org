<?php get_header();?>
<?php hexwp_breadcrumbs();?>


<div class="hw-middle-content">
 
	<div class="hw-content hw-woocommerce-content <?php  echo hexwp_column('column');?>">
                  
		<?php if(hexwp_column(1)){get_template_part('sidebar-'.hexwp_column(1));} ?>
		<?php if(hexwp_column(2)){get_template_part('sidebar-'.hexwp_column(2));} ?>
 		<div class="hw-column-main">
				<?php
				wp_reset_query();
    
				if ( is_singular( 'product' ) ) {
					get_template_part('woocommerce/content-single-loop');// Include Blog  
				} else {
					get_template_part('woocommerce/content-product-loop');// Include Blog  
				}
				?>
		</div>
                 
		<?php if( hexwp_column(3)) get_template_part('sidebar-'.hexwp_column(3)); ?>
		<?php if( hexwp_column(4)) get_template_part('sidebar-'.hexwp_column(4)); ?>    
                     	 
	</div>
	 
 </div>
 
 <?php get_footer();?>
