<?php
 
class hexwp_element_blog_carousel extends \Elementor\Widget_Base {

 
	public function get_name() {
		return hexwp_slug().'_blog_carousel';
	}

 
	public function get_title() {
		return __( 'Blog Carousel', 'hexwp' );
	}

 
	public function get_icon() {
		return 'eicon-posts-carousel';
	}
	public function get_categories() {
		return [ 'hexwp' ];
	}


	protected function register_controls() {
		$this->register_controls_title_box();
		$this->register_controls_general();
		$this->register_controls_slider();
		$this->register_controls_layout();
 		$this->register_controls_title_box_style();
		$this->register_controls_post_style();
		$this->register_controls_slider_style();		
 		$this->register_controls_caption_style();
		$this->register_controls_typography(); 
	}
    




	protected function register_controls_title_box(){
 		include  hexwp_PATH . '/elementor/blog/elementor-blog-title-box.php';  
 	}
	 
	 
	protected function register_controls_general(){
 		include  hexwp_PATH . '/elementor/blog/elementor-blog-carousel-general.php';  
 	}
	protected function register_controls_slider(){
 		include hexwp_PATH . '/elementor/general/elementor-slider.php';  
 	}
	protected function register_controls_layout(){
 		include hexwp_PATH . '/elementor/blog/elementor-blog-carousel-layout.php';  
 	}
	
  
	protected function register_controls_title_box_style(){
 		include hexwp_PATH . '/elementor/general/elementor-title-box-style.php';  
 	}		

 	
	protected function register_controls_post_style(){
 		include hexwp_PATH . '/elementor/blog/elementor-blog-post-style.php';  
 	}	
 
	protected function register_controls_slider_style(){
 		include hexwp_PATH . '/elementor/general/elementor-slider-style.php';  
 	}	 
	protected function register_controls_caption_style(){
 		include hexwp_PATH . '/elementor/general/elementor-caption-style.php';  
 	}		
  
	protected function register_controls_typography(){
 		include  hexwp_PATH . '/elementor/blog/elementor-blog-typography.php';  
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
				'ignore_sticky_posts'		=> hexwp_settings($option,'ignore_sticky_posts'),
				'title_limit'				=> hexwp_settings($option,'title_limit'),
				'excerpt' 					=> hexwp_settings($option,'excerpt'),
				'excerpt_limit'				=> hexwp_settings($option,'excerpt_limit'),
				'meta'=> array(
					'meta_author'				=> 	hexwp_settings($option,'meta_author'),
					'meta_category'				=>	hexwp_settings($option,'meta_category'),
					'meta_date'					=>	hexwp_settings($option,'meta_date'),
					'meta_view'					=>	hexwp_settings($option,'meta_view'),
					'meta_comments'				=>	hexwp_settings($option,'meta_comments'),
				),
				'readmore' 					=> hexwp_settings($option,'readmore'),
				'hover_post_icon'			=> hexwp_settings($option,'hover_post_icon'),
 			
			
			/*****************************************************************************************************************
			Slider
			******************************************************************************************************************/
				'pager'						=>  hexwp_settings($option,'pager'),
				'arrows' 					=> hexwp_settings($option,'arrows'),
				'arrow_layout'				=> array(
					'location'					=>	hexwp_settings($option,'arrow_location'),
					'layout'					=>	hexwp_settings($option,'arrow_layout'),
					'size'						=>	hexwp_settings($option,'arrow_size'),
				),
				'auto' 						=> hexwp_settings($option,'auto'),
				'speed' 					=> hexwp_settings($option,'speed'),
				'pause' 					=> hexwp_settings($option,'pause'),
 
 
			/*****************************************************************************************************************
			Layout
			******************************************************************************************************************/
				'layout' 					=> hexwp_settings($option,'layout'),
				'column' 					=> hexwp_settings($option,'column'),
				'responsive_column' 		=> hexwp_settings($option,'responsive_column'),
				'between' 					=> hexwp_settings($option,'between'),
				'ratio' 					=> hexwp_settings($option,'ratio'),
				'image_width'				=> hexwp_settings($option,'image_width'),
				'image_size'				=> hexwp_settings($option,'image_size'),
				'alignment' 				=> hexwp_settings($option,'alignment'),
				'box_layout' 				=> hexwp_settings($option,'box_layout'),
				'caption_layout' 			=> hexwp_settings($option,'caption_layout'),
 
	  
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
			Blog Style
			******************************************************************************************************************/
				'background_color' 			=> hexwp_settings($option,'background_color'),
				'title_color'				=> array(
					'link'						=> 	hexwp_settings($option,'title_color_link'),
					'text'						=>	hexwp_settings($option,'title_color_hover')
				),
				'excerpt_color'				=>  hexwp_settings($option,'excerpt_color'),
				'meta_color'				=>  hexwp_settings($option,'meta_color'),
				'border_color' 				=> hexwp_settings($option,'border_color'),
				'box_border_color' 				=> hexwp_settings($option,'box_border_color'),
				'radius' 					=> hexwp_settings($option,'radius'),
			/*****************************************************************************************************************
			Slider Style
			******************************************************************************************************************/
				'arrow_color'		=> array(
					'background'				=>	hexwp_settings($option,'arrow_background_color'),
					'text'						=>	hexwp_settings($option,'arrow_text_color')
				),
				'pager_style'		=> array(
					'pager'						=>	hexwp_settings($option,'pager_color'),
					'pager_actvie'				=>	hexwp_settings($option,'pager_active_color')
				),
						
			/*****************************************************************************************************************
			Image And Caption Style
			******************************************************************************************************************/			
				'image_effect' 				=> hexwp_settings($option,'image_effect'),
				'caption_effect'			=> hexwp_settings($option,'caption_effect'),
				'caption_background_color'	=> hexwp_settings($option,'caption_background_color'),
				'caption_color'				=> hexwp_settings($option,'caption_color'),
				
  			/*****************************************************************************************************************
			Typo Grap
			******************************************************************************************************************/
				'title_box_main_typo'		=> hexwp_elmentor_typo_css($option,'title_box_main'),
				'title_box_tab_typo'		=> hexwp_elmentor_typo_css($option,'title_box_tab'),
				'post_title_typo'			=> hexwp_elmentor_typo_css($option,'post_title'),
				'excerpt_typo'				=> hexwp_elmentor_typo_css($option,'excerpt'),
				'meta_typo'					=> hexwp_elmentor_typo_css($option,'meta'),	
				
  
   		); ?>
  
  		 <div class="hw-elementor-<?php echo esc_attr($this->get_id());?>">      
  			
			<?php echo hexwp_blog_carousel_config($args,true);?>
		  
  			<?php if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {?>
            
                 <div class="hw-elementor-script">    
					<script src='<?php echo  hexwp_DIR . '/js/lib/lightslider.min.js'?>' id='sao_lightslider_script-js'></script>    
 
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
  