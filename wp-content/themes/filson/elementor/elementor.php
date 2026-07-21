<?php

function hexwp_elementor_widgets() {
 
			
 
 		 require_once(hexwp_PATH . '/elementor/elementor-blog.php' ); 
 		 require_once(hexwp_PATH . '/elementor/elementor-blog_carousel.php' ); 
 		 require_once(hexwp_PATH . '/elementor/elementor-blog_masonry.php' ); 
	 
 
 
		if ( function_exists ( "is_woocommerce" )){
			 require_once(hexwp_PATH . '/elementor/elementor-product.php' ); 
			 require_once(hexwp_PATH . '/elementor/elementor-product_carousel.php' ); 
			 require_once(hexwp_PATH . '/elementor/elementor-product_cat.php' ); 
		}
 
		
		require_once(hexwp_PATH . '/elementor/elementor-social_icons.php' ); 
		require_once(hexwp_PATH . '/elementor/elementor-share_icons.php' ); 
		require_once(hexwp_PATH . '/elementor/elementor-contact_form_7.php' ); 		
		require_once(hexwp_PATH . '/elementor/elementor-comments.php' ); 
	 
 

		if(function_exists('sao_slide_post_type')){
  		 require_once(hexwp_PATH . '/elementor/elementor-slider_featured.php' ); 
		}
 		 require_once(hexwp_PATH . '/elementor/elementor-image_list.php' ); 
 		 require_once(hexwp_PATH . '/elementor/elementor-menu.php' ); 
  
 		 	 \Elementor\Plugin::instance()->widgets_manager->register( new \hexwp_element_blog() );  
  		 	 \Elementor\Plugin::instance()->widgets_manager->register( new \hexwp_element_blog_carousel() );
  		 	 \Elementor\Plugin::instance()->widgets_manager->register( new \hexwp_element_blog_masonry() );
		 
		 	  
		if ( function_exists ( "is_woocommerce" )){
 			\Elementor\Plugin::instance()->widgets_manager->register( new \hexwp_element_product() );  
		 	\Elementor\Plugin::instance()->widgets_manager->register( new \hexwp_element_product_carousel() );  
		 	\Elementor\Plugin::instance()->widgets_manager->register( new \hexwp_element_product_cat() );  
		}
 	
	 
		\Elementor\Plugin::instance()->widgets_manager->register( new \hexwp_element_social_icons() );		 
		\Elementor\Plugin::instance()->widgets_manager->register( new \hexwp_element_share_icons() );		
	  
		\Elementor\Plugin::instance()->widgets_manager->register( new \hexwp_element_content_form_7() );		 
		 \Elementor\Plugin::instance()->widgets_manager->register( new \hexwp_element_comments() );		 
 
 
		 
		\Elementor\Plugin::instance()->widgets_manager->register( new \hexwp_element_image_list() );  
	//	\Elementor\Plugin::instance()->widgets_manager->register( new \hexwp_element_menu() ); 
		
 
}
add_action( 'elementor/widgets/register', 'hexwp_elementor_widgets' );

function hexwp_elementor_widget_categories( $elements_manager ) {

	$elements_manager->add_category(
		'hexwp',
		[
			'title' =>__('Theme','hexwp') ,
			'icon' => 'fa fa-plug',
		]
	);
 

}
 
add_action( 'elementor/elements/categories_registered', 'hexwp_elementor_widget_categories' );