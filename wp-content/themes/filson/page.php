<?php get_header() ;?>
   
<?php hexwp_breadcrumbs();?>

<div class="hw-middle-content">

 	<?php hexwp_above_content();?>

    <div class="hw-content <?php echo esc_attr(hexwp_column('column'));?>">
                  
		<?php if(!empty(hexwp_column(1))){get_template_part('sidebar-'.hexwp_column(1));} ?>
		<?php if(!empty(hexwp_column(2))){get_template_part('sidebar-'.hexwp_column(2));} ?>
 
		<div class="hw-column-main">
 	 
			<?php hexwp_above_center();?>

			<?php if ( have_posts() ) :?>
            <?php while ( have_posts() ) : the_post(); ?>
            
   				<div id="post-<?php the_ID(); ?>" class="hw-el-single hw-aw  <?php echo esc_attr(hexwp_box_layout_single());?>">
                             
                    <div class="hw-single-details">
                        <h1 class="hw-title"><?php the_title(); ?></h1>
                    </div>
                    
                    <?php if ( has_post_thumbnail()) {?>
                        <div class="hw-single-thumb">
                            <?php the_post_thumbnail('full'); ?>
                        </div>
                    <?php }?>
                 
                    <article class="hw-single-content">
                        <?php the_content();?>
                    </article>
                         
                    <?php hexwp_wp_link_pages();?>
                    <?php edit_post_link(hexwp_t('edit')); ?>
                       
            	</div> 
            <?php endwhile;?>
            <?php endif; ?>
       
            <?php comments_template( '', true ); ?>
                      
			<?php hexwp_below_center(); ?>
            
		</div>
		
		<?php if(!empty(hexwp_column(3))) get_template_part('sidebar-'.hexwp_column(3)); ?>
		<?php if(!empty(hexwp_column(4))) get_template_part('sidebar-'.hexwp_column(4)); ?>   
                      	 
	</div>
 	
	<?php hexwp_below_content();?>

</div>
 
<?php get_footer();?>
