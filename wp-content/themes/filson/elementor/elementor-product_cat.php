<?php
 
class hexwp_element_product_cat extends \Elementor\Widget_Base {

 
	public function get_name() {
		return hexwp_slug().'_product_cat';
	}

 
	public function get_title() {
		return __( 'Product Categories', 'hexwp' );
	}

 
	public function get_icon() {
		return 'eicon-product-categories';
	}
	public function get_categories() {
		return [ 'hexwp' ];
	}


	protected function  register_controls() {
		$this->register_controls_title_box();
 
    		$this->register_controls_general();
 		$this->register_controls_layout();
  		$this->register_controls_style();
  		$this->register_controls_title_box_style();
		
  		$this->register_controls_typography(); 
  
	}
    




	protected function register_controls_title_box(){
 		include  hexwp_PATH . '/elementor/product_cat/elementor-product_cat-title-box.php';  
 	}
	 
	 
	protected function register_controls_general(){
 		include  hexwp_PATH . '/elementor/product_cat/elementor-product_cat-general.php';  
 	}
	
	protected function register_controls_layout(){
 		include hexwp_PATH . '/elementor/product_cat/elementor-product_cat-layout.php';  
 	}
 
 
	protected function register_controls_title_box_style(){
 		include hexwp_PATH . '/elementor/general/elementor-title-box-style.php';  
 	}		

	protected function register_controls_style(){
 		include hexwp_PATH . '/elementor/product_cat/elementor-product_cat-style.php';  
 	}	 	
	
 
	protected function register_controls_typography(){
 		include  hexwp_PATH . '/elementor/product_cat/elementor-product_cat-typography.php';  
 	}	  
	
	protected function render() {
 		$option = $this->get_settings_for_display();
 		$args=array();
		$args['key']= $this->get_id();
		
		
 		$args['option'] = array(
		
		/*****************************************************************************************************************
			Title Box
			******************************************************************************************************************/
				'title'						=> hexwp_settings($option,'title'),
				'title_box_type'			=> hexwp_settings($option,'title_box_type'),
   			
			
			/*****************************************************************************************************************
			General
			******************************************************************************************************************/
				'number'					=> hexwp_settings($option,'number'),
				'source'					=> hexwp_settings($option,'source'),
				
				 'categories'				=> hexwp_multi_cats_array(hexwp_settings($option,'categories')),
 				'parent' 					=> hexwp_settings($option,'parent'),
				'hide_empty'				=> 	hexwp_settings($option,'hide_empty'),
				'orderby'					=>	hexwp_settings($option,'orderby'),
 				'order'						=>	hexwp_settings($option,'order'),
				'hide_title'				=>	hexwp_settings($option,'hide_title'),
 				'hide_count' 				=> hexwp_settings($option,'hide_count'),
	 
 
			/*****************************************************************************************************************
			Layout
			******************************************************************************************************************/
				'column' 					=> hexwp_settings($option,'column'),
				'responsive_column' 				=> hexwp_settings($option,'responsive_column'),
				'between' 					=> hexwp_settings($option,'between'),
 				'ratio'						=> hexwp_settings($option,'ratio'),
				'box_layout' 					=> hexwp_settings($option,'box_layout'),
 
				
  
   		); 
  	 ?>
  		 <div class="hw-elementor-<?php echo esc_attr($this->get_id());?>">      
  			
            
            <?php echo hexwp_product_cat_config($args,true);?>
		  
  		 
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