<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
 include_once VISUALSLIDER_PATH . 'admin/setting-options.php';
include_once VISUALSLIDER_PATH . 'admin/slide-options.php';
 
include_once VISUALSLIDER_PATH . 'admin/includes/metabox.php';

include_once VISUALSLIDER_PATH . 'admin/includes/options-functions.php';
include_once VISUALSLIDER_PATH . 'admin/includes/setting.php';
include_once VISUALSLIDER_PATH . 'admin/includes/slide.php';
include_once VISUALSLIDER_PATH . 'admin/includes/layer.php';
include_once VISUALSLIDER_PATH . 'admin/includes/template.php';
include_once VISUALSLIDER_PATH . 'admin/includes/perview-global.php';
include_once VISUALSLIDER_PATH . 'admin/includes/perview-slide.php';
 include_once VISUALSLIDER_PATH . 'admin/includes/perview-glider-css.php';
include_once VISUALSLIDER_PATH . 'admin/includes/perview-glider-tablet-css.php';
include_once VISUALSLIDER_PATH . 'admin/includes/perview-glider-mobile-css.php';
include_once VISUALSLIDER_PATH . 'admin/custom-css.php';
include_once VISUALSLIDER_PATH . 'admin/slider-array.php';

  include_once VISUALSLIDER_PATH . 'admin/fonts/fa-icon.php'; 
include_once VISUALSLIDER_PATH . 'admin/fonts/flaticon.php';
include_once VISUALSLIDER_PATH . 'admin/fonts/flaticon_thin.php';
include_once VISUALSLIDER_PATH . 'admin/fonts/metrizeicon.php';
include_once VISUALSLIDER_PATH . 'admin/fonts/typcn.php';
include_once VISUALSLIDER_PATH . 'admin/fonts/googlefont.php';

 
 
if( ! function_exists( 'vs_admin_shortcode' ) ) {
add_action('admin_enqueue_scripts', 'vs_admin_shortcode');
function vs_admin_shortcode($hook) {
	$var ='1.0';
	wp_enqueue_style( 'vs_glider',VISUALSLIDER_DIR .'assets/css/glider.css',array(),$var,false); 

  	wp_enqueue_style( 'vs_slider',VISUALSLIDER_DIR .'assets/css/slider.css',array(),$var,false); 
	wp_enqueue_style( 'vs_layer',VISUALSLIDER_DIR .'assets/css/layer.css',array(),$var,false); 
 
 	wp_enqueue_script( 'vs_slider', VISUALSLIDER_DIR .'assets/js/slider.js',array('jquery'),$var ,false); 
  	wp_enqueue_script( 'vs_script', VISUALSLIDER_DIR .'assets/js/script.js' ,array('jquery'),$var,false); 
	 

 
}
}


 if( ! function_exists( 'vs_admin_enqueue' ) ) {
add_action('admin_enqueue_scripts', 'vs_admin_enqueue');
function vs_admin_enqueue($hook) {
	$var =wp_rand(0,9999999);
	
	global $pagenow;
	if ( is_admin() && in_array( $pagenow, array( 'post-new.php', 'post.php') )&& get_post_type() ==  'visualslider') {
	
		wp_enqueue_style('vs_fontawesome',VISUALSLIDER_DIR. 'assets/css/fonts/fontawesome.css','',$var,false);
		wp_enqueue_style('vs_admin_style',VISUALSLIDER_DIR.  'admin/assets/css/admin-style.css',array(),$var,false);
		wp_enqueue_style('vs_admin_options',VISUALSLIDER_DIR.  'admin/assets/css/admin-options.css',array(),$var,false);
		wp_enqueue_style('vs_admin_layer',VISUALSLIDER_DIR.  'admin/assets/css/admin-layer.css',array(),$var,false);
		wp_enqueue_style('vs_admin_setting',VISUALSLIDER_DIR.  'admin/assets/css/admin-setting.css',array(),$var,false);
		wp_enqueue_style('vs_admin_perview',VISUALSLIDER_DIR.  'admin/assets/css/admin-perview.css',array(),$var,false);
		wp_enqueue_style('vs_admin_iconpicker',VISUALSLIDER_DIR.  'admin/assets/css/admin-iconpicker.css',array(),$var,false);
		wp_enqueue_style( 'vs_admin_template',VISUALSLIDER_DIR .'admin/assets/css/admin-template.css',array(),$var,false); 
		
		
 
				
 		$rtl = array('rtl'=>is_rtl()?'true':'false');
		
			 
		wp_enqueue_style( 'vs_fontfamily',VISUALSLIDER_DIR .'assets/css/fontfamily.css',array(),$var,false); 
	 
	  
		
		wp_enqueue_script("jquery-ui-draggable");
		wp_enqueue_script('jquery-ui-slider');
		
		global $post;
		$post_id='';
		if(!empty( get_the_ID())){
			$post_id = get_the_ID();	
		}
		if(!empty(get_post_type())){
			$post_type =get_post_type();	
		}		
		$array = array( 
			'ajaxurl'			=> admin_url( 'admin-ajax.php'),
			'is_rtl'			=> is_rtl()?'true':false,
			'font_url' 			=> VISUALSLIDER_DIR .'assets/fonts',
			'nonce'				=> wp_create_nonce( 'vs_slider_nonce' ),
			'post_id'			=>	$post_id,
			'post_type'			=> $post_type,			
		);
		
		wp_enqueue_style( 'rang_coloris',VISUALSLIDER_DIR .'admin/assets/css/coloris.css',array(),$var,false); 
		wp_enqueue_script( 'rang_coloris', VISUALSLIDER_DIR .'admin/assets/js/coloris.js' ,array('jquery'),$var,false); 
		wp_localize_script('rang_coloris', 'rang_coloris',array('rtl'=>is_rtl()?'true':''));
				
		 
		 
		 $script =array(
			'serializejson',
 			'var-custom-css',
			'fontfamily',
			'custom-css',
			'options',
			'layer',
			'slide',
			'admin', 
			'template',
			'perview',
			'responsive'
			
			);
 	
			foreach($script as $id){
				wp_enqueue_script( 'vs_'.$id, VISUALSLIDER_DIR .'admin/assets/js/'.$id.'.js' ,array('jquery', 'jquery-ui-sortable','jquery-ui-draggable','jquery-ui-resizable'),$var,false); 
				wp_localize_script( 'vs_'.$id, 'visualslider',$array);	
				wp_enqueue_script( 'vs_'.$id); 
			} 
			
		
	    wp_enqueue_media();
 	
	}

 
}
}
if( ! function_exists( 'vs_shortcode_column' ) ) {
add_filter('manage_visualslider_posts_columns', 'vs_shortcode_column', 5);
function vs_shortcode_column($columns){
   $columns['shortcode'] = __('Shortcode','visual-slider');
  return $columns;
} 
}
if( ! function_exists( 'vs_shortcode_display_column' ) ) {
add_action('manage_visualslider_posts_custom_column', 'vs_shortcode_display_column', 5, 2);
function vs_shortcode_display_column($column_name, $post_id){
  switch($column_name){
    case 'shortcode':
 
       if (!empty($post_id)) {
         echo '<textarea style="height: 30px;width: 200px;resize: none;border-color: rgb(200, 198, 198) !important;color: #666 !important;text-align: right;direction: ltr;"  id="vs_shortcode" name="vs_shortcode" readonly >[visualslider id="'.esc_attr($post_id).'"]</textarea>';
      }
      break;
  }
}
}

  