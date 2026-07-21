<?php 
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																Head Singe Temeplate
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
global $post; 
 ?>
 
<?php if ( have_posts() ) : ?>
<?php while ( have_posts() ) : the_post();


 
 	$thumbnail= wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );

	 ?>
	<div class="hw-el-head-single  hw-aw " style="--hw-bg:url('<?php echo get_the_post_thumbnail_url($post->ID, 'full');?>');">
 		<div class="hw-inner">


			<?php the_post_thumbnail( 'full',array('alt'=>esc_attr(get_the_title())));?>
           
             
                       
			<div class="hw-single-details">
				<h1 class="hw-title"><?php the_title(); ?></h1>
				<?php hexwp_single_meta(); ?>
				<?php hexwp_share_post(); ?>
			</div>
			<?php                

			if( hexwp_meta('video') ){
                hexwp_video();
            } ?> 
            
		</div>
 	</div>
<?php endwhile;?>
<?php endif; ?>
