<?php 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Register Widget Socail
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_action( 'widgets_init', 'hexwp_register_widget_social' );
 function hexwp_register_widget_social() {
    register_widget( 'hexwp_widget_social' );
}
class hexwp_widget_social extends WP_Widget {
	function __construct() {
		parent::__construct(
			hexwp_slug().'_widget_social', // Base ID
			 __('hexwp','hexwp').' - '.esc_html__('Social Icon', 'hexwp') );
	}	
		public  function option(){
		$option=array();
  		$option=array();
     	$option[]=array(
				'name' =>__('Title' , 'hexwp' ),
 				'id' => 'title',
 				'type' => 'text'
		);
		 
		$option[]= array( 
			
		
			"name"			=> esc_html__('Space Between Item','hexwp'),
			"id"			=> "between",
			"group"			=>  esc_html__('Layout','hexwp'),
			"type"			=> "select",
 			"options"		=>  hexwp_array_options('between',true),						

		); 	 	
		
		$option[]= array( 
			"name"			=> __('Icon Style','hexwp'),
			"id"			=> "icon_style",
			"group"			=>  esc_html__('Social Icon','hexwp'),
			"type"			=> "select",
			"options"		=> array(
				"style-1" => esc_html__('Style 1: only icon','hexwp'),
				"style-2" => esc_html__('Style 2: Boxed Icon','hexwp'),
				"style-3" => esc_html__('Style 3: Boxed Original Color','hexwp'),
			),						
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
		$defaults = array( 'title'=>__('Social' , 'hexwp' ),'icon_style'=>'style-3');
		$instance = wp_parse_args( (array) $instance, $defaults );
 
		hexwp_widget_options($instance,$this->id_base,$this->number,$this->option());
  
 	}	
    /**********  The widget For Display *******/
	function widget( $args, $instance ) {
		global $smof_data;
		
		extract( $args );
	
 		$args['key'] = 'widget_social';
 
 
	$between_class= !empty($instance['between']) ?'hw-gap-'.$instance['between']:'hw-gap-5px';
	$icon_size= !empty($instance['icon_size']) ?$instance['icon_size']:'';
	$style = !empty($instance['icon_style']) ?$instance['icon_style']:'style-1';		
		
	$classes = array(
  		'hw-el-widget_social',
    		 $between_class,
    	);?>
        
		<aside class="<?php echo esc_attr(join( ' ', $classes ));?> ">
			
			<?php hexwp_post_title_tabs($instance);?>
 			
            <div class="hw-gap-warp ">
			<div class="hw-item-list  hw-social-icon-<?php echo esc_attr($style);?>">
 				<?php hexwp_social_content($style,$smof_data,'social_');?>
   	     	</div>
			</div>
		</aside>  
	<?php
 	}
}
