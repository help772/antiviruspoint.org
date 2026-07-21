<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/*
Element Description: VC visualslider Slider
*/

if( ! function_exists( 'vs_vc_build' ) ) {
  function vs_vc_build() {
    return function_exists( 'vc_is_inline' ) && vc_is_inline() ? true : false;
}
}
 if (  class_exists( 'WPBakeryShortCode' ) ) {

 if ( ! class_exists( 'vc_visualslider' ) ) {
class vc_visualslider extends WPBakeryShortCode {
     
    // Element Init
    function __construct() {
        add_action( 'init', array( $this, 'vc_visualslider_mapping' ) );
        add_shortcode( 'vc_visualslider', array( $this, 'vc_visualslider_html' ) );
    }
     
    // Element Mapping
    public function vc_visualslider_mapping() {
         
        // Stop all if VC is not enabled
        if ( !defined( 'WPB_VC_VERSION' ) ) {
            return;
        }
$page_args = array(
			'sort_order' => 'asc',
			'sort_column' => 'post_title',
 
			'post_type' => 'visualslider',
			'post_status' => 'publish'
		); 
		 
		$options_page = array();
		$options_page_obj =get_posts($page_args); 
 
		if(!empty($options_page_obj) && is_array($options_page_obj) ){
		foreach ($options_page_obj as $rezapage) {
			$options_page[$rezapage->post_title] = $rezapage->ID;
		}
	}			
        // Map the block with vc_map()
        vc_map( 
            array(
                'name' => __('Visual Slider', 'visual-slider'),
                'base' => 'vc_visualslider',
                'description' => __('Show sliders related to Visual Slider plugin', 'visual-slider'), 
                'category' => __('Visual Slider', 'visual-slider'),   
                'icon'        => VISUALSLIDER_DIR .'admin/assets/image/icon.png',
                'params' => array(   
                         
                    array(
                        'type'       => 'dropdown',
                        'heading'    => esc_html__( 'Select Slider', 'visual-slider' ),
                        'param_name' => 'sliders',
						'admin_label' => true,
                         'value'      => $options_page,
                    ),
		array(
 				'type' => 'css_editor',
				'heading' => esc_html__( 'Box Style', 'visual-slider' ),
				'param_name' => 'css',
				'group' => esc_html__( 'Design Settings', 'visual-slider' ),
		),                      
                        
                ),
            )
        );                                
        
    }
     
     
    // Element HTML
    public function vc_visualslider_html( $atts,$content=false) {

		extract( shortcode_atts( array(
			'sliders'				=>		'',
 			'css'				=>		'',
		), $atts ) );
	 		$content = wpb_js_remove_wpautop($content);
		
		$key=wp_rand(0,999999999999);
		// var_dump($settings);
 		$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), $this->settings['base'], $atts );
	 	
  		  $html = '<div class="vs-elementor-item  vs-elementor-'.esc_attr($key).' '.esc_attr( trim( $css_class ) ).'" >';
			$html.=vs_slider_config($sliders);
			if(vs_vc_build()==true){
			ob_start();
  
			?>
               <div class="vs-elementor-script">     
                        <script type="text/javascript">
                          (function($) {
                            'use strict';
                                jQuery(document).ready(function() {
                                setTimeout(function(){  
                                        $('.vs-elementor-<?php echo  esc_html($key);?>').vs_custom_slider();
     
                                     
                                }, 1000);
                                         
                                }); 
                                 
                            })(jQuery);
                       </script>
            
                </div>
                <?php
			}
		    $html.=ob_get_clean();
		  $html.='</div>';         
           
         
        return $html;
 		 

 			
          
    }
     
} // End Element Class
 
 
// Element Class Init
new vc_visualslider(); 
 }}
 