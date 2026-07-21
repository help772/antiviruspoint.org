<?php
 
class hexwp_element_menu extends \Elementor\Widget_Base {

 
	public function get_name() {
		return hexwp_slug().'_menu';
	}

 
	public function get_title() {
		return __( 'Menu', 'hexwp' );
	}

 
	public function get_icon() {
		return 'eicon-menu-bar';
	}
	public function get_categories() {
		return [ 'hexwp' ];
	}


protected function  register_controls() {
 		$this->register_controls_general();
		$this->register_controls_layout();
		$this->register_controls_menu_style();
		$this->register_controls_typography();
 
	 
	}
    


 
	 
	protected function register_controls_general(){
 		include  hexwp_PATH . '/elementor/menu/elementor-menu-general.php';  
 	}
	
 
	protected function register_controls_layout(){
 		include hexwp_PATH . '/elementor/menu/elementor-menu-layout.php';  
 	}
 
 
	protected function register_controls_menu_style(){
 		include hexwp_PATH . '/elementor/menu/elementor-menu-style.php';  
 	}		
	protected function register_controls_typography(){
 		include hexwp_PATH . '/elementor/menu/elementor-menu-typography.php';  
 	}
 
	protected function render() {
 		$option = $this->get_settings_for_display();
 		$args=array();
		$args['key']= $this->get_id();
 
 		$args['option'] = array(
		
			//Title Box//
   			'title'						=> hexwp_settings($option,'title'),
			'menu'						=> hexwp_settings($option,'menu'),	
   			'number'					=> hexwp_settings($option,'number'),
 	
 				'title_background'						=> 	hexwp_settings($option,'title_background'),
				'title_background_2'						=> 	hexwp_settings($option,'title_background_2'),
				'title_text_color'						=>	hexwp_settings($option,'title_text_color'),
 			'background_color'				=>  hexwp_settings($option,'background_color'),
			'menu_item_color'				=> array(
				'background'						=> 	hexwp_settings($option,'menu_item_color'),
				'text'						=>	hexwp_settings($option,'menu_item_hover_color')
			),		
			'more_color'				=> array(
				'background'						=> 	hexwp_settings($option,'more_background_color'),
				'text'						=>	hexwp_settings($option,'more_text_color')
			),	 		 
  			'border_color' 				=> hexwp_settings($option,'border_color'),
  			'radius' 					=> hexwp_settings($option,'radius'),
   			'title_typo'				=> hexwp_elmentor_typo_css($option,'title_typo'),
			'menu_item_typo'			=> hexwp_elmentor_typo_css($option,'menu_item_typo'),
			'more_typo'					=> hexwp_elmentor_typo_css($option,'more_typo'),
    		); 
  ?>
  
  
		<div class="hw-elementor-<?php echo esc_attr($this->get_id());?>">      
  			
			<?php echo hexwp_menu_config($args,true);?>
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
			<?php }	?>
		</div>
    
		<?php            
 		 
	}
	
}
 
?>
