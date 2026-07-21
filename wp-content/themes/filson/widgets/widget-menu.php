<?php 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Register Widget Product
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_action( 'widgets_init', 'hexwp_register_widget_menu' );
 function hexwp_register_widget_menu() {
    register_widget( 'hexwp_widget_menu' );
}

class hexwp_widget_menu extends WP_Widget {
 	function __construct() {
		parent::__construct(
			hexwp_slug().'_menu',
			__('hexwp','hexwp').' - '.  __('Menu', 'hexwp') 
		);
	}
	public function option(){
		$option[]= array( 
		"name"			=> esc_html__('Title Box','hexwp'),
 		"id"			=> "title",
 		"default"		=> esc_html__('Title Box','hexwp'),
 		"type"			=> "text",
 		   
	);
					
 	$option[]= array( 
		"name"			=> esc_html__('Select Menu','hexwp'),
 		"id"			=> "menu",
  		"type"			=> "select",
 		"options"		=>  hexwp_category_array_options('menu'),						
 	);	
	
	$option[]= array( 
		"name"			=> esc_html__('Max Count','hexwp'),
 		"id"			=> "number",
 		"default"		=> 10,
  		"type"			=> "number",
 		  
	);
 
  
 	
		return $option;
    }	
	
   /********** Update the widget info from the admin panel *******/
 	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
  		return	hexwp_widget_options_save( $new_instance, $old_instance,$this->option() );
 	}
 
	/********** Display the widget update form *******/
 	public function form( $instance ) { 
		$defaults = array( 'number' => '5', 'image_size' => 'full' );
		$instance = wp_parse_args( (array) $instance, $defaults );
 
		hexwp_widget_options($instance,$this->id_base,$this->number,$this->option());
  
 	}	
    /**********  The widget For Display *******/
 	public function widget( $args, $instance ) {
		extract( $args );
 		$title = apply_filters( 'widget_title', $instance[ 'title' ] );
 		$filson_data =$instance;
 		$count = 0;
		 $args=array();
	
 		$args['key'] = 'widget_menu';
 
		$args['option'] =array(
 			'title' => !empty( $instance['title'] ) ? $instance['title'] : '',
 			'number' => !empty( $instance['number'] ) ? $instance['number'] : '',
 			'menu' => !empty( $instance['menu'] ) ? $instance['menu'] : '',
 			 
		) ; 
 
		global $smof_data; 
		?>
 
		<div id="<?php echo esc_attr($widget_id) ?>" class="hw-element-menu hw-widget-post hw-element-item">
 			 <?php 	echo hexwp_menu_config($args, true);?>
		</div>
        
        
	<?php 
	
	}

    /********** Update the widget info from the admin panel *******/
 
}
 ?>