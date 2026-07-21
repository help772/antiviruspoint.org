<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Style
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_style_array() {
	return array(
		/******************************************************************************
		Body Css
		*******************************************************************************/
			'body'						=> '1 - Body' ,
			'body/body'						=> '	1.1 - Body' ,
			'body/html-tags'				=> '	1.2 - Html Tags' ,
			'body/form'						=> '	1.3 - Form' ,
			'body/content'					=> '	1.4 - Content' ,
			'body/column-warp'				=> '	1.5 - Column Warp' ,
 			'body/responsive'				=> '	1.6 - Responsvie' ,
			'body/breadcrumbs'				=> '	1.7 - Breadcrumbs' ,
			'body/footer'					=> '	1.8 - Footer' ,			
 			
		/*******************************************************************************
		Header
		*******************************************************************************/
			'header'						=> '2 - Header' ,
 			'header/header-content'			=> '	2.1 - Header Content' ,
			'header/header-sticky'			=> '	2.2 - Header Sticky' ,
			'header/header-nav'				=> '	2.2 - Header Nav' ,
			'header/logo'					=> '	2.3 - Logo' ,
 			'header/menu'					=> '	2.4 - Menu' ,
			'header/category-menu'			=> '	2.5 - Category Menu' ,
			'header/mobile-menu'			=> '	2.6 - Mobile Menu' ,
 			'header/search'					=> '	2.7 - Search' ,
			'header/social'					=> '	2.8 - Social' ,
 			'header/header-element'			=> '	2.9 - Header Element' ,
			'header/mobbar'					=> '	2.10 - Mobile bar' ,
	 
		/*******************************************************************************
		Post 
		*******************************************************************************/
			'post'							=> '3 - Post' ,
 			'post/ajax'						=> '	3.1 - Ajax' ,
			'post/title-box'				=> '	3.1 Title Box' ,
			'post/thumb'					=> '	3.2 Thumb' ,
			'post/thumb-hover'				=> '	3.3 Thumb Hover' ,
			'post/details'					=> '	3.4 Details' ,
			'post/meta'						=> '	3.5 Meta' ,
 			'post/hover-icon'				=> '	3.6 Hover Icon ' ,
			'post/load-more'				=> '	3.7 Load More ' ,
			'post/pagenavi'					=> '	3.8 Pagenavi ' ,
		/*******************************************************************************
		Module 
		*******************************************************************************/
			'module'						=> '4 - Module' ,
			'module/slider'					=> '	4.1 - Slider' ,
			'module/module'					=> '	4.2 - Module' ,
			'module/module-1'				=> '	4.3 - Module 1' ,
			'module/module-2'				=> '	4.4 - Module 2' ,
			'module/glider'					=> '	4.5 - Glider' ,
 			'module/post-layout'			=> '	4.6 - Post Layout' ,
			'module/facaption'				=> '	4.7 - facaption ' ,
 			'module/404'					=> '	4.8 - 404' ,
			'module/macy'					=> '	4.9 - Macy ' ,
			'module/widgets'				=> '	4.10 - Widgets ' ,
			 
		/*******************************************************************************
		Elements 
		*******************************************************************************/
			'elements'						=> '5 - Element' ,
			'elements/element'				=> '	5.1 - Element' ,
			'elements/menu'					=> '	5.2 - Menu' ,
    			'elements/contactform'		=> '	5.3 - Contactform' ,
		
		/*******************************************************************************
		Single
		*******************************************************************************/
			'single'						=> '6 - Single' ,
			'single/single'					=> '	6.1 - Single' ,
			'single/head-single'			=> '	6.2 - Head Single' ,
			'single/video'					=> '	6.3 - Video' ,
			'single/single-content'			=> '	6.4 - Single Content' ,
 			'single/author'					=> '	6.5 - Author' ,
			'single/tags'					=> '	6.6 - Tags' ,
			'single/lightbox'				=> '	6.7 - Lightbox' ,
			'single/comments'				=> '	6.8 - Comments' ,
			'single/wp-block'				=> '	6.9 - wp-block' ,
 
	); 
 
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Woocommerce Style
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 function hexwp_woocommerce_array() {
	return array(
		'woocommerce/nav-cart'					=> '1 - Nav Cart' ,
  		'woocommerce/product'					=> '2 - Product',
   		'woocommerce/price'						=> '3 - Price' ,
  		'woocommerce/rating'					=> '4 - Rating' ,
  		'woocommerce/countdown'					=> '5 - Countdown' ,
 		'woocommerce/product-button'			=> '6 - Product button' ,
  		'woocommerce/orderby'					=> '7 - Orderby' ,
 		'woocommerce/product-single'			=> '8 - Product Single' ,
  		'woocommerce/product-gallery'			=> '9 - Product qty' ,
  		'woocommerce/product-single-cart'		=> '10 - Product gallery' ,
  		'woocommerce/product-qty'				=> '11 - Product gallery' ,
 		'woocommerce/product-cart'				=> '12 - Form cart' ,
 		'woocommerce/product-description'		=> '13 - product Description' ,
 		'woocommerce/woocommerce-page'			=> '14 - Woocommerce Page' ,
 		'woocommerce/woocommerce-widget'		=> '15 - woocommerce' ,
 		'woocommerce/dokan'						=> '16 - dokan' ,
  	 
	);
 }
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Script  
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_scripts_array() {
	return array(
 
		'lib/header'							=> 'Header' ,
		'lib/mobbar'							=> 'Mobile Bar' ,
		'lib/auto-width'						=> 'Auto Width' ,
  		'lib/countdown'							=> 'Countdown' ,
  		'lib/cart-button'						=> 'Cart button' ,
		'lib/slider'							=> 'Slider' ,
 		'lib/dragscroll'						=> 'Dragscroll' ,
 		'lib/figcaption'						=> 'figcaption' ,
		'lib/lightbox'							=> 'lightbox' ,
  		'lib/translate'							=> 'translate' ,
		'lib/tabs-transition'					=> 'tabs-transition' ,
		'lib/tabs-ajax'							=> 'tabs-ajax' ,
  		'lib/load-more'							=> 'load-more' ,
		'lib/page-number'						=> 'page-number' ,
		'lib/widgets'							=> 'widgets' ,
   		'lib/sidebar-sticky'					=> 'sidebar sticky' ,
  		'lib/woocommerce'						=> 'Woocommerce' ,
		'lib/title-box'							=> 'Title box' ,
   		'scripts'							=> 'Scripts' ,
 		
 	);
 }
 
 
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Register Enqueue
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if ( !is_admin()){
add_action( 'wp_enqueue_scripts', 'hexwp_register' ); 
function hexwp_register() {
 	global $minify;
 
 	$var = '1.4';
 		// Minify*************************************************************************	
		
	if(!empty($minify)){ 
 		wp_enqueue_style( 'hexwp_style', get_stylesheet_uri(),'',$var );
		
		wp_add_inline_style( 'hexwp_style', hexwp_custom_css(hexwp_header_data()) ); 
		
		if ( function_exists ( "is_woocommerce" )){
  	 		wp_enqueue_style( 'hexwp_woocommerce', hexwp_DIR.'/css/woocommerce.css','',$var);
		}
 	
		wp_register_script( 'hexwp_all', hexwp_DIR.'/js/all.js', array( 'jquery'),$var);
		$array = array( 'ajaxurl' => admin_url( 'admin-ajax.php'  ));
		wp_localize_script( 'hexwp_all', 'hexwp_js', $array  );
		wp_enqueue_script( 'hexwp_all' );
 
		hexwp_enqueue_google_fonts();
 
 	 
 
 	}else{
		 	$var = rand(0,999999);
 		// Not Minify*************************************************************************	
 		foreach(hexwp_style_array() as $key => $value){
  			wp_enqueue_style( 'hexwp_'.str_replace('/','_',$key), hexwp_DIR.'/css/'.$key.'.css','',$var);
 		}
	    if ( function_exists ( "is_woocommerce" )){
			foreach(hexwp_woocommerce_array() as $key => $value){
  				wp_enqueue_style( 'hexwp_'.str_replace('/','_',$key), hexwp_DIR.'/css/'.$key.'.css','',$var);
 			}
		 }	
		 
		wp_add_inline_style( 'hexwp_body_body', hexwp_custom_css(hexwp_header_data()) ); 
		foreach(hexwp_scripts_array() as $key => $value){
  			wp_enqueue_script( 'hexwp_'.str_replace('/','_',$key), hexwp_DIR.'/js/'.$key.'.js',array('jquery'),$var);
 		}
  	$array = array( 'ajaxurl' => admin_url( 'admin-ajax.php'  ));
 	wp_localize_script( 'hexwp_scripts', 'hexwp_js', $array  );
	wp_enqueue_script( 'hexwp_scripts' );
	  
		 
	}
 
	
 
}

}
 
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Enqueue Footer
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 
function hexwp_enqueue_footer($has_enqueue=false) {
		 	wp_reset_query();
	wp_reset_postdata();

	if ( is_single() ) {
 	  hexwp_setPostViews( get_the_ID() );
	} 
}
 
add_action('wp_footer', 'hexwp_enqueue_footer');
 

 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Unregester Style
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_action( 'wp_enqueue_scripts', 'hexwp_deregister_styles',99999999 ); 
    add_action( 'wp_print_styles', 'hexwp_deregister_styles', 9999 );
	add_action( 'wp_head', 'hexwp_deregister_styles', 9999 );
function hexwp_deregister_styles()    { 
	if (is_front_page()) wp_dequeue_script('wc-cart-fragments');

		global $post;
		$disable_page=hexwp_option('disable_page');	




	if($disable_page=='sao'){
	   wp_deregister_style( 'imagehover' ); 
	   wp_deregister_style( 'buttonhover' ); 
	   wp_deregister_style( 'aos' ); 
	   wp_deregister_style( 'jquery.hexagonprogress' ); 
	   wp_deregister_style( 'jquery-circle-progress' ); 
	   wp_deregister_style( 'sao_builder' ); 
	   wp_deregister_style( 'zoomove' ); 
	   wp_deregister_style( 'sao_auto_width' ); 
	   wp_deregister_style( 'sao_rtl_builder' ); 
	}
	
	
	
	
	$font_fontawesome=hexwp_option('font_fontawesome');	
	if($font_fontawesome!=='enable'){
	   wp_deregister_style( 'sao_font_awesomes' ); 
	   wp_deregister_style( 'yith-wcwl-font-awesome' ); 

	}

	if($disable_page=='elementor'){
		wp_deregister_style( 'yith-wcwl-font-awesome' ); 
		wp_deregister_style( 'elementor-common' ); 
		wp_deregister_style( 'elementor-icons' ); 

   }
   
   	wp_dequeue_style( 'yith-wcwl-main' );
	wp_deregister_style( 'yith-wcwl-font-awesome' ); 
	if ( ! class_exists( 'Classic_Editor' ) ) {
		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'wp-block-library-theme' );
		wp_dequeue_style( 'wc-blocks-style' );
		wp_deregister_style( 'wp-block-library-rtl' ); 
		wp_deregister_style( 'wc-block-vendors-style' ); 
		wp_deregister_style( 'wc-block-style-rtl' ); 
	}
 
 
} 
/********************************************************************
Font Url
*********************************************************************/
function hexwp_enqueue_google_fonts() {
    $font_url = '';
	global  $smof_data;
    $body_font_family = hexwp_option('body_font_family');
	$font_families = array();
 	 if( $body_font_family !== 'Rubik'){
	 if( $body_font_family !== 'rubik'){
	if ( !empty($body_font_family)  ) {
		$font_families[] =$body_font_family ;
		
	 
	
	 
	$query_args = array(
		'family' => urlencode( implode( '|', $font_families ) ),
		'subset' => urlencode( 'latin,latin-ext' ),
	);
		
	$fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
 
	
			wp_enqueue_style( 'hexwp_google_font', esc_url_raw( $fonts_url ), array(), '1.0.0' );
	 } }}
}
   function hexwp_perview_register() {
	
	$var='1.0';
	wp_enqueue_style('hexwp_header_perview_css', hexwp_DIR .'/css/header/perview.css',$var);
	wp_enqueue_script('hexwp_header_perview_js', hexwp_DIR .'/js/lib/perview.js', array('jquery') ,$var );
    wp_localize_script('hexwp_header_perview_js', 'hexwp_ajax', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
	
}
add_action( 'customize_preview_init', 'hexwp_perview_register' );
