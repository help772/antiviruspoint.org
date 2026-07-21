<?php get_header();?>
<?php hexwp_breadcrumbs();?>

<div class="hw-middle-content">

 	<?php hexwp_above_content();?>
	
    <div class="hw-content <?php echo esc_attr(hexwp_column('column'));?>">
                  
		<?php if(!empty(hexwp_column(1))){get_template_part('sidebar-'.hexwp_column(1));} ?>
		<?php if(!empty(hexwp_column(2))){get_template_part('sidebar-'.hexwp_column(2));} ?>
 		
        <div class="hw-column-main">  
			<?php hexwp_above_center();?>

                    <?php 
                    if ( have_posts() ) :
						ob_start();
						printf( esc_html(hexwp_t('search_for').' %s'), esc_html( get_search_query() ) ); 
					
                    	hexwp_blog_archive(ob_get_clean()) ;
                    
					else :
 						
					 $class = ' hw-tbox-'.hexwp_option('title_box_style');
 
				?>
					<div class="hw-el-archive  hw-gap-<?php echo hexwp_option('blog_between');?> hw-<?php echo hexwp_option('new_blog_box_layout');?>">

                        <div class="hw-tbox-<?php echo hexwp_option('title_box_style');?>">
                        <h4>
                  	      <div class="hw-tab-main"><span><?php printf( esc_html(hexwp_t('search_for').' %s'), esc_html( get_search_query() ) ); ?></span></div>
                        </h4>
						</div> 

						<div class="hw-gap-content">
       					<div class="hw-gap-warp">
                            <div class="hw-item-list hw-aw">
                            <div class="hw-item   hw-module-2"><div class="hw-post-blog"><p><?php echo esc_html(hexwp_t('sorry'));?></p><?php  get_search_form(); ?></div></div>
                            </div>
                        </div>
                        </div>
                        
                         
 					</div>
 
				<?php endif;?>
                
			<?php hexwp_below_center(); ?>

 		</div>
		
		<?php if(!empty(hexwp_column(3))) get_template_part('sidebar-'.hexwp_column(3)); ?>
		<?php if(!empty(hexwp_column(4))) get_template_part('sidebar-'.hexwp_column(4)); ?>   
                      	 
	</div>
 	
	<?php hexwp_below_content();?>

</div>
 
<?php get_footer();?>

   