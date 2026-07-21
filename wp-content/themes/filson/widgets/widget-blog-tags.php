<?php 

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Register Widget Blog Tags
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_action( 'widgets_init', 'hexwp_register_blog_tags' );
 function hexwp_register_blog_tags() {
    register_widget( 'hexwp_blog_tags' );
}
class hexwp_blog_tags extends WP_Widget {
	function __construct() {
		parent::__construct(
			hexwp_slug().'_blog_tags',
			 __('hexwp','hexwp').' - '.esc_html__('Blog Tags' , 'hexwp') 
		);
	}
		public  function option(){
		$option=array();
 
     	$option[]=array(
				'name' =>__('Title' , 'hexwp' ),
 				'id' => 'title',
 				'type' => 'text'
		);
     	$option[]=array(
				'name' =>__('Count' , 'hexwp' ),
 				'id' => 'count',
 				'type' => 'number'
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
	
		$defaults = array( 'title'=>__('Tags' , 'hexwp' ));
		$instance = wp_parse_args( (array) $instance, $defaults );
 
		hexwp_widget_options($instance,$this->id_base,$this->number,$this->option());
  
 	}	
    /**********  The widget For Display *******/
	function widget( $args, $instance ) {
 		extract( $args );
 		$title = apply_filters('widget_title', $instance['title'] );
  		$count =  !empty( $instance['count'] ) ? $instance['count'] : '25';
  		echo wp_kses_post($args['before_widget']); ?>


		<?php if( !empty($title)){ ?>
        
 			<?php 
            echo wp_kses_post($before_title);
 			echo esc_html($title); 
 			echo wp_kses_post($after_title);
		}
		?>
			<div class="hw-tags-box ">
				<?php wp_tag_cloud( $tags = array( 'largest' => 8,'number' => $count, 'orderby'=> 'count', 'order' => 'DESC' )); ?>
			</div>
  		<?php  
  		echo wp_kses_post($args['after_widget']); 
	
	}
    

}