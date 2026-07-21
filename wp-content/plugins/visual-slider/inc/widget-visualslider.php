<?php // Register Posts Box 4 Widget
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
 if ( !function_exists ( "register_visualslider_widget" )){
add_action( 'widgets_init', 'register_visualslider_widget' );
 function register_visualslider_widget() {
    register_widget( 'visualslider_widget' );
}
}
if ( ! class_exists( 'visualslider_widget' ) ) {
class visualslider_widget extends WP_Widget {
 	function __construct() {
		parent::__construct(
			'visualslider',__('Visual Slider', 'visual-slider')
			);
	}

    /**********  The widget For Display *******/
 	public function widget( $args, $instance ) {
		extract( $args );
 		$title = apply_filters( 'widget_title', $instance[ 'title' ] );
 		$beyshop_data =$instance;
	 
		global $smof_data;
 		$beyshop_style_layout = '';
		?>
		<div id="<?php echo esc_attr($widget_id) ?>" class="vs-element-visualslider ">
		 <?php 
 		    // Define output and open element div.
			if(!empty($instance['sliders'])){
             vs_slider_config($instance['sliders']);
			}
 			?>
		</div>
	<?php }

    /********** Update the widget info from the admin panel *******/
 	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
  		$instance[ 'sliders' ] = wp_strip_all_tags( $new_instance[ 'sliders' ] );
 		
    	return $instance;
	}
    
	/********** Display the widget update form *******/
 	public function form( $instance ) { 
 		$instance = wp_parse_args( (array) $instance,[] );
		$title = !empty( $instance[ 'title' ] ) ? $instance[ 'title' ] : '';

		$sliders = !empty( $instance[ 'sliders' ] ) ? $instance[ 'sliders' ] : '';
	 
 
		$page_args = array(
			'sort_order' => 'asc',
			'sort_column' => 'post_title',
 
			'post_type' => 'visualslider',
			'post_status' => 'publish'
		); 
		 
		$options_page = array();
		$options_page_obj =get_posts($page_args); 
 		$options_page[''] = __('Default','visual-slider');

		if(!empty($options_page_obj) && is_array($options_page_obj) ){
		foreach ($options_page_obj as $rezapage) {
			$options_page[$rezapage->ID] = $rezapage->post_title;
		}
	}			 	 
	 
 		$slider_array=$options_page; 
		
		
		
 
  		 ?>
		 
        
		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php echo esc_html__('Title' , 'visual-slider' );?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" value="<?php echo esc_attr($title); ?>" class="widefat" type="text" style="width:100%;" />
		</p>        
        
		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'sliders' )); ?>"><?php echo esc_html__( 'Select Slider', 'visual-slider' );?></label>
			<select id="<?php echo esc_attr($this->get_field_id( 'sliders' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'sliders')); ?>"  >
				<?php foreach (  $slider_array as $key => $name ) {?>
			 	<option value="<?php echo esc_attr($key) ?>"<?php echo selected( $sliders, $key ) ?>><?php echo esc_html($name); ?></option>
				<?php }?> 
			</select>
		</p> 
 		   
                  
  	<?php
 	}
}
} 