<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
get_header() ?>
 
<?php 
  global $post;
  
		


	$setting_json = get_post_meta($post->ID, 'vs_setting_json', true);
  	$setting= vs_options_array_row($setting_json);
 
 
 	 
	$slide_json = get_post_meta($post->ID, 'vs_slide', true);  
	$slide= vs_options_array_row($slide_json);

		 
	?>
	<div  class="vs-elementor-item">
   		<?php vs_global_config($post->ID, $setting,$slide);?>
	</div> 
<?php get_footer(); ?>
