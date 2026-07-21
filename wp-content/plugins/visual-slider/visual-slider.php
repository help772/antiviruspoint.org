<?php
/**
 * Plugin Name: Visual Slider
 * Description: Responsive Slider
 * Version: 1.4.1
 * Author: sdastan800
 * Text Domain: visual-slider
 * Domain Path: /languages/ 
 * License:GPLv2 or later
 * Requires at least: 6.0
 * Requires PHP: 7.4.0
 * License URI:https://www.gnu.org/licenses/gpl-2.0.html
/* Plugin Framework Version Check */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if( !defined('VISUALSLIDER_PATH') ){
	define( 'VISUALSLIDER_PATH', plugin_dir_path(__FILE__) );
}
if( !defined('VISUALSLIDER_DIR') ){
	define( 'VISUALSLIDER_DIR', plugin_dir_url(__FILE__) );
}	
  
if( ! function_exists( 'visualslider_constructor' ) ) {
    function visualslider_constructor() {

		  if ( version_compare( $GLOBALS['wp_version'], '6.7', '<' ) ) {
            load_plugin_textdomain( 'visual-builder' );
			} else {
			  load_textdomain( 'visual-slider', plugin_dir_path(__FILE__) . 'languages/visual-slider-' . determine_locale() . '.mo' );
			}
  
    }
}
add_action( 'visualslider_init', 'visualslider_constructor' );

 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																 Slider Install
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if( ! function_exists( 'visualslider_install' ) ) {
function visualslider_install() {

     
            do_action( 'visualslider_init' );
}
 
add_action( 'plugins_loaded', 'visualslider_install', 1 );
}

 
if(  function_exists( 'visualslider_constructor' ) ) {

if( ! function_exists( 'visualslider_post_type' ) ) {
		add_action( 'init', 'visualslider_post_type' );

function visualslider_post_type() {
	$labels = array(
		'name' 					=> __('Slider','visual-slider'),
		'singular_name'			=> __('Sliders','visual-slider'),
		'add_new'				=> __('Add New','visual-slider'),
		'add_new_item'			=>__('Add New slider','visual-slider'),
		'edit_item'				=> __('Edit Slider','visual-slider'),
		'new_item'				=> __('New Slider','visual-slider'),
		'view_item'				=> __('View Slider','visual-slider'),
 		'all_items'				=>__('All Sliders','visual-slider'),
 		'search_items'			=> __('Search Sliders','visual-slider'),
		'not_found'				=> __('Slider not found','visual-slider'),
		'not_found_in_trash'	=> __('The slider was found in the trash','visual-slider'),
		'parent_item_colon'		=> '',
		'menu_name'				=> __('Visual Slider','visual-slider')
	);
	
	$args = array(
		'labels'				=> $labels,
		'public'				=> true,
		'publicly_queryable'	=> true,
		'show_ui'				=> true, 
		'show_in_menu'			=> true, 
		'query_var'				=> true,
		'rewrite'				=> true,
		'capability_type'		=> 'post',
		'has_archive'			=> false, 
		'hierarchical'			=> false,
		'menu_position'			=> null,
		'supports' => array( 'title' )
	); 

	register_post_type( 'visualslider', $args );
}
}

include_once VISUALSLIDER_PATH . 'inc/widget-visualslider.php';
include_once VISUALSLIDER_PATH . 'inc/vb-visualslider.php';
 if ( !function_exists ( "visualslider_elementor_widgets" )){
function visualslider_elementor_widgets() {
 
			
 		 require_once(VISUALSLIDER_PATH . 'inc/elementor-visualslider.php' );  
			// Register widget
			
		\Elementor\Plugin::instance()->widgets_manager->register( new \visualslider_slider() );  
 
 
}
add_action( 'elementor/widgets/register', 'visualslider_elementor_widgets' );
}
 
include_once VISUALSLIDER_PATH . 'inc/composer-visualslider.php';
 

}






if( ! function_exists( 'vs_layer_options_element' ) ) {
function vs_layer_options_element() { 
	global  $vs_layer_options_element;

	$slider=array();

 	if(has_filter('vs_layer_element_options')) {
		$vs_layer_options_element = apply_filters('vs_layer_element_options', $slider);
	}
 				 
 
}
add_action('init','vs_layer_options_element');
}



global $vs_fonticon_style;


if( ! function_exists( 'vs_icon_fontfamily' ) ) {
function vs_icon_fontfamily($value =false) {
 	 	if(!empty($value)){
	 	global $vs_fonticon_style;
	 	if(strpos($value,'fa-')!==false){
			$vs_fonticon_style['FontAwesome']=true;
		}
	 	if(strpos($value,'flaticonarrow-')!==false){
			$vs_fonticon_style['flaticonarrow']=true;
		}
		if(strpos($value,'flaticonmultimedia-')!==false){
			$vs_fonticon_style['flaticonmultimedia']=true;
		} 
		
		if(strpos($value,'flaticonbusiness-')!==false){
			$vs_fonticon_style['flaticonbusiness']=true;
		} 
			
		if(strpos($value,'flaticonoffice-')!==false){
			$vs_fonticon_style['flaticonoffice']=true;
		} 
		if(strpos($value,'flaticoninterface-')!==false){
			$vs_fonticon_style['flaticoninterface']=true;
		} 
		
		if(strpos($value,'flaticonessentialset-')!==false){
			$vs_fonticon_style['flaticonessentialset']=true;
		} 
		if(strpos($value,'flaticontechsupport-')!==false){
			$vs_fonticon_style['flaticontechsupport']=true;
		} 
		if(strpos($value,'flaticontech-')!==false){
			$vs_fonticon_style['flaticontech']=true;
		} 
		if(strpos($value,'flaticonstrategy-')!==false){
			$vs_fonticon_style['flaticonstrategy']=true;
		} 
		if(strpos($value,'flaticonhipster-')!==false){
			$vs_fonticon_style['flaticonhipster']=true;
		} 
		if(strpos($value,'flaticonfashion-')!==false){
			$vs_fonticon_style['flaticonfashion']=true;
		} 
		if(strpos($value,'flaticonwebdesign-')!==false){
			$vs_fonticon_style['flaticonwebdesign']=true;
		} 
		if(strpos($value,'flaticontravel-')!==false){
			$vs_fonticon_style['flaticontravel']=true;
		} 
		if(strpos($value,'flaticonnetwork-')!==false){
			$vs_fonticon_style['flaticonnetwork']=true;
		} 
 		 
	 	if(strpos($value,'metrizeicon-')!==false){
			$vs_fonticon_style['metrizeicon']=true;
		}
 		if(strpos($value,'typcn-')!==false){
			$vs_fonticon_style['typcn']=true;
		} 
		}
	 
 }
}
if( ! function_exists( 'vs_icon_enqueue' ) ) {
 function vs_icon_enqueue($var=false,$true=false) {
	global $vs_fonticon_style;
	
 	if(!empty($vs_fonticon_style['FontAwesome']) || !empty($true)) wp_enqueue_style('vs_fontawesome',VISUALSLIDER_DIR. 'assets/css/fonts/fontawesome.css','',$var);  
 	if(!empty($vs_fonticon_style['flaticonarrow'])  || !empty($true)) wp_enqueue_style('vs_flaticonarrow',VISUALSLIDER_DIR. 'assets/css/fonts/flaticonarrow.css','',$var);  
 	if(!empty($vs_fonticon_style['flaticonmultimedia'])  || !empty($true) ) wp_enqueue_style('vs_flaticonmultimedia',VISUALSLIDER_DIR. 'assets/css/fonts/flaticonmultimedia.css','',$var);  
 	if(!empty($vs_fonticon_style['flaticonbusiness'])  || !empty($true)) wp_enqueue_style('vs_flaticonbusiness',VISUALSLIDER_DIR. 'assets/css/fonts/flaticonbusiness.css','',$var);  
 	if(!empty($vs_fonticon_style['flaticonoffice'])  || !empty($true)) wp_enqueue_style('vs_flaticonoffice',VISUALSLIDER_DIR. 'assets/css/fonts/flaticonoffice.css','',$var);  
 	if(!empty($vs_fonticon_style['flaticoninterface'])  || !empty($true) ) wp_enqueue_style('vs_flaticoninterface',VISUALSLIDER_DIR. 'assets/css/fonts/flaticoninterface.css','',$var);  
 	if(!empty($vs_fonticon_style['flaticonessentialset']) || !empty($true)) wp_enqueue_style('vs_flaticonessentialset',VISUALSLIDER_DIR. 'assets/css/fonts/flaticonessentialset.css','',$var);  
 	if(!empty($vs_fonticon_style['flaticontechsupport']) || !empty($true)) wp_enqueue_style('vs_flaticontechsupport',VISUALSLIDER_DIR. 'assets/css/fonts/flaticontechsupport.css','',$var);  
 	if(!empty($vs_fonticon_style['flaticontech'])  || !empty($true)) wp_enqueue_style('vs_flaticontech',VISUALSLIDER_DIR. 'assets/css/fonts/flaticontech.css','',$var);  
 	if(!empty($vs_fonticon_style['flaticonstrategy'])  || !empty($true)) wp_enqueue_style('vs_flaticonstrategy',VISUALSLIDER_DIR. 'assets/css/fonts/flaticonstrategy.css','',$var);  
 	if(!empty($vs_fonticon_style['flaticonhipster'])  || !empty($true) ) wp_enqueue_style('vs_flaticonhipster',VISUALSLIDER_DIR. 'assets/css/fonts/flaticonhipster.css','',$var);
  	if(!empty($vs_fonticon_style['flaticonfashion']) || !empty($true) ) wp_enqueue_style('vs_flaticonfashion',VISUALSLIDER_DIR. 'assets/css/fonts/flaticonfashion.css','',$var);  
 	if(!empty($vs_fonticon_style['flaticonwebdesign']) || !empty($true) ) wp_enqueue_style('vs_flaticonwebdesign',VISUALSLIDER_DIR. 'assets/css/fonts/flaticonwebdesign.css','',$var);  
 	if(!empty($vs_fonticon_style['flaticontravel']) || !empty($true) ) wp_enqueue_style('vs_flaticontravel',VISUALSLIDER_DIR. 'assets/css/fonts/flaticontravel.css','',$var);  
 	if(!empty($vs_fonticon_style['flaticonnetwork']) || !empty($true)) wp_enqueue_style('vs_flaticonnetwork',VISUALSLIDER_DIR. 'assets/css/fonts/flaticonnetwork.css','',$var);  
 	if(!empty($vs_fonticon_style['metrizeicon']) || !empty($true)) wp_enqueue_style('vs_metrizeicon',VISUALSLIDER_DIR. 'assets/css/fonts/metrizeicon.css','',$var);  
 	if(!empty($vs_fonticon_style['typcn']) || !empty($true)) wp_enqueue_style('vs_typcn',VISUALSLIDER_DIR. 'assets/css/fonts/typcn.css','',$var);   
}
}


 
 if( ! function_exists( 'vs_rtl_has' ) ) {
function vs_rtl_has($element =false) {
 	 
 	if(is_rtl() && strpos($element,'left')!==false){
		return str_replace('left','right', $element);
		
	}elseif(is_rtl() && strpos($element,'right')!==false){
		
		return str_replace('right','left',$element);
		
	} else{
		return $element;
		
	}
}
 }
 if ( ! function_exists( 'vs_kses' ) ) {
function vs_kses() {
    $safe_attrs = [
        'id', 'class', 'style', 'title', 'lang', 'dir', 'tabindex', 'hidden',
        'draggable', 'contenteditable',
        'data-*', 'aos-*',
        'href', 'target', 'rel', 'src', 'alt', 'srcset', 'sizes', 'poster', 'preload', 'loading',
        'width', 'height',
        'role', 'aria-*',
    ];

    $safe_tags = [
        'span', 'strong', 'b', 'em', 'i', 'u', 's', 'small', 'sub', 'sup', 'mark', 'del', 'ins', 'code',
        'abbr', 'cite', 'q', 'var', 'dfn', 'br', 'p', 'div', 'section', 'article', 'aside', 'header',
        'footer', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'ul', 'ol', 'li',
        'table', 'thead', 'tbody', 'tfoot', 'tr', 'th', 'td', 'caption',
        'img', 'picture', 'source',
        'a', 'details', 'summary',
    ];

    $result = [];

    foreach ($safe_tags as $tag) {
        $result[$tag] = [];
        foreach ($safe_attrs as $attr) {
            $result[$tag][$attr] = true;
        }
    }

    return $result;
}
}
 if ( is_admin()  ) {
 
include_once VISUALSLIDER_PATH . 'admin/index.php';

}
 
include_once VISUALSLIDER_PATH . 'inc/element-css.php';
include_once VISUALSLIDER_PATH . 'inc/post-functions.php';
include_once VISUALSLIDER_PATH . 'inc/var-element-css.php';

 
  
 include_once VISUALSLIDER_PATH . 'inc/global-config.php';
 include_once VISUALSLIDER_PATH . 'inc/global-slider-config.php';
 include_once VISUALSLIDER_PATH . 'inc/global-glider-config.php';
 include_once VISUALSLIDER_PATH . 'inc/global-single-config.php';
 
 include_once VISUALSLIDER_PATH . 'inc/slide-config.php';
 include_once VISUALSLIDER_PATH . 'inc/glide-config.php';
 include_once VISUALSLIDER_PATH . 'inc/single-config.php';
 
 include_once VISUALSLIDER_PATH . 'inc/layer-config.php'; 
 include_once VISUALSLIDER_PATH . 'admin/includes/code.php';

 
include_once VISUALSLIDER_PATH . 'layer/text.php';
include_once VISUALSLIDER_PATH . 'layer/icon.php';
include_once VISUALSLIDER_PATH . 'layer/button.php';
include_once VISUALSLIDER_PATH . 'layer/image.php';
include_once VISUALSLIDER_PATH . 'layer/box.php';
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																  Slider Custom Template
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	  
 
 
 if( ! function_exists( 'vs_enqueue' ) ) {
add_action('wp_enqueue_scripts', 'vs_enqueue');
function vs_enqueue($hook) {
	$var ='1.1';
  
        	wp_enqueue_style( 'vs_glider',VISUALSLIDER_DIR .'assets/css/glider.css',array(),$var,false); 
        	wp_enqueue_style( 'vs_slider',VISUALSLIDER_DIR .'assets/css/slider.css',array(),$var,false); 
    	wp_enqueue_style( 'vs_layer',VISUALSLIDER_DIR .'assets/css/layer.css',array(),$var,false); 
 
 vs_icon_enqueue('1.0');

 
	wp_enqueue_script( 'vs_slider', VISUALSLIDER_DIR .'assets/js/slider.js',array('jquery'),$var,false ); 
	wp_enqueue_script( 'vs_script', VISUALSLIDER_DIR .'assets/js/script.js' ,array('jquery'),$var,false); 
	 
 	
}
 }
 if( ! function_exists( 'vs_slider_config' ) ) {
function vs_slider_config($id) {
	  
	if(!empty($id)){
	$setting_json = get_post_meta($id, 'vs_setting_json', true);
  	$setting= vs_options_array_row($setting_json);
  	$slide_json = get_post_meta($id, 'vs_slide', true);  
	$slide= vs_options_array_row($slide_json);
   	vs_global_config($id, $setting,$slide);
	}
}
 }

 
 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																 slider_elementor_widget_categories
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	  
  if ( !function_exists ( "visualslider_shortcode" )){
function visualslider_shortcode($id) {
    extract(shortcode_atts(array(
        'id' => 'id'
    ), $id));
    
    // check what type user entered
  return vs_slider_config($id);
}
add_shortcode('visualslider', 'visualslider_shortcode');
  }
  
  
  if ( !function_exists ( "visualslider_single_template" )){
  add_filter( 'single_template', 'visualslider_single_template' );
function visualslider_single_template( $template ){
    global $post;
 
     
 
    if ( 'visualslider' === $post->post_type && locate_template( array( 'single-visualslider.php' ) ) !== $template ) {
        /*
         * This is a 'movie' post
         * AND a 'single movie template' is not found on
         * theme or child theme directories, so load it
         * from our plugin directory.
         */
        return plugin_dir_path( __FILE__ ) . 'inc/single-visualslider.php';
    }

 
 
    return $template;
}
 }
  if ( !function_exists ( "visualslider_load_cpt_template" )){
  function visualslider_load_cpt_template($template) {
    global $post;

    // Is this a "my-custom-post-type" post?
    if ($post->post_type == "visualslider"){

        //Your plugin path 
        $plugin_path = plugin_dir_path( __FILE__ );

        // The name of custom post type single template
        $template_name = 'single-visualslider.php';

        // A specific single template for my custom post type exists in theme folder? Or it also doesn't exist in my plugin?
        if($template === get_stylesheet_directory() . '/' . $template_name
            || !file_exists($plugin_path . $template_name)) {

            //Then return "single.php" or "single-my-custom-post-type.php" from theme directory.
            return $template;
        }

        // If not, return my plugin custom post type template.
        return $plugin_path . $template_name;
    }

    //This is not my custom post type, do nothing with $template
    return $template;
}
add_filter('single_template', 'visualslider_load_cpt_template');
  }
 