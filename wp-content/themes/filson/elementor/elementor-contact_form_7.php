<?php
 
class hexwp_element_content_form_7 extends \Elementor\Widget_Base {

 
	public function get_name() {
		return hexwp_slug().'_content_form_7';
	}

 
	public function get_title() {
		return __( 'Content Form 7', 'hexwp' );
	}

 
	public function get_icon() {
		return 'eicon-post-list';
	}
	public function get_categories() {
		return [ 'hexwp' ];
	}


protected function register_controls() {
 		  
		$this->register_controls_general();
 	  	$this->register_controls_layout();
 	 	$this->register_controls_style();
 		$this->register_controls_typography(); 
  
	}
     
	protected function register_controls_general(){
			$this->start_controls_section(
				'general',
				[
					'label'			=> __( 'General', 'hexwp' ),
				]
			); 	
 
			$this->add_control(
				'contactform_id',
				[
					'label'			=>__('Contact Form','hexwp'),
					'type'			=> \Elementor\Controls_Manager::SELECT ,
					"options"		=> hexwp_category_array_options('contactform'),			
				]
			); 		 
			$this->end_controls_section();


	} 		 

	protected function register_controls_layout(){
			$this->start_controls_section(
				'layout_contact',
				[
					'label'			=> __( 'Layout', 'hexwp' ),
				]
			); 	
 
			$this->add_control(
				'height',
				[
					'label'			=>__('Field Height','hexwp'),
					'type'			=> \Elementor\Controls_Manager::NUMBER ,
					'selectors' 	=> array(
								'{{WRAPPER}} [class*="hw-el-"]' => '--hw-input-ht: {{VALUE}}px',
					),			
  			
   				]
			); 	
			
			$this->add_control(
				'textarea_height',
				[
					'label'			=>__('Textarea Height','hexwp'),
					'type'			=> \Elementor\Controls_Manager::NUMBER ,
					'selectors' 	=> array(
								'{{WRAPPER}} [class*="hw-el-"]' => '--hw-textarea-ht: {{VALUE}}px',
					),		
 
 				]
			); 	
 
			$this->add_control(
				'between',
				[
					'label'			=>__('Between Field','hexwp'),
					'type'			=> \Elementor\Controls_Manager::SELECT ,
					"options"		=>	hexwp_array_options('between',true)
							 
 				]
			); 	
  
	 	 
			$this->end_controls_section();

	} 		 
  
 	
	protected function register_controls_style(){
 			
			
		$this->start_controls_section(
			'style',
			[
				'label'			=> __( 'Style', 'hexwp' ),
				'tab'			=> \Elementor\Controls_Manager::TAB_STYLE,
			]
		); 	
			
			
		$this->add_control(
			'text_color',
			[
				'label'			=>__('Text Color','hexwp'),
				'type'			=> \Elementor\Controls_Manager::COLOR,
				'selectors' 	=> array(
								'{{WRAPPER}} [class*="hw-el-"]' => '--hw-main-lk: {{VALUE}}',
					),	
			]
		); 		
			
		$this->add_control(
			'field_background_color',
			[
				'label'			=>__('Field Background Color','hexwp'),
				'type'			=> \Elementor\Controls_Manager::COLOR,
				'selectors' 	=> array(
								'{{WRAPPER}} [class*="hw-el-"]' => '--hw-form-bg: {{VALUE}}',
					),
				
			]
		); 			
	 
		$this->add_control(
			'field_text_color',
			[
				'label'			=>__('Field Text Color','hexwp'),
				'type'			=> \Elementor\Controls_Manager::COLOR,
				'selectors' 	=> array(
								'{{WRAPPER}} [class*="hw-el-"]' => '--hw-form-txt: {{VALUE}}',
				),
			]
		); 			
	  
		$this->add_control(
			'field_border_color',
			[
				'label'			=>__('Field Border Color','hexwp'),
				'type'			=> \Elementor\Controls_Manager::COLOR,
				'selectors' 	=> array(
								'{{WRAPPER}} [class*="hw-el-"]' => '--hw-main-br-cr: {{VALUE}}',
				),
			]
		); 
 							
		$this->add_control(
			'button_background_color',
			[
				'label'			=>__('Button Background Color','hexwp'),
				'type'			=> \Elementor\Controls_Manager::COLOR,
				'selectors' 	=> array(
								'{{WRAPPER}} [class*="hw-el-"]' => '--hw-primary-bg: {{VALUE}}',
				),
			]
		); 
 		$this->add_control(
			'button_text_color',
			[
				'label'			=>__('Button Text Color','hexwp'),
				'type'			=> \Elementor\Controls_Manager::COLOR,
				'selectors' 	=> array(
								'{{WRAPPER}} [class*="hw-el-"]' => '--hw-primary-txt: {{VALUE}}',
				),
			]
		);  
		
		
 		$this->add_control(
			'border_radius',
			[
				'label'			=>__('Border Radius','hexwp'),
				'type'			=> \Elementor\Controls_Manager::SELECT,
				"options"	=>  hexwp_array_options('radius',true),
				'selectors' 	=> array(
								'{{WRAPPER}} [class*="hw-el-"]' => '--hw-btn-rd: {{VALUE}}',
					),
			]
		);  		
		
		
 		$this->add_control(
			'textarea_radius',
			[
				'label'			=>__('Textarea Border Radius' , 'hexwp'),
				'type'			=> \Elementor\Controls_Manager::SELECT,
				"options"		=>  hexwp_array_options('radius_mini',true),
				'selectors' 	=> array(
								'{{WRAPPER}} [class*="hw-el-"]' => '--hw-main-rd: {{VALUE}}',
					),
			]
		);  	
		$this->end_controls_section();
  	

	} 		 
	 
	protected function register_controls_typography(){

		 $this->start_controls_section(
			'typography',
			[
				'label' => __('Typography','hexwp'),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
						
			]
		);
		   
		$this->add_control(
			'text_font_size',
			[
				'label'			=>__('Font Size','hexwp'),
				'type'			=> \Elementor\Controls_Manager::NUMBER ,
				'selectors' 	=> array(
								'{{WRAPPER}} [class*="hw-el-"]' => '--hw-form-fn-sz: {{VALUE}}px',
				),
				
			]
		); 
		
		$this->add_control(
			'text_font_weight',
			[
				'label'			=>__('Font Weight','hexwp'),
				'type'			=> \Elementor\Controls_Manager::SELECT ,
				'options'		=> hexwp_array_options('font_weight'),
				'selectors' 	=> array(
								'{{WRAPPER}} [class*="hw-el-"]' => '--hw-form-fn-wt: {{VALUE}}',
					),
			]
		); 		
		
			$this->end_controls_section();
		
 	}	  
	protected function render() {
 		$option = $this->get_settings_for_display();
 		$args=array();
		$args['key']= $this->get_id();
		
 		
 		$args['option'] = array(
		
			/*****************************************************************************************************************
			General
			******************************************************************************************************************/
				'contactform_id'					=> hexwp_settings($option,'contactform_id'),
				'height'							=>hexwp_settings($option,'height'),
				'textarea_height'					=> hexwp_settings($option,'textarea_height'),
				'between' 							=> hexwp_settings($option,'between'),
 
  			/*****************************************************************************************************************
			Style
			******************************************************************************************************************/
				'text_color' 						=> hexwp_settings($option,'text_color'),
				'field_background_color'			=>  hexwp_settings($option,'field_background_color'),
				'field_text_color'					=>  hexwp_settings($option,'field_text_color'),
				'field_border_color'				=> hexwp_settings($option,'field_border_color'),
				'button_color'			=> array(
					'background'					=> hexwp_settings($option,'button_background_color'),
					'text'							=> hexwp_settings($option,'button_text_color'),
				),
				'border_radius' 				=> hexwp_settings($option,'border_radius'),
				'textarea_radius'				=> hexwp_settings($option,'textarea_radius'),
 
			/*****************************************************************************************************************
			Typo
			******************************************************************************************************************/
   				'text_typo'					=> hexwp_elmentor_typo_css($option,'text'),
			
     	); ?>
   
 		 <div class="hw-elementor-<?php echo esc_attr($this->get_id());?>">      
  			
            
            <?php echo hexwp_contactform_config($args,true);?>
		  
  		 
			<?php if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {?>
                 <div class="hw-elementor-script">     
                        <script type="text/javascript">
                          (function($) {
                            'use strict';
                            jQuery(document).ready(function() {				
                                $('.hw-elementor-<?php echo esc_html($this->get_id());?>').hexwp_elementor();
        
                             });
                            })(jQuery);
                       </script>
            
                </div>
			<?php }?>	
	
    	</div>
   
		 <?php
		 } 
	
}
