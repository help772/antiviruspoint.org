<?php
function hexwpdemoimport_homepage_array() {
	return array(
  	 	1 			=> 'blog_image' ,
  	 	2 			=> 'blog' , 
 		3			=> 'product_image' ,
 		4			=> 'cat_image' ,
 		5 			=> 'cat' ,
 		6 			=> 'product' ,
 		7 			=> 'product_page' ,
   	 	8			=> 'about_image' ,
  	 	9			=> 'contactform' ,
 	 	10			=> 'slider_image' ,
 	 	11			=> 'slider',
   	 	12=> 		'homepage_image' ,
  	 	13			=> 'homepage',
  	 	14			=> 'header_homepage' ,		
  	 	15			=> 'menu',		
 	 	16			=> 'widgets',		
   	);

}

$hexwpdemoimport_demo_settings = array(
  'menu_parent' => 'themes.php',
  'menu_title'  => __('Import Data Demo', 'hexwp'),
  'menu_type'   => 'add_submenu_page',
  'menu_slug'   => 'hexwpdemoimport',
);
 
    
	
 
$hexwpdemoimport_demo_options['custom'] 	= array(
	'title'     => __('Custom Import', 'hexwp'),
		'name'	=> array(
				'blog' 				=> __('Blog', 'hexwp'),
 				'product'			=> __('Product', 'hexwp'),
'product_page'			=> __('WooCommerce Pages', 'hexwp'),				
  				'contactform'		=> __('Contact us - About me', 'hexwp'),
    			'menu'				=> __('Menu', 'hexwp'),					  
				'widgets'		=> __('Widgets', 'hexwp'),
				'slider'		=> __('Slider', 'hexwp'),
				'homepage'		=> __('Homepage', 'hexwp'),
				'header'		=> __('Header', 'hexwp'),
    	 ), 
		'options'	=> array(
            'blog'				=> array(
				1 			=> 'blog_image' ,
				2 			=> 'blog' ,
 			),
          
            'product'			=> array(
 				1			=> 'cat_image' ,
 				2			=> 'product_image' ,
				3			=> 'cat' ,
 				4			=> 'product' ,
  			),
'product_page'			=> array(
  				1 			=> 'product_page' ,
 			),
			'contactform'		=> array(
				1			=> 'about_image' ,
			 	2 			=> 'contactform' ,
 			),	
			 						
 			'menu'	=> array(
				1			=> 'menu' ,
  			),
			'widgets'	=> array(
				1			=> 'widgets' ,
  			),
			'slider'	=> array(
				1			=> 'slider_image' ,
				2			=> 'slider' ,
  			),
			'homepage'	=> array(
				1			=> 'homepage_image' ,
				2			=> 'homepage' ,
  			),	
			'header'	=> array(
				1			=> 'header_homepage' ,
  			),			
			
	),	 
);  
  
  		$hexwpdemoimport_demo_options['homepage'] =  array(
			'title'		=>  __('Homepage', 'hexwp'),
			'options'	=> 	hexwpdemoimport_homepage_array(),           
		);
		
 
 
 
	hexwpdemoimport_Importer::instance( $hexwpdemoimport_demo_settings, $hexwpdemoimport_demo_options );