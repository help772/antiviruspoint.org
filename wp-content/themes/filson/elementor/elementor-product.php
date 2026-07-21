<?php
 
class hexwp_element_product extends \Elementor\Widget_Base {

 
	public function get_name() {
		return hexwp_slug().'_product';
	}

 
	public function get_title() {
		return __( 'Product', 'hexwp' );
	}

 
	public function get_icon() {
		return 'eicon-products';
	}
	public function get_categories() {
		return [ 'hexwp' ];
	}


	protected function  register_controls() {
 
		$this->register_controls_title_box();
  		$this->register_controls_general();
 		$this->register_controls_layout();
		 $this->register_controls_featured();
  		$this->register_controls_title_box_style();
 		$this->register_controls_post_style();
  		$this->register_controls_typography(); 
  
	}
    




	protected function register_controls_title_box(){
 		include  hexwp_PATH . '/elementor/product/elementor-product-title-box.php';  
 	}
	 
	 
	protected function register_controls_general(){
 		include  hexwp_PATH . '/elementor/product/elementor-product-general.php';  
 	}
	
	protected function register_controls_layout(){
 		include hexwp_PATH . '/elementor/product/elementor-product-layout.php';  
 	}
 
	protected function register_controls_featured(){
 		include hexwp_PATH . '/elementor/product/elementor-product-featured.php';  
 	}
	
 
	protected function register_controls_title_box_style(){
 		include hexwp_PATH . '/elementor/general/elementor-title-box-style.php';  
 	}		

 	
	protected function register_controls_post_style(){
 		include hexwp_PATH . '/elementor/product/elementor-product-style.php';  
 	}	 	
	
 
	protected function register_controls_typography(){
 		include  hexwp_PATH . '/elementor/product/elementor-product-typography.php';  
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
				'title_box_all'				=> hexwp_settings($option,'title_box_all'),
				'title_box_list_all' 		=> hexwp_settings($option,'title_box_list_all'),				
				'tabs'						=> hexwp_settings($option,'tabs'),
			
			
			/*****************************************************************************************************************
			General
			******************************************************************************************************************/
				'number'					=> hexwp_settings($option,'number'),
				'multi_cats'				=> hexwp_multi_cats_array(hexwp_settings($option,'multi_cats')),
				'orderby'					=> hexwp_settings($option,'orderby'),
 				'title_limit'				=> hexwp_settings($option,'title_limit'),
				'excerpt' 					=> hexwp_settings($option,'excerpt'),
								'excerpt_limit' 					=> hexwp_settings($option,'excerpt_limit'),

				'countdown'					=> 	hexwp_settings($option,'countdown'),
				'meta_category'				=>	hexwp_settings($option,'meta_category'),
 				'rating'					=>	hexwp_settings($option,'rating'),
				'addcart'					=>	hexwp_settings($option,'addcart'),
 				'more_posts' 				=> hexwp_settings($option,'more_posts'),
	 
 
			/*****************************************************************************************************************
			Layout
			******************************************************************************************************************/
				'layout' 					=> hexwp_settings($option,'layout'),
				'list_layout' 				=> hexwp_settings($option,'list_layout'),
				'grid_layout' 				=> hexwp_settings($option,'grid_layout'),
 				'responsive_column'			=> hexwp_settings($option,'responsive_column'),
				'between' 					=> hexwp_settings($option,'between'),
				'ratio' 					=> hexwp_settings($option,'ratio'),
				'ratio_2' 					=> hexwp_settings($option,'ratio_2'),
				'image_width'				=> hexwp_settings($option,'image_width'),
 				'image_size'				=> hexwp_settings($option,'image_size'),
 				'image_size_2'				=> hexwp_settings($option,'image_size_2'),
 				'second_image'				=> hexwp_settings($option,'second_image'),
 				'alignment' 				=> hexwp_settings($option,'alignment'),
				'box_layout' 				=> hexwp_settings($option,'box_layout'),
 
				
			/*****************************************************************************************************************
			Featured Item
			******************************************************************************************************************/
	  
				'image_featured'			=> hexwp_settings($option,'image_featured'),
				'image_featured_query' 		=> hexwp_settings($option,'image_featured_query'),
				'sliders'					=> hexwp_settings($option,'sliders'),
				'image_featured_id'			=>  !empty($image_featured_id['id'])?$image_featured_id['id']:'',
				'image_featured_image_size'	=> hexwp_settings($option,'image_featured_image_size'),
				'image_featured_image_link'	=> hexwp_settings($option,'image_featured_image_link'),
				'image_featured_image_url'	=> hexwp_settings($option,'image_featured_image_url'),
				'image_featured_width'		=> hexwp_settings($option,'image_featured_width'),
	  
	  
			/*****************************************************************************************************************
			Title Box Style
			******************************************************************************************************************/
				'title_box_style' 			=> hexwp_settings($option,'title_box_style'),
				'title_box_main_color'		=> array(
					'background'				=> 	hexwp_settings($option,'title_box_main_background'),
					'text'						=>	hexwp_settings($option,'title_box_main_text')
				),
				'title_box_tab_color'		=> array(
					'background'				=> 	hexwp_settings($option,'title_box_tab_background'),
					'text'						=>	hexwp_settings($option,'title_box_tab_text')
				),
				'title_box_active_color'	=> array(
					'background'				=> 	hexwp_settings($option,'title_box_active_background'),
					'text'						=>	hexwp_settings($option,'title_box_active_text')
				),
				'title_box_radius' 			=> hexwp_settings($option,'title_box_radius'),
				'title_box_border_color' 		=> hexwp_settings($option,'title_box_border_color'),
				
				
			/*****************************************************************************************************************
			Product Style
			******************************************************************************************************************/
				'background_color' 			=> hexwp_settings($option,'background_color'),
				'title_color'				=> array(
					'link'						=> 	hexwp_settings($option,'title_color_link'),
					'text'						=>	hexwp_settings($option,'title_color_hover')
				),
				'price_color'		=> array(
					'main'						=>	hexwp_settings($option,'price_main_color'),
					'sale'						=>	hexwp_settings($option,'price_sale_color'),
					'regular'					=>	hexwp_settings($option,'price_regular_color'),
				),
				'excerpt_color'				=>  hexwp_settings($option,'excerpt_color'),
				'meta_color'				=>  hexwp_settings($option,'meta_color'),
				'rating_color'		=> array(
					'rating'						=>	hexwp_settings($option,'rating_rating_color'),
					'none'						=>	hexwp_settings($option,'rating_none_color')
				),
				'countdown_color'		=> array(
					'background'						=>	hexwp_settings($option,'countdown_background'),
					'number'						=>	hexwp_settings($option,'countdown_number'),
					'text'						=>	hexwp_settings($option,'countdown_text'),
				),		
				'between_border' 				=> hexwp_settings($option,'between_border'),
				'box_border_color' 				=> hexwp_settings($option,'box_border_color'),
				'border_color' 				=> hexwp_settings($option,'border_color'),
				'radius' 					=> hexwp_settings($option,'radius'),
			/*****************************************************************************************************************
			Typo
			******************************************************************************************************************/	
				'title_box_main_typo'		=> hexwp_elmentor_typo_css($option,'title_box_main'),
				'title_box_tab_typo'		=> hexwp_elmentor_typo_css($option,'title_box_tab'),
				'post_title_typo'			=> hexwp_elmentor_typo_css($option,'post_title'),
				'price_typo'				=> hexwp_elmentor_typo_css($option,'price'),
				'excerpt_typo'				=> hexwp_elmentor_typo_css($option,'excerpt'),
				'meta_typo'					=> hexwp_elmentor_typo_css($option,'meta'),	
				
  
   		); 
  	 ?>
  		 <div class="hw-elementor-<?php echo esc_attr($this->get_id());?>">      
  			
            
            <?php echo hexwp_product_config($args,true);?>
		  
  		 
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