<?php  
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Register Widget ADS
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_action( 'widgets_init', 'register_hexwp_widget_ads' );
 function register_hexwp_widget_ads() {
    register_widget( 'hexwp_widget_ads' );
}

class hexwp_widget_ads extends WP_Widget {
	function __construct() {
		parent::__construct(
			hexwp_slug().'_widget_ads',
			 __('hexwp','hexwp').' - '.esc_html__('Ads Banner' , 'hexwp')
		);
	}
	public  function option(){
		$option=array();
 
     	$option[]=array(
				'name' =>__('Ads Image path' , 'hexwp' ),
 				'id' => 'ads_img',
 				'type' => 'text'
		);
		$option[]= array( 
			"name"			=> esc_html__('Ads link','hexwp'),
			"id"			=> "ads_url",
 			"type"			=> "text",
			  
		);
	
		 
		$option[]= array( 
			"name"			=> esc_html__('Full Size Image','hexwp'),
			"id"			=> "resize",
			"type"			=> "checkbox",
 		);
			
		 
		$option[]= array( 
				"name"			=> esc_html__('Open in New Window','hexwp'),
				"id"			=> "window",
				"type"			=> "checkbox",
		);		
		
 		$option[]= array( 
			"name"			=> esc_html__('Nofollow','hexwp'),
			"id"			=> "nofollow", 	
			"type"			=> "checkbox",
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
 ?>
		<div  class="hw-el-widget_ads">
	 
			<div class="hw-ads <?php if ( !empty($instance[ 'resize' ]) ) {echo 'hw-resize';}?>">
                <a href="<?php if(!empty($instance[ 'ads_url' ])){echo esc_url(@$instance[ 'ads_url' ] );}?>"   <?php if ( !empty($instance[ 'window' ]) ) echo 'target="_blank"' ; ?> <?php if ( !empty($instance[ 'nofollow' ]) ) echo 'rel="nofollow"'?> >
               	<?php if(!empty( $instance[ 'ads_img' ])){ ?>
   					<img alt="ads" src="<?php echo esc_url( $instance[ 'ads_img' ] ); ?>" />
              	<?php }?>
 				</a> 		
 			</div>
            
		</div>
	<?php
	}
    

}