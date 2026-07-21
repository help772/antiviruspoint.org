<?php
 
class hexwp_element_share_icons extends \Elementor\Widget_Base {

 
	public function get_name() {
		return hexwp_slug().'_share_icons';
	}

 	public function get_title() {
		return  __('Share Icons','hexwp');
	}

 
	public function get_icon() {
		return 'eicon-share';
	}
	public function get_categories() {
		return [ 'hexwp' ];
	}


protected function register_controls() {
 		 
   		 $this->register_controls_share_icons();
 		 $this->register_controls_layout();
  		$this->register_controls_style(); 
    
	}
    


 
	 
	 
	protected function register_controls_share_icons(){
 			
		$this->start_controls_section(
			'share_icons',
			[
				'label'			=> __( 'Share Icons', 'hexwp' ),
			]
		); 	
		 
	$this->add_control(
			'share_url',
			[
				'label'			=> __('URL Share','hexwp'),
	 			'type'			=> \Elementor\Controls_Manager::TEXT ,
 				"default"		=> "http://google.com",
 				 
 			]
		); 
		
  	foreach ( hexwp_array_options('share') as $key => $name){
	$this->add_control(
			$key,
			[
				'label'			=> __('Share by','hexwp').' '.$name,
	 			'type'			=> \Elementor\Controls_Manager::SWITCHER ,
	 			"default"		=> 'yes',
 				 
 			]
		); 
	} 
								 		
	   
  
    
    $this->end_controls_section();

 		 
		
 	} 
 protected function register_controls_layout(){
 			
		$this->start_controls_section(
			'share_layout',
			[
				'label'			=> __('Layout','hexwp'),
			]
		); 	
		  
	$this->add_control(
			'icon_size',
			[
				'label'			=> __('Share Icon Size','hexwp'),
				'type'			=> \Elementor\Controls_Manager::NUMBER ,
				'selectors' 	=> array(
					'{{WRAPPER}} [class*="hw-el-"]' => '--hw-scl-sz: {{VALUE}}px',
				),
				 
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
			'alignment',
			[
				'label'			=> __('Alignment','hexwp'),
				'type'			=> \Elementor\Controls_Manager::SELECT ,
				"default"		=> "center",
			"options" 		=> hexwp_array_options('alignment',true), 
			]
		); 	
 
 
	
    $this->end_controls_section();
 }
 	
	protected function register_controls_style(){
		$this->start_controls_section(
			'style',
			[
				'label'			=> __('Style','hexwp'),
				'tab'			=> \Elementor\Controls_Manager::TAB_STYLE,
			]
		); 	
		$this->add_control(
			'icon_style',
			[
				'label'			=> __('Icon Style','hexwp'),
				'type'			=> \Elementor\Controls_Manager::SELECT ,
				"default"		=> "style-1",
				"options"		=> array(
					"style-1" 		=> esc_html__('Style 1: only icon','hexwp'),
					"style-2" 		=> esc_html__('Style 2: Boxed Icon','hexwp'),
					"style-3" 		=> esc_html__('Style 3: Boxed Original Color','hexwp'),
				),					
			]
		); 	
		
		 $this->add_control(
			'icon_color',
			[
				'label'			=>__('Share Icon Color','hexwp'),
				'type'			=> \Elementor\Controls_Manager::COLOR,
				'condition'		=> array('icon_style' => array('style-1','style-2')),
				
				'selectors' 	=> array(
							'{{WRAPPER}} [class*="hw-el-"]' => '--hw-scl-txt: {{VALUE}}',
					),			
			]
		); 	
	  
		 $this->add_control(
			'icon_background',
			[
				'label'			=>__('Share Icon Background','hexwp'),
				'type'			=> \Elementor\Controls_Manager::COLOR,
				'condition'		=> array('icon_style' => array('style-2')),
				
				'selectors' 	=> array(
							'{{WRAPPER}} [class*="hw-el-"]' => '--hw-scl-bg: {{VALUE}}',
					),			
			]
		); 	
	  
		 $this->add_control(
			'icon_border_color',
			[
				'label'			=>__('Share Icon Border Color','hexwp'),
				'type'			=> \Elementor\Controls_Manager::COLOR,
				'condition'		=> array('icon_style' => array('style-2')),
				
				'selectors' 	=> array(
							'{{WRAPPER}} [class*="hw-el-"]' => '--hw-scl-br-cr: {{VALUE}}',
					),			
			]
		); 	
	  
	 
		 $this->add_control(
			'icon_radius',
			[
				'label'			=>__('Share Icon Border Radius','hexwp'),
				'type'			=> \Elementor\Controls_Manager::SELECT,
				'condition'		=> array('icon_style' => array('style-2','style-3')),
  		"options"		=>  hexwp_array_options('radius',true),						
				'selectors' 	=> array(
							'{{WRAPPER}} [class*="hw-el-"]' => '--hw-scl-rd: {{VALUE}}',
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
   			'share_url' 							=> hexwp_settings($option,'share_url'),
   			'between' 							=> hexwp_settings($option,'between'),
   			'alignment' 						=> hexwp_settings($option,'alignment'),
 
 			'icon_style' 							=> hexwp_settings($option,'icon_style'),
			
			 
     	); 
		
		 
				
		foreach ( hexwp_array_options('share') as $key => $name){
			
 	 		if(hexwp_settings($option,$key)){
				$args['option'][$key] = hexwp_settings($option,$key);
			}
		}
									
	 
		
		?>
   
 		 <div class="hw-elementor-<?php echo esc_attr($this->get_id());?>">      
  			
            
            <?php echo hexwp_share_icons_config($args,true,true);?>
		  
  		 
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