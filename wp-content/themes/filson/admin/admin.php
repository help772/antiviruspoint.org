<?php

add_action( 'admin_menu', 'hexwp_admin_menu' );
function hexwp_admin_menu() {
 
 	
	 $of_page = add_menu_page( 
		esc_html__('Theme','hexwp'),
		esc_html__('Theme','hexwp'),
 		'manage_options',
		'hexwp_theme',
		'optionsframework_options_page','',60 );
    	add_action("admin_print_scripts-$of_page", 'of_load_only');
	add_action("admin_print_styles-$of_page",'of_style_only');
}


include_once hexwp_PATH . '/admin/menu/admin-menu.php';
include_once hexwp_PATH . '/admin/theme-options/functions.options.php' ;
include_once hexwp_PATH . '/admin/theme-options/index.php';
include_once hexwp_PATH . '/admin/array.php';
include_once hexwp_PATH . '/admin/multi-array.php';
include_once hexwp_PATH . '/admin/category-array.php';
include_once hexwp_PATH . '/admin/admin-custom-css.php';



include_once hexwp_PATH . '/admin/post-type/blog_post_type.php';   
include_once hexwp_PATH . '/admin/metabox/blog-metabox.php';  
include_once hexwp_PATH . '/admin/metabox/page-metabox.php';   
include_once hexwp_PATH . '/admin/metabox/author-metabox.php';   
include_once hexwp_PATH . '/admin/metabox/product-metabox.php';   
 
include_once hexwp_PATH . '/admin/header-builder/header-builder.php';
 
global $pagenow;
function hexwp_builder_has_type() {
  global $post, $typenow, $current_screen;
	  if ( $post && $post->post_type ) {
		return $post->post_type;
	  }
	  elseif ( $typenow ) {
		return $typenow;
	  }
	  elseif ( $current_screen && $current_screen->post_type ) {
		return $current_screen->post_type;
	  }
	  elseif ( isset( $_REQUEST['post_type'] ) ) {
		return sanitize_key( $_REQUEST['post_type'] );
	  }
	  elseif ( isset( $_REQUEST['post'] ) ) {
		return get_post_type( $_REQUEST['post'] );
	  }
	  return null;
}
add_action('admin_enqueue_scripts', 'hexwp_admin_enqueue');
function hexwp_admin_enqueue($hook) {
  	$var = '1.7';
		global $pagenow,$post; 
	wp_enqueue_style('hexwp_fontsite',hexwp_DIR. '/css/fonts/fontsite.css','',$var);  

   	wp_enqueue_style('hexwp-meta-box', hexwp_DIR . '/admin/assets/css/meta_box.css',1.1);
 
		wp_add_inline_style( 'hexwp-meta-box', hexwp_admin_custom_css() ); 
 
 	    // enqueue style
  	    // enqueue style
 
	wp_enqueue_style( 'rang_coloris',hexwp_DIR .'/admin/assets/css/coloris.css'); 
	wp_enqueue_script( 'rang_coloris', hexwp_DIR .'/admin/assets/js/coloris.js'); 
	wp_localize_script('rang_coloris', 'rang_coloris',array('rtl'=>is_rtl()?'true':''));
 
 
  
  	wp_enqueue_script('migrate', hexwp_DIR . '/admin/assets/js/jquery-migrate-1.0.0.js','1.0', true );
  	wp_register_script('hexwp-meta-box', hexwp_DIR . '/admin/assets/js/scripts.js', array('jquery', 'jquery-ui-sortable') ,'1.5', true );
    wp_enqueue_script( 'hexwp-meta-box' );

		hexwp_enqueue_google_fonts();

	wp_enqueue_media();
			

			
} 

add_action('elementor/preview/enqueue_styles', 'hexwp_perview_elementor');
function hexwp_perview_elementor(){
	
		wp_enqueue_script( 'hexwp-elementor', hexwp_DIR . '/js/elementor-scripts.js', array( 'jquery'),'1');
	 
}
 function hexwp_radio_buttons($name, $options_array, $selected_value = '') {
    $output = '';
    
    foreach ($options_array as $value => $label) {
		$value= !empty($value)?$value:'';
		$label= !empty($label)?$label:'';
		if ($selected_value === $value) {
            $checked = ' checked="checked"';
        }else{
            $checked = '';
		}
		 
   ?>
       	<input id="<?php echo esc_attr($name.'_'.$value)?>" type="radio" name="<?php echo esc_attr($name);?>" value="<?php echo esc_attr($value);?>" <?php echo  $checked;?>>
        
          <label for="<?php echo esc_attr($name.'_'.$value)?>" ><?php echo esc_html($label);?></label> 
    <?php
    }
    
 }
 
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		Post Views
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_setPostViews($postID) {
    $count_key = 'hexwp_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        $count = 0;
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
    }else{
		$counter=  $count + 0.5;
		update_post_meta($postID, $count_key, $counter);
    }
}

// Add it to a column in WP-Admin 
add_filter('manage_posts_columns', 'hexwp_posts_column_views');
function hexwp_posts_column_views($defaults){
    $defaults['hexwp_post_views'] = esc_html__('Views' , 'hexwp' );
    return $defaults;
}

// Posts Custom Column Views

add_action('manage_posts_custom_column', 'hexwp_posts_custom_column_views',5,2);
function hexwp_posts_custom_column_views($column_name, $id){
	if($column_name === 'hexwp_post_views'){
        echo hexwp_number_replace(hexwp_getPostViews(get_the_ID()));
    }
}

 