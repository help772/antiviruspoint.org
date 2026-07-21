<?php

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		Nav Logo 
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_filter('hexwp_header_builder_logo', 'hexwp_nav_logo');
add_filter('hexwp_header_builder_mobile_logo', 'hexwp_nav_logo');
function hexwp_nav_logo($opt) {
 
	$logo = hexwp_isset($opt,'logo');
	$logo_image = wp_get_attachment_image_src($logo, 'full');
 	
	$logo_overlap = hexwp_isset($opt,'logo_overlap');
	$logo_overlap_image = wp_get_attachment_image_src($logo_overlap, 'full');
 	
	$logo_image_src=!empty($logo_image[0])?$logo_image[0]:hexwp_DIR.'/images/logo.png';
	if(!empty($logo_image_src)){
 	?>

	<div class="hw-nav-logo hw-nav-<?php echo hexwp_isset($opt,'key');?>">
         
             
				<a  title="<?php echo esc_attr(bloginfo('name')); ?>" href="<?php echo esc_url(home_url( '/') ); ?>">
					<img class="hw-logo-main" alt="<?php esc_url(bloginfo( 'name' )); ?>" src="<?php echo esc_url($logo_image_src); ?>" width="<?php echo hexwp_isset($opt,'logo_width');?>"  />
					
					<?php if(hexwp_header_overlap() == 'enable' && !empty($logo_overlap_image[0]) ){?>
					<img class="hw-logo-overlap" alt="<?php esc_url(bloginfo( 'name' )); ?>" src="<?php echo esc_url($logo_overlap_image[0]); ?>" width="<?php echo hexwp_isset($opt,'logo_width');?>"  />
					<?php }?>
				</a>
                
			 
            
 	</div>
<?php }
}
 