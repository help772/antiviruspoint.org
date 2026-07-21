<?php
 
class hexwp_element_comments extends \Elementor\Widget_Base {

 
	public function get_name() {
		return hexwp_slug().'_comments';
	}

 
	public function get_title() {
		return __( 'Comments', 'hexwp' );
	}

 
	public function get_icon() {
		return 'eicon-post-list';
	}
	public function get_categories() {
		return [ 'hexwp' ];
	}


protected function register_controls() {
 		  
  	  	$this->register_controls_layout();
 	 	$this->register_controls_style();
 		$this->register_controls_typography(); 
  
	}
     
	 
	protected function register_controls_layout(){
			$this->start_controls_section(
				'layout_contact',
				[
					'label'			=> __( 'Layout', 'hexwp' ),
				]
			); 	
 
			$this->add_control(
				'comments_layout_type',
				[
					'label'			=>__('Comments Layout','hexwp'),
					'type'			=> \Elementor\Controls_Manager::SELECT,
					"options"		=>  array(
							"" 				=>  esc_html__('Default','hexwp'),
							'hw-thread' 	=> esc_html__('Thread' , 'hexwp'),
							'hw-list' 		=> esc_html__('List' , 'hexwp'),
					),	
				]
			); 	
					
			$this->add_control(
				'box_layout',
				[
					'label'			=>__('Box Layout','hexwp'),
					'type'			=> \Elementor\Controls_Manager::SELECT,
					"options"		=>  array(
						"" 				=>  esc_html__('Default','hexwp'),
						"none"			=> esc_html__('None','hexwp'),
						"boxed" 		=> esc_html__('Boxed','hexwp'),
					),	
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
			'text_color',
			[
				'label'			=>__('Text Color','hexwp'),
				'type'			=> \Elementor\Controls_Manager::COLOR,
				'selectors' 	=> array(
								'{{WRAPPER}} [class*="hw-el-"]' => '--hw-main-txt: {{VALUE}}',
					),	
			]
		); 				
		
		
		
		$this->add_control(
			'label_color',
			[
				'label'			=>__('Label Color','hexwp'),
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
			'border_color',
			[
				'label'			=>__('Border Color','hexwp'),
				'type'			=> \Elementor\Controls_Manager::COLOR,
				'selectors' 	=> array(
								'{{WRAPPER}} [class*="hw-el-"]' => '--hw-main-br-cr: {{VALUE}}',
				),
			]
		); 	
		
		$this->add_control(
			'box_border_color',
			[
				'label'			=>__('Box Shadow Color','hexwp'),
				'type'			=> \Elementor\Controls_Manager::COLOR,
				'selectors' 	=> array(
								'{{WRAPPER}} [class*="hw-el-"]' => '--hw-post-sd: {{VALUE}}',
				),
			]
		); 	
				
		
 		$this->add_control(
			'border_radius',
			[
				'label'			=>__('Input and Button Border Radius','hexwp'),
				'type'			=> \Elementor\Controls_Manager::SELECT,
				"options"	=>  hexwp_array_options('radius',true),
				'selectors' 	=> array(
								'{{WRAPPER}} [class*="hw-el-"]' => '--hw-main-btn-rd: {{VALUE}}',
					),
			]
		);  		
		
		
 		$this->add_control(
			'textarea_radius',
			[
				'label'			=>__('Border Radius' , 'hexwp'),
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
		   
		 //*****************************************---------Title Typography------------------********************************************
		$this->add_control(
			'author_typo',
			[
				'label' 		=>__('Author Typography','hexwp'),
				'type' 			=> \Elementor\Controls_Manager::HEADING,
				'separator'		=> 'before',
			]
		);
		 
		 
		 
		$this->add_control(
			'author_font_size',
			[
				'label'			=>__('Font Size','hexwp'),
				'type'			=> \Elementor\Controls_Manager::NUMBER ,
				'selectors' 	=> array(
							'{{WRAPPER}} [class*="hw-el-"]' => '--hw-at-fn-sz: {{VALUE}}px',
					),	
			]
		);  
		$this->add_control(
			'author_font_weight',
			[
				'label'			=>__('Font Weight','hexwp'),
				'type'			=> \Elementor\Controls_Manager::SELECT ,
				'options'		=> hexwp_array_options('font_weight'),
				'selectors' 	=> array(
							'{{WRAPPER}} [class*="hw-el-"]' => '--hw-at-fn-wt: {{VALUE}}',
					),
				
			] 
		);
		
		
		//*****************************************---------Title Typography------------------********************************************
		$this->add_control(
			'comments_typo',
			[
				'label' 		=>__('Comments Typography','hexwp'),
				'type' 			=> \Elementor\Controls_Manager::HEADING,
				'separator'		=> 'before',
			]
		);
		 
		 
		 
		$this->add_control(
			'comments_font_size',
			[
				'label'			=>__('Font Size','hexwp'),
				'type'			=> \Elementor\Controls_Manager::NUMBER ,
				'selectors' 	=> array(
							'{{WRAPPER}} [class*="hw-el-"]' => '--hw-cm-fn-sz: {{VALUE}}px',
					),	
			]
		);  
		$this->add_control(
			'comments_font_weight',
			[
				'label'			=>__('Font Weight','hexwp'),
				'type'			=> \Elementor\Controls_Manager::SELECT ,
				'options'		=> hexwp_array_options('font_weight'),
				'selectors' 	=> array(
							'{{WRAPPER}} [class*="hw-el-"]' => '--hw-cm-fn-wt: {{VALUE}}',
					),
				
			] 
		);
		
		//*****************************************---------Title Typography------------------********************************************
		$this->add_control(
			'text_typo',
			[
				'label' 		=>__('Title Typography','hexwp'),
				'type' 			=> \Elementor\Controls_Manager::HEADING,
				'separator'		=> 'before',
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
   			'comments_layout_type'				=> hexwp_settings($option,'comments_layout_type'),
  			'box_layout'						=> hexwp_settings($option,'box_layout'),
		 
     	); ?>
   
 		 <div class="hw-elementor-<?php echo esc_attr($this->get_id());?>">      
  			
            
            <?php echo hexwp_comments_config($args,true);?>
		  
  		 
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
  