<?php
add_filter('hexwp_header_builder_banner', 'hexwp_banner_header');
add_filter('hexwp_header_builder_mobile_banner', 'hexwp_banner_header');

function hexwp_banner_header($opt=array()) {
   
   	$banner = hexwp_isset($opt,'banner_header_img');
	$banner_image = wp_get_attachment_image_src($banner, 'full');
 	
 	$banner_header_url = hexwp_isset($opt,'banner_header_url');
	$banner_header_window = !empty(hexwp_isset($opt,'banner_header_window')) ? 'target="_blank"':'';
	$banner_header_nofollow = !empty(hexwp_isset($opt,'banner_header_nofollow')) ? 'rel="nofollow"':'';
     if(!empty($banner_image[0])){ ?>

     <div class="hw-nav-banner-header <?php echo hexwp_isset($opt,'side');?>">
    
             <a href="<?php echo hexwp_isset($opt,'banner_header_url'); ?>"  <?php echo wp_kses_post($banner_header_window.' '.$banner_header_nofollow);?>   >
                <img alt="#" src="<?php echo esc_url($banner_image[0]); ?>" />
            </a>
		 
 	</div>
 	
<?php  }
}
