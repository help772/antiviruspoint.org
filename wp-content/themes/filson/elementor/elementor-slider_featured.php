<?php
 
class hexwp_element_slider_featured extends \Elementor\Widget_Base {

 
	public function get_name() {
		return hexwp_slug().'_slider_featured';
	}

 
	public function get_title() {
		return __( 'Slider Featured', 'hexwp' );
	}

 
	public function get_icon() {
		return 'eicon-posts-group';
	}
	public function get_categories() {
		return [ 'hexwp' ];
	}


protected function register_controls() {
 
 	 
 		 $this->register_controls_general();
		 
		$this->register_controls_layout();
		 
 		$this->register_controls_post_style();
   
	}
    




/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

															Blog General
																		
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
	protected function register_controls_general(){
  		$this->start_controls_section(
			'general',
			[
				'label'			=> __( 'General', 'hexwp' ),
			]
		); 
				
		$this->add_control(
			'number',
			[
				'label'			=>__('Number of Posts to show','hexwp'),
				'type'			=> \Elementor\Controls_Manager::NUMBER ,
				"default"		=> '5' ,
			]
		); 		
		$slider_array = array();
		$sliders_obj = get_categories('taxonomy=sao_sliders&type=sao_slides&hide_empty=0'); 
		if(!empty($sliders_obj) && is_array($sliders_obj) ){
		foreach ($sliders_obj as $slider_item) {
			$slider_array[$slider_item->slug] = $slider_item->cat_name;
		}	 
		}
		$this->add_control(
			'sliders',
			[
				'label'			=> __('Sliders','hexwp'),
				'type'			=> \Elementor\Controls_Manager::SELECT,
				'multiple'		=> true,
				'options'		=> $slider_array,	
			]
		); 
 		$this->end_controls_section();
	
 	}
	
	protected function register_controls_layout(){
	/*****************************************************************************************************************************************************
	******************************************************************************************************************************************************

															Slider Layout
																		
	*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////         
		$this->start_controls_section(
			'layout_section',
			[
				'label' => __( 'Layout', 'hexwp' ),
			]
		);
  			
		$column=__('Column','hexwp');
		$layout=__('Layout','hexwp');
		$this->add_control(
			'featured_layout',
			[
				'label'			=> __('Glider Layout','hexwp'),
				'type'			=> \Elementor\Controls_Manager::SELECT,
				'default' => 'featured_3',
  				"options"		=> array(
						"featured_1"		=> "1 $column", 
						"featured_2"		=> "2 $column", 
						"featured_3"		=> "3 $column", 
						"featured_4"		=> "4 $column", 
						"featured_5"		=> "5 $column", 
						"featured_6"		=> "6 $column", 
						"featured_7"		=> "7 $column", 
						"featured_8"		=> "8 $column",
						"featured_9"		=> "$layout 9 ",
						"featured_10"		=> "$layout 10",
						"featured_11"		=> "$layout 11",
						"featured_12"		=> "$layout 12",
						"featured_13"		=> "$layout 13",
						"featured_14"		=> "$layout 14",
						"featured_15"		=> "$layout 15",
						"featured_16"		=> "$layout 16",
						"featured_17"		=> "$layout 17",
						"featured_18"		=> "$layout 18", 
						"featured_19"		=> "$layout 19",
						"featured_20"		=> "$layout 20",
						"featured_21"		=> "$layout 21",
						"featured_22"		=> "$layout 22",
						"featured_23"		=> "$layout 23",
						"featured_24"		=> "$layout 24",
						"featured_25"		=> "$layout 25",
						"featured_26"		=> "$layout 26",
						"featured_27"		=> "$layout 27",
						"featured_28"		=> "$layout 28",
						"featured_29"		=> "$layout 29",
						"featured_30"		=> "$layout 30",
						"featured_31"		=> "$layout 31",
						"featured_32"		=> "$layout 32",
					
				)
			]
		);	
			
		$this->add_control(
			'responsive_column',
			[ 	
				"label"			=> __('Column in Tablet and Mobile','hexwp'),
				'type'			=> \Elementor\Controls_Manager::SELECT,
				"options" 		=> hexwp_array_options('first_responsive_column',true), 
		
				]
		);
		 
		  
		$this->add_control(
			'between',
			[
				'label'			=> __('Space Between Item','hexwp'),
				'type'			=> \Elementor\Controls_Manager::SELECT,
				'options'		=> hexwp_array_options('between',true),
			]
		);	
 
		$this->add_control(
			'ratio',
			[
				'label'			=> __('Image Ratio','hexwp'),
				'type' 			=> \Elementor\Controls_Manager::SELECT,
				'options'		=> hexwp_array_options('ratio',true),
			]
		);	  
		  
		 
		
 
	  
		$this->end_controls_section();
 	}
 
	/*****************************************************************************************************************************************************
	******************************************************************************************************************************************************

															Post Style
																		
	*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////        
	protected function register_controls_post_style(){
		$this->start_controls_section(
			'post_style',
			[
				'label'			=> __('Post Style','hexwp'),
				'tab'			=> \Elementor\Controls_Manager::TAB_STYLE,
						
			]
		);
			
		$this->add_control(
			'background_color',
			[
				'label'			=>__('Background Color','hexwp'),
				'type'			=> \Elementor\Controls_Manager::COLOR,
				'selectors' 	=> array(
							'{{WRAPPER}} [class*="hw-el-"]' => '--hw-post-bg: {{VALUE}}',
					),	
			]
		);		
		$this->add_control(
			'box_border_color',
			[
				'label'			=>__('Box Shadow Color','hexwp'),
				'type'			=> \Elementor\Controls_Manager::COLOR,
				'selectors' 	=> array(
							'{{WRAPPER}} [class*="hw-el-"]' => '--hw-post-sd: {{VALUE}};--hw-post-hv-sd: {{VALUE}};',
					),	
			]
		); 	
		
			
		$this->add_control(
			'radius',
			[
				'label'			=>__('Border Radius','hexwp'),
				'type'			=> \Elementor\Controls_Manager::SELECT,
				"options"		=>   hexwp_array_options('radius_mini',true),
				'selectors' 	=> array(
							'{{WRAPPER}} [class*="hw-el-"]' => '--hw-main-rd: {{VALUE}}',
					),
			]
		); 	 
		$this->add_control(
			'image_effect',
			[
				'label' 		=>__('Hover Image Effect','hexwp'),
				'type' 			=> \Elementor\Controls_Manager::SELECT,
				"options"		=>   hexwp_array_options('hover_image',true)
			]
		); 
		$this->end_controls_section();

 	}		
  	protected function render() {
 		$option = $this->get_settings_for_display();
 		$args=array();
		$args['key']= $this->get_id();
	
 		 
 		$args['option'] = array(
		
 
  			//General
   			'number'					=> hexwp_settings($option,'number'),
   			'sliders'					=>  hexwp_settings($option,'sliders'), 
   			'featured_layout'			=> hexwp_settings($option,'featured_layout'),
 			'between' 					=> hexwp_settings($option,'between'),
 			'responsive_column'			=> hexwp_settings($option,'responsive_column'),
			'ratio' 					=> hexwp_settings($option,'ratio'),
 
  			'background_color'			=> hexwp_settings($option,'background_color'),
 			'box_border_color'			=> hexwp_settings($option,'box_border_color'),
 			'radius' 					=> hexwp_settings($option,'radius'),
  			'image_effect' 				=> hexwp_settings($option,'image_effect'), 
		); 
		
		?>
		<div class="hw-elementor-<?php echo esc_attr($this->get_id());?>">      
			<?php echo hexwp_slider_featured_config($args, true);
 
 		
 		 	if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {?>
			 <div class="hw-elementor-script">     
					<script type="text/javascript">
					  (function($) {
						'use strict';
						jQuery(document).ready(function() {				
 
							$('.hw-elementor-<?php echo $this->get_id();?>').hexwp_elementor();
							setTimeout(function(){ 
															$('.hw-elementor-<?php echo $this->get_id();?>').sao_slider();
	 
 							$('.hw-elementor-<?php echo $this->get_id();?>').sao_custom_slider();
							}, 1000);

						 });
						})(jQuery);
 	               </script>
		
			</div>
		<?php		
		 }
		  		

	}
	
}
 
?>
