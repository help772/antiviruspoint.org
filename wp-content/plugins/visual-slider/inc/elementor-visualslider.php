<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																 Elementor sorshyant_slider
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	  

if ( ! class_exists( 'visualslider_slider' ) ) {
class visualslider_slider extends \Elementor\Widget_Base {

 
	public function get_name() {
		return 'visualslider_slider';
	}

 
	public function get_title() {
		return __('Visual Slider', 'visual-slider');
	}

 
	public function get_icon() {
		return 'eicon-slides';
	}
 


protected function  register_controls() {
 
  	 $this->register_controls_general(); 

	}
    
 
 	protected function register_controls_general(){
 	
 		 

		$this->start_controls_section(
			'general',
			[
				'label'			=> __( 'General', 'visual-slider' ),
			]
		); 
		
	$page_args = array(
			'sort_order' => 'asc',
			'sort_column' => 'post_title',
			'numberposts'      => -1,
			'post_type' => 'visualslider',
			'post_status' => 'publish'
		); 
		 
		$options_page = array();
		$options_page_obj =get_posts($page_args); 
 
		if(!empty($options_page_obj) && is_array($options_page_obj) ){
		foreach ($options_page_obj as $rezapage) {
			$options_page[$rezapage->ID] = $rezapage->post_title;
		}
	}			 
		$this->add_control(
			'sliders',
			[
				'label'			=> __( 'Select Slider','visual-slider'),
				'type'			=> \Elementor\Controls_Manager::SELECT,
				'multiple'		=> true,
				'options'		=> $options_page,	
			]
		);
			   
				
		 $this->end_controls_section();

 
 	}
	
	 
	 
 	 
	protected function render() {
 		$option = $this->get_settings_for_display();
		 
		 
 

		 if(!empty($option['sliders'])){
		 ?>
		<div  class="vs-elementor-<?php echo esc_attr($this->get_id());?> vs-elementor-item">
        <?php   vs_slider_config($option['sliders']);?>
 	<?php
	   	 
	 if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
		 if(!empty($option['sliders'])){
		 	$setting_json = get_post_meta($option['sliders'], 'vs_setting_json', true);
 		 	$setting= vs_options_array_row($setting_json);
if(empty($setting['disable_typography'])){
	 echo '<'.esc_attr('link').' rel="'.esc_attr('stylesheet').'" id="vs_fontfamily-css"  href="'.esc_url(VISUALSLIDER_DIR.'assets/css/fontfamily.css').'" '.esc_attr('media').'="all" />';

 	}}
		vs_icon_fonts(); 
		 ?>
			 <div class="sao-elementor-script">     
					<script type="text/javascript">
					  (function($) {
						'use strict';
							jQuery(document).ready(function() {
 	 	 					setTimeout(function(){  
      								$('.vs-elementor-<?php echo esc_html($this->get_id());?>').vs_custom_slider();
 
 								 
							}, 1000);
									 
   							}); 
							 
 						})(jQuery);
 	               </script>
		
			</div>
		<?php		
	   }	
	   ?> 
         </div> 
 
	
	<?php
    }
	}
	
}
}
 
