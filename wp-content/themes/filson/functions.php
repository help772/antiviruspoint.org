<?php
global $smof_data;
$minify_create='';
$minify='1';
 global $minify;
 
if( !defined('hexwp_PATH') ){
	define( 'hexwp_PATH', get_template_directory() );
}
if( !defined('hexwp_DIR') ){
	define( 'hexwp_DIR', get_template_directory_uri() );
}

if( !defined('hexwp_FILE') ){
	define( 'hexwp_FILE', dirname( __FILE__ ) );
}	
 
function add_etag_headers($headers) {
    $headers['ETag'] = md5(time()); // Generates a unique ETag
    return $headers;
}
add_filter('wp_headers', 'add_etag_headers');


 
 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Include Inc
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
include_once hexwp_PATH . '/inc/option.php';
include_once hexwp_PATH . '/inc/default.php';
include_once hexwp_PATH . '/inc/translation.php';
include_once hexwp_PATH . '/inc/mobile_detect.php';
include_once hexwp_PATH . '/header/header-functions.php';
include_once hexwp_PATH . '/inc/menu/walker_menu.php';   
include_once hexwp_PATH . '/inc/menu/walker_menu_mobile.php'; 
include_once hexwp_PATH . '/inc/wp_list_comments.php';
include_once hexwp_PATH . '/inc/ads-functions.php';
include_once hexwp_PATH . '/inc/post-functions.php';
include_once hexwp_PATH . '/inc/slider/slider-functions.php';
 
include_once hexwp_PATH . '/inc/blog/blog-functions.php'; 
include_once hexwp_PATH . '/inc/column-sidebar.php';
include_once hexwp_PATH . '/inc/breadcrumbs.php';
include_once hexwp_PATH . '/inc/footer-functions.php'; 
include_once hexwp_PATH . '/inc/color.php';
include_once hexwp_PATH . '/inc/ads-functions.php';
include_once hexwp_PATH . '/inc/pagenavi.php'; 
 include_once hexwp_PATH . '/inc/visual-slider.php'; 
  
if ( function_exists ( "is_woocommerce" )){
	include_once hexwp_PATH . '/inc/product/product-functions.php';
} 
 
include_once hexwp_PATH . '/custom-css/custom-css.php';
include_once hexwp_PATH . '/custom-css/enqueue.php';
if(!empty($minify_create)){
include_once hexwp_PATH . '/custom-css/minify.php';
}
include_once hexwp_PATH . '/config-builder/config.php'; 
include_once hexwp_PATH . '/elementor/elementor.php'; 
include_once hexwp_PATH . '/widgets/widget.php';
      
include_once hexwp_PATH . '/admin/admin.php';
include_once hexwp_PATH . '/admin/custom-sidebar.php'; 

 include_once hexwp_PATH . '/import/import.php';
if(is_admin()){
include_once hexwp_PATH . '/admin/tgm-plugin-activation/tgm-plugin.php';
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		Output Buffer Start
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_action('wp_loaded', 'hexwp_output_buffer_start');
function hexwp_output_buffer_start() { 
    ob_start("hexwp_output_callback"); 
}
 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		Output Callback
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function hexwp_output_callback($buffer) {
    return preg_replace( "%[ ]type=[\'\"]text\/(javascript|css)[\'\"]%", '', $buffer );
}
 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																Site Icon

*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 
function hexwp_site_icon($tags) {
    $tags[] = sprintf('', esc_url(get_site_icon_url(null, 64)));

    return $tags;
}
add_filter('site_icon_meta_tags', 'hexwp_site_icon');

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		Setup
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_action( 'after_setup_theme', 'hexwp_setup' );
function hexwp_setup() {
 
 	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'woocommerce' );  
	   add_theme_support( 'post-thumbnails' ); 
 	add_filter( 'enable_post_format_ui', '_ereturn_false' );


	if ( version_compare( $GLOBALS['wp_version'], '6.7', '<' ) ) {
	  load_theme_textdomain( 'hexwp', get_template_directory() . '/languages' );
	} else {
	  load_textdomain( 'hexwp', get_template_directory() . '/languages/' . determine_locale() . '.mo' );
	}


add_image_size( 'hexwp_thumbnail', 150  ); // 300 pixels wide (and unlimited height)
	add_image_size( 'hexwp_medium', 225  ); // 300 pixels wide (and unlimited height)
	add_image_size( 'hexwp_large', 300 ); // 300 pixels wide (and unlimited height)
	add_image_size( 'hexwp_big', 375  ); // 300 pixels wide (and unlimited height)
	
	

 
} 
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		Language Selector Flags
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function hexwp_language_selector_flags(){
	if( function_exists( 'icl_get_languages' )){
		$languages = icl_get_languages('skip_missing=0&orderby=code');
		if(!empty($languages)){
			echo '<div id="hexwp_lang_switcher">';
			foreach($languages as $l){
				if(!$l['active']) echo '<a href="'.esc_attr($l['url']).'">';
					echo '<img src="'.esc_attr($l['country_flag_url']).'" height="12" alt="'.esc_attr($l['language_code']).'" width="18" />';
				if(!$l['active']) echo '</a>';
			}
			echo '</div>';
		}
	}
}  
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																Number Replace
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_number_replace($English_Number){
 
		return $English_Number;
	 
 }
function hexwp_number_replace_page_number($English_Number){
  
		return $English_Number;
	 
 }
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																	Mobile
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_ismobile() {
return  wp_is_mobile();

}
   function hexwp_admin_login_header() {
    remove_action('wp_head', '_admin_bar_bump_cb');
}
add_action('get_header', 'hexwp_admin_login_header');


///////this is the custome function.php code ///**

// Add custom field for multiple license keys in the product edit page
// // 
// add_action('woocommerce_product_options_general_product_data', 'add_license_keys_field');
// function add_license_keys_field() {
//     woocommerce_wp_textarea_input([
//         'id'          => '_license_keys',
//         'label'       => __('License Keys', 'woocommerce'),
//         'description' => __('Enter one license key per line for this product.', 'woocommerce'),
//         'desc_tip'    => true,
//     ]);
// }

// // Save the license keys
// add_action('woocommerce_process_product_meta', 'save_license_keys_field');
// function save_license_keys_field($post_id) {
//     $license_keys = isset($_POST['_license_keys']) ? sanitize_textarea_field($_POST['_license_keys']) : '';
//     $license_keys_array = array_filter(array_map('trim', explode("\n", $license_keys))); // Convert to array
//     update_post_meta($post_id, '_license_keys', $license_keys_array);
// }

// // Allocate a license key to the order
// add_action('woocommerce_checkout_create_order_line_item', 'assign_license_key_to_order', 10, 4);
// function assign_license_key_to_order($item, $cart_item_key, $values, $order) {
//     $product_id = $values['product_id'];
//     $license_keys = get_post_meta($product_id, '_license_keys', true);

//     if (!empty($license_keys) && is_array($license_keys)) {
//         // Get the next available license key
//         $allocated_key = array_shift($license_keys); // Remove the first key from the array

//         if (!empty($allocated_key)) {
//             // Save the allocated key in order item meta
//             $item->add_meta_data(__('License Key', 'woocommerce'), $allocated_key, true);

//             // Update the remaining license keys for the product
//             update_post_meta($product_id, '_license_keys', $license_keys);
//         }
//     }
// }

// // Show license key in order details for completed orders
// add_action('woocommerce_order_item_meta_end', 'display_license_key_in_order_details', 10, 4);
// function display_license_key_in_order_details($item_id, $item, $order, $plain_text) {
//     if ($order->get_status() === 'completed') {
//         $license_key = wc_get_order_item_meta($item_id, __('License Key', 'woocommerce'));

//         if (!empty($license_key)) {
//             echo '<p><strong>' . __('License Key:', 'woocommerce') . '</strong> ' . esc_html($license_key) . '</p>';
//         }
//     }
// }

// // Add license key to completed order email
// add_action('woocommerce_email_after_order_table', 'add_license_key_to_completed_email', 10, 4);
// function add_license_key_to_completed_email($order, $sent_to_admin, $plain_text, $email) {
//     if ($email->id === 'customer_completed_order') {
//         foreach ($order->get_items() as $item_id => $item) {
//             $license_key = wc_get_order_item_meta($item_id, __('License Key', 'woocommerce'));

//             if (!empty($license_key)) {
//                 echo '<p><strong>' . __('License Key:', 'woocommerce') . '</strong> ' . esc_html($license_key) . '</p>';
//             }
//         }
//     }
// }

