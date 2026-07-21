<?php
add_filter( 'gutenberg_use_widgets_block_editor', '__return_false' );
add_filter( 'use_widgets_block_editor', '__return_false' );
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Widget Class
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
include_once hexwp_PATH . '/widgets/widget-blog.php';  

if ( function_exists ( "is_woocommerce" )){
include_once hexwp_PATH . '/widgets/widget-product.php';  
}
 
include_once hexwp_PATH . '/widgets/widget-menu.php';  
include_once hexwp_PATH . '/widgets/widget-ads.php';  
include_once hexwp_PATH . '/widgets/widget-custom-html.php';  
include_once hexwp_PATH . '/widgets/widget-social.php';  
include_once hexwp_PATH . '/widgets/widget-blog-tags.php';
 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Widget Options
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_widget_options($instance,$bass='',$number='',$hexwp_options='') { 
	global $post;
  
	foreach ($hexwp_options as  $option) {
		hexwp_widget_options_item($instance,$bass,$number,$option);
	}; 
	
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Widget Options Save
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_widget_options_save($new_instance, $old_instance,$hexwp_options) { 
		$instance = $old_instance;
	if(!empty($hexwp_options)){
	foreach ($hexwp_options as  $option) {
		$type = !empty($option['type'])? $option['type']:'';

		if(!empty( $option['id'])){
			if($option['id']=='title'){
					$instance['title'] = sanitize_text_field( $new_instance[$option['id']] );

			}elseif($type =='select' || $type =='number' || $type =='text'){
				$instance[$option['id']]= strip_tags($new_instance[$option['id']]);
			}else{
				$instance[$option['id']]=!empty( $new_instance[$option['id']]);
			}
		}
   	}
	}
	
	return $instance;

}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Widget Options Item
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_widget_options_item($value,$bass=false,$number=false,$array=false) { 
	
 	$label = !empty($array['name'])?$array['name']:'';
	$id = !empty($array['id'])? $array['id']:'';
	$type = !empty($array['type'])? $array['type']:'';
	$options = !empty($array['options'])?$array['options']:'';
 	$name='widget-'.$bass.'['.$number.']['.$id.']';
	$data='widget-'.$bass.'-'.$number.'-'.$id.'';
  
  	$value_id = isset( $value[esc_html($id)]  ) ? $value[esc_html($id)] : null;
  	if ( 1 == $value_id ){
	$checked= 'checked="checked"'; 
	} else{
		$checked='';
	}
 
 
   
	echo '<p>';
	if(!empty($label)){	
		echo '<label for="'.esc_attr($data).'">'. esc_html($label).'</label>  ';
	}

	switch( $type ) {
	// Title
	case 'text':
		echo '<input type="text" name="'.esc_attr($name).'" id="'.esc_attr($data).'" class="widefat"   style="width:100%;"  value="'.$value_id.'">';
  	break;

		
	case 'textarea':
		echo '<textarea name="'.esc_attr($name).'" id="'.esc_attr($data).'" >'.esc_textarea($value_id).'</textarea>';
 	break;
		
	case 'number':
		echo '<input type="text"  name="'.esc_attr($name).'" id="'.esc_attr($data).'" style="width: 100px;" value="'.esc_attr($value_id).'" > ';
	break;
	case 'checkbox':
 		echo '<input type="checkbox"  name="'.esc_attr($name).'" id="'.esc_attr($data).'" '.wp_kses_post($checked).'   value="1">';
  	break;
	
 
	// Categories
	case 'select': 
 
		echo '<select name="'.esc_attr($name).'" id="'.esc_attr($data).'" >';
 			foreach ($options as  $keys => $text) { 	
 				echo'<option id="hw_'.esc_attr($id).'_'.esc_attr($keys).'" class="select_'.esc_attr($type).'" value="'.esc_attr($keys).'"'.selected( $value_id, $keys).'>'.esc_html($text).'</option>'; 
			}
		echo '</select>';
	break;	 
 
  
 	}
 	echo '</p>';
}
 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Widgets init Register Sidebar
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  
add_action( 'widgets_init', 'hexwp_widgets_init' );
function hexwp_widgets_init() {
	
 	global $smof_data;
 
	$class = ' hw-tbox-'.hexwp_option('title_box_style');
	$footer_column = hexwp_option('footer_column'); 


	if( hexwp_option('column')== 'main_right'  ){
		$right=true;				
	}
	if( hexwp_option('column')== 'left_main'  ){
		$left=true;				
	}
	if( hexwp_option('column')== 'main_left_right' ||  hexwp_option('column')== 'left_main_right' || hexwp_option('column')== 'left_right_main'  ){
		$left=true;
		$right=true;				

	}
	
 		if ( function_exists ( "is_woocommerce" )){

	if( hexwp_option('product_column')== 'main_right'  ){
		$product_right=true;				
	}
	if( hexwp_option('product_column')== 'left_main'  ){
		$product_left=true;				
	}
	if( hexwp_option('product_column')== 'main_left_right' ||  hexwp_option('product_column')== 'left_main_right' || hexwp_option('product_column')== 'left_right_main'  ){
		$product_left=true;
		$product_right=true;				

	}
		}




 	if ( function_exists('register_sidebar') ) {
		
		if(!empty($right)){
		register_sidebar(array(
			'name' 				=>  esc_html__('Primary Right Sidebar' , 'hexwp'),
			'id' 				=> 'sidebar_main_right',  
			'before_widget'		=> '<div id="%1$s" class="widget %2$s">',
			'after_widget' 		=> '</aside></div>',
 			'before_title' 		=> '<div class="'.$class.'"><h4 class="hw-title-box"><div class="hw-tab-main"><span>',
        	'after_title'   	=> '</span></div></div><aside class="widget-container">',
		)); 
 	 }
	
		if(!empty($left)){
		register_sidebar(array(
			'name' 				=>  esc_html__('Primary Left Sidebar' , 'hexwp'),
			'id' 				=> 'sidebar_main_left',  
			'before_widget'		=> '<div id="%1$s" class="widget %2$s">',
			'after_widget' 		=> '</aside></div>',
 			'before_title' 		=> '<div class="'.$class.'"><h4 class="hw-title-box"><div class="hw-tab-main"><span>',
        	'after_title'   	=> '</span></div></div><aside class="widget-container">',
		)); 
 	 }
		if(!empty($product_right)){
		register_sidebar(array(
			'name' 				=> esc_html__('Woocommerce Right Sidebar' , 'hexwp'), 
			'id' 				=> 'sidebar_woocommerce_right',  
			'before_widget'		=> '<div id="%1$s" class="widget %2$s">',
			'after_widget' 		=> '</aside></div>',
 			'before_title' 		=> '<div class="'.$class.'"><h4 class="hw-title-box"><div class="hw-tab-main"><span>',
        	'after_title'   	=> '</span></div></div><aside class="widget-container">',
		)); 
 	 }
	

		if(!empty($product_left)){
		register_sidebar(array(
			'name' 				=>  esc_html__('Woocommerce Left Sidebar' , 'hexwp'),  
			'id' 				=> 'sidebar_woocommerce_left',  
			'before_widget'		=> '<div id="%1$s" class="widget %2$s">',
			'after_widget' 		=> '</aside></div>',
 			'before_title' 		=> '<div class="'.$class.'"><h4 class="hw-title-box"><div class="hw-tab-main"><span>',
        	'after_title'   	=> '</span></div></div><aside class="widget-container">',
		)); 
 	 }
	 	 
	 
 
 
	   
 		register_sidebar(array(
			'name' 				=> esc_html__('Footer Column 1' , 'hexwp'), 
			'id' 				=> 'sidebar_footer_1',
			'before_widget'		=> '<div id="%1$s" class="widget %2$s">',
			'after_widget' 		=> '</aside></div>',
 			'before_title' 		=> '<div class="'.$class.'"><h4 class="hw-title-box"><div class="hw-tab-main"><span>',
        	'after_title'   	=> '</span></div></div><aside class="widget-container">',
		)); 
 
 
	if ( $footer_column=='2' || $footer_column=='3' || $footer_column=='4' ||$footer_column=='5' ||$footer_column=='6') {
		register_sidebar(array(
			'name' 				=> esc_html__('Footer Column 2' , 'hexwp'), 
			'id' 				=> 'sidebar_footer_2',
			'before_widget'		=> '<div id="%1$s" class="widget %2$s">',
			'after_widget' 		=> '</aside></div>',
 			'before_title' 		=> '<div class="'.$class.'"><h4 class="hw-title-box"><div class="hw-tab-main"><span>',
        	'after_title'   	=> '</span></div></div><aside class="widget-container">',
		)); 
	} 
	   
	if ( $footer_column=='3' || $footer_column=='4' ||$footer_column=='5' ||$footer_column=='6' ) {
		register_sidebar(array(
			'name' 				=> esc_html__('Footer Column 3' , 'hexwp'), 
			'id' 				=> 'sidebar_footer_3',
			'before_widget'		=> '<div id="%1$s" class="widget %2$s">',
			'after_widget' 		=> '</aside></div>',
 			'before_title' 		=> '<div class="'.$class.'"><h4 class="hw-title-box"><div class="hw-tab-main"><span>',
        	'after_title'   	=> '</span></div></div><aside class="widget-container">',
		)); 
	} 
 
 	if (  $footer_column=='4' ||$footer_column=='5' ||$footer_column=='6')  {
		register_sidebar(array(
			'name' 				=> esc_html__('Footer Column 4' , 'hexwp'), 
			'id' 				=> 'sidebar_footer_4',
			'before_widget'		=> '<div id="%1$s" class="widget %2$s">',
			'after_widget' 		=> '</aside></div>',
 			'before_title' 		=> '<div class="'.$class.'"><h4 class="hw-title-box"><div class="hw-tab-main"><span>',
        	'after_title'   	=> '</span></div></div><aside class="widget-container">',
		)); 
	} 
	 
  	if (  $footer_column=='5' ||$footer_column=='6') {
		register_sidebar(array(
			'name' 				=> esc_html__('Footer Column 5' , 'hexwp'), 
			'id' 				=> 'sidebar_footer_5',
			'before_widget'		=> '<div id="%1$s" class="widget %2$s">',
			'after_widget' 		=> '</aside></div>',
 			'before_title' 		=> '<div class="'.$class.'"><h4 class="hw-title-box"><div class="hw-tab-main"><span>',
        	'after_title'   	=> '</span></div></div><aside class="widget-container">',
		)); 
	} 	 
  	if (  $footer_column=='6')  {
		register_sidebar(array(
			'name' 				=> esc_html__('Footer Column 6' , 'hexwp'), 
			'id' 				=> 'sidebar_footer_6',
			'before_widget'		=> '<div id="%1$s" class="widget %2$s">',
			'after_widget' 		=> '</aside></div>',
 			'before_title' 		=> '<div class="'.$class.'"><h4 class="hw-title-box"><div class="hw-tab-main"><span>',
        	'after_title'   	=> '</span></div></div><aside class="widget-container">',
		)); 
	} 	 	
	
	if (   !empty( $smof_data[ 'above_content' ])) {
		register_sidebar(array(
			'name' 				=> esc_html__('Above Content For Ads' , 'hexwp'), 
			'id' 				=> 'sidebar_above_content_ads', 
			'description'   	=> esc_html__('just for show ADS widget' , 'hexwp'),		
			'before_widget'		=> '<div id="%1$s" class="widget %2$s">',
			'after_widget' 		=> '</aside></div>',
 			'before_title' 		=> '<div class="'.$class.'"><h4 class="hw-title-box"><div class="hw-tab-main"><span>',
        	'after_title'   	=> '</span></div></div><aside class="widget-container">',
 		)); 
	} 
  
	if ( !empty( $smof_data[ 'below_content' ] )) {
		register_sidebar(array(
			'name' 				=> esc_html__('Below Content For Ads' , 'hexwp'), 
			'id'				=> 'sidebar_below_content_ads',
			'description'   	=> esc_html__('just for show ADS widget' , 'hexwp'),		
			'before_widget'		=> '<div id="%1$s" class="widget %2$s">',
			'after_widget' 		=> '</aside></div>',
 			'before_title' 		=> '<div class="'.$class.'"><h4 class="hw-title-box"><div class="hw-tab-main"><span>',
        	'after_title'   	=> '</span></div></div><aside class="widget-container">',
		)); 
     }  
	 
	if (  !empty($smof_data[ 'above_center' ]  ) ) {
          register_sidebar(array(
			'name' 				=> esc_html__('Above Main Column For Ads' , 'hexwp'),
			'id' 				=> 'sidebar_above_center_ads', 
			'description'   	=> esc_html__('just for show ADS widget' , 'hexwp'),		
			'before_widget'		=> '<div id="%1$s" class="widget %2$s">',
			'after_widget' 		=> '</aside></div>',
 			'before_title' 		=> '<div class="'.$class.'"><h4 class="hw-title-box"><div class="hw-tab-main"><span>',
        	'after_title'   	=> '</span></div></div><aside class="widget-container">',
 		)); 
     }  
	 
	if (   !empty( $smof_data[ 'below_center' ])) {
		register_sidebar(array(
			'name' 				=> esc_html__('Below Main Column Area' , 'hexwp'), 
			'id' 				=> 'sidebar_below_center_ads',
			'before_widget'		=> '<div id="%1$s" class="widget %2$s">',
			'after_widget' 		=> '</aside></div>',
 			'before_title' 		=> '<div class="'.$class.'"><h4 class="hw-title-box"><div class="hw-tab-main"><span>',
        	'after_title'   	=> '</span></div></div><aside class="widget-container">',
		)); 
	} 

	if (   !empty( $smof_data[ 'below_article' ] ) ) {
		register_sidebar(array(
			'name' 				=> esc_html__('Below Article Area' , 'hexwp'), 
			'id' 				=> 'sidebar_below_article_ads',
			'description'   	=> esc_html__('just for show ADS widget' , 'hexwp'),		
			'before_widget'		=> '<div id="%1$s" class="widget %2$s">',
			'after_widget' 		=> '</aside></div>',
 			'before_title' 		=> '<div class="'.$class.'"><h4 class="hw-title-box"><div class="hw-tab-main"><span>',
        	'after_title'   	=> '</span></div></div><aside class="widget-container">',
        )); 
     }  
	    
 
	if (   !empty( $smof_data[ 'widget_header' ] ) ) {
		register_sidebar(array(
			'name' 				=> esc_html__('Widget in Header' , 'hexwp'), 
			'id' 				=> 'widget_header',
			'description'   	=>   esc_html__('just for show ADS widget' , 'hexwp'),				
			'before_widget'		=> '<div id="%1$s" class="widget %2$s">',
			'after_widget' 		=> '</aside></div>',
 			'before_title' 		=> '<div class="'.$class.'"><h4 class="hw-title-box"><div class="hw-tab-main"><span>',
        	'after_title'   	=> '</span></div></div><aside class="widget-container">',
 
        )); 
     }	   
	}
	    
}