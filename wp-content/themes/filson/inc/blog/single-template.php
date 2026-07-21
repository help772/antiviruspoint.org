<?php 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
																		
																		Sinle Template
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$video_class=hexwp_meta('video') ?'hw-post-video':''; ?>
  
<?php if ( have_posts() ) :?>
<?php while ( have_posts() ) : the_post(); ?>
         
 	<div id="post-<?php the_ID(); ?>" class="hw-el-single hw-aw <?php echo esc_attr($video_class); ?>   <?php echo esc_attr(hexwp_box_layout_single());?>">
				 
              
		<?php 
		if( hexwp_single() =='1' || hexwp_single() =='2'|| hexwp_single() =='3'|| hexwp_single() =='4' ){?>
                        
			<div class="hw-single-details">
				<h1 class="hw-title"><?php the_title(); ?></h1>
				<?php  hexwp_single_meta();  ?>
			</div>
            
			<?php 
            if( hexwp_single() !=='4' &&  has_post_thumbnail() && !is_attachment() ) {?>
                 <div class="hw-single-thumb"><?php the_post_thumbnail('full',array('alt'=>esc_attr(get_the_title()))); ?></div>
             <?php 
            }
             ?>
 
                             
 		<?php }?>
        
		<article class="hw-single-content entry-content ">
			<?php the_content();?>
		</article>
                 
		<?php hexwp_wp_link_pages();?>
		<?php edit_post_link(hexwp_t('edit')); ?>
		<?php hexwp_tags();  ?>
		<?php hexwp_share_post(); ?>
                  
	</div>
    
    
    
<?php endwhile;?>
<?php endif; ?>

<?php hexwp_below_article();?>

<?php hexwp_author_box();  ?> 
    
<?php get_template_part('inc/blog/blog-related'); ?> 
    
<?php comments_template( '', true ); ?>
